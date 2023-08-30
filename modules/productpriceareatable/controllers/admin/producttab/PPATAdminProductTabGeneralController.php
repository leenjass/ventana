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

class PPATAdminProductTabGeneralController extends PPATControllerCore
{

	public function __construct($sibling, $params = array())
	{
		parent::__construct($sibling, $params);
		$this->sibling = $sibling;
		$this->base_url = Tools::getShopProtocol().Tools::getShopDomain().__PS_BASE_URI__;
	}

	public function setMedia()
	{
	}

	public function render()
	{
		$ppat_product_model = new PPATProductModel();
		$ppat_unit_model = new PPATUnitModel();
		$units = PPATUnitModel::getUnits(Context::getContext()->language->id, Context::getContext()->shop->id);

		$ppat_product_model->load($this->params['id_product'], Context::getContext()->shop->id);

		foreach ($units as $unit)
		{
			if ($unit['name'] == 'row')
				$row_name = $unit['display_name'];

			if ($unit['name'] == 'col')
				$col_name = $unit['display_name'];
		}

		Context::getContext()->smarty->assign(array(
			'row_name' => $row_name,
			'col_name' => $col_name,
			'module_ajax_url' => $this->module_ajax_url,
			'ppat_product_model' => $ppat_product_model,
			'id_product' => $this->params['id_product'],
		));
		return $this->sibling->display($this->sibling->module_file, 'views/templates/admin/producttab/general.tpl');
	}

	public function processForm()
	{
		$ppat_product_model = new PPATProductModel();
		$ppat_product_model->load(Tools::getValue('id_product'), Context::getContext()->shop->id);

		$ppat_product_model->id_product = (int)Tools::getValue('id_product');
		$ppat_product_model->id_shop = (int)Context::getContext()->shop->id;
		$ppat_product_model->enabled = (int)Tools::getValue('enabled');
		$ppat_product_model->min_row = (float)Tools::getValue('min_row');
		$ppat_product_model->max_row = (float)Tools::getValue('max_row');
		$ppat_product_model->min_col = (float)Tools::getValue('min_col');
		$ppat_product_model->max_col = (float)Tools::getValue('max_col');
		$ppat_product_model->save();
	}

	public function route()
	{
		$return = '';

		switch (Tools::getValue('action'))
		{
			case 'processform':
				die($this->processForm());

			default:
				return $this->render();
		}

	}

}