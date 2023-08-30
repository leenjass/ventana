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

use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;

class PPATFrontProductController extends PPATControllerCore {

	protected $sibling;

	public function __construct(&$sibling)
	{
		parent::__construct($sibling);
		if ($sibling !== null) {
            $this->sibling = &$sibling;
        }
	}

	public function setMedia()
	{
        $this->sibling->context->controller->addJquery();
        $this->sibling->context->controller->addJqueryPlugin('typewatch');
        $this->sibling->context->controller->addJS($this->sibling->_path . 'views/js/front/PPATFrontProductController.js');
        $this->sibling->context->controller->addCSS($this->sibling->_path . 'views/css/front/front.css');
	}

	/**
	 * Add script initialisation vars for the PPBS widgt which will be loaded via ajax
	 * @param $params
	 * @return bool
	 */
	public function hookDisplayFooter($params)
	{
		if (Context::getContext()->controller->php_self != 'product') return false;

		$ppat_product_model = new PPATProductModel();
		$ppat_product_model->load(Tools::getValue('id_product'), Context::getContext()->shop->id);
		if (!$ppat_product_model->enabled) {
			return false;
		}		

		$ppat_product_model = new PPATProductModel();
		$ppat_product_model->load(Tools::getValue('id_product'), Context::getContext()->shop->id);
		if (empty($ppat_product_model->id_ppat_product)) return false;

		$this->sibling->smarty->assign(array(
			'baseDir' => __PS_BASE_URI__,
			'ppat_module_ajax_url' => $this->module_ajax_url,
            'ppat_enabled' => '1',
			'action' => Tools::getValue('action')
		));
		return $this->sibling->display($this->sibling->module_file, 'views/templates/front/hook_product_footer.tpl');
	}

	/**
	 * Get price matrix organised by options
	 * @param $table_options
	 * @return array
	 */
	public function getPricesMatrix($table_options)
	{
		$price_matrix = array();
		foreach ($table_options as $key => $option) {
			$prices = PPATProductPriceTableModel::getOptionGridData($option['id_option'], true);

			foreach ($prices as $key => $rows) {
                $row = number_format((float)$key, 2, '.', '');
				foreach ($rows as $key2 => $cols) {
                    $col_key = number_format((float)$key2, 2, '.', '');
                    $row_values[$col_key] = $cols;
				}
                $price_matrix[$option['id_option']][$row] = $row_values;
			}
		}
		return $price_matrix;
	}
	

	/**
	 * Displ;ay the module on the product page
	 * @param $module_file
	 * @return string
	 */
	public function renderWidget()
	{
		$units = PPATUnitModel::getUnitsList(Context::getContext()->language->id, Context::getContext()->shop->id);
		//$table_options_original = PPATProductTableOptionModel::getProductTableOptions(Tools::getValue('id_product'), Context::getContext()->shop->id, Context::getContext()->language->id, true);
        $table_options_original = PPATProductTableOptionHelper::getProductTableOptions(Tools::getValue('id_product'), Context::getContext()->language->id);

		$table_options = array();
		$id_option_default = 0;
		foreach ($table_options_original as $option)
		{
			if ($option['enabled'] == 1)
				$table_options[] = $option;
			if ($id_option_default == 0)
				$id_option_default = $option['id_option'];
		}

		if (empty($table_options)) {
			return false;
		}		

		$price_matrix = $this->getPricesMatrix($table_options);

		$ppat_product_model = new PPATProductModel();
		$ppat_product_model->load(Tools::getValue('id_product'), Context::getContext()->shop->id);

		$product_option_label_model = new PPATProductOptionLabelLangModel();
		$product_option_label_model->loadByLang(Tools::getValue('id_product'), Context::getContext()->language->id, Context::getContext()->shop->id);

		$lowest_size = PPATProductPriceTableModel::getLowestSizeAndPrice(Tools::getValue('id_product'), $id_option_default);

		if (empty($lowest_size['row'])) $lowest_size['row'] = '';
		if (empty($lowest_size['col'])) $lowest_size['col'] = '';

		$this->sibling->smarty->assign(array(
			'ppat_product_json' => json_encode($ppat_product_model),
			'ppat_price_matrix_json' => json_encode($price_matrix),
			'table_options_json' => json_encode($table_options),
			'ppat_product' => $ppat_product_model,
			'table_options' => $table_options,
			'units' => $units,
			'id_language' => Context::getContext()->language->id,
			'ppat_product_option_label' => $product_option_label_model->text,
			'default_row' => $lowest_size['row'],
			'default_col' => $lowest_size['col']
		));
		return $this->sibling->display($this->sibling->module_file, 'views/templates/front/ppat.tpl');
	}

	/**
	 * Gets the rows and cols optyions for the dropdown on the product page
	 * @param $name
	 * @param $id_option
	 * @return mixed
	 */
	public function getUnitEntryValues($name, $id_option)
	{
		return json_encode(PPATProductPriceTableModel::getUnitEntryValues($name, $id_option));
	}

	/**
	 * Get Product Information such as prices, tax etc based on id_product and id_product_attribute
	 */
	public function getProductInfo()
	{
		return (json_encode(PPATProductHelper::getProductInfo(Tools::getValue('id_product'), Tools::getValue('group'))));
	}

	/**
	 * Format non formatted price to correct currency format, invoked via ajax
	 * @return string
	 */
	public function formatPrice()
	{
        $decimals = PPATToolsHelper::getPricePrecision();
        $priceFormatter = new PriceFormatter();
        $price = Tools::ps_round(Tools::getValue('price'), $decimals);
		return $priceFormatter->convertAndFormat($price);
	}

	public function lookupPrice()
	{
		$ppat_product_options = new PPATProductTableOptionModel((int)Tools::getValue('id_option'));
		return json_encode(PPATProductPriceTableModel::getUnitEntryPrice(Tools::getValue('id_option'), Tools::getValue('row'), Tools::getValue('col'), $ppat_product_options->lookup_rounding_mode));
	}

	public function route()
	{
		switch (Tools::getValue('action'))
		{
			case 'renderwidget' :
				return $this->renderWidget();

			case 'getproductinfo' :
				die ($this->getProductInfo());

			case 'getUnitEntryValues' :
				die ($this->getUnitEntryValues(Tools::getValue('name'), Tools::getValue('id_option')));

			case 'formatprice' :
				die ($this->formatPrice());

			case 'lookupprice' :
				die ($this->lookupPrice());
		}
	}

}