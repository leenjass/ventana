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

class PPATAdminProductTabPricesController extends PPATControllerCore
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
		$languages = Language::getLanguages();

		$ppat_option_label_model = new PPATProductOptionLabelLangModel();
		$option_label = $ppat_option_label_model->loadByProduct($this->params['id_product'], Context::getContext()->shop->id);

		Context::getContext()->smarty->assign(array(
			'module_ajax_url' => $this->module_ajax_url,
			'option_label' => $option_label,
			'id_product' => $this->params['id_product'],
			'languages' => $languages,
			'id_lang_default' => Configuration::get('PS_LANG_DEFAULT', null, Context::getContext()->shop->id_shop_group, Context::getContext()->shop->id)
		));
		return $this->sibling->display($this->sibling->module_file, 'views/templates/admin/producttab/prices.tpl');
	}


	/**
	 * render the edit option form
	 */
	public function renderEditOption()
	{
		$ppat_product_option = new PPATProductTableOptionModel(Tools::getValue('id_option'));

		$languages = Language::getLanguages();
		Context::getContext()->smarty->assign(array(
			'id_product' => Tools::getValue('id_product'),
			'id_option' => Tools::getValue('id_option'),
			'ppat_product_option' => $ppat_product_option,
			'module_ajax_url' => $this->module_ajax_url,
			'languages' => $languages,
			'id_lang_default' => Configuration::get('PS_LANG_DEFAULT', null, Context::getContext()->shop->id_shop_group, Context::getContext()->shop->id)
		));
		return $this->sibling->display($this->sibling->module_file, 'views/templates/admin/producttab/prices_optionedit.tpl');
	}

	/**
	 * update / create the product options label-
	 */
	public function processUpdateLabel()
	{
		$languages = Language::getLanguages(false);

		PPATProductOptionLabelLangModel::deleteByProduct(Tools::getValue('id_product'), Context::getContext()->shop->id);

		foreach ($languages as $key => $language)
		{
			$ppat_option_label_model = new PPATProductOptionLabelLangModel();
			$ppat_option_label_model->id_product = (int)Tools::getValue('id_product');
			$ppat_option_label_model->id_shop = (int)Context::getContext()->shop->id;
			$ppat_option_label_model->id_lang = $language['id_lang'];
			$ppat_option_label_model->text = pSQL(Tools::getValue('text_'.$language['id_lang']));
			$ppat_option_label_model->save();
		}
	}

	/**
	 * save option value text
	 */
	public function processOptionValue()
	{
		$languages = Language::getLanguages(false);
		$ppat_table_option_model = new PPATProductTableOptionModel();
		$ppat_table_option_model->id_product = (int)Tools::getValue('id_product');

		foreach ($languages as $lang)
			$ppat_table_option_model->option_text[$lang['id_lang']] = Tools::getValue('option_text_'.$lang['id_lang']);

		$ppat_table_option_model->add();
	}

	/**
	 * Refresh the product table options list
	 */
	public function refreshOptionsList()
	{
		$ppat_table_option_model = new PPATProductTableOptionModel();
		$options_collection = $ppat_table_option_model->loadByProduct(Tools::getValue('id_product'), $this->ps_lang_default);

		Context::getContext()->smarty->assign(array(
			'module_ajax_url' => $this->module_ajax_url,
			'options' => $options_collection,
			'id_product' => $this->params['id_product']
		));
		return $this->sibling->display($this->sibling->module_file, 'views/templates/admin/producttab/prices_optionslist.tpl');
	}

	/**
	 * Update the positions of the options
	 */
	public function processOptionsPositions()
	{
		if (!Tools::getIsset('ids_option')) return false;

		$position = 0;
		foreach (Tools::getValue('ids_option') as $id_option)
		{
			$ppat_table_option_model = new PPATProductTableOptionModel($id_option);
			$ppat_table_option_model->position = $position;
			$ppat_table_option_model->update();
			$position++;
		}
	}

	/**
	 * delete table option completely
	 */
	public function processDeleteOption()
	{
		$ppat_table_option_model = new PPATProductTableOptionModel((int)Tools::getValue('id_option'));
		$ppat_table_option_model->delete();
	}

	/**
	 * Edit Option
	 */
	public function processEditOption()
	{
        $languages = Language::getLanguages(false);

		$product_table_option = new PPATProductTableOptionModel(Tools::getValue('id_option'));
		$product_table_option->id_product = (int)Tools::getValue('id_product');
		$product_table_option->position = 0;
		$product_table_option->enabled = (int)Tools::getValue('enabled');
		$product_table_option->lookup_rounding_mode = pSQL(Tools::getValue('lookup_rounding_mode'));
		$product_table_option->default_row = (float)Tools::getValue('default_row');
		$product_table_option->default_col = (float)Tools::getValue('default_col');

		foreach ($languages as $lang)
		{
			if (Tools::getIsset('option_text_'.$lang['id_lang']))
				$product_table_option->option_text[$lang['id_lang']] = pSQL(Tools::getValue('option_text_'.$lang['id_lang']));
			else
				$product_table_option->option_text[$lang['id_lang']] = '';
		}
		$product_table_option->update();
	}

	/**
	 * render the price table for the option supplied
	 */
	public function renderPriceTable()
	{
		$grid_cols = PPATProductPriceTableModel::getOptionGridColumns(Tools::getValue('id_option'));
		$grid_data = PPATProductPriceTableModel::getOptionGridData(Tools::getValue('id_option'));
		$units = PPATUnitModel::getUnitsList(Context::getContext()->language->id, Context::getContext()->shop->id);

		$this->sibling->smarty->assign(array(
			'id_option' => Tools::getValue('id_option'),
			'data_columns' => json_encode($grid_cols),
			'data_grid' => json_encode($grid_data),
			'row_title' => $units['row']['display_name'].'('.$units['row']['suffix'].')',
			'col_title' => $units['col']['display_name'].'('.$units['col']['suffix'].')',
		));

		return $this->sibling->display($this->sibling->module_file, 'views/templates/admin/producttab/prices_optiontable.tpl');
	}

	public function processPriceTable()
	{
		$columns = json_decode(Tools::getValue('grid_json_columns'));
		$data = json_decode(Tools::getValue('grid_json_data'));

		$price_matrix_collection = array(); //of TPPATProductTablePrice
		foreach ($data as $data_row)
		{
			$i = 0;
			$row = $data_row[0];
			foreach ($columns[1]->colModel as $col)
			{
				$product_table_price = [];
				$product_table_price['id_option'] = Tools::getValue('id_option');
				$product_table_price['row'] = $row;
				$product_table_price['col'] = $columns[1]->colModel[$i]->title;
				$product_table_price['row_max'] = 0;
				$product_table_price['col_max'] = 0;
				if (isset($data_row[$i + 1]))
					$product_table_price['price'] = $data_row[$i + 1];
				else
					$product_table_price['price'] = '0.00';
				$price_matrix_collection[] = $product_table_price;
				$i++;
			}
		}
		PPATProductPriceTableModel::saveGrid($price_matrix_collection, Tools::getValue('id_option'));
	}

	public function processImportCSV()
	{
		ini_set("auto_detect_line_endings", true);
		$price_matrix_collection = array();
		$post_file = $_FILES['file'];

		//detect delimiter
        $delimiter = ',';
        $handle = fopen($post_file['tmp_name'], 'r');
        $data = fgetcsv($handle, 9000, ",");

        if (count($data) == 1) {
            $delimiter = "\t";
        }
        fclose($handle);

		$csv_prices = array();
		$handle = fopen($post_file['tmp_name'], 'r');
		if (empty($handle) === false) {
            while (($data = fgetcsv($handle, 9000, $delimiter)) !== false) {
                $csv_prices[] = $data;
            }
        }
        fclose($handle);

		$columns = $csv_prices[1];

		$i = 0;
		foreach ($csv_prices as $key => $value) {
			$i++;
			if ($i <= 2) {
			    continue;
            }

			$j = 0;
			foreach ($value as $key2 => $value2) {
				$j++;
				if ($j <= 1) {
				    continue;
                }

				/* get min max col values for ranges */
				$col_arr = array();
				$col = $columns[$key2];
				if (strpos($col, '-') > 0)
				{
					$tmp_arr = explode('-', $col);
					$col_arr[0] = $tmp_arr[0];
					$col_arr[1] = $tmp_arr[1];
				}
				else
				{
					$col_arr[0] = $col;
					$col_arr[1] = $col;
				}

				/* get min max row values for ranges */
				$row = $value[0];
				$row_arr = array();
				if (strpos($row, '-') > 0)
				{
					$tmp_arr = explode('-', $row);
					$row_arr[0] = $tmp_arr[0];
					$row_arr[1] = $tmp_arr[1];
				}
				else
				{
					$row_arr[0] = $row;
					$row_arr[1] = $row;
				}

				$product_table_price = [];
				$product_table_price['id_option'] = Tools::getValue('id_option');
				$product_table_price['row'] = $row_arr[0];
				$product_table_price['row_max'] = 0;
				$product_table_price['col'] = $col_arr[0];
				$product_table_price['col_max'] = 0;
				//$product_table_price->row_max = $row_arr[1];
				//$product_table_price->col_max = $col_arr[1];
				$product_table_price['price'] = $value2;
				$price_matrix_collection[] = $product_table_price;
			}
		}
		PPATProductPriceTableModel::saveGrid($price_matrix_collection, Tools::getValue('id_option'));
	}

	public function route()
	{
		$return = '';

		switch (Tools::getValue('action'))
		{
			case 'renderpricetable' :
				die ($this->renderPriceTable());

			case 'rendereditoption' :
				die ($this->renderEditOption());

			case 'processupdatelabel' :
				die ($this->processUpdateLabel());

			case 'processoptionvalue' :
				die ($this->processOptionValue());

			case 'refreshoptionslist' :
				die ($this->refreshOptionsList());

			case 'processoptionspositions' :
				die ($this->processOptionsPositions());

			case 'processdeleteoption' :
				die ($this->processDeleteOption());

			case 'processeditoption' :
				die ($this->processEditOption());

			case 'processpricetable' :
				die ($this->processPriceTable());

			case 'processimportcsv' :
				die ($this->processImportCSV());

			default:
				return $this->render();
		}

	}

}