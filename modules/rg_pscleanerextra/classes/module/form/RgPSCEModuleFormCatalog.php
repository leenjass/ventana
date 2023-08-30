<?php
/**
 * Rolige PrestaShop Cleaner Extra
 *
 * @author    Rolige <www.rolige.com>
 * @copyright 2011-2019 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 */

class RgPSCEModuleFormCatalog extends RgPSCEModuleForm
{
    public function __construct()
    {
        parent::__construct();

        $this->menu_active = 'catalog';
        $this->submit_action = 'submitTruncateCatalog';
        $this->p = 'RGPSCE_CAT_';
    }

    public function renderForm()
    {
        $fields_value = $this->getFormValues();

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
                        'title' => $this->l('Catalog'),
                        'icon' => 'icon-th-large'
                    ),
                    'input' => array(
                        array(
                            'type' => 'switch',
                            'is_bool' => true,
                            'label' => $this->l('I understand that all the catalog data will be removed without possible rollback: products, features, categories, tags, images, prices, attachments, scenes, stocks, attribute groups and values, manufacturers, suppliers...'),
                            'name' => $this->p.'CHECKTRUNCATECATALOG',
                            'values' => array(
                                array(
                                    'id' => $this->p.'CHECKTRUNCATECATALOG_on',
                                    'value' => 1,
                                    'label' => $this->l('Enabled')
                                ),
                                array(
                                    'id' => $this->p.'CHECKTRUNCATECATALOG_off',
                                    'value' => 0,
                                    'label' => $this->l('Disabled')
                                )
                            )
                        )
                    ),
                    'submit' => array(
                        'title' => $this->l('Delete catalog')
                    )
                )
            )
        ));
    }

    public function getFormValues($for_save = false)
    {
        $fields_value = array(
            ($name = $this->p.'CHECKTRUNCATECATALOG') => (int)Tools::getValue($name),
        );

        return $fields_value;
    }

    public function validateForm()
    {
        $val = $this->getFormValues();

        $panel = $this->l('Catalog').' > ';
        if (!$val[$this->p.'CHECKTRUNCATECATALOG']) {
            return $panel.$this->l('Please read the disclaimer and click "Yes" above before continue.');
        }

        return false;
    }

    public function processForm()
    {
        RgPSCETools::truncate('catalog');
        return $this->l('Catalog truncated');
    }
}
