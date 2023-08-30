<?php
/**
 * Login as Customer
 *
 *  @author    Rolige <www.rolige.com>
 *  @copyright 2011-2022 Rolige - All Rights Reserved
 *  @license   Proprietary and confidential
 */

class Rg_LoginascustomerLoginModuleFrontController extends ModuleFrontControllerCore
{
    public $ssl = true;
    public $display_column_left = false;

    public function initContent()
    {
        parent::initContent();

        $id_customer = (int) Tools::getValue('id_customer');
        $token = Tools::hash($id_customer . date('YmdH'));
        if ($id_customer && Tools::getValue('xtoken') == $token) {
            $customer = new Customer((int) $id_customer);
            if (Validate::isLoadedObject($customer)) {
                $this->context->updateCustomer($customer);
                Tools::redirect('index.php?controller=my-account');
            }
        }

        $this->setTemplate('module:' . $this->module->name . '/views/templates/front/failed.tpl');
    }
}
