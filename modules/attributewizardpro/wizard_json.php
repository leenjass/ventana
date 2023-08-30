<?php
/**
 * 2008 - 2017 Presto-Changeo
 *
 * MODULE Attribute Wizard Pro
 *
 * @version   2.0.0
 * @author    Presto-Changeo <info@presto-changeo.com>
 * @link      http://www.presto-changeo.com
 * @copyright Copyright (c) permanent, Presto-Changeo
 * @license   Addons PrestaShop license limitation
 *
 * NOTICE OF LICENSE
 *
 * Don't use this module on several shops. The license provided by PrestaShop Addons
 * for all its modules is valid only once for a single shop.
 */

include(dirname(__FILE__) . '/../../config/config.inc.php');
include(dirname(__FILE__) . '/attributewizardpro.php');
$awp = new AttributeWizardPro();

$awp_random = Tools::getValue('awp_random');
if ($awp_random != Configuration::get('AWP_RANDOM')) {
    die('No Access');
}

$result = Db::getInstance()->ExecuteS('SELECT * FROM `' . _DB_PREFIX_ . 'awp_attribute_wizard_pro`');
$result = $result[0]['awp_attributes'];

$attributes = unserialize($result);
$nattributes = array();

$id_group = Tools::getValue('id_group');
if ($id_group == 'row') {
    $attribute_group_order = 1;
} else {
    $attribute_value_order = Tools::getValue('attribute_value_order');
}
if ($attribute_group_order == 1)
{
    $idsInOrder = Tools::getValue('idsInOrder', '');
    $idsInOrder = explode(",", $idsInOrder);

    $current_page = Tools::getValue('page', 1);
    $items_per_page = Tools::getValue('groupsPerPage', (int)Configuration::get('AWP_GROUPS_COUNT'));

    if($current_page < 1) {
        $current_page = 1;
    }

    if ($items_per_page == 0) {
        $items_per_page = count($attributes);
    }

    $offset = ($current_page - 1) * $items_per_page;

    $head = array_slice($attributes, 0, $offset);
    $target = array_slice($attributes, $offset, $items_per_page);
    $tail = array_slice($attributes, $offset + $items_per_page);

    foreach ($head as $item) {
        $nattributes[] = $item;
    }

    foreach ($idsInOrder as $id) {
        $group = $awp->isInGroup($id, $target);
        $nattributes[] = $target[$group];
    }

    foreach ($tail as $item) {
        $nattributes[] = $item;
    }

    if (count($nattributes) == count($attributes)) {
        Db::getInstance()->Execute('UPDATE `' . _DB_PREFIX_ . 'awp_attribute_wizard_pro` SET awp_attributes = "' . pSQL((serialize($nattributes))) . '"');
    }
} else if ($attribute_value_order == 1) {
    $order = 0;
    $group = Tools::getValue('id_group');
    $idsInOrder = Tools::getValue('idsInOrder');
    $idsInOrder = explode(",", $idsInOrder);
    foreach ($idsInOrder as $ids) {
        if ($ids != "") {
            $id_value = $ids;
            $group = $awp->isInGroup($id_group, $attributes);
            $attr = $awp->isInAttribute($id_value, $attributes[$group]["attributes"]);
            $nattributes[$order] = $attributes[$group]["attributes"][$attr];
            $order++;
        }
    }
    $attributes[$group]['attributes'] = $nattributes;
    Db::getInstance()->Execute('UPDATE `' . _DB_PREFIX_ . 'awp_attribute_wizard_pro` SET awp_attributes = "' . pSQL((serialize($attributes))) . '"');
}
