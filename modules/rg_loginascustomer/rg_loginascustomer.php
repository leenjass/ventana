<?php
/**
 * Login as Customer
 *
 *  @author    Rolige <www.rolige.com>
 *  @copyright 2011-2022 Rolige - All Rights Reserved
 *  @license   Proprietary and confidential
 */

use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Rg_LoginAsCustomer extends Module
{
    public function __construct()
    {
        $this->name = 'rg_loginascustomer';
        $this->tab = 'back_office_features';
        $this->version = '1.0.1';
        $this->author = 'Rolige';
        $this->controllers = ['login'];

        $this->bootstrap = true;
        parent::__construct();

        $this->ps_versions_compliancy = ['min' => '1.7.1.0', 'max' => _PS_VERSION_];
        $this->displayName = $this->l('Login as Customer');
        $this->description = $this->l('Allows you login as an specific customer at front office.');
    }

    public function install()
    {
        return parent::install() &&
            $this->registerHook('displayAdminCustomers') &&
            $this->registerHook('actionCustomerGridDefinitionModifier');
    }

    public function hookDisplayAdminCustomers($request)
    {
        $customer = new Customer((isset($request['id_customer']) && $request['id_customer']) ? $request['id_customer'] : $request['request']->get('customerId'));
        if (!Validate::isLoadedObject($customer)) {
            return;
        }

        $link = $this->context->link->getModuleLink($this->name, 'login', ['id_customer' => $customer->id, 'xtoken' => Tools::hash($customer->id . date('YmdH'))]);

        return '<div class="col-md-3">
                <div class="card">
                  <h3 class="card-header">
                    <i class="material-icons">lock_open</i>
                    ' . $this->l('Login as Customer') . '
                  </h3>
                  <div class="card-body">
                    <p class="text-muted text-center">
                        <a href="' . $link . '" target="_blank" style="text-decoration: none;">
                            <i class="material-icons d-block">lock_open</i>' . $this->l('Login as Customer') . '
                        </a>
                    </p>
                  </div>
                </div>
                </div>';
    }

    public function hookActionCustomerGridDefinitionModifier(array $params)
    {
        $definition = $params['definition'];

        foreach ($definition->getColumns() as $column) {
            if ($column->getId() == 'actions') {
                $options = $column->getOptions();
                $options['actions']->add(
                    (new LinkRowAction('login_as_customer'))
                        ->setName($this->l('Login As'))
                        ->setIcon('lock_open')
                        ->setOptions([
                            'route' => 'loginascustomer_login_as_customer',
                            'route_param_name' => 'customerId',
                            'route_param_field' => 'id_customer',
                        ])
                );
            }
        }
    }
}
