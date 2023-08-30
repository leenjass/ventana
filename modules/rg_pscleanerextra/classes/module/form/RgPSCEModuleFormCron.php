<?php
/**
 * Rolige PrestaShop Cleaner Extra
 *
 * @author    Rolige <www.rolige.com>
 * @copyright 2011-2019 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 */

class RgPSCEModuleFormCron extends RgPSCEModuleForm
{
    public function __construct()
    {
        parent::__construct();

        $this->menu_active = 'cron';
    }

    public function renderForm()
    {
        $fields_value = $this->getFormValues();

        $cron_base_url = Tools::getShopDomainSsl(true).$this->module->getPathUri().'crons/cron.php?token='.$this->module->secure_key.'&id_shop='.Context::getContext()->shop->id;
        Media::addJsDef(array('cron_base_url' => $cron_base_url));
        Media::addJsDef($fields_value);

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->module = $this->module;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = (int)Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG');
        $helper->submit_action = $this->submit_action;
        $helper->currentIndex = $this->currentIndex.'&menu_active='.$this->menu_active;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $fields_value,
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array(
            array(
                'form' => array(
                    'legend' => array(
                        'title' => $this->l('Database cron job'),
                        'icon' => 'icon-clock-o'
                    ),
                    'input' => array(
                        array(
                            'type' => 'text',
                            'label' => $this->l('Obsolete connections statistics'),
                            'prefix' => '>=',
                            'suffix' => $this->l('days old'),
                            'col' => 3,
                            'name' => 'connections'
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Abandoned cart clean range'),
                            'prefix' => '>=',
                            'suffix' => $this->l('days old'),
                            'col' => 3,
                            'name' => 'cart_range'
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Old cart rules clean range'),
                            'prefix' => '>=',
                            'suffix' => $this->l('days old'),
                            'col' => 3,
                            'name' => 'cart_rule_range'
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Obsolete search statistics'),
                            'prefix' => '>=',
                            'suffix' => $this->l('days old'),
                            'col' => 3,
                            'name' => $this->p.'stats_search'
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Obsolete error logs'),
                            'prefix' => '>=',
                            'suffix' => $this->l('days old'),
                            'col' => 3,
                            'name' => $this->p.'ps_log'
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Old emails clean range'),
                            'prefix' => '>=',
                            'suffix' => $this->l('days old'),
                            'col' => 3,
                            'name' => $this->p.'mails'
                        ),
                        array(
                            'type' => 'switch',
                            'label' => $this->l('Functional integrity constraints'),
                            'name' => 'integrity',
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'id' => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Enabled'),
                                ),
                                array(
                                    'id' => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('Disabled'),
                                ),
                            ),
                            'hint' => $this->l('This will check all tables to delete obsolete and unnecessary data from your database.'),
                        ),
                        array(
                            'type' => 'html',
                            'label' => $this->l('Cron job URL'),
                            'html_content' => '<code class="command"></code>',
                            'name' => 'cron_url'
                        ),
                    ),
                )
            ),
        ));
    }

    public function getFormValues($for_save = false)
    {
        $fields_value = array(
            $this->p.'connections' => 90,
            $this->p.'cart_range' => 30,
            $this->p.'cart_rule_range' => 60,
            $this->p.'stats_search' => 60,
            $this->p.'ps_log' => 180,
            $this->p.'mails' => 180,
            $this->p.'integrity' => 1,
            $this->p.'cron_url' => '',
        );

        return $fields_value;
    }
}
