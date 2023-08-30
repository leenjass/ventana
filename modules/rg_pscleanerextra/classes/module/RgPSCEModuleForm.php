<?php
/**
 * Rolige PrestaShop Cleaner Extra
 *
 * @author    Rolige <www.rolige.com>
 * @copyright 2011-2019 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 */

class RgPSCEModuleForm
{
    public $menu_active;
    protected $submit_action;
    protected $p;
    protected $tpl;
    protected $module;
    protected $currentIndex;

    private static $default_class = 'RgPSCEModuleFormDashboard';

    public function __construct()
    {
        $this->module = Module::getInstanceByName('rg_pscleanerextra');
        $this->context = Context::getContext();
        $this->currentIndex = $this->context->link->getAdminLink('AdminModules').'&configure='.$this->module->name;
    }

    final public static function getForm($form_name)
    {
        $class_name = 'RgPSCEModuleForm'.Tools::ucfirst($form_name);

        if (!empty($form_name) && class_exists($class_name)) {
            return new $class_name();
        }

        return new self::$default_class();
    }

    public function renderForm()
    {
        $file = $this->module->getLocalPath().'views/templates/admin/'.$this->tpl;

        if (@is_file($file)) {
            return $this->context->smarty->fetch($file);
        }
    }

    public function isSubmitForm()
    {
        return Tools::isSubmit($this->submit_action);
    }

    public function validateForm()
    {
        return false;
    }

    public function getFormValues($for_save = false)
    {
        return array();
    }

    public function processForm()
    {
        $val = $this->getFormValues(true);

        foreach ($val as $k => $v) {
            if (is_array($v)) {
                Configuration::updateValue($k, implode(',', $v));
            } else {
                Configuration::updateValue($k, $v);
            }
        }

        return $this->l('Configuration updated successfully.');
    }

    protected function l($string)
    {
        return $this->module->l($string, get_class($this));
    }
}
