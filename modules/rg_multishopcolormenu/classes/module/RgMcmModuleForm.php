<?php
/**
 * Multishop Color Menu
 *
 * @author    Rolige <www.rolige.com>
 * @copyright 2011-2019 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 */

class RgMcmModuleForm
{
    public $menu_active;
    protected $submit_action;
    protected $p;
    protected $tpl;
    protected $module;
    protected $currentIndex;

    private static $default_class = 'RgMcmModuleFormDashboard';

    public function __construct()
    {
        $this->module = Module::getInstanceByName('rg_multishopcolormenu');
        $this->context = Context::getContext();
        $this->currentIndex = $this->context->link->getAdminLink('AdminModules').'&configure='.$this->module->name;
        $this->p = RgMcmConfig::prefix('config');
    }

    final public static function getForm($form_name, $menu = array())
    {
        $class_name = 'RgMcmModuleForm'.Tools::ucfirst($form_name);

        if (!empty($form_name) && class_exists($class_name)) {
            if ($menu && !in_array($form_name, array_keys(call_user_func_array('array_merge', $menu)))) {
                return new self::$default_class();
            }

            return new $class_name();
        }

        return new self::$default_class();
    }

    public function renderForm()
    {
        $form_fields = $this->getFormFields();

        if ($form_fields) {
            $helper = new HelperForm();
            $helper->tpl_vars = array(
                'fields_value' => $this->getFormValues(),
                'languages' => $this->context->controller->getLanguages(),
                'id_language' => $this->context->language->id,
            );
            $helper->show_toolbar = false;
            $helper->module = $this->module;
            $helper->default_form_language = $this->context->language->id;
            $helper->allow_employee_form_lang = $this->context->controller->allow_employee_form_lang;
            $helper->submit_action = $this->submit_action;
            $helper->currentIndex = $this->currentIndex.'&menu_active='.$this->menu_active;
            $helper->token = Tools::getAdminTokenLite('AdminModules');

            return $helper->generateForm($this->getFormFields());
        } else {
            $file = $this->module->getLocalPath().'views/templates/admin/'.$this->tpl;

            if (@is_file($file)) {
                return $this->context->smarty->fetch($file);
            }
        }
    }

    public function getFormFields()
    {
        return array();
    }

    public function getFormValues($for_save = false)
    {
        return array();
    }

    public function isSubmitForm()
    {
        return Tools::isSubmit($this->submit_action);
    }

    public function validateForm()
    {
        return false;
    }

    public function processForm()
    {
        $val = $this->getFormValues(true);

        foreach ($val as $k => $v) {
            Configuration::updateValue($k, $v);
        }

        return $this->module->l('Configuration updated successfully.', __CLASS__);
    }

    protected function l($string)
    {
        return $this->module->l($string, get_class($this));
    }
}
