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

use PrestaShop\PrestaShop\Adapter\Cart\CartPresenter;

class Ps_ShoppingcartOverride extends Ps_Shoppingcart
{
    public function renderModal(Cart $cart, $id_product, $id_product_attribute, $id_customization)
    {
        $data = (new CartPresenter())->present($cart);
        $product = null;
        $hasOption = false;
        if (Module::isInstalled('wkproductoptions') && Module::isEnabled('wkproductoptions')) {
            include_once _PS_MODULE_DIR_ . 'wkproductoptions/classes/WkProductOptionsClasses.php';
            $objOption = new WkProductOptionsConfig();
            $options = $objOption->getAvailableOptionByIdProduct($id_product, $id_product_attribute);
            if (!empty($options)) {
                $hasOption = true;
            }
        }
        if ($hasOption) {
            foreach ($data['products'] as $p) {
                if (
                    (int) $p['id_product'] == $id_product
                    && (int) $p['id_product_attribute'] == $id_product_attribute
                ) {
                    $product = $p;
                    break;
                }
            }
        } else {
            foreach ($data['products'] as $p) {
                if (
                    (int) $p['id_product'] == $id_product
                    && (int) $p['id_product_attribute'] == $id_product_attribute
                    && (int) $p['id_customization'] == $id_customization
                ) {
                    $product = $p;
                    break;
                }
            }
        }
        $this->smarty->assign([
            'product' => $product,
            'cart' => $data,
            'cart_url' => $this->getCartSummaryURL(),
        ]);

        return $this->fetch('module:ps_shoppingcart/modal.tpl');
    }

    /**
     * @return string
     */
    private function getCartSummaryURL()
    {
        return $this->context->link->getPageLink(
            'cart',
            null,
            $this->context->language->id,
            [
                'action' => 'show',
            ],
            false,
            null,
            true
        );
    }
}
