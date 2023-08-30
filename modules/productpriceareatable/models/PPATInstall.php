<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 * @author    Musaffar Patel
 * @copyright 2016-2017 Musaffar Patel
 * @license   LICENSE.txt
 */

class PPATInstall
{
	public static function install()
	{
		self::installDB();
		self::installData();
	}

	public static function installDB()
	{
		$return = true;
		$return &= Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ppat_product` (			
			  `id_ppat_product` int(10) unsigned NOT NULL AUTO_INCREMENT,			
			  `id_product` int(10) unsigned NOT NULL,
			  `id_shop` int(10) unsigned NOT NULL,
			  `enabled` tinyint(3) unsigned NOT NULL DEFAULT 0,
			  `min_row` decimal(15,2) NOT NULL DEFAULT "0.00",
			  `max_row` decimal(15,2) NOT NULL DEFAULT "0.00",
			  `min_col` decimal(15,2) NOT NULL DEFAULT "0.00",
			  `max_col` decimal(15,2) NOT NULL DEFAULT "0.00",
			  PRIMARY KEY (`id_ppat_product`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;');

		$return &= Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ppat_product_option_label_lang` (				
			  `id_product_option_label` int(10) unsigned NOT NULL AUTO_INCREMENT,				
			  `id_product` int(10) unsigned NOT NULL,
			  `id_lang` int(10) unsigned NOT NULL,
			  `id_shop` int(10) unsigned NOT NULL,
			  `text` varchar(255) DEFAULT NULL,
			  PRIMARY KEY (`id_product_option_label`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;');

		$return &= Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ppat_product_price_table` (
			  `id_price` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `row` varchar(32) NOT NULL,			  
			  `col` varchar(32) NOT NULL,
			  `row_max` varchar(32) NOT NULL,			  
			  `col_max` varchar(32) NOT NULL,			  
			  `price` varchar(15) NOT NULL DEFAULT "",
			  `id_option` int(11) NOT NULL,
			  PRIMARY KEY (`id_price`)			  
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;');

		$return &= Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ppat_product_table_options` (
				`id_option` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`id_product` int(11) NOT NULL,
				`position` int(10) unsigned DEFAULT "0",
				`enabled` tinyint(3) unsigned NOT NULL DEFAULT "0",
				`lookup_rounding_mode` varchar(6) NOT NULL DEFAULT "up",
				`default_row` varchar(32) NOT NULL DEFAULT "0",
				`default_col` varchar(32) NOT NULL DEFAULT "0",
			PRIMARY KEY (`id_option`)			
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;');

		$return &= Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ppat_product_table_options_lang` (
			  `id_option` int(10) unsigned NOT NULL,
			  `id_lang` int(11) unsigned NOT NULL,
			  `option_text` varchar(128) DEFAULT NULL
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;');

		$return &= Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ppat_product_unit` (
			  `id_ppat_unit` int(10) unsigned NOT NULL,
			  `id_product` int(10) unsigned NOT NULL,
			  `visible` bit(1) NOT NULL,
			  PRIMARY KEY (`id_ppat_unit`,`id_product`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;');

		$return &= Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ppat_unit` (
			  `id_ppat_unit` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `id_shop` mediumint(8) unsigned NOT NULL DEFAULT "1",
			  `name` varchar(32) NOT NULL,
			  `type` varchar(32) NOT NULL,
			  `position` int(10) unsigned NOT NULL DEFAULT "0",
			  PRIMARY KEY (`id_ppat_unit`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;');

		$return &= Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ppat_unit_lang` (
			  `id_ppat_unit` int(10) unsigned NOT NULL,
			  `id_lang` int(10) unsigned NOT NULL,
			  `display_name` varchar(128) NOT NULL,
			  `suffix` varchar(128) NOT NULL,
			  PRIMARY KEY (`id_ppat_unit`,`id_lang`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;');

		self::addColumn('cart_product', 'ppat', 'SMALLINT UNSIGNED DEFAULT 0');
		self::addColumn('customized_data', 'ppat_dimensions', 'TEXT');
		self::addColumn('customization_field', 'ppat', 'tinyint(1) UNSIGNED NOT NULL');
		self::installData();
		return $return;
	}

	private static function addColumn($table, $name, $type)
	{
		try
		{
			$return = Db::getInstance()->execute('ALTER TABLE  `'._DB_PREFIX_.''.pSQL($table).'` ADD `'.pSQL($name).'` '.pSQL($type));
		} catch(Exception $e)
		{
			return true;
		}
		return true;
	}

	private static function dropColumn($table, $name)
	{
		Db::getInstance()->execute('ALTER TABLE  `'._DB_PREFIX_.''.pSQL($table).'` DROP `'.pSL($name).'`');
	}

	private static function dropTable($table_name)
	{
		Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.''.pSQL($table_name).'`;');
	}

	private static function addUnit($name, $display_name, $suffix, $id_lang, $id_shop)
	{
		$id_ppat_unit = -1;
		$sql = 'SELECT
					id_ppat_unit
				FROM '._DB_PREFIX_.'ppat_unit
				WHERE id_shop = '.(int)$id_shop."
				AND name LIKE '$name'";
		$result = Db::getInstance()->executeS($sql);
		if ($result) $id_ppat_unit = $result[0]['id_ppat_unit'];

		if ($id_ppat_unit == -1)
		{
			Db::getInstance()->insert('ppat_unit', array(
				'id_shop' => (int)$id_shop,
				'name' => pSQL($name),
				'position' => 0,
			));
			$id_ppat_unit = Db::getInstance()->Insert_ID();
		}

		$sql = 'SELECT COUNT(*) AS total_count
				FROM '._DB_PREFIX_.'ppat_unit_lang
				WHERE id_ppat_unit = '.(int)$id_ppat_unit.'
				AND id_lang = '.(int)$id_lang.'
				';
		$result = Db::getInstance()->executeS($sql);
		if ($result && $result[0]['total_count'] == 0)
		{
			Db::getInstance()->insert('ppat_unit_lang', array(
				'id_ppat_unit' => (int)$id_ppat_unit,
				'id_lang' => (int)$id_lang,
				'display_name' => pSQL($display_name),
				'suffix' => pSQL($suffix)
			));
		}
	}

	public static function installData()
	{
		$languages = Language::getLanguages();
		$shops = ShopCore::getCompleteListOfShopsID();

		/* Install Dimensions */
		foreach ($shops as $id_shop)
		{
			foreach ($languages as $language)
			{
				self::addUnit('row', 'Height', 'mm', $language['id_lang'], $id_shop);
				self::addUnit('col', 'Width', 'mm', $language['id_lang'], $id_shop);
			}
		}
	}

	public static function uninstallData()
	{
	}

	public static function uninstall()
	{
		self::dropTable('ppat_product');
		self::dropTable('ppat_product_option_label_lang');
		self::dropTable('ppat_product_price_table');
		self::dropTable('ppat_product_table_options');
		self::dropTable('ppat_product_table_options_lang');
		self::dropTable('ppat_product_unit');
		self::dropTable('ppat_unit');
		self::dropTable('ppat_unit_lang');
	}

}