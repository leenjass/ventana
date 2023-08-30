<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class MyCustomFields extends Module
{
    public function __construct()
    {
        $this->name = 'mycustomfields';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Rishab';
        $this->need_instance = 0;
        $this->bootstrap = true;
        
        parent::__construct();
        
        $this->displayName = $this->l('My Custom Fields');
        $this->description = $this->l('Adds custom fields to the product page settings');
    }

    public function hookDisplayAdminProductsExtra($params)
    {
        $product = new Product((int)Tools::getValue('id_product'));
        $form = new ProductCustomFieldsForm();
        $form->fields_value['width'] = $product->width;
        $form->fields_value['height'] = $product->height;
        
        return $form->generateForm(array(array('form' => $form->fields_form)));
    }

}