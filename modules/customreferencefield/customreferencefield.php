<?php 

class CustomReferenceField extends Module
{
    public function __construct()
    {
        $this->name = 'customreferencefield';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Your Name';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = $this->l('Custom Reference Field');
        $this->description = $this->l('Adds a custom reference field to product page.');
    }

    public function install()
    {
        return parent::install() &&
            $this->registerHook('displayFooterProduct');
    }

    public function hookDisplayFooterProduct($params)
    {
        return $this->display(__FILE__, 'views/templates/hook/displayProductAdditionalInfo.tpl');
    }

    // Implement any additional hooks and functionality here
}
