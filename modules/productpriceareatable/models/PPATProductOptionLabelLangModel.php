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

if (!defined('_PS_VERSION_'))
	exit;

class PPATProductOptionLabelLangModel extends ObjectModel
{
	/** @var integer Unique ID */
	public $id_product_option_label;

	/** @var integer Product ID */
	public $id_product;

	/** @var integer Shop ID */
	public $id_lang;

	/** @var string Unit Name */
	public $id_shop;

	/** @var integer min row */
	public $text;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'ppat_product_option_label_lang',
		'primary' => 'id_product_option_label',
		'fields' => array(
			'id_product'  => array('type' => self::TYPE_INT),
			'id_lang' => array('type' => self::TYPE_INT),
			'id_shop' => array('type' => self::TYPE_FLOAT),
			'text' => array('type' => self::TYPE_STRING)
		)
	);

	public function loadByProduct($id_product, $id_shop)
	{
		$languages = Language::getLanguages();

		$sql = new DbQuery();
		$sql->select('*');
		$sql->from(self::$definition['table']);
		$sql->where('id_product = '.(int)$id_product);
		$sql->where('id_shop = '.(int)$id_shop);

		$result = DB::getInstance()->executeS($sql);
        $return = array();
        $return['id_product'] = $id_product;
        $return['id_shop'] = $id_shop;

        if ($result) {
            foreach ($languages as $lang)
                $return['text'][$lang['id_lang']] = '';

            foreach ($result as $row)
                $return['text'][$row['id_lang']] = $row['text'];
        } else {
            foreach ($languages as $lang) {
                $return['text'][$lang['id_lang']] = '';
            }
        }
        return $return;
    }

	public static function deleteByProduct($id_product, $id_shop)
	{
		DB::getInstance()->delete(self::$definition['table'], 'id_product='.(int)$id_product.' AND id_shop='.(int)$id_shop);
	}

	public function loadByLang($id_product, $id_lang, $id_shop)
	{
		$sql = new DbQuery();
		$sql->select('*');
		$sql->from(self::$definition['table']);
		$sql->where('id_product = '.(int)$id_product);
		$sql->where('id_lang = '.(int)$id_lang);
		$sql->where('id_shop = '.(int)$id_shop);

		$row = DB::getInstance()->getRow($sql);

		if ($row)
			$this->hydrate($row);
		else
			return false;
	}

};