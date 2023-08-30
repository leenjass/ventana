<?php
/**
 * Rolige PrestaShop Cleaner Extra
 *
 * @author    Rolige <www.rolige.com>
 * @copyright 2011-2019 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 */

class RgPSCEModuleFormDashboard extends RgPSCEModuleForm
{
    public function __construct()
    {
        parent::__construct();

        $this->menu_active = 'dashboard';
        $this->tpl = 'configure-dashboard.tpl';
    }

    public function renderForm()
    {
        $source = isset($this->module->module_key) && $this->module->module_key ? 'addons' : 'rolige';

        $this->module->boSmartyAssign(array(
            'displayName' => $this->module->displayName,
            'description' => $this->module->description,
            'author' => $this->module->author,
            'author_link' => RgPSCETools::getLink('author', $this->module),
            'module_link' => RgPSCETools::getLink('module', $this->module),
            'partner_link' => RgPSCETools::getLink('partner'),
            'source' => $source,
            'products_marketing' => RgPSCETools::getProductsMarketing($this->module->name, $source),
        ));

        return parent::renderForm();
    }
}
