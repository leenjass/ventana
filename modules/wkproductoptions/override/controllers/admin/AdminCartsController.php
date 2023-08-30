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
 * versions in the future. If you wish to customize this module for your
 * needs please refer to CustomizationPolicy.txt file inside our module for more information.
 *
 * @author Webkul IN
 * @copyright Since 2010 Webkul
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */
class AdminCartsController extends AdminCartsControllerCore
{
    public function renderView()
    {
        if (Module::isInstalled('wkproductoptions') && Module::isEnabled('wkproductoptions')) {
            $idCart = Tools::getValue('id_cart');
            $cart = new Cart($idCart);
            $customer = new Customer($cart->id_customer);
            $currency = new Currency($cart->id_currency);
            $this->context->cart = $cart;
            $this->context->currency = $currency;
            $this->context->customer = $customer;
            $this->toolbar_title = $this->trans('Cart #%ID%', ['%ID%' => $this->context->cart->id], 'Admin.Orderscustomers.Feature');
            $products = $cart->getProducts();
            $summary = $cart->getSummaryDetails();

            $id_order = (int) Order::getIdByCartId($cart->id);
            $order = new Order($id_order);
            if (Validate::isLoadedObject($order)) {
                $tax_calculation_method = $order->getTaxCalculationMethod();
                $id_shop = (int) $order->id_shop;
            } else {
                $id_shop = (int) $cart->id_shop;
                $tax_calculation_method = Group::getPriceDisplayMethod(Group::getCurrent()->id);
            }

            if ($tax_calculation_method == PS_TAX_EXC) {
                $total_products = $summary['total_products'];
                $total_discounts = $summary['total_discounts_tax_exc'];
                $total_wrapping = $summary['total_wrapping_tax_exc'];
                $total_price = $summary['total_price_without_tax'];
                $total_shipping = $summary['total_shipping_tax_exc'];
            } else {
                $total_products = $summary['total_products_wt'];
                $total_discounts = $summary['total_discounts'];
                $total_wrapping = $summary['total_wrapping'];
                $total_price = $summary['total_price'];
                $total_shipping = $summary['total_shipping'];
            }
            foreach ($products as &$product) {
                if ($tax_calculation_method == PS_TAX_EXC) {
                    $product['product_price'] = $product['price'];
                    $product['product_total'] = $product['total'];
                } else {
                    $product['product_price'] = $product['price_wt'];
                    $product['product_total'] = $product['total_wt'];
                }
                $image = [];
                if (isset($product['id_product_attribute']) && (int) $product['id_product_attribute']) {
                    $image = Db::getInstance()->getRow('SELECT id_image FROM ' . _DB_PREFIX_ . 'product_attribute_image WHERE id_product_attribute = ' . (int) $product['id_product_attribute']);
                }
                if (!isset($image['id_image'])) {
                    $image = Db::getInstance()->getRow('SELECT id_image FROM ' . _DB_PREFIX_ . 'image WHERE id_product = ' . (int) $product['id_product'] . ' AND cover = 1');
                }

                $product['qty_in_stock'] = StockAvailable::getQuantityAvailableByProduct($product['id_product'], isset($product['id_product_attribute']) ? $product['id_product_attribute'] : null, (int) $id_shop);

                $image_product = new Image($image['id_image']);
                $product['image'] = (isset($image['id_image']) ? ImageManager::thumbnail(_PS_IMG_DIR_ . 'p/' . $image_product->getExistingImgPath() . '.jpg', 'product_mini_' . (int) $product['id_product'] . (isset($product['id_product_attribute']) ? '_' . (int) $product['id_product_attribute'] : '') . '.jpg', 45, 'jpg') : '--');

                $customized_datas = Product::getAllCustomizedDatas($this->context->cart->id, null, true, null, (int) $product['id_customization']);
                $this->context->cart->setProductCustomizedDatas($product, $customized_datas);
                if ($customized_datas) {
                    Product::addProductCustomizationPrice($product, $customized_datas);
                }
            }

            $helper = new HelperKpi();
            $helper->id = 'box-kpi-cart';
            $helper->icon = 'icon-shopping-cart';
            $helper->color = 'color1';
            $helper->title = $this->trans('Total Cart', [], 'Admin.Orderscustomers.Feature');
            $helper->subtitle = $this->trans('Cart #%ID%', ['%ID%' => $cart->id], 'Admin.Orderscustomers.Feature');
            $helper->value = Tools::displayPrice($total_price, $currency);
            $kpi = $helper->generate();
            $this->tpl_view_vars = [
                'kpi' => $kpi,
                'products' => $products,
                'discounts' => $cart->getCartRules(),
                'order' => $order,
                'cart' => $cart,
                'currency' => $currency,
                'customer' => $customer,
                'customer_stats' => $customer->getStats(),
                'total_products' => $total_products,
                'total_discounts' => $total_discounts,
                'total_wrapping' => $total_wrapping,
                'total_price' => $total_price,
                'total_shipping' => $total_shipping,
                'tax_calculation_method' => $tax_calculation_method,
            ];
            $this->context->smarty->assign($this->tpl_view_vars);

            return $this->context->smarty->fetch(
                _PS_MODULE_DIR_ . '/wkproductoptions/views/templates/admin/admin_cart_view.tpl'
            );
        }

        return parent::renderView();
    }
}
