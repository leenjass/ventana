<?php

include(dirname(__FILE__) . '/../../config/config.inc.php');

$awp = Module::getInstanceByName('attributewizardpro');

$awp_random = Tools::getValue('awp_random');
if ($awp_random != Configuration::get('AWP_RANDOM') || Tools::getValue('action') != 'get_parent_group_names') {
    die('No Access');
}

$result = Db::getInstance()->ExecuteS('SELECT * FROM `' . _DB_PREFIX_ . 'awp_attribute_wizard_pro`');
$result = $result[0]['awp_attributes'];

$attributes = unserialize($result);
$id_lang = (int)Tools::getValue('id_lang');
$parent_group_names = array();
foreach ($attributes as $attribute) {
	if(isset($attribute['parent_group_name_'.$id_lang]) && $attribute['parent_group_name_'.$id_lang]) {
		$parent_group_names[] = $attribute['parent_group_name_'.$id_lang];
	}
}

die(Tools::jsonEncode(array('pgn' => $parent_group_names)));
