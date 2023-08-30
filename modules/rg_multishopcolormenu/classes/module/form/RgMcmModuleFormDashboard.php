<?php
/**
 * Multishop Color Menu
 *
 * @author    Rolige <www.rolige.com>
 * @copyright 2011-2019 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 */

class RgMcmModuleFormDashboard extends RgMcmModuleForm
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
            'author_link' => RgMcmTools::getLink('author', $this->module),
            'module_link' => RgMcmTools::getLink('module', $this->module),
            'partner_link' => RgMcmTools::getLink('partner'),
            'source' => $source,
            'products_marketing' => RgMcmTools::getProductsMarketing($this->module->name, $source),
        ));

        return parent::renderForm();
    }
}
