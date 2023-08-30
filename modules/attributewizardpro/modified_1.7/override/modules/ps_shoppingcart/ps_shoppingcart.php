<?php

use PrestaShop\PrestaShop\Adapter\Cart\CartPresenter;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * overridden for AWP file upload field preview purpose
 */
class Ps_ShoppingcartOverride extends Ps_Shoppingcart {

  public function renderModal(Cart $cart, $id_product, $id_product_attribute, $instructions_valid = null)
    {
        // populate Smarty object with cart_url private method data
        parent::renderModal($cart, $id_product, $id_product_attribute, $instructions_valid);

        $data = (new CartPresenter)->present($cart);

        $product = null;
        foreach ($data['products'] as $p) {
            if ($p['id_product'] == $id_product && $p['id_product_attribute'] == $id_product_attribute && $p['instructions_valid'] == $instructions_valid) {
                $product = $p;
                break;
            }
        }
        $this->smarty->assign('product', $product);

        return parent::fetch('module:ps_shoppingcart/modal.tpl');
    }

}
