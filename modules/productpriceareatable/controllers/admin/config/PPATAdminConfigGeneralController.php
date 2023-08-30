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

class PPATAdminConfigGeneralController extends PPATControllerCore
{
	protected $sibling;

	public function __construct(&$sibling = null)
	{
		parent::__construct($sibling);
		if ($sibling !== null)
			$this->sibling = &$sibling;
	}

	public function render()
	{
		$ppat_unit_model = new PPATUnitModel();
		$unit_collection = $ppat_unit_model->getUnits(Context::getContext()->language->id, Context::getContext()->shop->id);

		Context::getContext()->smarty->assign(array(
			'units' => $unit_collection,
		));

		return $this->sibling->display($this->sibling->module_file, 'views/templates/admin/config/general.tpl');
	}

	public function renderEditForm()
	{
		$ppat_unit_model = new PPATUnitModel((int)Tools::getValue('id_unit'));
		$languages = Language::getLanguages();

		Context::getContext()->smarty->assign(array(
			'id_lang' => Context::getContext()->language->id,
			'unit' => $ppat_unit_model,
			'languages' => $languages,
			'id_lang_default' => Configuration::get('PS_LANG_DEFAULT', null, Context::getContext()->shop->id_shop_group, Context::getContext()->shop->id)
		));
		return $this->sibling->display($this->sibling->module_file, 'views/templates/admin/config/general_edit.tpl');
	}

	public function processEditForm()
	{
		$languages = Language::getLanguages(false);

		if (Tools::getValue('id_ppat_unit') != '')
			$ppat_unit = new PPATUnitModel(Tools::getValue('id_ppat_unit'));
		else
			return false;

		$ppat_unit->type = Tools::getValue('type');

		foreach ($languages as $key => $language)
		{
            $ppat_unit->display_name[$language['id_lang']] = Tools::getValue('display_name_' . $language['id_lang']);
            $ppat_unit->suffix[$language['id_lang']] = Tools::getValue('suffix_' . $language['id_lang']);
        }
		$ppat_unit->position = 0;
		$ppat_unit->id_shop = (int)Context::getContext()->shop->id;
		$ppat_unit->save();
	}

	public function route()
	{
		switch (Tools::getValue('action'))
		{
			case 'rendereditform' :
				die ($this->renderEditForm());

			case 'processeditform' :
				die ($this->processEditForm());

			default :
				die ($this->render());
		}
	}

}