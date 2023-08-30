<?php
/**
 * Rolige PrestaShop Cleaner Extra
 *
 * @author    Rolige <www.rolige.com>
 * @copyright 2011-2019 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 */

class RgPSCEModuleFormImages extends RgPSCEModuleForm
{
    public function __construct()
    {
        parent::__construct();

        $this->menu_active = 'images';
        $this->submit_action = 'submitDeleteOldImages';
        $this->p = 'RGPSCE_IMG_';
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
                        'title' => $this->l('Product images cleaning'),
                        'icon' => 'icon-image'
                    ),
                    'description' => $this->l('This function will delete only empty images folders and obsolete images of products already deleted from your shop'),
                    'submit' => array(
                        'title' => $this->l('Delete old images'),
                    )
                )
            )
        ));
    }

    public function processForm()
    {
        if ($deleted_images = RgPSCETools::deleteOldImages()) {
            return sprintf($this->l('%d old product images were deleted'), $deleted_images);
        } else {
            return $this->l('There are not obsolete images to delete');
        }
    }
}
