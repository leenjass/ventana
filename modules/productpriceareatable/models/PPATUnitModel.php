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

class PPATUnitModel extends ObjectModel
{
	/** @var integer Unique ID */
	public $id_ppat_unit;

	/** @var integer Shop ID */
	public $id_shop;

	/** @var string Unit Name */
	public $name;

	/** @var string Display name */
	public $display_name;

	/** @var string Unit Suffix */
	public $suffix;

	/** @var integer display position */
	public $position;

	/** @var string render control type */
	public $type;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'ppat_unit',
		'primary' => 'id_ppat_unit',
		'multilang' => true,
		'fields' => array(
			'id_shop'     =>	array(
				'type' => self::TYPE_INT,
			),
			'name'     =>	array(
				'type' => self::TYPE_STRING,
				'validate' => 'isMessage',
				'size' => 32,
				'required' => true
			),
			'display_name'  =>	array(
				'type' => self::TYPE_STRING,
				'validate' => 'isMessage',
				'size' => 32,
				'required' => true,
				'lang' => true
			),
			'suffix'        =>	array(
				'type' => self::TYPE_STRING,
				'validate' => 'isMessage',
				'size' => 12,
				'lang' => true,
				'required' => true
			),
			'type'    =>  array(
				'type' => self::TYPE_STRING,
				'validate' => 'isMessage',
				'size' => 24,
				'lang' => false,
				'required' => true
			),
			'position' => array(
				'type' => self::TYPE_INT
			)
		)
	);

	public static function getUnitDefinition($id_unit, $id_lang = 1)
	{
		$sql = '
			SELECT
				ppat_u.id_ppat_unit,
				ppat_u.name,
				ppat_u.position,
				ppat_ul.display_name,
				ppat_ul.suffix
			FROM `'._DB_PREFIX_.'ppat_unit` ppat_u
			JOIN `'._DB_PREFIX_.'ppat_unit_lang` ppat_ul ON (ppat_u.id_ppat_unit = ppat_ul.id_ppat_unit)
			WHERE ppat_ul.`id_lang` = '.(int)$id_lang.'
			AND ppat_u.id_ppat_unit = '.(int)$id_unit.'
			';
		$unit = Db::getInstance()->executeS($sql);
		if (is_array($unit)) return $unit[0];
		else return false;
	}

	/**
	 * @param int $id_lang
	 * @param int $id_shop
	 * @return array|false|mysqli_result|null|PDOStatement|resource
	 * @throws PrestaShopDatabaseException
	 */
	public static function getUnits($id_lang = 1, $id_shop = 1)
	{
		$sql = '
			SELECT
				ppat_u.id_ppat_unit,
				ppat_u.name,
				ppat_u.position,
				ppat_u.type,
				ppat_u.id_shop,
				ppat_ul.display_name,
				ppat_ul.suffix
			FROM `'._DB_PREFIX_.'ppat_unit` ppat_u
			JOIN `'._DB_PREFIX_.'ppat_unit_lang` ppat_ul ON (ppat_u.id_ppat_unit = ppat_ul.id_ppat_unit)
			WHERE ppat_ul.`id_lang` = '.(int)$id_lang.'
			AND ppat_u.id_shop = '.(int)$id_shop.'
			ORDER BY ppat_u.position ASC';

		$result = Db::getInstance()->executeS($sql);
		return $result;
	}

	public static function getUnitsList($id_lang = 1, $id_shop = null)
	{
		$units_collection = array();
		$units = self::getUnits($id_lang);

		if (is_array($units))
		{
			foreach ($units as $unit)
			{
				$unit_ppat = [];
				$unit_ppat['id_ppat_unit'] = $unit['id_ppat_unit'];
				$unit_ppat['display_name'] = $unit['display_name'];
				$unit_ppat['name'] = $unit['name'];
				$unit_ppat['suffix'] = $unit['suffix'];
				$unit_ppat['type'] = $unit['type'];
				$unit_ppat['position'] = $unit['position'];
				$units_collection[$unit['name']] = $unit_ppat;
			}
		}
		return $units_collection;
	}
};