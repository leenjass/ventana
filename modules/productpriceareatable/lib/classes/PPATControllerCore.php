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

class PPATControllerCore extends Module
{
	protected $module_ajax_url = '';
	protected $module_config_url = '';
	protected $sibling;
	protected $helper_form;
	protected $helper_list;
	protected $params = array();
	protected $ps_lang_default = 0;

	protected $key_tab = 'ModuleProductpriceareatable';

	public function __construct($sibling, $params = array())
	{
		$this->sibling = $sibling;
        $this->ps_lang_default = Configuration::get('PS_LANG_DEFAULT');

		if (!empty($params))
			$this->params = $params;

		parent::__construct();
        $link = new Link();
        $this->module_ajax_url = $link->getModuleLink('productpriceareatable', 'ajax', array());

		if (Tools::getValue('controller') == 'AdminModules')
			$this->module_config_url = AdminController::$currentIndex.'&configure='.$this->sibling->name.'&token='.Tools::getAdminTokenLite('AdminModules');
		else
			$this->module_config_url = '';

		if (AdminController::$currentIndex != '')
			$this->module_tab_url = AdminController::$currentIndex.'&'.'updateproduct&id_product='.Tools::getValue('id_product').'&token='.Tools::getAdminTokenLite('AdminProducts').'&key_tab='.$this->key_tab;
	}

	/**
	 * Get the url to the module folder
	 * @return string
	 */
	protected function getShopBaseUrl()
	{
		if (Tools::getShopDomain() != $_SERVER['HTTP_HOST'])
			$domain = $_SERVER['HTTP_HOST'];
		else
			$domain = Tools::getShopDomain();

		if (empty($_SERVER['HTTPS']) || !$_SERVER['HTTPS'])
			return "http://".$domain.__PS_BASE_URI__.'modules/'.$this->sibling->name.'/';
		else
			return "https://".$domain.__PS_BASE_URI__.'modules/'.$this->sibling->name.'/';
	}

	/**
	 * get pth to admin folder
	 * @return mixed
	 */
	protected function getAdminWebPath()
	{
        $admin_webpath = str_ireplace(_PS_CORE_DIR_, '', _PS_ADMIN_DIR_);
        $admin_webpath = preg_replace('/^'.preg_quote(DIRECTORY_SEPARATOR, '/').'/', '', $admin_webpath);
		return __PS_BASE_URI__.$admin_webpath;
	}

	protected function assignTranslations($translations_collection, $instance_smarty=null)
	{
		$id_language = Context::getContext()->language->id;
		foreach ($translations_collection as $ddw_translation)
		{
			$this->sibling->smarty->assign(array(
				$ddw_translation->name => $ddw_translation->text_collection[$id_language]
			));
		}
	}

}