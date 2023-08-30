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

class PPATAdminProductTabController extends PPATControllerCore
{

	public function __construct($sibling, $params = array())
	{
		parent::__construct($sibling, $params);
		$this->sibling = $sibling;
		//$this->set_module_base_admin_url();
		$this->base_url = Tools::getShopProtocol().Tools::getShopDomain().__PS_BASE_URI__;
	}

	public function setMedia()
	{
		if (Tools::getValue('controller') == 'AdminProducts')
		{
			Context::getContext()->controller->addCSS($this->sibling->_path.'views/css/lib/tools.css');
			Context::getContext()->controller->addCSS($this->sibling->_path.'views/css/admin/admin.css');
			Context::getContext()->controller->addCSS($this->sibling->_path.'views/css/grid/pqgrid.min.css');
			//Context::getContext()->controller->addCSS('http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/themes/base/jquery-ui.css');
			Context::getContext()->controller->addCSS('https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/base/jquery-ui.css');

			Context::getContext()->controller->addJS($this->sibling->_path.'views/js/lib/Tools.js');
			Context::getContext()->controller->addJS($this->sibling->_path.'views/js/admin/producttab/PPATAdminProductTabGeneralController.js');
			Context::getContext()->controller->addJS($this->sibling->_path.'views/js/admin/producttab/PPATGridController.js');
			Context::getContext()->controller->addJS($this->sibling->_path.'views/js/admin/producttab/PPATAdminProductTabPricesController.js');
		}
	}

	protected function set_module_base_admin_url()
	{
		if (!defined('_PS_ADMIN_DIR_'))
		{
			$this->admin_mode = false;
			return false;
		}
		else
		{
			$this->admin_mode = true;
			$arr_temp = explode('/', _PS_ADMIN_DIR_);
			if (count($arr_temp) <= 1) $arr_temp = explode('\\', _PS_ADMIN_DIR_);
			$dir_name = end($arr_temp);
			$this->module_base_url = $dir_name.'/index.php?controller=AdminProducts&id_product='.Tools::getValue('id_product').'&updateproduct&token='.Tools::getAdminTokenLite('AdminProducts');
		}
	}

	public function render()
	{
		Context::getContext()->smarty->assign(array(
			'module_ajax_url' => $this->module_ajax_url,
			'module_url' => $this->getShopBaseUrl(),
			'id_product' => $this->params['id_product']
		));
		return $this->sibling->display($this->sibling->module_file, 'views/templates/admin/producttab/main.tpl');
	}

	public function route()
	{
		$return = '';

		switch (Tools::getValue('route'))
		{
			case 'ppatadminproducttabgeneralcontroller' :
				$ppat_admin_producttab_general_controller = new PPATAdminProductTabGeneralController($this->sibling, $this->params);
				return $ppat_admin_producttab_general_controller->route();

			case 'ppatadminproducttabpricescontroller' :
				$ppat_admin_producttab_prices_controller = new PPATAdminProductTabPricesController($this->sibling, $this->params);
				return $ppat_admin_producttab_prices_controller->route();

			default:
				return $this->render();
		}

	}

}