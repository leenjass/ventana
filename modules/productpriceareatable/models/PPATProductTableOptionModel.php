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

class PPATProductTableOptionModel extends ObjectModel
{
	/** @var integer Option ID */
	public $id_option;

	/** @var integer Product ID */
	public $id_product;

	/** @var integer Position (Index) */
	public $position;

	/** @var boolean Enabled */
	public $enabled;

	/** @var string Lookup_rounding_mode (up/down) */
	public $lookup_rounding_mode;

	/** @var float Default Row value */
	public $default_row;

	/** @var float Default Col value */
	public $default_col;

	/** @var string Option Text */
	public $option_text;


	/**
	 * @see ObjectModel::$definition
	 */

	public static $definition = array(
		'table' => 'ppat_product_table_options',
		'primary' => 'id_option',
		'multilang' => true,
		'fields' => array(
			'id_product' => array('type' => self::TYPE_INT),
			'position' => array('type' => self::TYPE_INT),
			'enabled' => array('type' => self::TYPE_INT),
			'lookup_rounding_mode' => array('type' => self::TYPE_STRING),
			'default_row' => array('type' => self::TYPE_FLOAT),
			'default_col' => array('type' => self::TYPE_FLOAT),
			'option_text' => array('type' => self::TYPE_STRING, 'lang' => true),
		)
	);

	public function loadByProduct($id_product, $id_lang = null)
	{
		$sql = new DbQuery();
		$sql->select('*');
		$sql->from(self::$definition['table']);
		$sql->where('id_product = '.(int)$id_product);
		$sql->orderBy('position');

		if (empty($id_lang))
			$sql->innerJoin('ppat_product_table_options_lang', 'l', _DB_PREFIX_.'ppat_product_table_options.id_option = l.id_option');
		else
			$sql->innerJoin('ppat_product_table_options_lang', 'l', _DB_PREFIX_.'ppat_product_table_options.id_option = l.id_option AND l.id_lang = '.(int)$id_lang);

		$result = DB::getInstance()->executeS($sql);

		if ($result) {
			if (empty($id_lang))
				return $this->hydrateCollection('PPATProductTableOptionModel', $result);
			else
				return $this->hydrateCollection('PPATProductTableOptionModel', $result, $id_lang);
		}
		else
			return array();
	}
}
