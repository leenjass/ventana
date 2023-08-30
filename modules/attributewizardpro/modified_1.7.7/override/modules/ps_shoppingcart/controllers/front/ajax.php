<?php


class Ps_ShoppingcartAjaxModuleFrontControllerOverride extends Ps_ShoppingcartAjaxModuleFrontController
{

    /**
     * overridden for AWP file upload field preview purpose
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        ModuleFrontController::initContent();

        $modal = null;

        if (Tools::getValue('action') === 'add-to-cart') {
            $modal = $this->module->renderModal(
                $this->context->cart,
                Tools::getValue('id_product'),
                Tools::getValue('id_product_attribute'),
                Tools::getValue('instructions_valid')
            );
        }

        ob_end_clean();
        header('Content-Type: application/json');
        die(json_encode([
            'preview' => $this->module->renderWidget(null, ['cart' => $this->context->cart]),
            'modal' => $modal
        ]));
    }
}