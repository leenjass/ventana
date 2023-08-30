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
class Product extends ProductCore
{
	/*
    * module: productpriceareatable
    * date: 2023-01-18 18:30:14
    * version: 2.0.18
    */
    public static function priceCalculation($id_shop, $id_product, $id_product_attribute, $id_country, $id_state, $zipcode, $id_currency,
		$id_group, $quantity, $use_tax, $decimals, $only_reduc, $use_reduc, $with_ecotax, &$specific_price, $use_group_reduction,
		$id_customer = 0, $use_customer_price = true, $id_cart = 0, $real_quantity = 0, $id_customization = 0)
	{
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
		include_once(_PS_MODULE_DIR_.'/productpriceareatable/lib/bootstrap.php');
		$module = Module::getInstanceByName('productpriceareatable');
		$ppat_front_cart_controller = new PPATFrontCartController($module);
		$price = parent::priceCalculation(
			$id_shop, $id_product, $id_product_attribute, $id_country, $id_state, $zipcode, $id_currency,
			$id_group, $quantity, $use_tax, $decimals, $only_reduc, $use_reduc, $with_ecotax, $specific_price, $use_group_reduction,
			$id_customer, $use_customer_price, $id_cart, $real_quantity
		);
		$params = array(
			'price' => $price,
			'quantity' => $quantity,
			'id_product' => $id_product,
			'id_product_attribute' => $id_product_attribute,
			'id_cart' => $id_cart,
			'id_shop' => $id_shop,
			'specific_price' => $specific_price,
			'id_country' => $id_country,
			'id_state' => $id_state,
			'zipcode' => $zipcode,
			'use_tax' => $use_tax,
			'id_customization' => $id_customization
		);
		return $ppat_front_cart_controller->priceCalculation($params);
	}
    /*
    * module: productpriceareatable
    * date: 2023-01-18 18:30:14
    * version: 2.0.18
    */
    public static function addProductCustomizationPrice(&$product, &$customized_datas)
    {
        parent::addProductCustomizationPrice($product, $customized_datas);
        if ($product['total_wt'] == 0) {
            $product['total_wt'] = $product['product_price_wt'];
        }
    }
}