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

class PPATProductPriceTableHelper
{
	/**
	 * Get all options available for a product
	 * @param $id_product
	 * @param $id_shop
	 * @param $id_lang
	 * @param bool $return_raw
	 * @return array|false|mysqli_result|null|PDOStatement|resource
	 */
	public static function getPriceTableByOption($id_option)
	{
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from(PPATProductPriceTableModel::$definition['table']);
        $sql->where('id_option = ' . (int)$id_option);
		$result = DB::getInstance()->executeS($sql);
		if (empty($result)) {
		    return array();
        } else {
            return $result;
        }
    }

    /**
     * Delete all price tables associated with a product
     * @param $id_product
     */
    public static function deleteByProduct($id_product)
    {
        $collection_options = PPATProductTableOptionHelper::getProductTableOptions($id_product);
        foreach ($collection_options as $collection_option) {
            DB::getInstance()->delete(PPATProductPriceTableModel::$definition['table'], 'id_option=' . (int)$collection_option['id_option']);
        }
    }
}
