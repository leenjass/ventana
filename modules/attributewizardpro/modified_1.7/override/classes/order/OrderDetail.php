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

class OrderDetail extends OrderDetailCore
{

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'order_detail',
        'primary' => 'id_order_detail',
        'fields' => array(
            'id_order' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_order_invoice' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_warehouse' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_shop' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'product_id' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'product_attribute_id' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'product_name' => array('type' => self::TYPE_HTML, 'required' => true),
            'product_quantity' => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true),
            'product_quantity_in_stock' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'product_quantity_return' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'product_quantity_refunded' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'product_quantity_reinjected' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'product_price' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'reduction_percent' => array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'reduction_amount' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'reduction_amount_tax_incl' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'reduction_amount_tax_excl' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'group_reduction' => array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'product_quantity_discount' => array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'product_ean13' => array('type' => self::TYPE_STRING, 'validate' => 'isEan13'),
            'product_isbn' => array('type' => self::TYPE_STRING, 'validate' => 'isIsbn'),
            'product_upc' => array('type' => self::TYPE_STRING, 'validate' => 'isUpc'),
            'product_reference' => array('type' => self::TYPE_STRING, 'validate' => 'isReference'),
            'product_supplier_reference' => array('type' => self::TYPE_STRING, 'validate' => 'isReference'),
            'product_weight' => array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'tax_name' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
            'tax_rate' => array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'tax_computation_method' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_tax_rules_group' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'ecotax' => array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'ecotax_tax_rate' => array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'discount_quantity_applied' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'download_hash' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
            'download_nb' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'download_deadline' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'unit_price_tax_incl' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'unit_price_tax_excl' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'total_price_tax_incl' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'total_price_tax_excl' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'total_shipping_price_tax_excl' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'total_shipping_price_tax_incl' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'purchase_supplier_price' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'original_product_price' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'original_wholesale_price' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice')
        ),
    );
    protected $webserviceParameters = array(
        'fields' => array(
            'id_order' => array('xlink_resource' => 'orders'),
            'product_id' => array('xlink_resource' => 'products'),
            'product_attribute_id' => array('xlink_resource' => 'combinations'),
            'product_quantity_reinjected' => array(),
            'group_reduction' => array(),
            'discount_quantity_applied' => array(),
            'download_hash' => array(),
            'download_deadline' => array()
        ),
        'hidden_fields' => array('tax_rate', 'tax_name'),
        'associations' => array(
            'taxes' => array('resource' => 'tax', 'getter' => 'getWsTaxes', 'setter' => false,
                'fields' => array('id' => array(),),
            ),
    ));

    /**
     * Create an order detail liable to an id_order
     * @param object $order
     * @param object $cart
     * @param array $product
     * @param int $id_order_status
     * @param int $id_order_invoice
     * @param bool $use_taxes set to false if you don't want to use taxes
     */
    protected function create(Order $order, Cart $cart, $product, $id_order_state, $id_order_invoice, $use_taxes = true, $id_warehouse = 0)
    {
        if ($use_taxes) {
            $this->tax_calculator = new TaxCalculator();
        }
        $this->id = null;

        $this->product_id = (int) $product['id_product'];
        $this->product_attribute_id = $product['id_product_attribute'] ? (int) $product['id_product_attribute'] : 0;
        $this->product_name = $product['name'] .
            ((!isset($product['instructions']) || $product['instructions'] == '') && isset($product['attributes']) && $product['attributes'] != null ?
                ' - ' . $product['attributes'] : '')
            . ((isset($product['instructions']) && $product['instructions'] != '') ? ' - ' . $product['instructions'] : '');

        $this->product_quantity = (int) $product['cart_quantity'];
        $this->product_ean13 = empty($product['ean13']) ? null : pSQL($product['ean13']);
        $this->product_upc = empty($product['upc']) ? null : pSQL($product['upc']);
        $this->product_reference = empty($product['reference']) ? null : pSQL($product['reference']);
        $this->product_supplier_reference = empty($product['supplier_reference']) ? null : pSQL($product['supplier_reference']);
        $this->product_weight = $product['id_product_attribute'] ? (float) $product['weight_attribute'] : (float) $product['weight'];
        $this->id_warehouse = $id_warehouse;

        $product_quantity = (int) Product::getQuantity($this->product_id, $this->product_attribute_id);
        $this->product_quantity_in_stock = ($product_quantity - (int) $product['cart_quantity'] < 0) ?
            $product_quantity : (int) $product['cart_quantity'];

        $this->setVirtualProductInformation($product);
        $this->checkProductStock($product, $id_order_state);

        if ($use_taxes) {
            $this->setProductTax($order, $product);
        }
        $this->setShippingCost($order, $product);
        $this->setDetailProductPrice($order, $cart, $product);

        // Set order invoice id
        $this->id_order_invoice = (int) $id_order_invoice;

        // Set shop id
        $this->id_shop = (int) $product['id_shop'];

        // Add new entry to the table
        $this->save();

        if ($use_taxes) {
            $this->saveTaxCalculator($order);
        }
        unset($this->tax_calculator);
    }
}
