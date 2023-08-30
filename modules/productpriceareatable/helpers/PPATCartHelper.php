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

class PPATCartHelper
{
	protected static $_cache;	

	private static function do_encoding($matches)
	{
		return mb_convert_encoding(pack('H*', $matches[1]), 'UTF-8', 'UTF-16');
	}

	public static function RawJsonEncode($input)
	{
		return preg_replace_callback(
			'/\\\\u([0-9a-zA-Z]{4})/',
			array('PPATCartHelper', 'do_encoding'),
            json_encode($input)
		);
	}

    /**
     * Get the measuerments display string for the dimensions ion the cart
     * @param $cart_unit_collection
     */
    public static function getCustomizationDisplayText($cart_unit_collection, $id_lang = 0, $id_shop = 0)
    {
        $id_product = $cart_unit_collection['id_product'];

        if ($id_lang == 0) {
            $id_lang = Context::getContext()->language->id;
        }
        if ($id_shop == 0) {
            $id_shop = Context::getContext()->shop->id;
        }

        $value = '';
        if (is_array($cart_unit_collection)) {
            foreach ($cart_unit_collection as $key => $cart_unit) {
                if (is_numeric($key)) {
                    if (!empty($cart_unit->display_value)) {
                        $value .= $cart_unit->display_name . ' : ' . $cart_unit->display_value . ' ' . $cart_unit->symbol . '. ';
                    } else {
                        $value .= $cart_unit->display_name . ' : ' . $cart_unit->value.' ';
                    }
                }
            }
        }

        if (!empty($cart_unit_collection['id_option'])) {
            $ppat_product_option_lang = new PPATProductOptionLabelLangModel();
            $ppat_product_option_lang->loadByLang($id_product, $id_lang, $id_shop);
            $value .= ' '. $ppat_product_option_lang->text . ' : ' . PPATProductTableOptionHelper::getTableName($cart_unit_collection['id_option'], Context::getContext()->language->id);
        }
        return $value;
    }

    /**
     * Get customization field for the sample and create one if it does not exist
     * @param $id_product
     * @param $id_shop
     * @throws PrestaShopDatabaseException
     */
    public static function getCustomizationField($id_product, $id_shop)
    {
        $languages = Language::getLanguages();
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('customization_field', 'cf');
        $sql->innerJoin('customization_field_lang', 'cfl', 'cfl.id_customization_field = cf.id_customization_field');
        $sql->where('id_product = ' . (int)$id_product);
        $sql->where('ppat = 1');
        $sql->groupBy('cf.id_customization_field');
        $row = DB::getInstance()->getRow($sql);

        if (empty($row)) {
            DB::getInstance()->insert('customization_field', array(
                'id_product' => (int)$id_product,
                'type' => 1,
                'required' => 0,
                'is_module' => 1,
                'is_deleted' => 0,
                'ppat' => 1
            ));
            $id_customization_field = Db::getInstance()->Insert_ID();
            foreach ($languages as $language) {
                DB::getInstance()->insert('customization_field_lang', array(
                    'id_customization_field' => (int)$id_customization_field,
                    'id_lang' => (int)$language['id_lang'],
                    'id_shop' => (int)$id_shop,
                    'name' => pSQL(PPATTranslationHelper::translate('dimensions', $language['iso_code'], '', false))
                ));
            }
        } else {
            $id_customization_field = $row['id_customization_field'];
            foreach ($languages as $language) {
                DB::getInstance()->update('customization_field_lang', array(
                    'name' => pSQL(PPATTranslationHelper::translate('dimensions', $language['iso_code'], '', false))
                ), 'id_customization_field = ' . (int)$id_customization_field);
            }
        }
        return $id_customization_field;
    }

	/**
	 * Create customization for the cart with dimensions
	 * @param $id_product
	 * @param $id_cart
	 * @param $ipa
	 * @param $id_address_delivery
	 * @param $cart_unit_collection
	 * @param $quantity
	 * @param int $id_shop
	 * @param string $awp_vars
	 * @return bool
	 * @throws PrestaShopDatabaseException
	 */
	public static function addCustomization($id_product, $id_cart, $ipa, $id_address_delivery, $cart_unit_collection, $id_module, $quantity, $id_shop = 1, $awp_vars = '')
	{
		if (!$cart_unit_collection) return false;
		if ($id_product == '') return false;
		if ($id_cart == '') return false;

        /*if (version_compare(_PS_VERSION_, '1.7.4.2', '>=')) {
            $quantity = 0;
        } else {
            $quantity = 1;
        }*/

		Db::getInstance()->insert('customization', array(
			'id_product_attribute' => (int)$ipa,
			'id_cart' => (int)$id_cart,
			'id_product' => (int)$id_product,
			'id_address_delivery' => (int)$id_address_delivery,
			'quantity' => (int)$quantity,
			'in_cart' => 1,
		));
		//customization qty is always 1 in PS 1.7 as each customization can occupy indivudla line items in the cart

		$id_customization = Db::getInstance()->Insert_ID();
        $value = self::getCustomizationDisplayText($cart_unit_collection);
        $id_customization_field = self::getCustomizationField($id_product, $id_shop);
        //self::addCustomizedData($id_customization, self::getIDCustomizationField($id_product), $value, $id_module, $cart_unit_collection);
        self::addCustomizedData($id_customization, $id_customization_field, $value, $id_module, $cart_unit_collection);

        Db::getInstance()->update('cart_product', array(
            'quantity' => 1,
            'ppat' => 1
        ),
            'id_cart=' . (int)$id_cart . '
			AND id_product = ' . (int)$id_product . '
			AND id_product_attribute = ' . (int)$ipa . '
			AND id_shop = ' . (int)$id_shop . '
			AND id_customization = ' . (int)$id_customization
        );
        return $id_customization;
	}

    /**
     * Add customized data entry
     * @param $id_customization
     * @param $index
     * @param $value
     * @param $id_module
     * @param $cart_unit_collection
     */
    public static function addCustomizedData($id_customization, $index, $value, $id_module, $cart_unit_collection)
    {
        Db::getInstance()->insert('customized_data', array(
            'id_customization' => (int)$id_customization,
            'type' => 1,
            'index' => (int)$index,
            'value' => pSQL($value),
            'ppat_dimensions' => pSQL(self::RawJsonEncode($cart_unit_collection), true),
            'id_module' => (int)$id_module
        ));
    }

    /**
	 * Determine if a product customization already exists in the cart
	 * @param $id_product
	 * @param $id_product_attribute
	 * @param $id_cart
	 * @param $cart_unit_collection
	 * @return int
	 */
	public static function getCustomizationID($id_product, $id_product_attribute, $id_cart, $cart_unit_collection)
	{
		$sql = new DbQuery();
		$sql->select('cd.id_customization');
		$sql->from('customized_data', 'cd');
		$sql->innerJoin('customization', 'c', 'cd.id_customization = c.id_customization');
		$sql->where('c.id_product = ' . (int)$id_product);
		$sql->where('c.id_product_attribute = ' . (int)$id_product_attribute);
		$sql->where('c.id_cart = ' . (int)$id_cart);
		$sql->where("cd.ppat_dimensions = '".$cart_unit_collection."'");
		$row = DB::getInstance()->getRow($sql);

		if (!empty($row)) {
			return $row['id_customization'];
		} else {
			return 0;
		}
	}

   /**
     * Get customizaed data row by id_customization
     * @param $id_customization
     * @param $id_product
     * @param $id_product_attribute
     * @param $id_cart
     */
    public static function hasPPATOnlyCustomizedData($id_customization, $id_product, $id_product_attribute, $id_cart)
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('customized_data', 'cd');
        $sql->innerJoin('customization', 'c', 'cd.id_customization = c.id_customization');
        $sql->where('c.id_product = ' . (int)$id_product);
        $sql->where('c.id_product_attribute = ' . (int)$id_product_attribute);
        $sql->where('c.id_cart = ' . (int)$id_cart);
        $sql->where('cd.id_customization = ' . (int)$id_customization);
        $result = DB::getInstance()->executeS($sql);

        $ppbs_only = true;
        if (!empty($result)) {
            foreach ($result as $row) {
                if (empty($row['ppat_dimensions']) && $row['index'] > 0) {
                    $ppbs_only = false;
                    break;
                }
            }
        }
        return $ppbs_only;
    }

    /**
     * Get existing customization ID for a product in the cart
     * @param $id_product
     * @param $id_product_attribute
     * @param $id_cart
     */
    public static function getExistingCustomizationId($id_product, $id_product_attribute, $id_cart)
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('customization');
        $sql->where('id_product = ' . (int)$id_product);
        $sql->where('id_product_attribute = ' . (int)$id_product_attribute);
        $sql->where('id_cart = ' . (int)$id_cart);

        $row = DB::getInstance()->getRow($sql);

        if (empty($row['id_customization'])) {
            return 0;
        } else {
            return $row['id_customization'];
        }
    }

    /**
     * Increment Product customization quantity by 1
     * @param $id_product
     * @param $id_product_attribute
     * @param $id_customization
     * @param $id_cart
     */
    public static function incrementProductCustomizationQuantity($id_product, $id_product_attribute, $id_customization, $id_cart)
    {
        $sql = 'UPDATE ' . _DB_PREFIX_ . 'customization SET quantity = quantity + 1 WHERE id_cart = ' . (int)$id_cart . ' AND id_product = ' . (int)$id_product . ' AND id_product_attribute = ' . (int)$id_product_attribute . ' AND id_customization = ' . (int)$id_customization;
        DB::getInstance()->execute($sql);
    }

    /**
     * Decrement Product customization quantity by 1
     * @param $id_product
     * @param $id_product_attribute
     * @param $id_customization
     * @param $id_cart
     */
    public static function decrementProductCustomizationQuantity($id_product, $id_product_attribute, $id_customization, $id_cart)
    {
        $sql = 'UPDATE ' . _DB_PREFIX_ . 'customization SET quantity = quantity - 1 WHERE quantity > 1 AND id_cart = ' . (int)$id_cart . ' AND id_product = ' . (int)$id_product . ' AND id_product_attribute = ' . (int)$id_product_attribute . ' AND id_customization = ' . (int)$id_customization;
        DB::getInstance()->execute($sql);
	}
	
    /**
     * Determine if a line item in the cart is a product price area table product
     * @param $id_cart
     * @param $id_product
     * @param $id_product_attribute
     * @param $id_customization
     */
    public static function isCartProductPPAT($id_cart, $id_product, $id_product_attribute, $id_customization)
    {
        if ($id_customization == 0) {
            return false;
        }

        $cache_id = 'isCartProductPPAT::' . (int)$id_product . '-' . (int)$id_product_attribute . '-' . (int)$id_customization . '-' . (int)$id_cart;

        if (!isset(self::$_cache[$cache_id])) {
            $sql = new DbQuery();
            $sql->select('ppat_dimensions');
            $sql->from('customized_data', 'cd');
            $sql->innerJoin('customization', 'c', 'cd.id_customization = c.id_customization');
            $sql->where('c.id_cart = ' . (int)$id_cart);
            $sql->where('c.id_product = ' . (int)$id_product);

            if ($id_product_attribute > 0) {
                $sql->where('c.id_product_attribute = ' . (int)$id_product_attribute);
            }
            $sql->where('cd.id_customization = ' . (int)$id_customization);
            $row = Db::getInstance()->getRow($sql);

            if (empty($row['ppat_dimensions'])) {
                self::$_cache[$cache_id] = '';
                return false;
            } else {
                self::$_cache[$cache_id] = $row['ppat_dimensions'];
                return true;
            }
        } else {
            if (empty(self::$_cache[$cache_id])) {
                return false;
            } else {
                return true;
            }
        }
    }	
}