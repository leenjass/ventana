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

class PPATProductHelper
{
	protected static $_pricesPPBS;

    /**
     * Return Group Customer Discount / Group Discount by Category as a Percentage
     * @param $id_product
     * @param $id_group
     * @return float|int
     */
    public static function getGroupReduction($id_product, $id_group)
    {
        $reduction_from_category = GroupReduction::getValueForProduct($id_product, $id_group);
        if ($reduction_from_category !== false) {
            $group_reduction = (float)$reduction_from_category * 100;
        } else {
            $group_reduction = Group::getReductionByIdGroup($id_group);
        }
        return $group_reduction;
    }

	/**
	 * Get Product info such as price, attribute p[rice based on Product ID and attributes array (group)
	 * @param $id_product
	 * @param $group
	 */
	public static function getProductInfo($id_product, $group, $id_product_attribute = 0)
	{
		if (!empty($group))
			$id_product_attribute = Product::getIdProductAttributeByIdAttributes((int)$id_product, $group);

        $group_reduction = 0;

        if (!empty(Context::getContext()->customer->id_default_group)) {
            $id_group = Context::getContext()->customer->id_default_group;
            $group_reduction = self::getGroupReduction($id_product, $id_group);
        }

        $product_obj = new Product($id_product);
		$product = [];
		$product['id_product'] = $id_product;
		$product['id_product_attribute'] = $id_product_attribute;
		$product['out_of_stock'] = $product_obj->out_of_stock;
		$product['id_category_default'] = $product_obj->id_category_default;
		$product['link_rewrite'] = ''; //$product_obj->link_rewrite;
		$product['ean13'] = $product_obj->ean13;
		$product['minimal_quantity'] = $product_obj->minimal_quantity;
		$product['unit_price_ratio'] = $product_obj->unit_price_ratio;
		$product['price_display'] = (int)Product::getTaxCalculationMethod(Context::getContext()->cookie->id_customer);

		$product_properties = Product::getProductProperties(Context::getContext()->language->id, $product, null);
		$product_properties['base_price_exc_tax'] = $product_obj->price;
        $product_properties['group_reduction'] = $group_reduction;

        return $product_properties;
	}

	/**
	 * Get Attribute price
	 * @param $id_product
	 * @param $id_shop
	 * @param $id_product_attribute
	 * @return mixed
	 * @throws PrestaShopDatabaseException
	 */
	public static function getProductAttributePrice($id_product, $id_shop, $id_product_attribute)
	{
        if ((int)$id_product_attribute == 0) {
            return 0;
        }
		
		$cache_id_2 = $id_product.'-'.$id_shop;
		if (!isset(self::$_pricesPPBS[$cache_id_2]))
		{
			$sql = new DbQuery();
			$sql->select('product_shop.`price`, product_shop.`ecotax`');
			$sql->from('product', 'p');
			$sql->innerJoin('product_shop', 'product_shop', '(product_shop.id_product=p.id_product AND product_shop.id_shop = '.(int)$id_shop.')');
			$sql->where('p.`id_product` = '.(int)$id_product);

			if (Combination::isFeatureActive())
			{
				$sql->select('product_attribute_shop.id_product_attribute, product_attribute_shop.`price` AS attribute_price, product_attribute_shop.default_on');
				$sql->leftJoin('product_attribute', 'pa', 'pa.`id_product` = p.`id_product`');
				$sql->leftJoin('product_attribute_shop', 'product_attribute_shop', '(product_attribute_shop.id_product_attribute = pa.id_product_attribute AND product_attribute_shop.id_shop = '.(int)$id_shop.')');
			}
			else
				$sql->select('0 as id_product_attribute');

			$res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

			foreach ($res as $row)
			{
				$array_tmp = array(
					'price' => $row['price'],
					'ecotax' => $row['ecotax'],
					'attribute_price' => (isset($row['attribute_price']) ? $row['attribute_price'] : null)
				);
				self::$_pricesPPBS[$cache_id_2][(int)$row['id_product_attribute']] = $array_tmp;

				if (isset($row['default_on']) && $row['default_on'] == 1)
					self::$_pricesPPBS[$cache_id_2][0] = $array_tmp;
			}
		}
		/*if (!isset(self::$_pricesLevel2[$cache_id_2][(int)$params['id_product_attribute']]))
			return;*/
		$result = self::$_pricesPPBS[$cache_id_2][(int)$id_product_attribute];
		return $result;
	}

	/**
	 * Ge the ppat data for an item in the cart
	 * @param $id_product
	 * @param $id_cart
	 * @param $ipa
	 * @param int $id_shop
	 * @param int $id_customization
	 * @return array|bool|false|mysqli_result|null|PDOStatement|resource
	 * @throws PrestaShopDatabaseException
	 */
	public static function getCartProductUnits($id_product, $id_cart, $ipa, $id_shop = 1, $id_customization = 0)
	{
		$cart_unit_collection = array();

		if ($id_product == '') return false;
		if ($id_cart == '') return false;

		$sql = 'SELECT
					ppat_dimensions,
					quantity
				FROM '._DB_PREFIX_.'customized_data cd
				INNER JOIN '._DB_PREFIX_.'customization c ON cd.id_customization = c.id_customization
				WHERE 
				    c.id_cart = '.(int)$id_cart.'
				    AND c.id_product = '.(int)$id_product.'
				    AND c.id_product_attribute = '.(int)$ipa;

		if ($id_customization > 0)
			$sql .= ' AND cd.id_customization = '.(int)$id_customization;

		$rows = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

		if (is_array($rows) && count($rows) > 0)
			return $rows;
		else return false;
	}

    /**
     * @param $id_product
     * @param $id_shop
     * @return bool
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public static function isPPATProduct($id_product, $id_shop)
    {
        $ppat_product = new PPATProductModel();
        $ppat_product->load($id_product, $id_shop);

        if ($ppat_product->enabled == false || !isset($ppat_product->enabled)) {
            return false;
        } else {
            return true;
        }
    }
}