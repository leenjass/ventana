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

class PPATProductModel extends ObjectModel
{
	/** @var integer Unique ID */
	public $id_ppat_product;

	/** @var integer Product ID */
	public $id_product;

	/** @var integer Shop ID */
	public $id_shop;

	/** @var string Unit Name */
	public $enabled;

	/** @var integer min row */
	public $min_row;

	/** @var integer max row */
	public $max_row;

	/** @var integer min col */
	public $min_col;

	/** @var integer max col */
	public $max_col;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'ppat_product',
		'primary' => 'id_ppat_product',
		'fields' => array(
			'id_product'  => array('type' => self::TYPE_INT),
			'id_shop' => array('type' => self::TYPE_INT),
			'enabled' => array('type' => self::TYPE_INT),
			'min_row' => array('type' => self::TYPE_FLOAT),
			'max_row' => array('type' => self::TYPE_FLOAT),
			'min_col' => array('type' => self::TYPE_FLOAT),
			'max_col' => array('type' => self::TYPE_FLOAT)
		)
	);

	public function load($id_product, $id_shop)
	{
		$sql = new DbQuery();
		$sql->select('*');
		$sql->from('ppat_product');
		$sql->where('id_product = '.(int)$id_product);
		$sql->where('id_shop = '.(int)$id_shop);

		$row = DB::getInstance()->getRow($sql);

		if ($row)
			$this->hydrate($row);
		else
			return false;
	}

    /**
     * Delete by product ID
     * @param $id_product
     * @param $id_shop
     */
	public static function deleteByProduct($id_product, $id_shop)
    {
        DB::getInstance()->delete(self::$definition['table'], 'id_product=' . (int)$id_product . ' AND id_shop=' . (int)$id_shop);
    }
};