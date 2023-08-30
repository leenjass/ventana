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

class PPATAdminConfigMainController extends PPATControllerCore
{
	protected $sibling;

	public function __construct(&$sibling = null)
	{
		parent::__construct($sibling);
		if ($sibling !== null)
			$this->sibling = &$sibling;
	}
	public function setMedia()
	{
		if (Tools::getValue('controller') == 'AdminModules' && Tools::getValue('configure') == 'productpriceareatable')
		{
			Context::getContext()->controller->addCSS($this->sibling->_path.'views/css/lib/tools.css');
			Context::getContext()->controller->addCSS($this->getAdminWebPath().'/themes/new-theme/public/theme.css');

			Context::getContext()->controller->addJquery();
			Context::getContext()->controller->addJS($this->sibling->_path.'views/js/lib/Tools.js');
			Context::getContext()->controller->addJS($this->sibling->_path.'views/js/lib/Breadcrumb.js');
			Context::getContext()->controller->addJS($this->sibling->_path.'views/js/admin/config/PPATAdminConfigGeneralController.js');
			Context::getContext()->controller->addJS($this->sibling->_path.'views/js/admin/config/PPATAdminConfigEditUnitController.js');
		}
	}

	public function render()
	{
		Context::getContext()->smarty->assign(array(
			'module_config_url' => $this->module_config_url,
		));
		return $this->sibling->display($this->sibling->module_file, 'views/templates/admin/config/main.tpl');
	}

	public function route()
	{
		return $this->render();
	}

}