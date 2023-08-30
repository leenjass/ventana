<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.txt
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to a newer
 * versions in the future. If you wish to customize this module for your needs
 * please refer to CustomizationPolicy.txt file inside our module for more information.
 *
 * @author Webkul IN
 * @copyright Since 2010 Webkul
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */
class HTMLTemplateOrderSlip extends HTMLTemplateOrderSlipCore
{
    public function getContent()
    {
        $delivery_address = $invoice_address = new Address((int) $this->order->id_address_invoice);
        $formatted_invoice_address = AddressFormat::generateAddress($invoice_address, [], '<br />', ' ');
        $formatted_delivery_address = '';

        if ($this->order->id_address_delivery != $this->order->id_address_invoice) {
            $delivery_address = new Address((int) $this->order->id_address_delivery);
            $formatted_delivery_address = AddressFormat::generateAddress($delivery_address, [], '<br />', ' ');
        }

        $customer = new Customer((int) $this->order->id_customer);
        $this->order->total_paid_tax_excl = $this->order->total_paid_tax_incl =
        $this->order->total_products = $this->order->total_products_wt = 0;

        if ($this->order_slip->amount > 0) {
            foreach ($this->order->products as &$product) {
                if (Module::isInstalled('wkproductoptions') && Module::isEnabled('wkproductoptions')) {
                    include_once _PS_MODULE_DIR_ . 'wkproductoptions/wkproductoptions.php';
                    if (WkProductCustomerOptions::checkOrderContainsOptionEntry(
                        $this->order->id_cart,
                        $product['product_id'],
                        $product['product_attribute_id'],
                        $product['id_customization']
                    )
                    ) {
                        $product['product_name'] = Hook::exec(
                            'displayAddBpProductName',
                            [
                                'id_order' => $product['id_order'],
                                'id_product' => $product['product_id'],
                                'id_cart' => $this->order->id_cart,
                                'id_product_attribute' => $product['product_attribute_id'],
                                'id_customization' => $product['id_customization'],
                                'product_name' => $product['product_name'],
                            ]
                        );
                    } else {
                        $product['product_name'] = $product['product_name'];
                    }
                }

                $product['total_price_tax_excl'] = $product['unit_price_tax_excl'] * $product['product_quantity'];
                $product['total_price_tax_incl'] = $product['unit_price_tax_incl'] * $product['product_quantity'];

                if ($this->order_slip->partial == 1) {
                    $order_slip_detail = Db::getInstance()->getRow('
                        SELECT * FROM `' . _DB_PREFIX_ . 'order_slip_detail`
                        WHERE `id_order_slip` = ' . (int) $this->order_slip->id . '
                        AND `id_order_detail` = ' . (int) $product['id_order_detail']);

                    $product['total_price_tax_excl'] = $order_slip_detail['amount_tax_excl'];
                    $product['total_price_tax_incl'] = $order_slip_detail['amount_tax_incl'];
                }

                $this->order->total_products += $product['total_price_tax_excl'];
                $this->order->total_products_wt += $product['total_price_tax_incl'];
                $this->order->total_paid_tax_excl = $this->order->total_products;
                $this->order->total_paid_tax_incl = $this->order->total_products_wt;
            }
        } else {
            $this->order->products = null;
        }

        unset($product); // remove reference

        if ($this->order_slip->shipping_cost == 0) {
            $this->order->total_shipping_tax_incl = $this->order->total_shipping_tax_excl = 0;
        }

        $tax = new Tax();
        $tax->rate = $this->order->carrier_tax_rate;
        $tax_excluded_display = Group::getPriceDisplayMethod((int) $customer->id_default_group);

        $this->order->total_shipping_tax_incl = $this->order_slip->total_shipping_tax_incl;
        $this->order->total_shipping_tax_excl = $this->order_slip->total_shipping_tax_excl;
        $this->order_slip->shipping_cost_amount = $tax_excluded_display ?
        $this->order_slip->total_shipping_tax_excl : $this->order_slip->total_shipping_tax_incl;

        $this->order->total_paid_tax_incl += $this->order->total_shipping_tax_incl;
        $this->order->total_paid_tax_excl += $this->order->total_shipping_tax_excl;

        $total_cart_rule = 0;
        if ($this->order_slip->order_slip_type == 1 && is_array($cart_rules = $this->order->getCartRules(
            $this->order_invoice->id
        ))) {
            foreach ($cart_rules as $cart_rule) {
                if ($tax_excluded_display) {
                    $total_cart_rule += $cart_rule['value_tax_excl'];
                } else {
                    $total_cart_rule += $cart_rule['value'];
                }
            }
        }

        $this->smarty->assign([
            'order' => $this->order,
            'order_slip' => $this->order_slip,
            'order_details' => $this->order->products,
            'cart_rules' => $this->order_slip->order_slip_type == 1 ? $this->order->getCartRules(
                $this->order_invoice->id
            ) : false,
            'amount_choosen' => $this->order_slip->order_slip_type == 2 ? true : false,
            'delivery_address' => $formatted_delivery_address,
            'invoice_address' => $formatted_invoice_address,
            'addresses' => ['invoice' => $invoice_address, 'delivery' => $delivery_address],
            'tax_excluded_display' => $tax_excluded_display,
            'total_cart_rule' => $total_cart_rule,
        ]);

        $tpls = [
            'style_tab' => $this->smarty->fetch($this->getTemplate('invoice.style-tab')),
            'addresses_tab' => $this->smarty->fetch($this->getTemplate('invoice.addresses-tab')),
            'summary_tab' => $this->smarty->fetch($this->getTemplate('order-slip.summary-tab')),
            'product_tab' => $this->smarty->fetch($this->getTemplate('order-slip.product-tab')),
            'total_tab' => $this->smarty->fetch($this->getTemplate('order-slip.total-tab')),
            'payment_tab' => $this->smarty->fetch($this->getTemplate('order-slip.payment-tab')),
            'tax_tab' => $this->getTaxTabContent(),
        ];
        $this->smarty->assign($tpls);

        return $this->smarty->fetch($this->getTemplate('order-slip'));
    }
}
