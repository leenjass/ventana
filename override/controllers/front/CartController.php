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
class CartController extends CartControllerCore
{
    /*
    * module: productpriceareatable
    * date: 2023-01-18 18:30:13
    * version: 2.0.18
    */
    protected function processChangeProductInCart()
    {
        include_once(_PS_MODULE_DIR_ . '/productpriceareatable/lib/bootstrap.php');
        $module = Module::getInstanceByName('productpriceareatable');
        $mode = (Tools::getIsset('update') && $this->id_product) ? 'update' : 'add';
        $ppat_cart_controller = new PPATFrontCartController($module);
        $id_product = Tools::getValue('id_product');
        $id_shop = Context::getContext()->shop->id;
        if ($mode == 'add') {
            if (Module::isEnabled('productcustomoptions')) {
                include_once(_PS_MODULE_DIR_ . '/productcustomoptions/lib/bootstrap.php');
                if (\MP\PCO\ProductHelper::isPCOProduct($id_product, $id_shop)) {
                    $module_pco = Module::getInstanceByName('productcustomoptions');
                    $pco_cart_controller = new \MP\PCO\CartController($module_pco);
                    if (PPATProductHelper::isPPATProduct($id_product, $id_shop)) {
                        $this->customization_id = $ppat_cart_controller->processChangeProductInCartAdd($mode, $this->customization_id);
                        $pco_cart_controller->addToCart($this->customization_id);
                    } else {
                        $this->customization_id = $pco_cart_controller->processChangeProductInCart($mode, $this->customization_id);
                    }
                } else {
                    $this->customization_id = $ppat_cart_controller->processChangeProductInCartAdd($mode, $this->customization_id);
                }
            } else {
                $this->customization_id = $ppat_cart_controller->processChangeProductInCartAdd($mode, $this->customization_id);
            }
        }
        if (version_compare(_PS_VERSION_, '1.7.7.0', '<')) {
            if ($mode == 'update') {
                $ppat_cart_controller->processChangeProductInCartUpdate($mode);
            }
        }
        parent::processChangeProductInCart();
    }
}