<?php
class ProductCustomFieldsForm extends HelperForm
{
    public function __construct()
    {
        $this->module = 'mycustomfields';
        $this->name_controller = 'mycustomfields';
        $this->identifier = 'id_product';
        $this->token = Tools::getAdminTokenLite('AdminProducts');
        $this->currentIndex = AdminController::$currentIndex.'&id_product='.(int)Tools::getValue('id_product');
        $this->show_toolbar = false;
        $this->toolbar_scroll = false;
        
        parent::__construct();
        
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Custom Fields'),
                'icon' => 'icon-cogs',
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Width'),
                    'name' => 'width',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Height'),
                    'name' => 'height',
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            ),
        );
    }
}
