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
function array2object($array)
{
    if (is_array($array)) {
        $obj = new StdClass();

        foreach ($array as $key => $val) {
            $obj->$key = $val;
        }
    } else {
        $obj = $array;
    }

    return $obj;
}

function add2cart($idProduct, $idProductAttribute, $qty, $ins, $ins_id)
{
    global $awp;
    $psv = (float) (Tools::substr(_PS_VERSION_, 0, 3));

    $errors = "";

    $add = true;
    $customizationId = 0;
    $instructions = $ins != "undefined" ? Tools::stripslashes($ins) : "";
    $instructions_id = $ins_id != "undefined" ? $ins_id : "";
    if ($qty == 0) {
        $errors = Tools::displayError('null quantity');
    } elseif (!$idProduct) {
        $errors = Tools::displayError('product not found');
    } else {
        $mode = 'add';
        $context = Context::getContext();
        $product = new Product((int) $idProduct, true, (int) $context->language->id);
        if (!$product->id || !$product->active) {
            $errors = Tools::displayError('Product is no longer available.', false);
            return;
        }
        if (!Product::isAvailableWhenOutOfStock($product->out_of_stock) && !$awp->checkCartQuantity($idProduct, $qty, $ins_id)) {
            return $awp->l('You already have the maximum quantity for this product in the cart (' . Product::isAvailableWhenOutOfStock($product->out_of_stock) . ' - ' . $awp->checkCartQuantity($idProduct, $qty, $ins_id) . ')');
        }
        // If no errors, process product addition
        if (!$errors && $mode == 'add') {
            // Add cart if no cart found
            if (!$context->cart->id) {
                $context->cart->add();
                if ($context->cart->id) {
                    $context->cookie->id_cart = (int) $context->cart->id;
                }
            }
            if (!$errors) {
                $cart_rules = $context->cart->getCartRules();
                $update_quantity = $context->cart->updateQty($qty, (int) $idProduct, (int) $idProductAttribute, 0, 'up', (int) Tools::getValue('id_address_delivery'), null, true, false, true, $instructions, $instructions_id);
                if ($update_quantity < 0) {
                    // If product has attribute, minimal quantity is set with minimal quantity of attribute
                    $minimal_quantity = ($idProductAttribute) ? Attribute::getAttributeMinimalQty($idProductAttribute) : $product->minimal_quantity;
                    $errors = sprintf(Tools::displayError('You must add %d minimum quantity', false), $minimal_quantity);
                } elseif (!$update_quantity) {
                    $errors = Tools::displayError('You already have the maximum quantity available for this product..', false);
                } elseif ((int) Tools::getValue('allow_refresh')) {
                    // If the cart rules has changed, we need to refresh the whole cart
                    $cart_rules2 = $context->cart->getCartRules();
                    if (count($cart_rules2) != count($cart_rules)) {
                        $this->ajax_refresh = true;
                    } else {
                        $rule_list = array();
                        foreach ($cart_rules2 as $rule) {
                            $rule_list[] = $rule['id_cart_rule'];
                        }
                        foreach ($cart_rules as $rule) {
                            if (!in_array($rule['id_cart_rule'], $rule_list)) {
                                $this->ajax_refresh = true;
                                break;
                            }
                        }
                    }
                }
            }
        }
    }

    return $errors;
}

function getUpdateQuantity($ids, $quantity, &$attribute_impact)
{
    $quantity_left = "";
    $tmp_ids = explode(",", Tools::substr($ids, 1));
    $first = true;
    foreach ($tmp_ids as $id) {
        if ($first) {
            $quantity_left = $attribute_impact[$id]['quantity'];
        } else {
            $quantity_left = min($quantity_left, $attribute_impact[$id]['quantity']);
        }
        $first = false;
    }
    foreach ($tmp_ids as $id) {
        $attribute_impact[$id]['quantity'] -= $quantity;
    }
    return $quantity_left;
}
//call module
require_once(dirname(__FILE__) . '/../../config/config.inc.php');
require_once(dirname(__FILE__) . '/../../init.php');
include(dirname(__FILE__) . '/attributewizardpro.php');
$awp = new AttributeWizardPro();

$psv = (float) Tools::substr(_PS_VERSION_, 0, 3);
$psv3 = (int) str_replace(".", "", Tools::substr(_PS_VERSION_, 0, 5) . (Tools::substr(_PS_VERSION_, 5, 1) != "." ? Tools::substr(_PS_VERSION_, 5, 1) : ""));

$result = Db::getInstance()->ExecuteS('SELECT * FROM `' . _DB_PREFIX_ . 'awp_attribute_wizard_pro`');
$result = $result[0]['awp_attributes'];

$id_product = (int) Tools::getValue('id_product');
$awp_is_quantity = Tools::getValue('awp_is_quantity');

// Get All the regular attributes (no default group)
$attributes = unserialize($result);
$attribute_impact = $awp->getAttributeImpact($id_product);
$attribute_impact_ignore = $awp->getAttributeImpactIgnore($id_product);


$cookie =  Context::getContext()->cookie;

$quantity_groups = explode(",", $awp_is_quantity);

$return = "";
$last_id_group = "";
$ids = "";
$price_impact = 0;
$weight_impact = 0;
$quantity_available = 0;
$minimal_quantity = 1;
$first = true;
$first_attribute = true;
$qty_to_add = array();
// Edit cart
if (Tools::getValue('awp_ins') != '') {
    Db::getInstance()->Execute('DELETE FROM `' . _DB_PREFIX_ . 'cart_product` '
        . 'WHERE id_product = ' . (int) Tools::getValue('id_product') . ' AND '
        . 'id_product_attribute = ' . (int) Tools::getValue('awp_ipa') . ' AND '
        . 'instructions_valid = "' . pSQL(Tools::getValue('awp_ins')) . '"');
}

/* Connected attributes */
$connected_ids = '';
$id_lang = (int) $cookie->id_lang;
$connectedAttributesGroups = array();
/* get all attributes */
$sqlConnectedAttributes = 'SELECT pa.*, product_attribute_shop.*, ag.`id_attribute_group`,
					ag.`is_color_group`, agl.`name` as group_name, al.`name` as attribute_name,
						a.`id_attribute`, pa.`unit_price_impact`, IFNULL(stock.quantity, 0) as quantity
					FROM `' . _DB_PREFIX_ . 'product_attribute` pa
					' . Shop::addSqlAssociation('product_attribute', 'pa') . '
					' . Product::sqlStock('pa', 'pa') . '
					LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac ON pac.`id_product_attribute` = pa.`id_product_attribute`
					LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a ON a.`id_attribute` = pac.`id_attribute`
					LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
					LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = ' . (int) $id_lang . ')
					LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = ' . (int) $id_lang . ')
					WHERE pa.`id_product` = ' . (int) Tools::getValue('id_product') . '
					GROUP BY pa.`id_product_attribute`, a.`id_attribute`
					ORDER BY pa.`id_product_attribute`';


$connectedAttributesSql = Db::getInstance()->ExecuteS($sqlConnectedAttributes);
//echo $sqlConnectedAttributes;
$connectedAttributesArray = array();
/* construct array with all attributes, groups & prices */
$defAttribute = 0;
foreach ($connectedAttributesSql as $row) {
    $connectedAttributesArray[$row['id_product_attribute']]['id_attribute_groups'][] = (int) $row['id_attribute_group'];
    $connectedAttributesArray[$row['id_product_attribute']]['attributes_values'][] = $row['attribute_name'];
    $connectedAttributesArray[$row['id_product_attribute']]['attributes'][] = (int) $row['id_attribute'];
    $connectedAttributesArray[$row['id_product_attribute']]['attributes_to_groups'][$row['id_attribute_group']][] = (int) $row['id_attribute'];
    $connectedAttributesArray[$row['id_product_attribute']]['price'] = (float) ($row['price']); //Tools::convertPriceFull($row['price'], null, Context::getContext()->currency);
    $connectedAttributesArray[$row['id_product_attribute']]['quantity'] = (int) $row['quantity'];
    $connectedAttributesArray[$row['id_product_attribute']]['weight'] = (float) $row['weight'];
    $connectedAttributesArray[$row['id_product_attribute']]['minimal_quantity'] = (int) $row['minimal_quantity'];
    $connectedAttributesArray[$row['id_product_attribute']]['reference'] = $row['reference'];

    if ($row['default_on']) {
        $defAttribute = $row['id_product_attribute'];
    }
}
/* Remove simple attributes - connected attributes must contain a fixed number of groups */
$notConnectedGroups = array();

$notConnectedAttributeValuesAll = array();
$connectedAttributeValuesAll = array();

$result = Db::getInstance()->getValue("SELECT id_attribute_group "
    . "FROM " . _DB_PREFIX_ . "attribute_group_lang "
    . "WHERE name = 'awp_details' "
    . "ORDER BY id_attribute_group DESC");

$awpDetailsIdGroup = $result;
$allConnected = true;


foreach ($connectedAttributesArray as $k => $row) {
    $row['id_attribute_groups'] = array_unique($row['id_attribute_groups']);

    if (count($row['id_attribute_groups']) == 1) {
        if ($row['id_attribute_groups'][0] != $awpDetailsIdGroup) {
            $notConnectedGroups[] = $row['id_attribute_groups'][0];
        }
        foreach ($row['attributes'] as $id_attribute) {
            if ($row['id_attribute_groups'][0] != $awpDetailsIdGroup) {
                $notConnectedAttributeValuesAll[] = $id_attribute;
            }
        }

        unset($connectedAttributesArray[$k]);

        if ($awpDetailsIdGroup != $row['id_attribute_groups'][0]) {
            $allConnected = false;
        }
    }
}

if (!$allConnected) {
    unset($connectedAttributesArray[$defAttribute]);
}

foreach ($connectedAttributesArray as $k => $row) {
    $row['id_attribute_groups'] = array_unique($row['id_attribute_groups']);

    if (count($row['id_attribute_groups']) > 1) {
        foreach ($row['id_attribute_groups'] as $groups) {
            $connectedAttributesGroups[] = $groups;
        }
    }
}

foreach ($connectedAttributesArray as $k => $row) {
    foreach ($row['attributes'] as $id_attribute) {
        $connectedAttributeValuesAll[] = $id_attribute;
    }
}

$notConnectedGroups = array_unique($notConnectedGroups);
$connectedAttributesGroups = array_unique($connectedAttributesGroups);

$notConnectedAttributeValuesAll = array_unique($notConnectedAttributeValuesAll);
$connectedAttributeValuesAll = array_unique($connectedAttributeValuesAll);

if (empty($notConnectedAttributeValuesAll)) {
    $bothConnectedAttributes = $connectedAttributeValuesAll;
} else {
    $bothConnectedAttributes = array_intersect($notConnectedAttributeValuesAll, $connectedAttributeValuesAll);
}

$bothConnectedAttributes = array_merge($bothConnectedAttributes, $connectedAttributeValuesAll);
$bothConnectedAttributes = array_unique($bothConnectedAttributes);

//$connectedAttributesGroups = array_diff($connectedAttributesGroups, $notConnectedGroups);
//$connectedAttributesGroups = array_diff($connectedAttributesGroups, $notConnectedGroups);
//print_r($notConnectedAttributeValuesAll);
// Remove from connected attribute group the groups which have a single value (old style)
//print_r($connectedAttributesArray);
$connectedSelectedAttr = array();
foreach ($_POST as $key => $val) {
    if (Tools::substr($key, 0, 6) == "group_") {
        $is_qty = false;
        $id_group = Tools::substr($key, 6);
        $id_group_arr = explode("_", $id_group);
        $id_group = (int) $id_group_arr[0];

        if (in_array($id_group, $quantity_groups)) {
            $is_qty = true;
        }
        $group = $awp->isInGroup($id_group, $attributes);
        $attr = $awp->isInAttribute($val, $attributes[$group]["attributes"]);
        if (sizeof($id_group_arr) == 1 || $attributes[$group]["group_type"] == "checkbox") {
            $id_attribute = (int) $val;
        } else {
            $id_attribute = (int) $id_group_arr[1];
        }
        if (in_array($id_attribute, $bothConnectedAttributes)) {
            $connectedSelectedAttr[] = $id_attribute;
        }
    }
}
//print_r($bothConnectedAttributes);
//print_r($connectedSelectedAttr);
//print_r($connectedAttributesGroups);
$connected = false;
if (count($connectedAttributesGroups) <= count($connectedSelectedAttr)) {
    $connected = true;
}

$connectedAttributeGroups = array();
//print_r($connected);
/* End - Connected attributes */
foreach ($_POST as $key => $val) {
    if (Tools::substr($key, 0, 6) == "group_") {
        $is_qty = false;
        $id_group = Tools::substr($key, 6);
        $id_group_arr = explode("_", $id_group);
        $id_group = (int) $id_group_arr[0];

        if (in_array($id_group, $quantity_groups)) {
            $is_qty = true;
        }
        $group = $awp->isInGroup($id_group, $attributes);
        $attr = $awp->isInAttribute($val, $attributes[$group]["attributes"]);
        if (sizeof($id_group_arr) == 1 || $attributes[$group]["group_type"] == "checkbox") {
            $id_attribute = (int) $val;
        } else {
            $id_attribute = (int) $id_group_arr[1];
        }

        if ($attributes[$group]["group_type"] == "calculation") {
            /* Connected Attributes */
            if ($connected && in_array($id_attribute, $connectedSelectedAttr) && in_array($id_attribute, $bothConnectedAttributes)) {
                $cur_price_impact = 0;
                $cur_weight_impact = 0;
                $connectedAttributeGroups[] = $id_group;
                /* END Connected attributes */
            } else {
                $cur_price_impact = $attribute_impact[$id_attribute]['price'] * $val * $awp->getFeatureVal($cookie->id_lang, $id_product, $attributes[$group]["group_calc_multiply"]) / 1000000;
                $cur_weight_impact = $attribute_impact[$id_attribute]['weight'];
            }
        } else {
            /* Connected Attributes */

            if ($connected && in_array($id_attribute, $connectedSelectedAttr) && in_array($id_attribute, $bothConnectedAttributes)) {
                $cur_price_impact = 0;
                $cur_weight_impact = 0;
                $connectedAttributeGroups[] = $id_group;
                /* END Connected attributes */
            } else {
                $cur_price_impact = $attribute_impact[$id_attribute]['price'];
                $cur_weight_impact = $attribute_impact[$id_attribute]['weight'];
            }
        }
        $cur_quantity_available = $attribute_impact_ignore[$id_attribute]['quantity'];

        // Quantity group
        if ($attributes[$group]["group_type"] == "quantity") {
            $attr = $awp->isInAttribute($id_group_arr[1], $attributes[$group]["attributes"]);
            /* Connected Attributes */
            if (in_array($id_attribute, $connectedSelectedAttr) && in_array($id_attribute, $bothConnectedAttributes)) {
                $connected_cur_ids = "," . $id_attribute;
            }
            /* END Connected attributes */
            $cur_ids = "," . $id_attribute;
            $cur_return = (!$first_attribute && $id_group != $last_id_group ? "<br />" : "") . "<b>" . ($psv >= 1.5 ? Db::getInstance()->_escape($attribute_impact_ignore[$id_attribute]["group"]) : mysql_real_escape_string($attribute_impact_ignore[$id_attribute]["group"])) . ":</b> " . ($psv >= 1.5 ? Db::getInstance()->_escape($attribute_impact_ignore[$id_attribute]["attribute"]) : mysql_real_escape_string($attribute_impact_ignore[$id_attribute]["attribute"]));
            $last_id_group = $id_group;
        } elseif (isset($id_group_arr[1]) && $attributes[$group]["group_type"] != "checkbox") {
            // Text or Image group
            /* Connected Attributes */
            if (in_array($id_attribute, $connectedSelectedAttr) && in_array($id_attribute, $bothConnectedAttributes)) {
                $connected_cur_ids = "," . $id_group_arr[1];
            }
            /* END Connected attributes */
            $cur_ids = "," . $id_group_arr[1];
            $attr = $awp->isInAttribute($id_group_arr[1], $attributes[$group]["attributes"]);
            $cur_return = (!$first_attribute && $id_group != $last_id_group ? "<br />" : "");
            if ($id_group != $last_id_group) {
                $cur_return .= "<b>" . ($psv >= 1.5 ? Db::getInstance()->_escape($attribute_impact_ignore[$id_attribute]["attribute"]) : mysql_real_escape_string($attribute_impact_ignore[$id_attribute]["attribute"]));
                $cur_return .= ":</b> ";
            } else {
                $cur_return .= ", ";
            }
            $val_arr = explode("%7C%7C%7C", $val, 2);
            if (sizeof($val_arr) == 2 && Tools::strtolower(Tools::substr($val_arr[0], strrpos($val_arr[0], "."))) == Tools::strtolower(Tools::substr($val_arr[1], strrpos($val_arr[1], ".")))) {
                $type = Tools::substr($val_arr[0], strrpos($val_arr[0], ".") + 1);
                $thumb = Tools::substr($val_arr[0], 0, strrpos($val_arr[0], "."));
                $full_url = (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://') . htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8') . __PS_BASE_URI__;
                if (file_exists(dirname(__FILE__) . '/file_uploads/' . urldecode($thumb) . '_small.jpg') && ($type == "jpg" || $type == "jpeg" || $type == "gif" || $type == "png")) {
                    $cur_return .= '<span class=awp_mark_' . $id_group_arr[1] . '><a href=' . $full_url . 'modules/attributewizardpro/file_uploads/' . urlencode($val_arr[0]) . ' target=_blank><img src=' . $full_url . 'modules/attributewizardpro/file_uploads/' . urlencode($thumb) . '_small.jpg /></a></span class=awp_mark_' . $id_group_arr[1] . ' val="' . $val . '">';
                } else {
                    $cur_return .= '<span class=awp_mark_' . $id_group_arr[1] . '><a href=' . $full_url . 'modules/attributewizardpro/file_uploads/' . urlencode($val_arr[0]) . ' target=_blank>' . $val_arr[1] . '</a></span class=awp_mark_' . $id_group_arr[1] . '>';
                }
            } else {
                $cur_return .= '<span class=awp_mark_' . $id_group_arr[1] . '>' . str_replace("\r", "", str_replace("\n", "", nl2br(htmlspecialchars(urldecode($val))))) . '</span class=awp_mark_' . $id_group_arr[1] . '>';
                //$cur_return .= nl2br(str_replace("#","%23",str_replace("&","%26",htmlspecialchars(Tools::stripslashes($val),ENT_QUOTES))));
            }
            // Exclude attribute text if group type is hidden //
            if ($attributes[$group]["group_type"] == "hidden") {
                $cur_return = '';
            }
            $last_id_group = 0;
        } else {
            // All other "simple" attributes
            /* Connected Attributes */
            if (in_array($id_attribute, $connectedSelectedAttr) && in_array($id_attribute, $bothConnectedAttributes)) {
                $connected_cur_ids = "," . $val;
            }
            //echo 'connected_cur_ids='.$connected_cur_ids.'<br/>';
            /* END Connected attributes */
            $cur_ids = "," . $val;
            $cur_return = (!$first_attribute && $id_group != $last_id_group ? "<br />" : "") . ($id_group != $last_id_group ? "<b>" . ($psv >= 1.5 ? Db::getInstance()->_escape($attribute_impact_ignore[$id_attribute]["group"]) : mysql_real_escape_string($attribute_impact_ignore[$id_attribute]["group"])) . ":</b> " : ", ") . ($psv >= 1.5 ? Db::getInstance()->_escape($attribute_impact_ignore[$id_attribute]["attribute"]) : mysql_real_escape_string($attribute_impact_ignore[$id_attribute]["attribute"]));
            $last_id_group = $id_group;
        }
        if (!$is_qty) {
            $return .= $cur_return;
            /* Connected Attributes */
            if (in_array($id_attribute, $connectedSelectedAttr) && in_array($id_attribute, $bothConnectedAttributes)) {
                $connected_ids .= $connected_cur_ids;
            }
            /* END Connected attributes */
            $ids .= $cur_ids;

            if ($attributes[$group]["group_type"] == 'textbox' || $attributes[$group]["group_type"] == 'textarea') {                
                //print_r($attributes[$group]);
                $minLimitCharge = $attributes[$group]['group_min_limit'];
                $chargePerCharacter = $attributes[$group]['price_impact_per_char'];
                $exceptions = $attributes[$group]['exceptions'];
                if (isset($chargePerCharacter) && $chargePerCharacter == 1){
                    // charge price per character
                    if (!isset($minLimitCharge) || $minLimitCharge == 0 || $minLimitCharge < 0) {
                        $minLimitCharge = 1;
                    }
                    $exceptionsArr = str_split($exceptions);
                    $valDecoded = urldecode($val);
                    $valWithoutExceptions = str_replace($exceptionsArr, "", $valDecoded);
                    
                    $charsValCount = strlen($valWithoutExceptions);
                    $priceImpactPerChar = 0;
                    if ($minLimitCharge < $charsValCount) {
                        $priceImpactPerChar = $charsValCount * $cur_price_impact;
                    } else {
                        $priceImpactPerChar = $minLimitCharge * $cur_price_impact;
                    }
                    //echo ' $valWithoutExceptions ' . $valWithoutExceptions;
                    //echo '$priceImpactPerChar' . $priceImpactPerChar;
                    $cur_price_impact = $priceImpactPerChar;
                }
                
                
            }
            
            $price_impact += $cur_price_impact;
            $weight_impact += $cur_weight_impact;
            $minimal_quantity = max($attribute_impact_ignore[$id_attribute]['minimal_quantity'], $minimal_quantity);
            if ($first) {
                $quantity_available = $cur_quantity_available;
                $first = false;
            } else {
                $quantity_available = min($quantity_available, $cur_quantity_available);
            }
        } else {
            // Need to still update the minimal quantity for quantity attribute type
            $minimal_quantity = max($attribute_impact[$id_attribute]['minimal_quantity'], $minimal_quantity);
        }

        if ($is_qty) {
            $qty_to_add[$id_attribute]["price"] = $price_impact + $cur_price_impact;
            $qty_to_add[$id_attribute]["weight"] = $weight_impact + $cur_weight_impact;
            $qty_to_add[$id_attribute]["quantity"] = $val;
            $qty_to_add[$id_attribute]["quantity_available"] = $cur_quantity_available;
            $qty_to_add[$id_attribute]["minimal_quantity"] = $minimal_quantity;
            $qty_to_add[$id_attribute]["ids"] = $ids . $cur_ids;

            /* Connected Attributes */
            $qty_to_add[$id_attribute]["connected_ids"] .= $connected_cur_ids;
            /* End - Connected Attributes */
            $qty_to_add[$id_attribute]["cart"] = $return . $cur_return;
        } else if (sizeof($qty_to_add) > 0) {
            foreach ($qty_to_add as $key => $product) {
                $qty_to_add[$key]["price"] += $cur_price_impact;
                $qty_to_add[$key]["weight"] += $cur_weight_impact;
                $qty_to_add[$key]["minimal_quantity"] = max($minimal_quantity, $qty_to_add[$id_attribute]["minimal_quantity"]);
                $qty_to_add[$key]["quantity_available"] = $cur_quantity_available; //min($cur_quantity_available,$qty_to_add[$id_attribute]["quantity_available"]);
                $qty_to_add[$key]["ids"] .= $cur_ids;
                /* Connected Attributes */
                $qty_to_add[$key]["connected_ids"] .= $connected_cur_ids;
                /* End - Connected Attributes */
                $qty_to_add[$key]["cart"] .= $cur_return;
            }
        }
        $first_attribute = false;
    }
}

/* Connected Attributes */
// Modify $Qty_to_add and add the proper price impact based on connected attributes
$new_qty_to_add = array();
foreach ($qty_to_add as $key => $product) {
    if (isset($product['connected_ids']) && !empty($product['connected_ids'])) {
        $connected_ids = explode(',', $product['connected_ids']);
        if ($connected_ids[0] == '') {
            unset($connected_ids[0]);
        }

        /* Get connected attribute based on selected connected ids */
        $containsSearch = false;
        $connectedAttributeAvailableQty = null;
        foreach ($connectedAttributesArray as $k => $connectedAttribute) {
            $containsSearch = count(array_intersect($connected_ids, $connectedAttribute['attributes'])) == count($connected_ids);
            if ($containsSearch) {
                $connectedAttributeAvailableQty = $connectedAttributesArray[$k];
            }
        }
        //echo 'connectedAttributeAvailableQty = ';
        //print_r($connectedAttributeAvailableQty);
        if (isset($connectedAttributeAvailableQty)) {
            $qty_to_add[$key]["price"] += $connectedAttributeAvailableQty['price'];
            $qty_to_add[$key]["weight"] += $connectedAttributeAvailableQty['weight'];
        }
    }

    $new_qty_to_add[$key] = $qty_to_add[$key];
}

$qty_to_add = $new_qty_to_add;

if (isset($connected_ids) && !empty($connected_ids)) {
    $connected_ids = explode(',', $connected_ids);
    if ($connected_ids[0] == '') {
        unset($connected_ids[0]);
    }
    /* Get connected attribute based on selected connected ids */

    //print_r($connected_ids);
    //print_r($price_impact);

    $containsSearch = false;
    $connectedAttributeAvailable = null;

    $connectedCombId = null;
    //print_r($connected_ids);
    foreach ($connectedAttributesArray as $k => $connectedAttribute) {
        $containsSearch = count(array_intersect($connected_ids, $connectedAttribute['attributes'])) == count($connected_ids);
        if ($containsSearch) {
            $connectedCombId = $k;
            $connectedAttributeAvailable = $connectedAttributesArray[$k];
        }
    }
    $connectedReference = null;

    if (isset($connectedCombId)) {
        $connectedReference = $connectedAttributesArray[$connectedCombId]['reference'];
        $quantity_available = $connectedAttributesArray[$connectedCombId]['quantity']; //min($quantity_available, $cur_quantity_available);
    }

    //print_r($connectedAttributeAvailable);
    /* If connected attribute exists increase price and weight */
    if (isset($connectedAttributeAvailable)) {
        if (count($connected_ids) >= count($connectedAttributesGroups)) {
            $isPricePerChar = false;
            foreach ($connectedAttributeAvailable['attributes_to_groups'] as $id_group => $connectedAttribute) {
                //echo $id_group.' = '; 
                $group = $awp->isInGroup($id_group, $attributes);
                
                if ($attributes[$group]["group_type"] == 'textbox' || $attributes[$group]["group_type"] == 'textarea') {                
                    //print_r($attributes[$group]);
                    //$price_impact = 0;
                    foreach ($connectedAttribute as $id_attribute) {
                        $minLimitCharge = $attributes[$group]['group_min_limit'];
                        $chargePerCharacter = $attributes[$group]['price_impact_per_char'];
                        $exceptions = $attributes[$group]['exceptions'];
                        if (isset($chargePerCharacter) && $chargePerCharacter == 1){
                            // charge price per character
                            if (!isset($minLimitCharge) || $minLimitCharge == 0 || $minLimitCharge < 0) {
                                $minLimitCharge = 1;
                            }
                           // print_r($val);
                            $val = Tools::getValue('group_'.$id_group.'_'.$id_attribute);
                            $exceptionsArr = str_split($exceptions);
                            $valDecoded = urldecode($val);
                            $valWithoutExceptions = str_replace($exceptionsArr, "", $valDecoded);

                            $charsValCount = strlen($valWithoutExceptions);
                            $priceImpactPerChar = 0;
                            if (strlen($val) > 0) {
                                if ($minLimitCharge < $charsValCount) {
                                    //echo '$charsValCount ' . $charsValCount;
                                    $priceImpactPerChar = $charsValCount * $connectedAttributeAvailable['price'];
                                } else {
                                    //echo '$minLimitCharge ' . $minLimitCharge;
                                    // echo 'XXXXXx$charsValCount ' . $charsValCount;
                                    $priceImpactPerChar = $minLimitCharge * $connectedAttributeAvailable['price'];
                                }
                                $isPricePerChar = true;
                                $price_impact += $priceImpactPerChar;
                                //echo 'group_'.$id_group.'_'.$id_attribute . '$val ' . $val. ' $price_impact ' . $price_impact;
                            }
                        }
                    }
                    
                   // break;
                }
            }
            if (!$isPricePerChar) {
                $price_impact += $connectedAttributeAvailable['price'];
            }
            $weight_impact += $connectedAttributeAvailable['weight'];
        }
    } else {
        /* If there is no connected attribute display error and return */
        $errors = Tools::displayError('The product options are invalid. Please select other options.');
        $redirect = array("error" => $errors, "added" => "");


        print json_encode($redirect);
        return;
    }
}
/* End Connected attributes */


$producToAdd = new Product((int) $id_product, true, (int) $cookie->id_lang, (int) Context::getContext()->shop->id);

//exit;
// Add multiple products
if (sizeof($qty_to_add) > 0) {
    $i = 1;
    foreach ($qty_to_add as $product) {
        $id_image = 0;
        $query = 'SELECT pai.id_image  '
            . 'FROM ' . _DB_PREFIX_ . 'product_attribute_combination pac, '
            . _DB_PREFIX_ . 'product_attribute_image pai, '
            . _DB_PREFIX_ . 'product_attribute pa '
            . 'WHERE `id_attribute` IN (' . pSQL(Tools::substr($product['ids'], 1)) . ') AND '
            . 'pac.id_product_attribute = pa.id_product_attribute AND '
            . 'pac.id_product_attribute = pai.id_product_attribute AND '
            . 'pa.id_product = ' . (int) $id_product . ' '
            . 'ORDER BY pa.default_on ASC';

        $res = Db::getInstance()->ExecuteS($query);
        if (is_array($res) && sizeof($res) > 0) {
            $id_image = $res[0]['id_image'];
        }
       
        
        $query = "SELECT pcp.id_product_attribute as pcp_id , pa.* "
        . "FROM " . _DB_PREFIX_ . "product_attribute as pa, "
        . _DB_PREFIX_ . 'product_attribute_shop as pas,' . " "
        . _DB_PREFIX_ . "product_attribute_combination as pac, "
        . _DB_PREFIX_ . "attribute as a "
        . ($id_image > 0 ? ", " . _DB_PREFIX_ . "product_attribute_image as pai" : "") . " "
        . ", " . _DB_PREFIX_ . "cart_product pcp "
        . " WHERE  pcp.id_product = " . (int) $id_product . " AND "
        . " pcp.instructions_id like '" . pSQL($product['ids']) . "' AND  "
        . " pa.id_product = '" . (int) $id_product . "' AND "
        . " pas.price = '" . (float) $product["price"]
        . "' AND pas.weight = '" . (float) $product["weight"] . "'"
        . ' AND pa.minimal_quantity = "' . (int) $product["minimal_quantity"] . '" '
        . " AND a.id_attribute = pac.id_attribute AND"
        . " pac.id_product_attribute = pa.id_product_attribute "
        . (isset($connectedReference) ? " AND pa.reference = '" . pSQL($connectedReference) . "' " : '' )
        . ' AND pa.id_product_attribute = pas.id_product_attribute AND '
        . ' pas.id_shop = ' . (int) Context::getContext()->shop->id . ' '
        . " AND pa.quantity = " . ((int) ($quantity_available))
        . ($id_image > 0 ? " AND pac.id_product_attribute = pai.id_product_attribute "
            . " AND pai.id_image = " . (int) $id_image : "");
        $result = Db::getInstance()->ExecuteS($query);

        $nqty = getUpdateQuantity($product['ids'], $product['quantity'], $attribute_impact);
        $nqty = $product['quantity_available'];
        
        $allow_oos_p = Tools::getValue('allow_oos');
        if ((int) ($allow_oos_p) != 1 && $nqty <= 0) {
            continue;
        }

        $id_product_attribute = "";
        foreach ($result as $k => $row) {
            if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && $producToAdd->advanced_stock_management && $producToAdd->depends_on_stock) {
                if ($product['quantity'] <= $new_stock = StockAvailable::getQuantityAvailableByProduct($producToAdd->id, $row['id_product_attribute'], Context::getContext()->shop->id)) {
                    $id_product_attribute = $row['pcp_id'];
                }
            } else {
                $id_product_attribute = $row['pcp_id'];
            }
        }
        if ($id_product_attribute == "") {
            Db::getInstance()->Execute("INSERT INTO " . _DB_PREFIX_ . "product_attribute "
                . "(reference, "
                . "id_product, "
                . "price, "
                . "weight, "
                . "quantity"
                . ",minimal_quantity"
                . ",available_date)
				VALUES ('" . (isset($connectedReference) ? $connectedReference : '' )
                . "' , '" . (int) $id_product
                . "','" . (float) $product["price"]
                . "','" . (float) $product["weight"]
                . "','" . (int) $nqty . "'"
                . ",'" . (int) $product["minimal_quantity"]
                . "','0000-00-00')");
            $id_product_attribute = Db::getInstance()->Insert_ID();
            Db::getInstance()->Execute("INSERT INTO " . _DB_PREFIX_ . "product_attribute_combination "
                . "(id_attribute, "
                . "id_product_attribute) "
                . "VALUES ("
                . (int) $awp->awp_default_item . " ,"
                . (int) $id_product_attribute . " )");

            //if (isset($connectedCombId))
            if (isset($connectedCombId) && isset($connected_ids) && !empty($connected_ids)) {
                $res = Db::getInstance()->ExecuteS('SELECT pai.id_image  '
                    . 'FROM ' . _DB_PREFIX_ . 'product_attribute_combination pac, '
                    . _DB_PREFIX_ . 'product_attribute_image pai, '
                    . _DB_PREFIX_ . 'product_attribute pa '
                    . 'WHERE `id_attribute` IN (' . pSQL(implode(',', $product['connected_ids'])) . ') '
                    . 'AND pac.id_product_attribute = pa.id_product_attribute AND '
                    . 'pac.id_product_attribute = pai.id_product_attribute AND '
                    . 'pai.id_product_attribute = ' . (int) $connectedCombId
                    . ' AND pa.id_product = ' . (int) $id_product
                    . ' ORDER BY pa.default_on ASC');
            } else {
                $res = Db::getInstance()->ExecuteS('SELECT pai.id_image  '
                    . 'FROM ' . _DB_PREFIX_ . 'product_attribute_combination pac, '
                    . _DB_PREFIX_ . 'product_attribute_image pai, '
                    . _DB_PREFIX_ . 'product_attribute pa '
                    . 'WHERE `id_attribute` IN (' . pSQL(Tools::substr($product['ids'], 1)) . ') AND '
                    . 'pac.id_product_attribute = pa.id_product_attribute AND '
                    . 'pac.id_product_attribute = pai.id_product_attribute AND '
                    . 'pa.id_product = ' . (int) $id_product
                    . ' ORDER BY pa.default_on ASC');
            }
            //$res = Db::getInstance()->ExecuteS('SELECT pai.id_image  FROM '._DB_PREFIX_.'product_attribute_combination pac, '._DB_PREFIX_.'product_attribute_image pai, '._DB_PREFIX_.'product_attribute pa WHERE `id_attribute` IN ('.Tools::substr($product['ids'],1).') AND pac.id_product_attribute = pa.id_product_attribute AND pac.id_product_attribute = pai.id_product_attribute AND pa.id_product = '.$_POST['id_product'].' ORDER BY pa.default_on ASC');
            if (is_array($res) && sizeof($res) > 0) {
                Db::getInstance()->Execute("INSERT INTO " . _DB_PREFIX_ . "product_attribute_image "
                    . "(id_product_attribute, id_image) "
                    . "VALUES ("
                    . (int) $id_product_attribute . " ,"
                    . (int) $res[0]['id_image'] . " )");
            }
            Db::getInstance()->Execute('INSERT INTO `' . _DB_PREFIX_ . 'product_attribute_shop` (
                `id_product`,
                `id_product_attribute`,
                `id_shop`,
                `wholesale_price`,
                `price`,
                `ecotax`,
                `weight`,
                `unit_price_impact`,
                `default_on`,
                `minimal_quantity`,
                `available_date`)
                VALUES ('
                . (int) $id_product . ','
                . (int) $id_product_attribute . ','
                . (int) Context::getContext()->shop->id . ',
                "0", "'
                . (float) $product["price"] . '",
                "0","'
                . (float) $product["weight"] . '",
                "0",'
                . 'NULL' . ',"'
                . (int) $product["minimal_quantity"]
                . '",  "0000-00-00"	)');

            $stock = (int) $nqty < 0 ? 0 : (int) $nqty;
            $awp->addStock15($producToAdd, $id_product_attribute, Context::getContext()->shop->id, $stock);

            $sql = 'SELECT * '
                . 'FROM `' . _DB_PREFIX_ . 'warehouse_product_location` '
                . 'WHERE id_product = ' . (int) $id_product;

            $warehouses = Db::getInstance()->ExecuteS($sql);

            if (isset($warehouses) && is_array($warehouses) && isset($warehouses[0]['id_warehouse'])) {
                $warehouse_location_entity = new WarehouseProductLocation();
                $warehouse_location_entity->id_product = (int) $id_product;
                $warehouse_location_entity->id_product_attribute = (int) $id_product_attribute;
                $warehouse_location_entity->id_warehouse = (int) $warehouses[0]['id_warehouse'];
                $warehouse_location_entity->location = pSQL('');
                $warehouse_location_entity->save();
            }
        } else {
            Db::getInstance()->Execute("UPDATE " . _DB_PREFIX_ . "product_attribute "
                . "SET quantity = " . (int) $nqty
                . " WHERE id_product_attribute = " . (int) $id_product_attribute);

            $awp->removeStock15($producToAdd, $id_product_attribute, Context::getContext()->shop->id, $nqty);
        }
        $errors = add2cart((int) $id_product, (int) $id_product_attribute, (int) $product["quantity"], urldecode($product["cart"]), $product["ids"]);
        $return = $product["cart"];
        $i++;
    }
    $redirect = array("error" => $errors, "added" => md5($return), 'id_product_attribute' => $id_product_attribute);
} else {
    // Single product to add
    if ($first) {
        $quantity_available = $producToAdd->quantity;
        if ($quantity_available == 0) {
            $quantity_available = (int) Tools::getValue('quantity');
        }
    }

    $id_image = 0;

    $query = 'SELECT pai.id_image  '
        . 'FROM ' . _DB_PREFIX_ . 'product_attribute_combination pac, '
        . _DB_PREFIX_ . 'product_attribute_image pai, '
        . _DB_PREFIX_ . 'product_attribute pa '
        . 'WHERE `id_attribute` IN (' . pSQL(Tools::substr($ids, 1)) . ') AND '
        . 'pac.id_product_attribute = pa.id_product_attribute AND '
        . 'pac.id_product_attribute = pai.id_product_attribute AND '
        . 'pa.id_product = ' . (int) $id_product
        . ' ORDER BY pa.default_on ASC';

    $res = Db::getInstance()->ExecuteS($query);
    if (is_array($res) && sizeof($res) > 0) {
        $id_image = $res[0]['id_image'];
    }

    $notConnectedGroups = array_filter($notConnectedGroups);

    $query = "SELECT pcp.id_product_attribute as pcp_id , pa.* "
        . "FROM " . _DB_PREFIX_ . "product_attribute as pa, "
        . _DB_PREFIX_ . 'product_attribute_shop as pas,' . " "
        . _DB_PREFIX_ . "product_attribute_combination as pac, "
        . _DB_PREFIX_ . "attribute as a "
        . ($id_image > 0 ? ", " . _DB_PREFIX_ . "product_attribute_image as pai" : "") . " "
        . ", " . _DB_PREFIX_ . "cart_product pcp "
        . " WHERE  pcp.id_product = " . (int) $id_product . " AND "
        . " pcp.instructions_id like '" . pSQL($ids) . "' AND  "
        . " pa.id_product = '" . (int) $id_product . "' AND "
        . " pas.price = '" . (float) $price_impact
        . "' AND pas.weight = '" . (float) $weight_impact . "'"
        . ' AND pa.minimal_quantity = "' . (int) $minimal_quantity . '" '
        . " AND a.id_attribute = pac.id_attribute AND"
        . " pac.id_product_attribute = pa.id_product_attribute "
        . (isset($connectedReference) ? " AND pa.reference = '" . pSQL($connectedReference) . "' " : '' )
        . ' AND pa.id_product_attribute = pas.id_product_attribute AND '
        . ' pas.id_shop = ' . (int) Context::getContext()->shop->id . ' '
        . " AND pa.quantity = " . ((int) ($quantity_available))
        . ($id_image > 0 ? " AND pac.id_product_attribute = pai.id_product_attribute "
            . " AND pai.id_image = " . (int) $id_image : "");
    $result = Db::getInstance()->ExecuteS($query);

    $singleAttributeGroup = false;
    if (empty($connectedAttributesGroups) && sizeof($notConnectedGroups) == 1) {
        $singleAttributeGroup = true;
    }
    if ($singleAttributeGroup) {
        $queryA = 'SELECT pa.reference  '
            . 'FROM ' . _DB_PREFIX_ . 'product_attribute_combination pac, '
            . _DB_PREFIX_ . 'product_attribute pa '
            . 'WHERE `id_attribute` IN (' . pSQL(Tools::substr($ids, 1)) . ') AND '
            . 'pac.id_product_attribute = pa.id_product_attribute AND '
            . 'pa.id_product = ' . (int) $id_product
            . ' ORDER BY pa.default_on ASC';
        $resA = Db::getInstance()->getRow($queryA);

        $connectedReference = $resA['reference'];
    }

    $id_product_attribute = "";    
    foreach ($result as $k => $row) {
        if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && $producToAdd->advanced_stock_management && $producToAdd->depends_on_stock) {
            if ($product['quantity'] <= $new_stock = StockAvailable::getQuantityAvailableByProduct($producToAdd->id, $row['id_product_attribute'], Context::getContext()->shop->id)) {
                $id_product_attribute = $row['pcp_id'];
            }
        } else {
            $id_product_attribute = $row['pcp_id'];
        }
    }

    if (isset($id_product_attribute) && $id_product_attribute > 0) {
        $checkProductAttribute = 'SELECT count(*) as cnt '
                . 'FROM ' . _DB_PREFIX_ . 'product_attribute pa '
                . 'WHERE pa.id_product_attribute = ' . (int) $id_product_attribute;
        $resPA = Db::getInstance()->getRow($checkProductAttribute);
        
        if (isset($resPA) && $resPA['cnt'] > 0) {
            
        } else {
            $id_product_attribute = '';
        }
    }
    if ($id_product_attribute == "") {
        Db::getInstance()->Execute("INSERT INTO " . _DB_PREFIX_ . "product_attribute "
            . "(reference,"
            . " id_product,"
            . " price,"
            . " weight,"
            . " quantity, "
            . " minimal_quantity, "
            . "available_date)
		 VALUES ('" . (isset($connectedReference) ? $connectedReference : '' )
            . "' ,'" . (int) $id_product
            . "','" . (float) $price_impact
            . "','" . (float) $weight_impact
            . "','" . (int) $quantity_available
            . "','" . (int) $minimal_quantity
            . "','0000-00-00')");

        $id_product_attribute = Db::getInstance()->Insert_ID();
        Db::getInstance()->Execute("INSERT INTO " . _DB_PREFIX_ . "product_attribute_combination "
            . "(id_attribute, id_product_attribute) "
            . "VALUES ("
            . (int) $awp->awp_default_item . " ,"
            . (int) $id_product_attribute . " )");

        //if (isset($connectedCombId))
        if (isset($connectedCombId) && isset($connected_ids) && !empty($connected_ids)) {
            $res = Db::getInstance()->ExecuteS('SELECT pai.id_image  '
                . 'FROM ' . _DB_PREFIX_ . 'product_attribute_combination pac, '
                . _DB_PREFIX_ . 'product_attribute_image pai, '
                . _DB_PREFIX_ . 'product_attribute pa '
                . 'WHERE `id_attribute` IN (' . pSQL(implode(',', $connected_ids)) . ') AND '
                . 'pac.id_product_attribute = pa.id_product_attribute AND '
                . 'pac.id_product_attribute = pai.id_product_attribute AND '
                . 'pai.id_product_attribute = ' . (int) $connectedCombId . ' AND '
                . 'pa.id_product = ' . (int) $id_product
                . ' ORDER BY pa.default_on ASC');
        } else {
            $res = Db::getInstance()->ExecuteS('SELECT pai.id_image  '
                . 'FROM ' . _DB_PREFIX_ . 'product_attribute_combination pac, '
                . _DB_PREFIX_ . 'product_attribute_image pai, '
                . _DB_PREFIX_ . 'product_attribute pa '
                . 'WHERE `id_attribute` IN (' . pSQL(Tools::substr($ids, 1)) . ') AND '
                . 'pac.id_product_attribute = pa.id_product_attribute AND '
                . 'pac.id_product_attribute = pai.id_product_attribute AND '
                . 'pa.id_product = ' . (int) $id_product
                . ' ORDER BY pa.default_on ASC');
        }

        if (is_array($res) && sizeof($res) > 0) {
            Db::getInstance()->Execute("INSERT INTO " . _DB_PREFIX_ . "product_attribute_image "
                . "(id_product_attribute, id_image) "
                . "VALUES ("
                . (int) $id_product_attribute . " ,"
                . (int) $res[0]['id_image'] . " )");
        }
        $query = 'INSERT INTO `' . _DB_PREFIX_ . 'product_attribute_shop` (
                `id_product`,
                `id_product_attribute`,
                `id_shop`,
				`wholesale_price`,
                `price`,
				`ecotax`,
                `weight`,
				`unit_price_impact`,
                `default_on`,
				`minimal_quantity`,
                `available_date`)
				VALUES ('
            . (int) $id_product . ','
            . (int) $id_product_attribute . ','
            . (int) Context::getContext()->shop->id
            . ',"0", "'
            . (float) $price_impact
            . '","0","'
            . (float) $weight_impact
            . '","0",'
            . 'NULL' . ',"'
            . (int) $minimal_quantity
            . '",  "0000-00-00"	)';
        Db::getInstance()->Execute($query);

        $stock = (int) $quantity_available < 0 ? 0 : (int) $quantity_available;
        $awp->addStock15($producToAdd, $id_product_attribute, Context::getContext()->shop->id, $stock);


        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'warehouse_product_location` '
            . 'WHERE id_product = ' . (int) $id_product;

        $warehouses = Db::getInstance()->ExecuteS($sql);
        if (isset($warehouses) && is_array($warehouses) && isset($warehouses[0]['id_warehouse'])) {
            $warehouse_location_entity = new WarehouseProductLocation();
            $warehouse_location_entity->id_product = (int) $id_product;
            $warehouse_location_entity->id_product_attribute = (int) $id_product_attribute;
            $warehouse_location_entity->id_warehouse = (int) $warehouses[0]['id_warehouse'];
            $warehouse_location_entity->location = pSQL('');
            $warehouse_location_entity->save();
        }
    } else {
        Db::getInstance()->Execute("UPDATE " . _DB_PREFIX_ . "product_attribute "
            . "SET quantity = " . (int) $quantity_available
            . " WHERE id_product_attribute = " . (int) $id_product_attribute);

        $stock = (int) $quantity_available < 0 ? 0 : (int) $quantity_available;
        $awp->removeStock15($producToAdd, $id_product_attribute, Context::getContext()->shop->id, $stock);
    }
    $errors = add2cart((int) $id_product, (int) $id_product_attribute, (int) Tools::getValue('quantity'), ($return), $ids);
    $redirect = array("error" => $errors, "added" => md5(Tools::stripslashes($return)), 'id_product_attribute' => $id_product_attribute);
}

print json_encode($redirect);

