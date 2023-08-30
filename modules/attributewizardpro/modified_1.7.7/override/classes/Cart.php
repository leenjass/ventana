<?php
/*
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2015 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

class Cart extends CartCore
{

    /** @var html AWP */
    public $special_instructions;

    public function getLastProduct()
    {
        $sql = '
			SELECT `id_product`, `id_product_attribute`, id_shop, `instructions`
			FROM `' . _DB_PREFIX_ . 'cart_product`
			WHERE `id_cart` = ' . (int) $this->id . '
			ORDER BY `date_add` DESC';

        $result = Db::getInstance()->getRow($sql);
        if ($result && isset($result['id_product']) && $result['id_product']) {
            foreach ($this->getProducts() as $product) {
                if ($result['id_product'] == $product['id_product'] && (
                    !$result['id_product_attribute'] || $result['id_product_attribute'] == $product['id_product_attribute']
                    )) {
                    return $product;
                }
            }
        }

        return false;
    }

    /**
     * Return cart products
     *
     * @result array Products
     */
    public function getProducts($refresh = false, $id_product = false, $id_country = null, $fullInfos = true, bool $keepOrderPrices = false)
    {
        if (!$this->id) {
            return array();
        }
        // Product cache must be strictly compared to NULL, or else an empty cart will add dozens of queries
        if ($this->_products !== null && !$refresh) {
            // Return product row with specified ID if it exists
            if (is_int($id_product)) {
                foreach ($this->_products as $product) {
                    if ($product['id_product'] == $id_product) {
                        return array($product);
                    }
                }
                return array();
            }
            return $this->_products;
        }

        // Build query
        $sql = new DbQuery();

        // Build SELECT
        $sql->select('p.`available_date`,product_shop.`show_price`, cp.`id_product_attribute`, cp.`id_product`, cp.`quantity` AS cart_quantity, cp.`instructions` AS instructions, cp.`instructions_valid` AS instructions_valid, cp.`instructions_id` AS instructions_id, cp.id_shop, pl.`name`, p.`is_virtual`,
						pl.`description_short`, pl.`available_now`, pl.`available_later`, product_shop.`id_category_default`, p.`id_supplier`,
                        p.`id_manufacturer`, m.`name` AS manufacturer_name, product_shop.`on_sale`, product_shop.`ecotax`, product_shop.`additional_shipping_cost`,
						product_shop.`available_for_order`, product_shop.`price`, product_shop.`active`, product_shop.`unity`, product_shop.`unit_price_ratio`,
						stock.`quantity` AS quantity_available, p.`width`, p.`height`, p.`depth`, stock.`out_of_stock`, p.`weight`,
						p.`date_add`, p.`date_upd`, IFNULL(stock.quantity, 0) as quantity, pl.`link_rewrite`, cl.`link_rewrite` AS category,
						CONCAT(LPAD(cp.`id_product`, 10, 0), LPAD(IFNULL(cp.`id_product_attribute`, 0), 10, 0), LPAD(IFNULL(cp.`instructions_valid`, 0), 10, 0), IFNULL(cp.`id_address_delivery`, 0)) AS unique_id, cp.id_address_delivery,
						product_shop.advanced_stock_management, ps.product_supplier_reference supplier_reference');

        // Build FROM
        $sql->from('cart_product', 'cp');

        // Build JOIN
        $sql->leftJoin('product', 'p', 'p.`id_product` = cp.`id_product`');
        $sql->innerJoin('product_shop', 'product_shop', '(product_shop.`id_shop` = cp.`id_shop` AND product_shop.`id_product` = p.`id_product`)');
        $sql->leftJoin('product_lang', 'pl', 'p.`id_product` = pl.`id_product` AND pl.`id_lang` = ' . (int) $this->id_lang . Shop::addSqlRestrictionOnLang('pl', 'cp.id_shop'));

        $sql->leftJoin('category_lang', 'cl', 'product_shop.`id_category_default` = cl.`id_category`
            AND cl.`id_lang` = ' . (int) $this->id_lang . Shop::addSqlRestrictionOnLang('cl', 'cp.id_shop'));

        $sql->leftJoin('product_supplier', 'ps', 'ps.`id_product` = cp.`id_product` AND ps.`id_product_attribute` = cp.`id_product_attribute` AND ps.`id_supplier` = p.`id_supplier`');
        $sql->leftJoin('manufacturer', 'm', 'm.`id_manufacturer` = p.`id_manufacturer`');

        // @todo test if everything is ok, then refactorise call of this method
        $sql->join(Product::sqlStock('cp', 'cp'));

        // Build WHERE clauses
        $sql->where('cp.`id_cart` = ' . (int) $this->id);
        if ($id_product) {
            $sql->where('cp.`id_product` = ' . (int) $id_product);
        }
        $sql->where('p.`id_product` IS NOT NULL');

        // Build ORDER BY
        $sql->orderBy('cp.`date_add`, cp.`id_product`, cp.`id_product_attribute` ASC');

        if (Customization::isFeatureActive()) {
            $sql->select('cu.`id_customization`, cu.`quantity` AS customization_quantity');
            $sql->leftJoin('customization', 'cu', 'p.`id_product` = cu.`id_product` AND cp.`id_product_attribute` = cu.`id_product_attribute` AND cp.`id_customization` = cu.`id_customization` AND cu.`id_cart` = ' . (int) $this->id);
            $sql->groupBy('cp.`id_product_attribute`, cp.`id_product`, cp.`id_shop`, cp.`id_customization`,  cp.`instructions_valid`');
        } else {
            $sql->select('NULL AS customization_quantity, NULL AS id_customization');
        }

        if (Combination::isFeatureActive()) {
            $sql->select('
				product_attribute_shop.`price` AS price_attribute, product_attribute_shop.`ecotax` AS ecotax_attr,
				IF (IFNULL(pa.`reference`, \'\') = \'\', p.`reference`, pa.`reference`) AS reference,
				(p.`weight`+ pa.`weight`) weight_attribute,
				IF (IFNULL(pa.`ean13`, \'\') = \'\', p.`ean13`, pa.`ean13`) AS ean13,
                IF (IFNULL(pa.`isbn`, \'\') = \'\', p.`isbn`, pa.`isbn`) AS isbn,
				IF (IFNULL(pa.`upc`, \'\') = \'\', p.`upc`, pa.`upc`) AS upc,
				IFNULL(product_attribute_shop.`minimal_quantity`, product_shop.`minimal_quantity`) as minimal_quantity,
				IF(product_attribute_shop.wholesale_price > 0,  product_attribute_shop.wholesale_price, product_shop.`wholesale_price`) wholesale_price
			');

            $sql->leftJoin('product_attribute', 'pa', 'pa.`id_product_attribute` = cp.`id_product_attribute`');
            $sql->leftJoin('product_attribute_shop', 'product_attribute_shop', '(product_attribute_shop.`id_shop` = cp.`id_shop` AND product_attribute_shop.`id_product_attribute` = pa.`id_product_attribute`)');
        } else {
            $sql->select('p.`reference` AS reference, p.`ean13`, p.`isbn`,
				p.`upc` AS upc, product_shop.`minimal_quantity` AS minimal_quantity, product_shop.`wholesale_price` wholesale_price');
        }

        $sql->select('image_shop.`id_image` id_image, il.`legend`');
        $sql->leftJoin('image_shop', 'image_shop', 'image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop=' . (int) $this->id_shop);
        $sql->leftJoin('image_lang', 'il', 'il.`id_image` = image_shop.`id_image` AND il.`id_lang` = ' . (int) $this->id_lang);
        $result = Db::getInstance()->executeS($sql);

        // Reset the cache before the following return, or else an empty cart will add dozens of queries
        $products_ids = array();
        $pa_ids = array();
        if ($result) {
            foreach ($result as $key => $row) {
                $products_ids[] = $row['id_product'];
                $pa_ids[] = $row['id_product_attribute'];
                $specific_price = SpecificPrice::getSpecificPrice($row['id_product'], $this->id_shop, $this->id_currency, $id_country, $this->id_shop_group, $row['cart_quantity'], $row['id_product_attribute'], $this->id_customer, $this->id);
                if ($specific_price) {
                    $reduction_type_row = array('reduction_type' => $specific_price['reduction_type']);
                } else {
                    $reduction_type_row = array('reduction_type' => 0);
                }

                $result[$key] = array_merge($row, $reduction_type_row);
            }
        }
        // Thus you can avoid one query per product, because there will be only one query for all the products of the cart
        Product::cacheProductsFeatures($products_ids);
        Cart::cacheSomeAttributesLists($pa_ids, $this->id_lang);

        $this->_products = array();
        if (empty($result)) {
            return array();
        }

        $ecotax_rate = (float) Tax::getProductEcotaxRate($this->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
        $apply_eco_tax = Product::$_taxCalculationMethod == PS_TAX_INC && (int) Configuration::get('PS_TAX');
        $cart_shop_context = Context::getContext()->cloneContext();

        $gifts = $this->getCartRules(CartRule::FILTER_ACTION_GIFT);
        $givenAwayProductsIds = array();

        if ($this->shouldSplitGiftProductsQuantity && count($gifts) > 0) {
            foreach ($gifts as $gift) {
                foreach ($result as $rowIndex => $row) {
                    if (!array_key_exists('is_gift', $result[$rowIndex])) {
                        $result[$rowIndex]['is_gift'] = false;
                    }

                    if (
                        $row['id_product'] == $gift['gift_product'] &&
                        $row['id_product_attribute'] == $gift['gift_product_attribute']
                    ) {
                        $row['is_gift'] = true;
                        $result[$rowIndex] = $row;
                    }
                }

                $index = $gift['gift_product'] . '-' . $gift['gift_product_attribute'];
                if (!array_key_exists($index, $givenAwayProductsIds)) {
                    $givenAwayProductsIds[$index] = 1;
                } else {
                    $givenAwayProductsIds[$index] ++;
                }
            }
        }
        foreach ($result as &$row) {
            if (!array_key_exists('is_gift', $row)) {
                $row['is_gift'] = false;
            }

            $givenAwayQuantity = 0;
            $giftIndex = $row['id_product'] . '-' . $row['id_product_attribute'];
            if ($row['is_gift'] && array_key_exists($giftIndex, $givenAwayProductsIds)) {
                $givenAwayQuantity = $givenAwayProductsIds[$giftIndex];
            }

            if (!$row['is_gift'] || (int) $row['cart_quantity'] === $givenAwayQuantity) {
                $row = $this->applyProductCalculations($row, $cart_shop_context, null, $keepOrderPrices);
            } else {
                // Separate products given away from those manually added to cart
                $this->_products[] = $this->applyProductCalculations($row, $cart_shop_context, $givenAwayQuantity, $keepOrderPrices);
                unset($row['is_gift']);
                $row = $this->applyProductCalculations(
                    $row, $cart_shop_context, $row['cart_quantity'] - $givenAwayQuantity, $keepOrderPrices
                );
            }

            $this->_products[] = $row;
        }

        return $this->_products;
    }

    public static function cacheSomeAttributesLists($ipa_list, $id_lang)
    {
        if (!Combination::isFeatureActive()) {
            return;
        }
        $pa_implode = array();
        $separator = Configuration::get('PS_ATTRIBUTE_ANCHOR_SEPARATOR');

        foreach ($ipa_list as $id_product_attribute) {
            if ((int) $id_product_attribute && !array_key_exists($id_product_attribute . '-' . $id_lang, self::$_attributesLists)) {
                $pa_implode[] = (int) $id_product_attribute;
                self::$_attributesLists[(int) $id_product_attribute . '-' . $id_lang] = array('attributes' => '', 'attributes_small' => '');
            }
        }

        if (!count($pa_implode)) {
            return;
        }

        $result = Db::getInstance()->executeS('SELECT pac.`id_product_attribute`, agl.`public_name` AS public_group_name, al.`name` AS attribute_name
            FROM `' . _DB_PREFIX_ . 'product_attribute_combination` pac
            LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a ON a.`id_attribute` = pac.`id_attribute`
            LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
            LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON (
                a.`id_attribute` = al.`id_attribute`
                AND al.`id_lang` = ' . (int) $id_lang . '
            )
            LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl ON (
                ag.`id_attribute_group` = agl.`id_attribute_group`
                AND agl.`id_lang` = ' . (int) $id_lang . '
            )
            WHERE pac.`id_product_attribute` IN (' . implode(',', $pa_implode) . ')
            ORDER BY ag.`position` ASC, a.`position` ASC');

        foreach ($result as $row) {
            self::$_attributesLists[$row['id_product_attribute'] . '-' . $id_lang]['attributes'] .= $row['public_group_name'] . ' : ' . $row['attribute_name'] . $separator . ' ';
            self::$_attributesLists[$row['id_product_attribute'] . '-' . $id_lang]['attributes_small'] .= $row['attribute_name'] . $separator . ' ';
        }

        foreach ($pa_implode as $id_product_attribute) {
            self::$_attributesLists[$id_product_attribute . '-' . $id_lang]['attributes'] = rtrim(
                self::$_attributesLists[$id_product_attribute . '-' . $id_lang]['attributes'], $separator . ' '
            );

            self::$_attributesLists[$id_product_attribute . '-' . $id_lang]['attributes_small'] = rtrim(
                self::$_attributesLists[$id_product_attribute . '-' . $id_lang]['attributes_small'], $separator . ' '
            );
        }
    }

    public function containsProduct($id_product, $id_product_attribute = 0, $id_customization = 0, $id_address_delivery = 0, $instructions = "")
    {
        $sql = 'SELECT sum(cp.`quantity`) as sum FROM `' . _DB_PREFIX_ . 'cart_product` cp';

        if ($id_customization) {
            $sql .= 'LEFT JOIN `' . _DB_PREFIX_ . 'customization` c ON (
					c.`id_product` = cp.`id_product`
					AND c.`id_product_attribute` = cp.`id_product_attribute`
				)';
        }
        $sql .= '
			WHERE cp.`id_product` = ' . (int) $id_product;
        if ($instructions == '') {
            $sql .= ' AND cp.`id_product_attribute` = ' . (int) $id_product_attribute;
        }
        if ($instructions != '') {
            $sql .= '
				AND cp.instructions_id in (SELECT cp.`instructions_id` FROM `' . _DB_PREFIX_ . 'cart_product` cp
					WHERE cp.`id_product` = ' . (int) $id_product . '
					AND (cp.instructions = "' . Db::getInstance()->_escape($instructions) . '" OR cp.instructions_valid= "' . Db::getInstance()->_escape($instructions) . '")
					AND cp.`id_cart` = ' . (int) $this->id . ')';
        }
        $sql .= ' AND cp.`id_cart` = ' . (int) $this->id;
        if (Configuration::get('PS_ALLOW_MULTISHIPPING') && $this->isMultiAddressDelivery()) {
            $sql .= ' AND cp.`id_address_delivery` = ' . (int) $id_address_delivery;
        }

        if ($id_customization) {
            $sql .= ' AND c.`id_customization` = ' . (int) $id_customization;
        }
        $ret = Db::getInstance()->getRow($sql);
        if ($ret['sum'] == '') {
            $ret = false;
        } else {
            $ret['quantity'] = $ret['sum'];
        }
        return $ret;
    }

    /**
     * Update product quantity
     *
     * @param int $quantity Quantity to add (or substract)
     * @param int $id_product Product ID
     * @param int $id_product_attribute Attribute ID if needed
     * @param string $operator Indicate if quantity must be increased or decreased
     */
    public function updateQty($quantity,
          $id_product,
          $id_product_attribute = null,
          $id_customization = false,
          $operator = 'up',
          $id_address_delivery = 0,
          Shop $shop = null,
          $auto_add_cart_rule = true,
          $skipAvailabilityCheckOutOfStock = false,
          bool $preserveGiftRemoval = true,
          $instructions = "",
          $instructions_id = ""
    )
    {
        if (!$shop) {
            $shop = Context::getContext()->shop;
        }

        if (Context::getContext()->customer->id) {
            if ($id_address_delivery == 0 && (int) $this->id_address_delivery) { // The $id_address_delivery is null, use the cart delivery address
                $id_address_delivery = $this->id_address_delivery;
            } elseif ($id_address_delivery == 0) { // The $id_address_delivery is null, get the default customer address
                $id_address_delivery = (int) Address::getFirstCustomerAddressId((int) Context::getContext()->customer->id);
            } elseif (!Customer::customerHasAddress(Context::getContext()->customer->id, $id_address_delivery)) { // The $id_address_delivery must be linked with customer
                $id_address_delivery = 0;
            }
        }

        $quantity = (int) $quantity;
        $id_product = (int) $id_product;
        $id_product_attribute = (int) $id_product_attribute;
        $product = new Product($id_product, false, Configuration::get('PS_LANG_DEFAULT'), $shop->id);

        if ($id_product_attribute) {
            $combination = new Combination((int) $id_product_attribute);
            if ($combination->id_product != $id_product) {
                return false;
            }
        }

        /* If we have a product combination, the minimal quantity is set with the one of this combination */
        if (!empty($id_product_attribute)) {
            $minimal_quantity = (int) Attribute::getAttributeMinimalQty($id_product_attribute);
        } else {
            $minimal_quantity = (int) $product->minimal_quantity;
        }

        if (!Validate::isLoadedObject($product)) {
            die(Tools::displayError());
        }

        if (isset(self::$_nbProducts[$this->id])) {
            unset(self::$_nbProducts[$this->id]);
        }

        if (isset(self::$_totalWeight[$this->id])) {
            unset(self::$_totalWeight[$this->id]);
        }
        $data = array(
            'cart' => $this,
            'product' => $product,
            'id_product_attribute' => $id_product_attribute,
            'id_customization' => $id_customization,
            'quantity' => $quantity,
            'operator' => $operator,
            'id_address_delivery' => $id_address_delivery,
            'shop' => $shop,
            'auto_add_cart_rule' => $auto_add_cart_rule,
        );

        /* @deprecated deprecated since 1.6.1.1 */
        // Hook::exec('actionBeforeCartUpdateQty', $data);
        Hook::exec('actionCartUpdateQuantityBefore', $data);

        if ((int) $quantity <= 0) {
            return $this->deleteProduct((int) $id_product, (int) $id_product_attribute, (int) $id_customization, 0, true, $instructions);
        } elseif (!$product->available_for_order || (Configuration::isCatalogMode() && !defined('_PS_ADMIN_DIR_'))) {
            return false;
        } else {
            /* Check if the product is already in the cart */
            $result = $this->containsProduct((int) $id_product, (int) $id_product_attribute, (int) $id_customization, (int) $id_address_delivery, $instructions);

            /* Update quantity if product already exist */
            if ($result) {
                if ($operator == 'up') {
                    $sql = 'SELECT stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity
							FROM ' . _DB_PREFIX_ . 'product p
							' . Product::sqlStock('p', $id_product_attribute, true, $shop) . '
							WHERE p.id_product = ' . $id_product;

                    $result2 = Db::getInstance()->getRow($sql);
                    $product_qty = (int) $result2['quantity'];
                    // Quantity for product pack
                    if (Pack::isPack($id_product)) {
                        $product_qty = Pack::getQuantity($id_product, $id_product_attribute);
                    }
                    $new_qty = (int) $result['quantity'] + (int) $quantity;
                    $qty = '+ ' . (int) $quantity;

                    if (!Product::isAvailableWhenOutOfStock((int) $result2['out_of_stock'])) {
                        if ($new_qty > $product_qty) {
                            return false;
                        }
                    }
                } elseif ($operator == 'down') {
                    $qty = '- ' . (int) $quantity;
                    $new_qty = (int) $result['quantity'] - (int) $quantity;
                    if ($new_qty < $minimal_quantity && $minimal_quantity > 1) {
                        return -1;
                    }
                } else {
                    return false;
                }

                /* Delete product from cart */
                if ($new_qty <= 0) {
                    return $this->deleteProduct((int) $id_product, (int) $id_product_attribute, (int) $id_customization, 0, true, $instructions);
                } elseif ($new_qty < $minimal_quantity) {
                    return -1;
                } else {
                    Db::getInstance()->execute('
						UPDATE `' . _DB_PREFIX_ . 'cart_product`
						SET `quantity` = `quantity` ' . $qty . ', `date_add` = NOW()
						WHERE `id_product` = ' . (int) $id_product .
                        (!empty($id_product_attribute) ? ' AND `id_product_attribute` = ' . (int) $id_product_attribute : '') . ' AND (instructions = "' . Db::getInstance()->_escape($instructions) . '" OR  instructions_valid = "' . Db::getInstance()->_escape($instructions) . '" OR  instructions_valid = "' . ($instructions ? md5($instructions) : "") . '")' . '
						AND `id_cart` = ' . (int) $this->id . (Configuration::get('PS_ALLOW_MULTISHIPPING') && $this->isMultiAddressDelivery() ? ' AND `id_address_delivery` = ' . (int) $id_address_delivery : '') . '
						LIMIT 1'
                    );
                }
            } elseif ($operator == 'up') {
                /* Add product to the cart */
                $sql = 'SELECT stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity
						FROM ' . _DB_PREFIX_ . 'product p
						' . Product::sqlStock('p', $id_product_attribute, true, $shop) . '
						WHERE p.id_product = ' . $id_product;

                $result2 = Db::getInstance()->getRow($sql);

                // Quantity for product pack
                if (Pack::isPack($id_product)) {
                    $result2['quantity'] = Pack::getQuantity($id_product, $id_product_attribute);
                }

                if (!Product::isAvailableWhenOutOfStock((int) $result2['out_of_stock'])) {
                    if ((int) $quantity > $result2['quantity']) {
                        return false;
                    }
                }

                if ((int) $quantity < $minimal_quantity) {
                    return -1;
                }

                $result_add = Db::getInstance()->insert('cart_product', array(
                    'id_product' => (int) $id_product,
                    'id_product_attribute' => (int) $id_product_attribute,
                    'instructions' => Db::getInstance()->_escape($instructions),
                    'instructions_valid' => ($instructions ? md5($instructions) : ""),
                    'instructions_id' => $instructions_id,
                    'id_cart' => (int) $this->id,
                    'id_address_delivery' => (int) $id_address_delivery,
                    'id_shop' => $shop->id,
                    'quantity' => (int) $quantity,
                    'date_add' => date('Y-m-d H:i:s'),
                    'id_customization' => (int) $id_customization,
                ));

                if (!$result_add) {
                    return false;
                }
            }
        }

        // refresh cache of self::_products
        $this->_products = $this->getProducts(true);
        $this->update();
        $context = Context::getContext()->cloneContext();
        $context->cart = $this;
        Cache::clean('getContextualValue_*');
        if ($auto_add_cart_rule) {
            CartRule::autoAddToCart($context);
        }

        if ($product->customizable) {
            return $this->_updateCustomizationQuantity((int) $quantity, (int) $id_customization, (int) $id_product, (int) $id_product_attribute, (int) $id_address_delivery, $operator);
        } else {
            return true;
        }
    }

    /**
     * Delete a product from the cart
     *
     * @param int $id_product Product ID
     * @param int $id_product_attribute Attribute ID if needed
     * @param int $id_customization Customization id
     * @return bool result
     */
    public function deleteProduct($id_product, $id_product_attribute = null, $id_customization = null, $id_address_delivery = 0, bool $preserveGiftsRemoval = true, $instructions = "")
    {
        if (isset(self::$_nbProducts[$this->id])) {
            unset(self::$_nbProducts[$this->id]);
        }

        if (isset(self::$_totalWeight[$this->id])) {
            unset(self::$_totalWeight[$this->id]);
        }

        if ((int) $id_customization) {
            $product_total_quantity = (int) Db::getInstance()->getValue(
                    'SELECT `quantity`
				FROM `' . _DB_PREFIX_ . 'cart_product`
				WHERE `id_product` = ' . (int) $id_product . '
				AND `id_cart` = ' . (int) $this->id . '
				AND `id_product_attribute` = ' . (int) $id_product_attribute);

            $customization_quantity = (int) Db::getInstance()->getValue('
			SELECT `quantity`
			FROM `' . _DB_PREFIX_ . 'customization`
			WHERE `id_cart` = ' . (int) $this->id . '
			AND `id_product` = ' . (int) $id_product . '
			AND `id_product_attribute` = ' . (int) $id_product_attribute . '
			' . ((int) $id_address_delivery ? 'AND `id_address_delivery` = ' . (int) $id_address_delivery : ''));

            if (!$this->_deleteCustomization((int) $id_customization, (int) $id_product, (int) $id_product_attribute, (int) $id_address_delivery)) {
                return false;
            }
        }

        /* Get customization quantity */
        $result = Db::getInstance()->getRow('
			SELECT SUM(`quantity`) AS \'quantity\'
			FROM `' . _DB_PREFIX_ . 'customization`
			WHERE `id_cart` = ' . (int) $this->id . '
			AND `id_product` = ' . (int) $id_product . '
			AND `id_product_attribute` = ' . (int) $id_product_attribute);

        if ($result === false) {
            return false;
        }

        /* If the product still possesses customization it does not have to be deleted */
        if (Db::getInstance()->NumRows() && (int) $result['quantity'])
            return Db::getInstance()->execute('
				UPDATE `' . _DB_PREFIX_ . 'cart_product`
				SET `quantity` = ' . (int) $result['quantity'] . '
				WHERE `id_cart` = ' . (int) $this->id . '
				AND  (instructions = "' . Db::getInstance()->_escape($instructions) . '" OR instructions_valid = "' . $instructions . '")
				AND `id_product` = ' . (int) $id_product .
                    ($id_product_attribute != null ? ' AND `id_product_attribute` = ' . (int) $id_product_attribute : '')
            );

        $preservedGifts = $this->getProductsGifts($id_product, $id_product_attribute);
        if ($preservedGifts[$id_product . '-' . $id_product_attribute] > 0) {
            return Db::getInstance()->execute(
                    'UPDATE `' . _DB_PREFIX_ . 'cart_product`
                SET `quantity` = ' . (int) $preservedGifts[$id_product . '-' . $id_product_attribute] . '
                WHERE `id_cart` = ' . (int) $this->id . '
                AND `id_product` = ' . (int) $id_product .
                    ($id_product_attribute != null ? ' AND `id_product_attribute` = ' . (int) $id_product_attribute : '')
            );
        }
        /* Product deletion */
        $result = Db::getInstance()->execute('
		DELETE FROM `' . _DB_PREFIX_ . 'cart_product`
		WHERE `id_product` = ' . (int) $id_product . '
		' . (!is_null($id_product_attribute) ? ' AND `id_product_attribute` = ' . (int) $id_product_attribute : '') . '
		AND (`instructions` = \'' . $instructions . '\' OR instructions_valid = "' . $instructions . '")
		AND `id_cart` = ' . (int) $this->id . '
		' . ((int) $id_address_delivery ? 'AND `id_address_delivery` = ' . (int) $id_address_delivery : ''));

        if ($result) {
            $return = $this->update();
            // refresh cache of self::_products
            $this->_products = $this->getProducts(true);
            CartRule::autoRemoveFromCart();
            CartRule::autoAddToCart();

            return $return;
        }

        return false;
    }

    public function duplicate()
    {
        if (!Validate::isLoadedObject($this)) {
            return false;
        }

        $cart = new Cart($this->id);
        $cart->id = null;
        $cart->id_shop = $this->id_shop;
        $cart->id_shop_group = $this->id_shop_group;

        if (!Customer::customerHasAddress((int) $cart->id_customer, (int) $cart->id_address_delivery)) {
            $cart->id_address_delivery = (int) Address::getFirstCustomerAddressId((int) $cart->id_customer);
        }

        if (!Customer::customerHasAddress((int) $cart->id_customer, (int) $cart->id_address_invoice)) {
            $cart->id_address_invoice = (int) Address::getFirstCustomerAddressId((int) $cart->id_customer);
        }

        if ($cart->id_customer) {
            $cart->secure_key = Cart::$_customer->secure_key;
        }

        $cart->add();
        include_once(dirname(__FILE__) . '/../../modules/attributewizardpro/attributewizardpro.php');
        $awp = new AttributeWizardPro();

        if (!Validate::isLoadedObject($cart)) {
            return false;
        }

        $success = true;
        $products = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'cart_product` WHERE `id_cart` = ' . (int) $this->id);

        $product_gift = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT cr.`gift_product`, cr.`gift_product_attribute` FROM `' . _DB_PREFIX_ . 'cart_rule` cr LEFT JOIN `' . _DB_PREFIX_ . 'order_cart_rule` ocr ON (ocr.`id_order` = ' . (int) $this->id . ') WHERE ocr.`id_cart_rule` = cr.`id_cart_rule`');

        $id_address_delivery = Configuration::get('PS_ALLOW_MULTISHIPPING') ? $cart->id_address_delivery : 0;


        // Customized products: duplicate customizations before products so that we get new id_customizations
        $customs = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT *
            FROM ' . _DB_PREFIX_ . 'customization c
            LEFT JOIN ' . _DB_PREFIX_ . 'customized_data cd ON cd.id_customization = c.id_customization
            WHERE c.id_cart = ' . (int) $this->id
        );

        // Get datas from customization table
        $customs_by_id = array();
        foreach ($customs as $custom) {
            if (!isset($customs_by_id[$custom['id_customization']])) {
                $customs_by_id[$custom['id_customization']] = array(
                    'id_product_attribute' => $custom['id_product_attribute'],
                    'id_product' => $custom['id_product'],
                    'quantity' => $custom['quantity']
                );
            }
        }

        // Backward compatibility: if true set customizations quantity to 0, they will be updated in Cart::_updateCustomizationQuantity
        $new_customization_method = (int) Db::getInstance()->getValue('
            SELECT COUNT(`id_customization`) FROM `' . _DB_PREFIX_ . 'cart_product`
            WHERE `id_cart` = ' . (int) $this->id .
                ' AND `id_customization` != 0'
            ) > 0;
        // Insert new customizations
        $custom_ids = array();
        foreach ($customs_by_id as $customization_id => $val) {
            if ($new_customization_method) {
                $val['quantity'] = 0;
            }
            Db::getInstance()->execute(
                'INSERT INTO `' . _DB_PREFIX_ . 'customization` (id_cart, id_product_attribute, id_product, `id_address_delivery`, quantity, `quantity_refunded`, `quantity_returned`, `in_cart`)
                VALUES(' . (int) $cart->id . ', ' . (int) $val['id_product_attribute'] . ', ' . (int) $val['id_product'] . ', ' . (int) $id_address_delivery . ', ' . (int) $val['quantity'] . ', 0, 0, 1)'
            );
            $custom_ids[$customization_id] = Db::getInstance(_PS_USE_SQL_SLAVE_)->Insert_ID();
        }

        // Insert customized_data
        if (count($customs)) {
            $first = true;
            $sql_custom_data = 'INSERT INTO ' . _DB_PREFIX_ . 'customized_data (`id_customization`, `type`, `index`, `value`, `id_module`, `price`, `weight`) VALUES ';
            foreach ($customs as $custom) {
                if (!$first) {
                    $sql_custom_data .= ',';
                } else {
                    $first = false;
                }

                $customized_value = $custom['value'];

                if ((int) $custom['type'] == 0) {
                    $customized_value = md5(uniqid(rand(), true));
                    Tools::copy(_PS_UPLOAD_DIR_ . $custom['value'], _PS_UPLOAD_DIR_ . $customized_value);
                    Tools::copy(_PS_UPLOAD_DIR_ . $custom['value'] . '_small', _PS_UPLOAD_DIR_ . $customized_value . '_small');
                }

                $sql_custom_data .= '(' . (int) $custom_ids[$custom['id_customization']] . ', ' . (int) $custom['type'] . ', ' .
                    (int) $custom['index'] . ', \'' . pSQL($customized_value) . '\', ' .
                    (int) $custom['id_module'] . ', ' . (float) $custom['price'] . ', ' . (float) $custom['weight'] . ')';
            }
            Db::getInstance()->execute($sql_custom_data);
        }

        foreach ($products as $product) {
            if ($id_address_delivery) {
                if (Customer::customerHasAddress((int) $cart->id_customer, $product['id_address_delivery'])) {
                    $id_address_delivery = $product['id_address_delivery'];
                }
            }
            foreach ($product_gift as $gift) {
                if (isset($gift['gift_product']) && isset($gift['gift_product_attribute']) && (int) $gift['gift_product'] == (int) $product['id_product'] && (int) $gift['gift_product_attribute'] == (int) $product['id_product_attribute']) {
                    $product['quantity'] = (int) $product['quantity'] - 1;
                }
            }
            $id_customization = (int) $product['id_customization'];
            $success &= $cart->updateQty(
                (int) $product['quantity'], (int) $product['id_product'], $product['instructions_id'] != '' ? $awp->getIdProductAttribute($product['id_product'], $product['instructions_id']) : (int) $product['id_product_attribute'], null, 'up', (int) $id_address_delivery, new Shop((int) $cart->id_shop), true, false, true, $product['instructions'], $product['instructions_id']
            );
        }
        return array('cart' => $cart, 'success' => $success);
    }

    /**
     * Duplicate Product
     *
     * @param int  $id_product              Product ID
     * @param int  $id_product_attribute    Product Attribute ID
     * @param int  $id_address_delivery     Delivery Address ID
     * @param int  $new_id_address_delivery New Delivery Address ID
     * @param int  $quantity                Quantity
     * @param bool $keep_quantity           Keep the quantity, do not reset if true
     *
     * @return bool Whether the product has been successfully duplicated
     */
    public function duplicateProduct($id_product, $id_product_attribute, $id_address_delivery, $new_id_address_delivery, $quantity = 1, $keep_quantity = false, $instructions_valid = '')
    {
        // Check address is linked with the customer
        if (!Customer::customerHasAddress(Context::getContext()->customer->id, $new_id_address_delivery)) {
            return false;
        }
        // Checking the product do not exist with the new address
        $sql = new DbQuery();
        $sql->select('count(*)');
        $sql->from('cart_product', 'c');
        $sql->where('id_product = ' . (int) $id_product);
        $sql->where('id_product_attribute = ' . (int) $id_product_attribute);
        $sql->where('id_address_delivery = ' . (int) $new_id_address_delivery);
        if ($instructions_valid != '') {
            $sql->where('instructions_valid = "' . $instructions_valid . '"');
        }
        $sql->where('id_cart = ' . (int) $this->id);
        $result = Db::getInstance()->getValue($sql);
        if ($result > 0) {
            return false;
        }
        // Get AWP data
        $instructions = '';
        $id_instructions = '';
        if ($instructions_valid != '') {
            $sql = new DbQuery();
            $sql->select('*');
            $sql->from('cart_product', 'c');
            $sql->where('instructions_valid = "' . $instructions_valid . '"');
            $sql->where('id_cart = ' . (int) $this->id);
            $result = Db::getInstance()->getRow($sql);
            $instructions = $result['instructions'];
            $id_instructions = $result['instructions_id'];
        }

        // Duplicating cart_product line
        $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'cart_product
			(`id_cart`, `id_product`, `id_shop`, `id_product_attribute`, `quantity`, `date_add`, `id_address_delivery`
			, `instructions`, `instructions_valid`, `instructions_id`)
			values(
				' . (int) $this->id . ',
				' . (int) $id_product . ',
				' . (int) $this->id_shop . ',
				' . (int) $id_product_attribute . ',
				' . (int) $quantity . ',
				NOW(),
				' . (int) $new_id_address_delivery . ',
				"' . Db::getInstance()->_escape($instructions) . '",
				"' . ($instructions_valid != '' ? $instructions_valid : '') . '",
				"' . $id_instructions . '")';

        Db::getInstance()->execute($sql);

        if (!$keep_quantity) {
            $sql = new DbQuery();
            $sql->select('quantity');
            $sql->from('cart_product', 'c');
            $sql->where('id_product = ' . (int) $id_product);
            $sql->where('id_product_attribute = ' . (int) $id_product_attribute);
            $sql->where('id_address_delivery = ' . (int) $id_address_delivery);
            if ($instructions_valid != '') {
                $sql->where('instructions_valid = "' . $instructions_valid . '"');
            }
            $sql->where('id_cart = ' . (int) $this->id);
            $duplicatedQuantity = Db::getInstance()->getValue($sql);

            if ($duplicatedQuantity > $quantity) {
                $sql = 'UPDATE ' . _DB_PREFIX_ . 'cart_product
					SET `quantity` = `quantity` - ' . (int) $quantity . '
					WHERE id_cart = ' . (int) $this->id . '
					AND id_product = ' . (int) $id_product . '
					AND id_shop = ' . (int) $this->id_shop . '
					AND id_product_attribute = ' . (int) $id_product_attribute . '
					AND instructions_valid = "' . $instructions_valid . '"
					AND id_address_delivery = ' . (int) $id_address_delivery;
                Db::getInstance()->execute($sql);
            }
        }

        // Checking if there is customizations
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('customization', 'c');
        $sql->where('id_product = ' . (int) $id_product);
        $sql->where('id_product_attribute = ' . (int) $id_product_attribute);
        $sql->where('id_address_delivery = ' . (int) $id_address_delivery);
        $sql->where('id_cart = ' . (int) $this->id);
        $results = Db::getInstance()->executeS($sql);

        foreach ($results as $customization) {
            // Duplicate customization
            $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'customization
				(`id_product_attribute`, `id_address_delivery`, `id_cart`, `id_product`, `quantity`, `in_cart`)
				VALUES (
					' . (int) $customization['id_product_attribute'] . ',
					' . (int) $new_id_address_delivery . ',
					' . (int) $customization['id_cart'] . ',
					' . (int) $customization['id_product'] . ',
					' . (int) $quantity . ',
					' . (int) $customization['in_cart'] . ')';

            Db::getInstance()->execute($sql);

            // Save last insert ID before doing another query
            $last_id = (int) Db::getInstance()->Insert_ID();

            // Get data from duplicated customizations
            $sql = new DbQuery();
            $sql->select('`type`, `index`, `value`');
            $sql->from('customized_data');
            $sql->where('id_customization = ' . $customization['id_customization']);
            $last_row = Db::getInstance()->getRow($sql);

            // Insert new copied data with new customization ID into customized_data table
            $last_row['id_customization'] = $last_id;
            Db::getInstance()->insert('customized_data', $last_row);
        }

        $customization_count = count($results);
        if ($customization_count > 0) {
            $sql = 'UPDATE ' . _DB_PREFIX_ . 'cart_product
				SET `quantity` = `quantity` + ' . (int) $customization_count * $quantity . '
				WHERE id_cart = ' . (int) $this->id . '
				AND id_product = ' . (int) $id_product . '
				AND id_shop = ' . (int) $this->id_shop . '
				AND id_product_attribute = ' . (int) $id_product_attribute . '
				AND id_address_delivery = ' . (int) $new_id_address_delivery;
            Db::getInstance()->execute($sql);
        }

        return true;
    }

    /**
     * Update products cart address delivery with the address delivery of the cart
     * ADDED , instructions_valid
     */
    public function setNoMultishipping()
    {
        $emptyCache = false;
        if (Configuration::get('PS_ALLOW_MULTISHIPPING')) {
            // Upgrading quantities
            $sql = 'SELECT sum(`quantity`) as quantity, id_product, id_product_attribute, count(*) as count
				FROM `' . _DB_PREFIX_ . 'cart_product`
				WHERE `id_cart` = ' . (int) $this->id . '
					AND `id_shop` = ' . (int) $this->id_shop . '
				GROUP BY id_product, id_product_attribute, instructions_valid
				HAVING count > 1';

            foreach (Db::getInstance()->executeS($sql) as $product) {
                $sql = 'UPDATE `' . _DB_PREFIX_ . 'cart_product`
				SET `quantity` = ' . (int) $product['quantity'] . '
				WHERE  `id_cart` = ' . (int) $this->id . '
					AND `id_shop` = ' . (int) $this->id_shop . '
					AND id_product = ' . (int) $product['id_product'] . '
					AND id_product_attribute = ' . (int) $product['id_product_attribute'];
                if (Db::getInstance()->execute($sql)) {
                    $emptyCache = true;
                }
            }

            // Merging multiple lines
            $sql = 'DELETE cp1
			FROM `' . _DB_PREFIX_ . 'cart_product` cp1
				INNER JOIN `' . _DB_PREFIX_ . 'cart_product` cp2
				ON (
					(cp1.id_cart = cp2.id_cart)
					AND (cp1.id_product = cp2.id_product)
					AND (cp1.id_product_attribute = cp2.id_product_attribute)
					AND (cp1.id_address_delivery <> cp2.id_address_delivery)
					AND (cp1.date_add > cp2.date_add)
				)';
            Db::getInstance()->execute($sql);
        }

        // Update delivery address for each product line
        $sql = 'UPDATE `' . _DB_PREFIX_ . 'cart_product`
		SET `id_address_delivery` = (
			SELECT `id_address_delivery` FROM `' . _DB_PREFIX_ . 'cart`
			WHERE `id_cart` = ' . (int) $this->id . ' AND `id_shop` = ' . (int) $this->id_shop . '
		)
		WHERE `id_cart` = ' . (int) $this->id . '
		' . (Configuration::get('PS_ALLOW_MULTISHIPPING') ? ' AND `id_shop` = ' . (int) $this->id_shop : '');

        $cache_id = 'Cart::setNoMultishipping' . (int) $this->id . '-' . (int) $this->id_shop . ((isset($this->id_address_delivery) && $this->id_address_delivery) ? '-' . (int) $this->id_address_delivery : '');
        if (!Cache::isStored($cache_id)) {
            if ($result = (bool) Db::getInstance()->execute($sql)) {
                $emptyCache = true;
            }
            Cache::store($cache_id, $result);
        }

        if (Customization::isFeatureActive()) {
            Db::getInstance()->execute('
			UPDATE `' . _DB_PREFIX_ . 'customization`
			SET `id_address_delivery` = (
				SELECT `id_address_delivery` FROM `' . _DB_PREFIX_ . 'cart`
				WHERE `id_cart` = ' . (int) $this->id . '
			)
			WHERE `id_cart` = ' . (int) $this->id);
        }

        if ($emptyCache) {
            $this->_products = null;
        }
    }

    /**
     * Set an address to all products on the cart without address delivery
     */
    public function autosetProductAddress()
    {
        $id_address_delivery = 0;
        // Get the main address of the customer
        if ((int) $this->id_address_delivery > 0) {
            $id_address_delivery = (int) $this->id_address_delivery;
        } else {
            $id_address_delivery = (int) Address::getFirstCustomerAddressId(Context::getContext()->customer->id);
        }

        if (!$id_address_delivery) {
            return;
        }
        $sql = 'UPDATE `' . _DB_PREFIX_ . 'cart_product`
			SET `id_address_delivery` = ' . (int) $id_address_delivery . '
			WHERE `id_cart` = ' . (int) $this->id . '
				AND (`id_address_delivery` = 0 OR `id_address_delivery` IS NULL)
				AND `id_shop` = ' . (int) $this->id_shop;
        Db::getInstance()->execute($sql);

        $sql = 'UPDATE `' . _DB_PREFIX_ . 'customization`
			SET `id_address_delivery` = ' . (int) $id_address_delivery . '
			WHERE `id_cart` = ' . (int) $this->id . '
				AND (`id_address_delivery` = 0 OR `id_address_delivery` IS NULL)';

        Db::getInstance()->execute($sql);
    }

    protected function applyProductCalculations($row, $shopContext, $productQuantity = null, bool $keepOrderPrices = false)
    {
        // Don't use this method on PS lower than 1.7.7
        if (version_compare(_PS_VERSION_,  '1.7.7.', '<') || Context::getContext()->controller->controller_type == 'front')
        {
            return parent::applyProductCalculations($row, $shopContext, $productQuantity, $keepOrderPrices);
        }

        if (null === $productQuantity) {
            $productQuantity = (int) $row['cart_quantity'];
        }

        if (isset($row['ecotax_attr']) && $row['ecotax_attr'] > 0) {
            $row['ecotax'] = (float) $row['ecotax_attr'];
        }

        $row['stock_quantity'] = (int) $row['quantity'];
        // for compatibility with 1.2 themes
        $row['quantity'] = $productQuantity;

        // get the customization weight impact
        $customization_weight = Customization::getCustomizationWeight($row['id_customization']);

        if (isset($row['id_product_attribute']) && (int) $row['id_product_attribute'] && isset($row['weight_attribute'])) {
            $row['weight_attribute'] += $customization_weight;
            $row['weight'] = (float) $row['weight_attribute'];
        } else {
            $row['weight'] += $customization_weight;
        }

        if (Configuration::get('PS_TAX_ADDRESS_TYPE') == 'id_address_invoice') {
            $address_id = (int) $this->id_address_invoice;
        } else {
            $address_id = (int) $row['id_address_delivery'];
        }
        if (!Address::addressExists($address_id)) {
            $address_id = null;
        }

        if ($shopContext->shop->id != $row['id_shop']) {
            $shopContext->shop = new Shop((int) $row['id_shop']);
        }

        $address = Address::initialize($address_id, true);
        $id_tax_rules_group = Product::getIdTaxRulesGroupByIdProduct((int) $row['id_product'], $shopContext);
        $tax_calculator = TaxManagerFactory::getManager($address, $id_tax_rules_group)->getTaxCalculator();

        $specific_price_output = null;
        // Specify the orderId if needed so that Product::getPriceStatic returns the prices saved in OrderDetails
        $orderId = null;
        if ($keepOrderPrices) {
            $orderId = Order::getIdByCartId($this->id);
            $orderId = (int) $orderId ?: null;
        }

        if (!empty($orderId)) {
            $orderPrices = $this->getOrderPrices($row, $orderId, $productQuantity, $address_id, $shopContext, $specific_price_output);
            $row = array_merge($row, $orderPrices);
        } else {
            $cartPrices = $this->getCartPrices($row, $productQuantity, $address_id, $shopContext, $specific_price_output);
            $row = array_merge($row, $cartPrices);
        }

        switch (Configuration::get('PS_ROUND_TYPE')) {
            case Order::ROUND_TOTAL:
                $row['total'] = $row['price_with_reduction_without_tax'] * $productQuantity;
                $row['total_wt'] = $row['price_with_reduction'] * $productQuantity;

                break;
            case Order::ROUND_LINE:
                $row['total'] = Tools::ps_round(
                    $row['price_with_reduction_without_tax'] * $productQuantity,
                    Context::getContext()->getComputingPrecision()
                );
                $row['total_wt'] = Tools::ps_round(
                    $row['price_with_reduction'] * $productQuantity,
                    Context::getContext()->getComputingPrecision()
                );

                break;

            case Order::ROUND_ITEM:
            default:
                $row['total'] = Tools::ps_round(
                        $row['price_with_reduction_without_tax'],
                        Context::getContext()->getComputingPrecision()
                    ) * $productQuantity;
                $row['total_wt'] = Tools::ps_round(
                        $row['price_with_reduction'],
                        Context::getContext()->getComputingPrecision()
                    ) * $productQuantity;

                break;
        }

        $row['price_wt'] = $row['price_with_reduction'];
        $row['description_short'] = Tools::nl2br($row['description_short']);

        // check if a image associated with the attribute exists
        if ($row['id_product_attribute']) {
            $row2 = Image::getBestImageAttribute($row['id_shop'], $this->id_lang, $row['id_product'], $row['id_product_attribute']);
            if ($row2) {
                $row = array_merge($row, $row2);
            }
        }

        $row['reduction_applies'] = ($specific_price_output && (float) $specific_price_output['reduction']);
        $row['quantity_discount_applies'] = ($specific_price_output && $productQuantity >= (int) $specific_price_output['from_quantity']);
        $row['id_image'] = Product::defineProductImage($row, $this->id_lang);
        $row['allow_oosp'] = Product::isAvailableWhenOutOfStock($row['out_of_stock']);
        $row['features'] = Product::getFeaturesStatic((int) $row['id_product']);

        if (array_key_exists($row['id_product_attribute'] . '-' . $this->id_lang, self::$_attributesLists)) {
            $row = array_merge($row, self::$_attributesLists[$row['id_product_attribute'] . '-' . $this->id_lang]);
        }

        // Modify rwo to display product instruction in admin cart page.
        if (array_key_exists($row['id_product_attribute'] . '-' . $this->id_lang, self::$_attributesLists)) {
            $row = array_merge($row, self::$_attributesLists[$row['id_product_attribute'] . '-' . $this->id_lang]);
            $row['attributes'] .= $row['instructions'];
        }

        return Product::getTaxesInformations($row, $shopContext);
    }

    private function getCartPrices(
        array $productRow,
        int $productQuantity,
        ?int $addressId,
        Context $shopContext,
        &$specificPriceOutput
    ): array {
        $cartPrices = [];
        $cartPrices['price_without_reduction'] = $this->getCartPriceFromCatalog(
            (int) $productRow['id_product'],
            isset($productRow['id_product_attribute']) ? (int) $productRow['id_product_attribute'] : null,
            (int) $productRow['id_customization'],
            true,
            false,
            true,
            $productQuantity,
            $addressId,
            $shopContext,
            $specificPriceOutput
        );

        $cartPrices['price_without_reduction_without_tax'] = $this->getCartPriceFromCatalog(
            (int) $productRow['id_product'],
            isset($productRow['id_product_attribute']) ? (int) $productRow['id_product_attribute'] : null,
            (int) $productRow['id_customization'],
            false,
            false,
            true,
            $productQuantity,
            $addressId,
            $shopContext,
            $specificPriceOutput
        );

        $cartPrices['price_with_reduction'] = $this->getCartPriceFromCatalog(
            (int) $productRow['id_product'],
            isset($productRow['id_product_attribute']) ? (int) $productRow['id_product_attribute'] : null,
            (int) $productRow['id_customization'],
            true,
            true,
            true,
            $productQuantity,
            $addressId,
            $shopContext,
            $specificPriceOutput
        );

        $cartPrices['price'] = $cartPrices['price_with_reduction_without_tax'] = $this->getCartPriceFromCatalog(
            (int) $productRow['id_product'],
            isset($productRow['id_product_attribute']) ? (int) $productRow['id_product_attribute'] : null,
            (int) $productRow['id_customization'],
            false,
            true,
            true,
            $productQuantity,
            $addressId,
            $shopContext,
            $specificPriceOutput
        );

        return $cartPrices;
    }

    private function getCartPriceFromCatalog(
        int $productId,
        int $combinationId,
        int $customizationId,
        bool $withTaxes,
        bool $useReduction,
        bool $withEcoTax,
        int $productQuantity,
        ?int $addressId,
        Context $shopContext,
        &$specificPriceOutput
    ): ?float {
        return Product::getPriceStatic(
            $productId,
            $withTaxes,
            $combinationId,
            6,
            null,
            false,
            $useReduction,
            $productQuantity,
            false,
            (int) $this->id_customer ? (int) $this->id_customer : null,
            (int) $this->id,
            $addressId,
            $specificPriceOutput,
            $withEcoTax,
            true,
            $shopContext,
            true,
            $customizationId
        );
    }
}
