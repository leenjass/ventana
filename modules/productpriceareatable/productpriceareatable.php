<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 * @author    Musaffar Patel
 * @copyright 2016-2017 Musaffar Patel
 * @license   LICENSE.txt
 */

if (!defined('_PS_VERSION_'))
	exit;

include_once(_PS_MODULE_DIR_.'/productpriceareatable/lib/bootstrap.php');

class ProductPriceAreaTable extends Module
{
	public function __construct()
	{
		$this->name = 'productpriceareatable';
		$this->tab = 'others';
		$this->version = '2.0.19';
		$this->author = 'Musaffar Patel';
		$this->need_instance = 0;
		$this->module_key = 'dc813c8159838de38340402c9e0cea2c';
		$this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

		parent::__construct();
		$this->displayName = $this->l('Product Price Area Table');
		$this->description = $this->l('Table / Matrix based dynamic prices for your products');

		$this->bootstrap = true;
		$this->module_file = __FILE__;

		$this->ps_versions_compliancy = array('min' => '1.7.0.0', 'max' => _PS_VERSION_);
	}

	public function setMedia()
	{
		(new PPATAdminConfigMainController($this))->setMedia();
		(new PPATAdminProductTabController($this))->setMedia();
		(new PPATFrontProductController($this))->setMedia();
		(new PPATFrontCartController($this))->setMedia();
	}

	public function install()
	{
		if (parent::install() == false
			|| !$this->registerHook('backOfficeHeader')
			|| !$this->registerHook('displayLeftColumnProduct')
			|| !$this->registerHook('displayAdminProductsExtra')
			|| !$this->registerHook('actionCartSave')
			|| !$this->registerHook('displayHeader')
			|| !$this->registerHook('displayFooter')
			|| !$this->registerHook('displayProductPriceBlock')
			|| !$this->registerHook('displayFooterProduct')
			|| !$this->registerHook('displayProductButtons')
            || !$this->registerHook('displayCustomization')
            || !$this->registerHook('actionProductAdd')
            || !$this->registerHook('actionProductDelete')
			|| !$this->installModule())
			return false;
		return true;
	}

	public function installModule()
	{
		PPATInstall::install();
		return true;
	}

	public function uninstall()
	{
		return parent::uninstall();
	}

	public function route()
	{
		switch (Tools::getValue('route'))
		{
			case 'ppatadminconfiggeneralcontroller' :
				$ppat_admin_config_general_controller = new PPATAdminConfigGeneralController($this);
				die ($ppat_admin_config_general_controller->route());
				
			case 'ppatfrontproductcontroller' :
				$ppat_front_product_controller = new PPATFrontProductController($this);
				die ($ppat_front_product_controller->route());

			default:
				$ppat_admin_config_main_controller = new PPATAdminConfigMainController($this);
				return $ppat_admin_config_main_controller->route();
		}
	}

	public function getContent()
	{
		return $this->route();
	}

	public function hookDisplayAdminProductsExtra($params)
	{
		$controller_product_tab = new PPATAdminProductTabController($this, $params);
		return $controller_product_tab->route();
	}

	/** Hooks  */

	public function hookDisplayHeader($params)
	{
		$this->setMedia();
	}

	/**
	 * Load scriptt code into cart footer page
	 * @param $params
	 * @return mixed
	 */
	public function hookDisplayFooter($params)
	{
		$ppat_front_cart_controller = new PPATFrontCartController($this);
		return $ppat_front_cart_controller->hookDisplayFooter($params);
	}

	public function hookBackOfficeHeader($params)
	{
		$this->setMedia();
	}

	/**
	 * The module widget is loaded here via Ajax
	 * @param $params
	 * @return mixed
	 */
	public function hookDisplayFooterProduct($params)
	{
		$ppat_front_product_controller = new PPATFrontProductController($this);
		return $ppat_front_product_controller->hookDisplayFooter($params);
	}

	/**
	 * use this hook to initialise widget on quick view product modal
	 * @param $params
	 */
	public function hookDisplayProductButtons($params)
	{
        if (Tools::getValue('action') == 'quickview') {
            return $this->hookDisplayFooterProduct($params);
        }
    }

    /**
     * @param $params
     * @return mixed
     */
    public function hookDisplayCustomization($params)
    {
        $return = (new PPATFrontCartController($this))->hookDisplayCustomization($params);
        return $return;
    }

    /**
     * on product duplicated in back office
     */
    public function hookActionProductAdd($params)
    {
        $id_product_old = $params['request']->attributes->get('id');
        $id_product = $params['id_product'];
        if ((int)$id_product != (int)$id_product_old) {
            PPATMassAssignHelper::duplicateProduct($id_product_old, $id_product, Context::getContext()->shop->id);
        }
    }

    /**
     * Product has been deleted in the back office
     * @param $params
     */
    public function hookActionProductDelete($params)
    {
        PPATMassAssignHelper::deleteProduct($params['id_product'], Context::getContext()->shop->id);
    }
}