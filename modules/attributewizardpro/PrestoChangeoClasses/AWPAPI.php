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

class AWPAPI extends Module
{
    protected $shop_group;

    public function __construct()
    {
        parent::__construct();
        $id_shop = (int) $this->context->shop->id;
        if ($id_shop === null) {
            $this->shop_group = Shop::getContextShopGroup();
        } else {
            $this->shop_group = new ShopGroup((int) Shop::getGroupFromShop((int) $id_shop));
        }
    }

    /*
     * Create temp awp_details attribute group and value
     * Used only at install
     */
    public function createTempAttributes()
    {
        $result = Db::getInstance()->getValue("SELECT id_attribute_group FROM " . _DB_PREFIX_ . "attribute_group_lang WHERE name = 'awp_details' ORDER BY id_attribute_group DESC");
        if ($result == '' || $result != $this->awp_default_group) {
            $defaultLanguage = Configuration::get('PS_LANG_DEFAULT');
            $obj = new AttributeGroup();
            $obj->is_color_group = false;
            $obj->name[$defaultLanguage] = "awp_details";
            $obj->public_name[$defaultLanguage] = "Details";

            $obj->group_type = 'select';
            $obj->add();

            Configuration::updateValue('AWP_DEFAULT_GROUP', $obj->id, false, null, null);

            $this->awp_default_group = $obj->id;

            $att = new Attribute();
            $att->id_attribute_group = $obj->id;
            $att->name[$defaultLanguage] = " ";
            $att->add();
            $id_attribute = $att->id;

            Configuration::updateValue('AWP_DEFAULT_ITEM', $id_attribute, false, null, null);

            $this->awp_default_item = $id_attribute;
        }
    }

    public function getDbAttributesCount() {
        $query = ' SELECT COUNT(ag.`id_attribute_group`) AS total
            FROM `' . _DB_PREFIX_ . 'attribute_group` ag
            WHERE ag.id_attribute_group != "' . (int) $this->awp_default_group . '"';

        return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }

    /*
     * Get all attribute groups and values
     */

    public function getDbOrderedAttributes($n = 0, $p = 1)
    {
        if ($this->context->language->id > 0 && $this->context->language->id != $this->context->cookie->id_lang)
          $this->context->cookie->id_lang = $this->context->language->id;
        $cookie = $this->context->cookie;
        $query = ' SELECT ag.`id_attribute_group`,
                ag.`group_type`,
                agl.`name` AS group_name,
                agl.`public_name` AS public_group_name,
                a.`id_attribute` ,
                al.`name` AS attribute_name,
                a.`color` AS attribute_color
			FROM `' . _DB_PREFIX_ . 'attribute_group` ag
			LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl ON ag.`id_attribute_group` = agl.`id_attribute_group`
			LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a ON a.`id_attribute_group` = ag.`id_attribute_group`
			LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON a.`id_attribute` = al.`id_attribute`
			WHERE ag.id_attribute_group != "' . (int) $this->awp_default_group . '" AND '
            . 'agl.`id_lang` = ' . (int) ($this->context->language->id) . ' AND '
            . 'al.`id_lang` = ' . (int) ($this->context->language->id)  . ''
            . ' ORDER BY public_group_name ASC , attribute_name ASC ';
        $result = Db::getInstance()->ExecuteS($query);


        $ordered_list = is_array($this->awp_attributes) ? $this->awp_attributes : array();
        $ordered_list_new = array();
        foreach ($result as $attribute) {
            if (array_key_exists($attribute["id_attribute_group"], $ordered_list_new) && is_array($ordered_list_new[$attribute["id_attribute_group"]])) {
                array_push($ordered_list_new[$attribute["id_attribute_group"]], $attribute['id_attribute']);
            } else {
                $ordered_list_new[$attribute["id_attribute_group"]] = array();
                array_push($ordered_list_new[$attribute["id_attribute_group"]], $attribute['id_attribute']);
            }
            $awp_order = $this->isInGroup($attribute["id_attribute_group"], $ordered_list);

            $filename = $this->getGroupImage($attribute["id_attribute_group"]);

            if ($awp_order >= 0) {
                if ($filename) {
                    $ordered_list[$awp_order]["filename"] = $filename;
                } else {
                    $ordered_list[$awp_order]["filename"] = '';
                }

                $ordered_list[$awp_order]["group_color"] = ($attribute['group_type'] == 'color' ? 1 : 0);
                if ($ordered_list[$awp_order]["group_name"] != $attribute['group_name'] || $ordered_list[$awp_order]["public_group_name"] != $attribute['public_group_name']) {
                    $ordered_list[$awp_order]["group_name"] = $attribute['group_name'];
                    if ($ordered_list[$awp_order]["group_type"] == "") {
                        $ordered_list[$awp_order]["group_type"] = "dropdown";
                    }
                    $ordered_list[$awp_order]["public_group_name"] = $attribute['public_group_name'];
                }
                $att_pos = $this->isInAttribute($attribute["id_attribute"], $ordered_list[$awp_order]["attributes"]);
                $awp_lid = $attribute['id_attribute'];
                $layered_filename = $this->getLayeredImage($awp_lid, false, $awp_order);

                if ($att_pos == -1) {
                    $ordered_list[$awp_order]["attributes"][sizeof($ordered_list[$awp_order]["attributes"])] = array(
                        "id_attribute" => (int) $attribute['id_attribute'],
                        "layered_filename" => $layered_filename,
                        "attribute_name" => $attribute['attribute_name'],
                        "attribute_color" => ($attribute['group_type'] == 'color' ? $attribute['attribute_color'] : ''));
                } else {
                    $ordered_list[$awp_order]["attributes"][$att_pos] = array(
                        "id_attribute" => (int) $attribute['id_attribute'],
                        "layered_filename" => $layered_filename,
                        "attribute_name" => $attribute['attribute_name'],
                        "image_upload_attr" => (isset($attribute['image_upload_attr']) ? $attribute['image_upload_attr'] : ''),
                        "attribute_color" => ($attribute['group_type'] == 'color' ? $attribute['attribute_color'] : '')
                        );
                }
            } else {
                $awp_lid = (int) $attribute['id_attribute'];
                $layered_filename = $this->getLayeredImage($awp_lid, false, sizeof($ordered_list));

                $ordered_list[sizeof($ordered_list)] = array(
                    "id_attribute_group" => (int) $attribute['id_attribute_group'],
                    "group_name" => $attribute['group_name'],
                    "filename" => ($filename ? $filename : ''),
                    "group_type" => "dropdown",
                    "public_group_name" => $attribute['public_group_name'],
                    "attributes" =>
                    array("0" =>
                        array(
                            "id_attribute" => (int) $attribute['id_attribute'],
                            "layered_filename" => $layered_filename,
                            "attribute_name" => $attribute['attribute_name'],
                            "attribute_color" => $attribute['attribute_color'],
                            "image_upload_attr" => (isset($attribute['image_upload_attr']) ? $attribute['image_upload_attr'] : '')                           
                        )
                    )
                );
            }
        }

        $rg = 0;
        foreach ($ordered_list as $key => $group) {
            if (!isset($group['id_attribute_group']) || !isset($ordered_list_new[$group['id_attribute_group']])) {
                unset($ordered_list[$key]);
                $rg++;
            } else {
                if ($rg > 0) {
                    $ordered_list[$key - $rg] = $ordered_list[$key];
                    unset($ordered_list[$key]);
                }
                $ri = 0;
                foreach ($group['attributes'] as $akey => $attribute) {
                    if (!in_array($attribute['id_attribute'], $ordered_list_new[$group['id_attribute_group']])) {
                        unset($ordered_list[$key]['attributes'][$akey]);
                        $ri++;
                    } else if ($ri > 0) {
                        $ordered_list[$key]['attributes'][$akey - $ri] = $ordered_list[$key]['attributes'][$akey];
                        unset($ordered_list[$key]['attributes'][$akey]);
                    }
                }
            }
        }

        Db::getInstance()->Execute('UPDATE `' . _DB_PREFIX_ . 'awp_attribute_wizard_pro` SET awp_attributes = "' . addslashes(serialize($ordered_list)) . '"');

        $n = (int)$n;

        if($n) {
            if($p <= 0){
                $p = 1;
            }

            $offset = ($p - 1) * $n;

            if($offset >= count($ordered_list)){
                return $ordered_list;
            }

            return array_slice($ordered_list, $offset, $n);
        } else {
            return $ordered_list;
        }
    }

    /*
     * Check if $id_ag (id_attribute_group) is in groups
     */
    public function isInGroup($id_ag, $groups)
    {
        if(is_array($groups)) {
            foreach ($groups as $order => $ag) {
                if ($ag['id_attribute_group'] == $id_ag) {
                    return $order;
                }
            }
        }
        return -1;
    }

    /*
     * Checks if $id_a (id_attribute) is in attributes
     */
    public function isInAttribute($id_a, $attributes)
    {
        foreach ($attributes as $order => $a) {
            if (isset($a['id_attribute']) && $a['id_attribute'] == $id_a) {
                return $order;
            }
        }
        return -1;
    }

    public function getAttributeValue($id, $attributes)
    {
        $val_arr = explode('<span class=awp_mark_' . $id . '>', $attributes);
        if (sizeof($val_arr) == 2) {
            $val_arr = explode('</span class=awp_mark_' . $id . '', $val_arr[1]);
            return str_replace("<br />", "\n", $val_arr[0]);
        }
        return false;
    }

    public function getAttributeFileValue($id, $attributes)
    {
        $val_arr = explode('<span class=awp_mark_' . $id . '>', $attributes);
        if (sizeof($val_arr) == 2) {
            $val_arr = explode('</span class=awp_mark_' . $id . '', $val_arr[1]);
            $val_arr = explode('"', $val_arr[1]);
            return isset($val_arr[1]) ? $val_arr[1] : false;
        }
        return false;
    }

    public function getLayeredImage($id_attribute, $filename = false, $pos)
    {
        if (!isset($this->awp_attributes[$pos])) {
            return;
        }

        $id = $this->isInAttribute($id_attribute, $this->awp_attributes[$pos]['attributes']);

        // todo check why it could be = null
        $temp_array_for_v = [];
        if(!is_null($this->awp_attributes[$pos]['attributes'][$id])) {
            $temp_array_for_v = $this->awp_attributes[$pos]['attributes'][$id];
        }

        $v = array_key_exists("image_upload_attr", $temp_array_for_v) ? $this->awp_attributes[$pos]['attributes'][$id]['image_upload_attr'] : "";
        $dir = ($filename ? '' : _MODULE_DIR_ . 'attributewizardpro/views/img/');

        if (is_file(dirname(__FILE__) . '/../views/img/id_attribute_' . $id_attribute . '.gif')) {
            return $dir . 'id_attribute_' . $id_attribute . '.gif' . ($filename ? '' : '?v=' . $v);
        } else if (is_file(dirname(__FILE__) . '/../views/img/id_attribute_' . $id_attribute . '.png')) {
            return $dir . 'id_attribute_' . $id_attribute . '.png' . ($filename ? '' : '?v=' . $v);
        } else if (is_file(dirname(__FILE__) . '/../views/img/id_attribute_' . $id_attribute . '.jpg')) {
            return $dir . 'id_attribute_' . $id_attribute . '.jpg' . ($filename ? '' : '?v=' . $v);
        } else if (is_file(dirname(__FILE__) . '/../views/img/id_attribute_' . $id_attribute . '.jpeg')) {
            return $dir . 'id_attribute_' . $id_attribute . '.jpeg' . ($filename ? '' : '?v=' . $v);
        } else {
            return false;
        }
    }

    /*
     * Check if domain matches with the one from SEO tab
     * *** Bug - in some cases it matches, but it still displays the error - RECHECK
     * returns - simple string with error message
     */
    public function getWarningDomainName()
    {
        $warning = false;

        $shop = Context::getContext()->shop;
        if ($_SERVER['HTTP_HOST'] != $shop->domain && $_SERVER['HTTP_HOST'] != $shop->domain_ssl) {
            $warning = $this->l('You are currently connected under the following domain name:') . ' <span style="color: #CC0000;">' . $_SERVER['HTTP_HOST'] . '</span><br />';
            if (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE')) {
                $warning .= sprintf($this->l('This is different from the shop domain name set in the Multistore settings: "%s".'), $shop->domain) . '
				' . preg_replace('@{link}(.*){/link}@', '<a href="index.php?controller=AdminShopUrl&id_shop_url=' . (int) $shop->id . '&updateshop_url&token=' . Tools::getAdminTokenLite('AdminShopUrl') . '">$1</a>', $this->l('If this is your main domain, please {link}change it now{/link} to be able to configure the module.'));
            } else {
                $warning .= $this->l('This is different from the domain name set in the "SEO & URLs" tab.') . '
				' . preg_replace('@{link}(.*){/link}@', '<a href="index.php?controller=AdminMeta&token=' . Tools::getAdminTokenLite('AdminMeta') . '#meta_fieldset_shop_url">$1</a>', $this->l('If this is your main domain, please {link}change it now{/link} to be able to configure the module.'));
            }
        }
        return $warning;
    }

    public function getGroupImage($id_group, $filename = false)
    {
        $id = $this->isInGroup($id_group, $this->awp_attributes);

        if ($id < 0) {
            return false;
        }
        $v = array_key_exists("image_upload", $this->awp_attributes[$id]) ? $this->awp_attributes[$id]['image_upload'] : "";
        $dir = ($filename ? '' : _MODULE_DIR_ . 'attributewizardpro/views/img/');

        if (is_file(dirname(__FILE__) . '/../views/img/id_group_' . $id_group . '.gif')) {
            return $dir . 'id_group_' . $id_group . '.gif' . ($filename ? '' : '?v=' . $v);
        } else if (is_file(dirname(__FILE__) . '/../views/img/id_group_' . $id_group . '.png')) {
            return $dir . 'id_group_' . $id_group . '.png' . ($filename ? '' : '?v=' . $v);
        } else if (is_file(dirname(__FILE__) . '/../views/img/id_group_' . $id_group . '.jpg')) {
            return $dir . 'id_group_' . $id_group . '.jpg' . ($filename ? '' : '?v=' . $v);
        } else if (is_file(dirname(__FILE__) . '/../img/id_group_' . $id_group . '.jpeg')) {
            return $dir . 'id_group_' . $id_group . '.jpeg' . ($filename ? '' : '?v=' . $v);
        } else {
            return false;
        }
    }

    public function displayLimitPostWarning()
    {
        /**
         * formula to count AWP fields, we count multilang fields + constant count of group fields:
         * awp_description_ * lang + 1
         * group_header_ * lang
         * group_ * 13
         */
        $languages_count = Language::countActiveLanguages();
        $groups_count = count(AttributeGroup::getAttributesGroups($this->context->cookie->id_lang));
        $count = ($languages_count * 2 + 1 + 13) * $groups_count;

        $limit_warning = array();
        if ((ini_get('suhosin.post.max_vars') && ini_get('suhosin.post.max_vars') < $count) || (ini_get('suhosin.request.max_vars') && ini_get('suhosin.request.max_vars') < $count)) {
            $limit_warning['error_type'] = 'suhosin';
            $limit_warning['post.max_vars'] = ini_get('suhosin.post.max_vars');
            $limit_warning['request.max_vars'] = ini_get('suhosin.request.max_vars');
            $limit_warning['needed_limit'] = $count + 100;
        } elseif (ini_get('max_input_vars') && ini_get('max_input_vars') < $count) {
            $limit_warning['error_type'] = 'conf';
            $limit_warning['max_input_vars'] = ini_get('max_input_vars');
            $limit_warning['needed_limit'] = $count + 100;
        }
        $return = '';
        if ($limit_warning) {
            if ($limit_warning['error_type'] == 'suhosin') {
                $return .= $this->l('Warning! Your hosting provider is using the Suhosin patch for PHP, which limits the maximum number of fields allowed in a form:') . '<br>' .
                    $limit_warning['post.max_vars'] . ' ' .
                    $this->l('for suhosin.post.max_vars.') . '<br>' .
                    $limit_warning['request.max_vars'] . ' ' . $this->l('for suhosin.request.max_vars.') . '<br>' .
                    $this->l('Please ask your hosting provider to increase the Suhosin limit to');
            } else {
                $return .= $this->l('Warning! Your PHP configuration limits the maximum number of fields allowed in a form:') . '<br>' .
                    $limit_warning['max_input_vars'] . ' ' . $this->l('for max_input_vars.') . '<br>' .
                    $this->l('Please ask your hosting provider to increase this limit to');
            }
            $return .= ' ' . sprintf($this->l('%s at least, or you will not able to save the attributes settings.'), $limit_warning['needed_limit']) . '';
        }

        return $return;
    }

    public function getAttributeImpact($id_product)
    {
        $cookie = $this->context->cookie;
        $use_stock = Configuration::get('PS_STOCK_MANAGEMENT');
        $ps_version = (float) (Tools::substr(_PS_VERSION_, 0, 3));

        $query = 'SELECT pac.id_product_attribute, '
                    . 'pac.id_attribute, '
                    . ($use_stock ? 'sa.quantity,' : '')
                    . ' pa_shop.price,
                    pa_shop.weight,
                    pa.minimal_quantity,
                    ag.`id_attribute_group`,
                    agl.`name` AS group_name,
                    agl.`public_name` AS public_group_name,
                    a.`id_attribute`,
                    al.`name` AS attribute_name
				FROM ' . _DB_PREFIX_ . 'product_attribute AS pa, ' .
                    _DB_PREFIX_ . 'product_attribute_shop AS pa_shop, ' .
                    ($use_stock ? _DB_PREFIX_ . 'stock_available AS sa,' : '') . ' ' .
                    _DB_PREFIX_ . 'product_attribute_combination AS pac, ' .
                    _DB_PREFIX_ . 'attribute AS a
				LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
				LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON a.`id_attribute` = al.`id_attribute`
				LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl ON ag.`id_attribute_group` = agl.`id_attribute_group`
				WHERE
                    pa_shop.id_product_attribute = pa.id_product_attribute AND
                    pa_shop.id_shop = ' . (int) Context::getContext()->shop->id . ' AND
                    pac.id_product_attribute = pa.id_product_attribute AND
                    pac.id_attribute = a.id_attribute
                    AND al.`id_lang` = ' . (int) ($cookie->id_lang) . '
                    AND agl.`id_lang` = ' . (int) ($cookie->id_lang) . '
                    AND a.id_attribute_group != ' . (int) $this->awp_default_group . '
                    AND pa.id_product = ' . (int) $id_product . '
                    AND (pa.default_on = 0 OR pa.default_on IS NULL)
                    ';
        $query = $this->appendStockSql($query, $use_stock);

        $result = Db::getInstance()->ExecuteS($query);

        /* Connected attributes */
        /* get all attributes */
        $sqlConnectedAttributes = 'SELECT pa.*,
                        product_attribute_shop.*,
                        ag.`id_attribute_group`,
                        ag.`is_color_group`,
                        agl.`name` AS group_name,
                        al.`name` AS attribute_name,
						a.`id_attribute`,
                        pa.`unit_price_impact`,
                        IFNULL(stock.quantity, 0) as quantity
					FROM `' . _DB_PREFIX_ . 'product_attribute` pa
					' . Shop::addSqlAssociation('product_attribute', 'pa') . '
					' . Product::sqlStock('pa', 'pa') . '
					LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac ON pac.`id_product_attribute` = pa.`id_product_attribute`
					LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a ON a.`id_attribute` = pac.`id_attribute`
					LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
					LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = ' . (int) ($cookie->id_lang) . ')
					LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = ' . (int) ($cookie->id_lang) . ')
					WHERE pa.`id_product` = ' . (int) ($id_product) . '
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
            if ($row['default_on']) {
                $defAttribute = (int) $row['id_product_attribute'];
            }
        }

        $allConnected = true;
        $resultA = Db::getInstance()->getValue("SELECT id_attribute_group FROM " . _DB_PREFIX_ . "attribute_group_lang WHERE name = 'awp_details' ORDER BY id_attribute_group DESC");
        $awpDetailsIdGroup = $resultA;

        foreach ($connectedAttributesArray as $k => $row) {
            $row['id_attribute_groups'] = array_unique($row['id_attribute_groups']);

            if (count($row['id_attribute_groups']) == 1) {
                unset($connectedAttributesArray[$k]);
                if ($awpDetailsIdGroup != $row['id_attribute_groups'][0]) {
                    $allConnected = false;
                }
            }
        }
        if (empty($connectedAttributesArray)) {
            $allConnected = true;
        }

        if (!$allConnected) {
            unset($connectedAttributesArray[$defAttribute]);
            unset($result[$defAttribute]);
        }

        $attribute_impact = array();

        foreach ($connectedAttributesArray as $k => $row) {
            foreach ($result as $l => $row) {
                if ($row['id_product_attribute'] == $k) {
                    unset($result[$l]);
                }
            }
        }
        /* End connected attributes */

        foreach ($result as $row) {
            $attribute_impact[$row['id_attribute']]['minimal_quantity'] = isset($row['minimal_quantity']) ? $row['minimal_quantity'] : 1;
            $attribute_impact[$row['id_attribute']]['quantity'] = $use_stock ? (int) $row['quantity'] : 0;
            $attribute_impact[$row['id_attribute']]['price'] = (float) $row['price'];
            $attribute_impact[$row['id_attribute']]['weight'] = (float) $row['weight'];
            $attribute_impact[$row['id_attribute']]['attribute'] = $row['attribute_name'];
            $attribute_impact[$row['id_attribute']]['group'] = $row['public_group_name'];
        }

        // Get attributes for the default group, and use only if not already used in the query above
        $query = 'SELECT pac.id_attribute, ' .
                    ($use_stock ? 'sa.quantity,' : '') .
                    ' pa_shop.price,
                    pa_shop.weight,
                    pa.minimal_quantity,
                    ag.`id_attribute_group`,
                    agl.`name` AS group_name,
                    agl.`public_name` AS public_group_name,
                    a.`id_attribute`,
                    al.`name` AS attribute_name
				FROM ' . _DB_PREFIX_ . 'product_attribute AS pa, ' .
                    _DB_PREFIX_ . 'product_attribute_shop AS pa_shop, ' .
                    ($use_stock ? _DB_PREFIX_ . 'stock_available AS sa,' : '') . ' ' .
                    _DB_PREFIX_ . 'product_attribute_combination AS pac, ' .
                    _DB_PREFIX_ . 'attribute AS a
				LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
				LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON a.`id_attribute` = al.`id_attribute`
				LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl ON ag.`id_attribute_group` = agl.`id_attribute_group`
				WHERE
                    pa_shop.id_product_attribute = pa.id_product_attribute AND
                    pa_shop.id_shop = ' . (int) Context::getContext()->shop->id . ' AND
                    ' . ($use_stock ? ' pa_shop.id_shop = sa.id_shop AND ' : '') . '
                    pac.id_product_attribute = pa.id_product_attribute AND
                    pac.id_attribute = a.id_attribute
                    ' . ($use_stock ? 'AND pa.`id_product_attribute` = sa.`id_product_attribute`' : '') . '
                    AND al.`id_lang` = ' . (int) ($cookie->id_lang) . '
                    AND agl.`id_lang` = ' . (int) ($cookie->id_lang) . '
                    AND a.id_attribute_group != ' . (int) $this->awp_default_group . '
                    AND pa.id_product = ' . (int) $id_product . '
                    AND pa.default_on = 1';


        $result = Db::getInstance()->ExecuteS($query);

        /* Connected attributes */
        /* If all combinations are connected add the default combination to impact */
        if ($allConnected) {
            foreach ($result as $row) {
                if (!array_key_exists($row['id_attribute'], $attribute_impact)) {
                    $attribute_impact[$row['id_attribute']]['minimal_quantity'] = isset($row['minimal_quantity']) ? $row['minimal_quantity'] : 1;
                    $attribute_impact[$row['id_attribute']]['quantity'] = $use_stock ? (int) $row['quantity'] : 0;
                    $attribute_impact[$row['id_attribute']]['price'] = (float) $row['price'];
                    $attribute_impact[$row['id_attribute']]['weight'] = (float) $row['weight'];
                    $attribute_impact[$row['id_attribute']]['attribute'] = $row['attribute_name'];
                    $attribute_impact[$row['id_attribute']]['group'] = $row['public_group_name'];
                }
            }
        }
        /* End connected attributes */

        return $attribute_impact;
    }

    /* Connected attributes
     * 	added impactIgnore function to get all attributes - getAttributeImpact function removed any connected attributes
     */
    public function getAttributeImpactIgnore($id_product)
    {
        $cookie = $this->context->cookie;
        $use_stock = Configuration::get('PS_STOCK_MANAGEMENT');
        $ps_version = (float) (Tools::substr(_PS_VERSION_, 0, 3));

        $query = 'SELECT pac.id_product_attribute, '
                    . 'pac.id_attribute, '
                    . ($use_stock ? 'sa.quantity,' : '')
                    . ' pa_shop.price,
                    pa_shop.weight,
                    pa.minimal_quantity,
                    ag.`id_attribute_group`,
                    agl.`name` AS group_name,
                    agl.`public_name` AS public_group_name,
                    a.`id_attribute`,
                    al.`name` AS attribute_name
				FROM ' . _DB_PREFIX_ . 'product_attribute AS pa, ' .
                    _DB_PREFIX_ . 'product_attribute_shop AS pa_shop, ' .
                    ($use_stock ? _DB_PREFIX_ . 'stock_available AS sa,' : '') . ' ' .
                    _DB_PREFIX_ . 'product_attribute_combination AS pac, ' .
                    _DB_PREFIX_ . 'attribute AS a
				LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
				LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON a.`id_attribute` = al.`id_attribute`
				LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl ON ag.`id_attribute_group` = agl.`id_attribute_group`
				WHERE
                    pa_shop.id_product_attribute = pa.id_product_attribute AND
                    pa_shop.id_shop = ' . (int) Context::getContext()->shop->id . ' AND
                    pac.id_product_attribute = pa.id_product_attribute AND
                    pac.id_attribute = a.id_attribute
                    AND al.`id_lang` = ' . (int) ($cookie->id_lang) . '
                    AND agl.`id_lang` = ' . (int) ($cookie->id_lang) . '
                    AND a.id_attribute_group != ' . (int) $this->awp_default_group . '
                    AND pa.id_product = ' . (int) $id_product . '
                    AND (pa.default_on = 0 OR pa.default_on IS NULL)';

        $query = $this->appendStockSql($query, $use_stock);

        $result = Db::getInstance()->ExecuteS($query);

        $attribute_impact = array();

        foreach ($result as $row) {
            $attribute_impact[$row['id_attribute']]['minimal_quantity'] = isset($row['minimal_quantity']) ? $row['minimal_quantity'] : 1;
            $attribute_impact[$row['id_attribute']]['quantity'] = $use_stock ? (int) $row['quantity'] : 0;
            $attribute_impact[$row['id_attribute']]['price'] = (float) $row['price'];
            $attribute_impact[$row['id_attribute']]['weight'] = (float) $row['weight'];
            $attribute_impact[$row['id_attribute']]['attribute'] = $row['attribute_name'];
            $attribute_impact[$row['id_attribute']]['group'] = $row['public_group_name'];
        }
        // Get attributes for the default group, and use only if not already used in the query above

        $query = 'SELECT pac.id_attribute, '
                    . ($use_stock ? 'sa.quantity,' : '')
                    . ' pa_shop.price,
                    pa_shop.weight,
                    pa.minimal_quantity,
                    ag.`id_attribute_group`,
                    agl.`name` AS group_name,
                    agl.`public_name` AS public_group_name,
                    a.`id_attribute`,
                    al.`name` AS attribute_name
				FROM ' . _DB_PREFIX_ . 'product_attribute AS pa, ' .
                    _DB_PREFIX_ . 'product_attribute_shop AS pa_shop, ' .
                    ($use_stock ? _DB_PREFIX_ . 'stock_available AS sa,' : '') . ' ' .
                    _DB_PREFIX_ . 'product_attribute_combination AS pac, ' .
                    _DB_PREFIX_ . 'attribute AS a
				LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
				LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON a.`id_attribute` = al.`id_attribute`
				LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl ON ag.`id_attribute_group` = agl.`id_attribute_group`
				WHERE
                     pa_shop.id_product_attribute = pa.id_product_attribute AND
                     pa_shop.id_shop = ' . (int) Context::getContext()->shop->id . ' AND
                    ' . ($use_stock ? ' pa_shop.id_shop = sa.id_shop AND ' : '') . '
                    pac.id_product_attribute = pa.id_product_attribute
                    AND pac.id_attribute = a.id_attribute
                    ' . ($use_stock ? 'AND pa.`id_product_attribute` = sa.`id_product_attribute`' : '') . '
                    AND al.`id_lang` = ' . (int) ($cookie->id_lang) . '
                    AND agl.`id_lang` = ' . (int) ($cookie->id_lang) . '
                    AND a.id_attribute_group != ' . (int) $this->awp_default_group . '
                    AND pa.id_product = ' . (int) $id_product . '
                    AND pa.default_on = 1';


        $result = Db::getInstance()->ExecuteS($query);

        foreach ($result as $row) {
            if (!array_key_exists($row['id_attribute'], $attribute_impact)) {
                $attribute_impact[$row['id_attribute']]['minimal_quantity'] = isset($row['minimal_quantity']) ? $row['minimal_quantity'] : 1;
                $attribute_impact[$row['id_attribute']]['quantity'] = $use_stock ? (int) $row['quantity'] : 0;
                $attribute_impact[$row['id_attribute']]['price'] = (float) $row['price'];
                $attribute_impact[$row['id_attribute']]['weight'] = (float) $row['weight'];
                $attribute_impact[$row['id_attribute']]['attribute'] = $row['attribute_name'];
                $attribute_impact[$row['id_attribute']]['group'] = $row['public_group_name'];
            }
        }

        return $attribute_impact;
    }

    /*
     * 	Function for PS 1.5.X - increasing advanced stock management for a product
     * 	$product	= Product class
     * 	$id_ac		= Product combination (id_product_combination, 0 if no combination selected or product does not have a combination)
     * 	$id_shop 	= current shop id selected
     * 	$stockIncreaseValue = stock value
     * 	$id_stock_mvt_reason = reason for changing the stock (1 - increase stock, 2 - decrease stock)
     * 	$id_employee = logged employee
     */
    public function addStock15($product, $id_ac, $id_shop, $stock)
    {
        if ($stock <= 0 && Configuration::get('PS_ORDER_OUT_OF_STOCK') == 0 && StockAvailable::outOfStock($product->id, $id_shop) == 0) {
            return $this->removeStock15($product, $id_ac, $id_shop, $stock);
        }
        /* Advanced stock management */
        if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && $product->advanced_stock_management && $product->depends_on_stock) {
            $id_stock_mvt_reason = 1;
            /* Creating the context employee */
            $employee = new Employee(Db::getInstance()->getValue('SELECT id_employee FROM ' . _DB_PREFIX_ . 'employee where active = 1 ORDER BY id_employee ASC'));
            $this->context->employee = $employee;
            /* Getting current warehouses for the product id and product combination id */
            $productAttributeWarehouse = Warehouse::getProductWarehouseList($product->id, $id_ac, $id_shop);
            /* If not accosiates witha warehouse, then associate it
             * with the first warehouse we find for the product */
            if (count($productAttributeWarehouse) <= 0) {
                $houses = WarehouseProductLocation::getCollection($product->id);
                $id_warehouse = 0;
                foreach ($houses as $warehouse) {
                    if ($warehouse->id_warehouse > 0) {
                        $id_warehouse = $warehouse->id_warehouse;
                        break;
                    }
                }
                if ($id_warehouse == 0) {
                    return;
                }
            } else {
                /* Get first warehouse where to increase the stock for product */
                $id_warehouse = (int) $productAttributeWarehouse[0]['id_warehouse'];
            }
            $warehouse = new Warehouse($id_warehouse);
            $wpl_id = (int) WarehouseProductLocation::getIdByProductAndWarehouse($product->id, $id_ac, $id_warehouse);

            /* Create a warehouse accosiation for the product combination */
            if ($wpl_id <= 0) {
                //	create new record
                $warehouse_location_entity = new WarehouseProductLocation();
                $warehouse_location_entity->id_product = (int) $product->id;
                $warehouse_location_entity->id_product_attribute = (int) $id_ac;
                $warehouse_location_entity->id_warehouse = (int) $id_warehouse;
                $warehouse_location_entity->location = "";
                $warehouse_location_entity->save();
            }
            $stock_manager = StockManagerFactory::getManager();
            /* Add stock + $startingCount (no of serials uploaded) to product and product attribute to first warehouse found */
            if ($stock_manager->addProduct((int) $product->id, (int) $id_ac, $warehouse, $stock, $id_stock_mvt_reason, $product->price, 1)) {
                /* Syncronize all stock for product id */
                StockAvailable::synchronize((int) $product->id);
            } else {
                return false;
            }
        } else {
            StockAvailable::setQuantity((int) $product->id, (int) $id_ac, $stock, $id_shop);
        }
    }

    public function removeStock15($product, $id_ac, $id_shop, $stock)
    {
        if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && $product->advanced_stock_management && $product->depends_on_stock) {
            $id_stock_mvt_reason = 2;
            $employee = new Employee(Db::getInstance()->getValue('SELECT id_employee FROM ' . _DB_PREFIX_ . 'employee where active = 1 ORDER BY id_employee ASC'));
            $this->context->employee = $employee;
            /* Getting current warehouses for the product id and product combination id */
            $productAttributeWarehouse = Warehouse::getProductWarehouseList($product->id, $id_ac, $id_shop);
            if (count($productAttributeWarehouse) <= 0) {
                return false;
            } else {
                /* Get first warehouse where to decrease the stock for product */
                $id_warehouse = (int) $productAttributeWarehouse[0]['id_warehouse'];
                $warehouse = new Warehouse($id_warehouse);
                $stock_manager = StockManagerFactory::getManager();
                $current_stock = StockAvailable::getQuantityAvailableByProduct($product->id, $id_ac, $id_shop);
                if ($current_stock == $stock) {
                    return;
                } else {
                    $stock = $current_stock - $stock;
                }
                $removed_products = $stock_manager->removeProduct((int) $product->id, (int) $id_ac, $warehouse, $stock, $id_stock_mvt_reason, 1);
                if (count($removed_products) >= 0) {
                    StockAvailable::synchronize((int) $product->id);
                } else {
                    return false;
                }
            }
        } else {
            if ($stock <= 0 && Configuration::get('PS_ORDER_OUT_OF_STOCK') == 0 && StockAvailable::outOfStock($product->id, $id_shop) == 0) {
                StockAvailable::removeProductFromStockAvailable((int) $product->id, (int) $id_ac, $id_shop);
            } else {
                StockAvailable::setQuantity((int) $product->id, (int) $id_ac, $stock, $id_shop);
            }
        }
    }

    public function getFeatureVal($id_lang, $id_product, $id_feature)
    {
        $query = 'SELECT fv.value
			FROM '
            . _DB_PREFIX_ . 'feature_product pf, '
            . _DB_PREFIX_ . 'feature_value_lang fv
			WHERE pf.id_feature = ' . (int) $id_feature
            . ' AND pf.id_product = ' . (int) $id_product
            . ' AND pf.id_feature_value = fv.id_feature_value '
            . ' AND fv.id_lang = ' . (int) $id_lang;
        $arr = Db::getInstance()->ExecuteS($query);
        return (is_array($arr) && sizeof($arr) > 0 ? $arr[0]['value'] : 1);
    }

    public function checkCartQuantity($idProduct, $qty, $ins_id)
    {
        $cookie = $this->context->cookie;
        $cart = $this->context->cart;

        $add = true;
        $ids = explode(",", Tools::substr($ins_id, 1));
        $attribute_impact = $this->getAttributeImpact($idProduct);
        $query = 'SELECT *
				FROM `' . _DB_PREFIX_ . 'cart_product`
				WHERE `id_product` = ' . (int) ($idProduct)
            . ' AND `id_cart` = ' . (int) ($cart->id);
        // 	Get the products from the cart //
        $result = Db::getInstance()->ExecuteS($query);

        foreach ($result as $row) {
            $tids = explode(",", Tools::substr($row['instructions_id'], 1));
            // 	remove cart quantity from total quantity
            foreach ($tids as $id) {
                if (isset($attribute_impact[$id]['quantity'])) {
                    $attribute_impact[$id]['quantity'] -= $row['quantity'];
                }
            }
        }
        foreach ($ids as $id) {
            if (isset($attribute_impact[$id]['quantity']) && $attribute_impact[$id]['quantity'] - $qty < 0) {
                $add = false;
            }
        }

        return $add;
    }

    /*
     * This function is used to retrieve an exising id_product attribute for a given product based on all the selected attributes (in $ids)
     * If the combination does not exist, we will generate it.
     */
    public function getIdProductAttribute($id_product, $ids)
    {
        $cookie = $this->context->cookie;

        $use_stock = Configuration::get('PS_STOCK_MANAGEMENT');

        $query = 'SELECT pac.id_attribute, '
            . '' . ($use_stock ? 'sa.quantity,' : '')
            . ' pa.minimal_quantity,
                pa.price,
                pa.weight,
				ag.`id_attribute_group`,
                agl.`name` AS group_name,
                agl.`public_name` AS public_group_name,
                a.`id_attribute`,
                al.`name` AS attribute_name
				FROM ' . _DB_PREFIX_ . 'product_attribute AS pa, '
            . ($use_stock ? _DB_PREFIX_ . 'stock_available AS sa,' : '') . ' '
            . _DB_PREFIX_ . 'product_attribute_combination AS pac, '
            . _DB_PREFIX_ . 'attribute AS a
				LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
				LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON a.`id_attribute` = al.`id_attribute`
				LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl ON ag.`id_attribute_group` = agl.`id_attribute_group`
				WHERE pac.id_product_attribute = pa.id_product_attribute
                    AND pac.id_attribute = a.id_attribute
                    ' . ($use_stock ? 'AND pa.`id_product_attribute` = sa.`id_product_attribute`' : '') . '
                    AND al.`id_lang` = ' . (int) ($cookie->id_lang) . '
                    AND agl.`id_lang` = ' . (int) ($cookie->id_lang) . '
                    AND a.id_attribute_group != ' . (int) $this->awp_default_group . '
                    AND pa.id_product = ' . (int) $id_product . '
                    AND (pa.default_on = 0 OR pa.default_on IS NULL)';

        $result = Db::getInstance()->ExecuteS($query);
        $attribute_impact = array();
        foreach ($result as $row) {
            $attribute_impact[$row['id_attribute']]['quantity'] = (int) $row['quantity'];
            $attribute_impact[$row['id_attribute']]['minimal_quantity'] = (int) $row['minimal_quantity'];
            $attribute_impact[$row['id_attribute']]['price'] = (float) $row['price'];
            $attribute_impact[$row['id_attribute']]['weight'] = (float) $row['weight'];
            $attribute_impact[$row['id_attribute']]['attribute'] = $row['attribute_name'];
            $attribute_impact[$row['id_attribute']]['group'] = $row['public_group_name'];
        }

        // Get attributes for the default group, and use only if not already used in the query above
        $query = 'SELECT pac.id_attribute, '
            . ($use_stock ? 'sa.quantity,' : '')
            . ' pa.minimal_quantity,
                pa.price,
                pa.weight,
				ag.`id_attribute_group`,
                agl.`name` AS group_name,
                agl.`public_name` AS public_group_name,
                a.`id_attribute`,
                al.`name` AS attribute_name
				FROM ' . _DB_PREFIX_ . 'product_attribute AS pa, '
            . ($use_stock ? _DB_PREFIX_ . 'stock_available AS sa,' : '') . ' '
            . _DB_PREFIX_ . 'product_attribute_combination AS pac, '
            . _DB_PREFIX_ . 'attribute AS a
				LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
				LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON a.`id_attribute` = al.`id_attribute`
				LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl ON ag.`id_attribute_group` = agl.`id_attribute_group`
				WHERE pac.id_product_attribute = pa.id_product_attribute
                    AND pac.id_attribute = a.id_attribute
                    ' . ($use_stock ? 'AND pa.`id_product_attribute` = sa.`id_product_attribute`' : '') . '
                    AND al.`id_lang` = ' . (int) ($cookie->id_lang) . '
                    AND agl.`id_lang` = ' . (int) ($cookie->id_lang) . '
                    AND a.id_attribute_group != ' . (int) $this->awp_default_group . '
                    AND pa.id_product = ' . (int) $id_product . '
                    AND pa.default_on = 1';

        $result = Db::getInstance()->ExecuteS($query);
        foreach ($result as $row) {
            if (!array_key_exists($row['id_attribute'], $attribute_impact)) {
                $attribute_impact[$row['id_attribute']]['quantity'] = (int) $row['quantity'];
                $attribute_impact[$row['id_attribute']]['minimal_quantity'] = (int) $row['minimal_quantity'];
                $attribute_impact[$row['id_attribute']]['price'] = (float) $row['price'];
                $attribute_impact[$row['id_attribute']]['weight'] = (float) $row['weight'];
                $attribute_impact[$row['id_attribute']]['attribute'] = $row['attribute_name'];
                $attribute_impact[$row['id_attribute']]['group'] = $row['public_group_name'];
            }
        }

        $ids = Tools::substr($ids, 1);
        $id_arr = explode(",", $ids);
        $price_impact = 0;
        $weight_impact = 0;
        $quantity_available = 0;
        $cur_quantity_minimal = 1;
        $first = true;

        $id_image = 0;
        $query = 'SELECT pai.id_image  '
            . 'FROM ' . _DB_PREFIX_ . 'product_attribute_combination pac, '
            . '' . _DB_PREFIX_ . 'product_attribute_image pai, '
            . '' . _DB_PREFIX_ . 'product_attribute pa '
            . 'WHERE `id_attribute` IN (' . pSQL($ids) . ') '
            . 'AND pac.id_product_attribute = pa.id_product_attribute '
            . 'AND pac.id_product_attribute = pai.id_product_attribute '
            . 'AND pa.id_product = ' . (int) $id_product . ' '
            . 'ORDER BY pa.default_on ASC';
        $res = Db::getInstance()->ExecuteS($query);
        if (is_array($res) && sizeof($res) > 0) {
            $id_image = $res[0]['id_image'];
        }

        $id_lang = (int) $cookie->id_lang;
        $connectedAttributesGroups = array();
        /* get all attributes */

        $sqlConnectedAttributes = 'SELECT pa.*,
                        product_attribute_shop.*,
                        ag.`id_attribute_group`,
                        ag.`is_color_group`,
                        agl.`name` AS group_name,
                        al.`name` AS attribute_name,
                        a.`id_attribute`,
                        pa.`unit_price_impact`,
                        IFNULL(stock.quantity, 0) as quantity
					FROM `' . _DB_PREFIX_ . 'product_attribute` pa
					' . Shop::addSqlAssociation('product_attribute', 'pa') . '
					' . Product::sqlStock('pa', 'pa') . '
					LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac ON pac.`id_product_attribute` = pa.`id_product_attribute`
					LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a ON a.`id_attribute` = pac.`id_attribute`
					LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
					LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = ' . (int) $id_lang . ')
					LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = ' . (int) $id_lang . ')
					WHERE pa.`id_product` = ' . (int) $id_product . '
					GROUP BY pa.`id_product_attribute`, a.`id_attribute`
					ORDER BY pa.`id_product_attribute`';


        $connectedAttributesSql = Db::getInstance()->ExecuteS($sqlConnectedAttributes);

        $connectedAttributesArray = array();
        /* construct array with all attributes, groups & prices */
        $defAttribute = 0;
        foreach ($connectedAttributesSql as $row) {
            $connectedAttributesArray[$row['id_product_attribute']]['id_attribute_groups'][] = (int) $row['id_attribute_group'];
            //$connectedAttributesArray[$row['id_product_attribute']]['attributes_values'][] = $row['attribute_name'];
            $connectedAttributesArray[$row['id_product_attribute']]['attributes'][] = (int) $row['id_attribute'];
            $connectedAttributesArray[$row['id_product_attribute']]['attributes_to_groups'][$row['id_attribute_group']][] = (int) $row['id_attribute'];
            $connectedAttributesArray[$row['id_product_attribute']]['price'] = (float) ($row['price']); //Tools::convertPriceFull($row['price'], null, Context::getContext()->currency);
            $connectedAttributesArray[$row['id_product_attribute']]['quantity'] = (int) $row['quantity'];
            $connectedAttributesArray[$row['id_product_attribute']]['weight'] = (float) $row['weight'];
            $connectedAttributesArray[$row['id_product_attribute']]['minimal_quantity'] = (int) $row['minimal_quantity'];
            $connectedAttributesArray[$row['id_product_attribute']]['reference'] = $row['reference'];

            if ($row['default_on']) {
                $defAttribute = (int) $row['id_product_attribute'];
            }
        }
        /* Remove simple attributes - connected attributes must contain a fixed number of groups */
        $notConnectedGroups = array();

        $notConnectedAttributeValuesAll = array();
        $connectedAttributeValuesAll = array();

        $result = Db::getInstance()->getValue("SELECT id_attribute_group FROM " . _DB_PREFIX_ . "attribute_group_lang WHERE name = 'awp_details' ORDER BY id_attribute_group DESC");
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

            if (count($row['id_attribute_groups']) == 1) {
            } else {
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

        // Remove from connected attribute group the groups which have a single value (old style)
        $connectedSelectedAttr = array();

        foreach ($id_arr as $id_attribute) {
            if (in_array($id_attribute, $bothConnectedAttributes)) {
                $connectedSelectedAttr[] = $id_attribute;
            }
        }

        $connectedCombId = null;

        /* Connected Attributes */
        $connected_ids = array();
        foreach ($id_arr as $id_attribute) {
            if (in_array($id_attribute, $connectedSelectedAttr) && in_array($id_attribute, $bothConnectedAttributes)) {
                $connected_ids[] = $id_attribute;
            }
        }
        /* END Connected attributes */
        if (isset($connected_ids) && !empty($connected_ids)) {
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
        }

        foreach ($id_arr as $id_attribute) {
            $cur_price_impact = $attribute_impact[$id_attribute]['price'];
            $cur_weight_impact = $attribute_impact[$id_attribute]['weight'];
            $cur_quantity_available = $attribute_impact[$id_attribute]['quantity'];
            $cur_quantity_minimal = max($attribute_impact[$id_attribute]['minimal_quantity'], $cur_quantity_minimal);

            if (!in_array($id_attribute, $connected_ids)) {
                $price_impact += $cur_price_impact;
                $weight_impact += $cur_weight_impact;
            }
            if ($first) {
                $quantity_available = $cur_quantity_available;
                $first = false;
            } else {
                $quantity_available = min($quantity_available, $cur_quantity_available);
            }
        }

        if (isset($connectedAttributeAvailable)) {
            if (count($connected_ids) >= count($connectedAttributesGroups)) {
                $price_impact += $connectedAttributeAvailable['price'];
                $weight_impact += $connectedAttributeAvailable['weight'];
            }
        }
        $query = "SELECT pa.* FROM " . _DB_PREFIX_ . "product_attribute AS pa, "
            . "" . _DB_PREFIX_ . "product_attribute_combination AS pac, "
            . "" . _DB_PREFIX_ . "attribute AS a " .
            ($id_image > 0 ? ", " . _DB_PREFIX_ . "product_attribute_image AS pai" : "") . " " .
            "WHERE id_product = '" . (int) $id_product . "' "
            . "AND price = '" . (float) $price_impact . "' "
            . "AND weight = '" . (float) $weight_impact . "' "
            . "AND a.id_attribute = pac.id_attribute "
            . "AND pac.id_product_attribute = pa.id_product_attribute "
            . "AND a.id_attribute_group = '" . (int) $this->awp_default_group . "' "
            . "AND pa.quantity = " . ((int) ($quantity_available)) .
            (isset($connectedReference) ? " AND pa.reference = '" . pSQL($connectedReference) . "' " : '' ) .
            ($id_image > 0 ? " AND pac.id_product_attribute = pai.id_product_attribute "
                . "AND pai.id_image = " . (int) $id_image : "");

        $result = Db::getInstance()->ExecuteS($query);
        $id_product_attribute = "";
        foreach ($result as $k => $row) {
            $id_product_attribute = $row['id_product_attribute'];
        }
        if ($id_product_attribute == "") {
            Db::getInstance()->Execute("INSERT INTO " . _DB_PREFIX_ . "product_attribute (reference, id_product, price, weight, quantity) VALUES ('" . (isset($connectedReference) ? pSQL($connectedReference) : '' ) . "' , '" . (int) $id_product . "','" . (float) $price_impact . "','" . (float) $weight_impact . "','" . (int) ($quantity_available) . "')");
            $id_product_attribute = Db::getInstance()->Insert_ID();

            Db::getInstance()->Execute("INSERT INTO " . _DB_PREFIX_ . "product_attribute_combination (id_attribute, id_product_attribute) VALUES ('$this->awp_default_item','$id_product_attribute')");
            $res = Db::getInstance()->ExecuteS('SELECT pai.id_image  FROM ' . _DB_PREFIX_ . 'product_attribute_combination pac, ' . _DB_PREFIX_ . 'product_attribute_image pai, ' . _DB_PREFIX_ . 'product_attribute pa WHERE `id_attribute` IN (' . pSQL($ids) . ') AND pac.id_product_attribute = pa.id_product_attribute AND pac.id_product_attribute = pai.id_product_attribute AND pa.id_product = ' . (int) $id_product . ' ORDER BY pa.default_on ASC');

            if (is_array($res) && sizeof($res) > 0) {
                Db::getInstance()->Execute("INSERT INTO " . _DB_PREFIX_ . "product_attribute_image (id_product_attribute, id_image) VALUES (" . (int) $id_product_attribute . ", '" . (int) $res[0]['id_image'] . "')");
            }
            Db::getInstance()->Execute('INSERT INTO `' . _DB_PREFIX_ . 'product_attribute_shop` '
                . '(' . ($this->comparePSV('>=', '1.6.1') ? '`id_product`,' : '') . '
                    `id_product_attribute`,
                    `id_shop`,
					`wholesale_price`,
                    `price`,
					`ecotax`, `weight`,
					`unit_price_impact`,
                    `default_on`,
					`minimal_quantity`,
                    `available_date`)
					VALUES (' . ($this->comparePSV('>=', '1.6.1') ? (int) $id_product . ',' : '') .
                (int) $id_product_attribute . ',' .
                (int) Context::getContext()->shop->id . ',
					"0", "' . (float) $price_impact . '",
					"0","' . (float) $weight_impact . '",
					"0",' . ($this->comparePSV('>=', '1.6.1') ? 'NULL' : '0') . ',
					"' . (int) $cur_quantity_minimal . '",  "0000-00-00"	)');
            $stock = (int) $quantity_available < 0 ? 0 : (int) $quantity_available;

            $this->addStock15(new Product((int) $id_product), (int) $id_product_attribute, (int) Context::getContext()->shop->id, (int) $stock);
        } else {
            Db::getInstance()->Execute("UPDATE " . _DB_PREFIX_ . "product_attribute SET quantity = " . ((int) ($quantity_available)) . " WHERE id_product_attribute = " . (int) $id_product_attribute);

            $stock = (int) $quantity_available < 0 ? 0 : (int) $quantity_available;

            $this->removeStock15(new Product((int) $id_product), (int) $id_product_attribute, (int) Context::getContext()->shop->id, (int) $stock);
        }
        return (int) $id_product_attribute;
    }

    public static function getGroupImageTag($id_group)
    {
        if (is_array($id_group)) {
            $alt = $id_group['alt'];
        }
        if (is_array($id_group)) {
            $v = $id_group['v'];
        }
        if (is_array($id_group)) {
            $id_group = $id_group['id_group'];
        }
        if (is_file(dirname(__FILE__) . '/../views/img/id_group_' . $id_group . '.gif')) {
            $filename = _MODULE_DIR_ . 'attributewizardpro/views/img/id_group_' . $id_group . '.gif?v=' . $v;
            $serverfile = dirname(__FILE__) . '/../views/img/id_group_' . $id_group . '.gif';
        } else if (is_file(dirname(__FILE__) . '/../views/img/id_group_' . $id_group . '.png')) {
            $filename = _MODULE_DIR_ . 'attributewizardpro/views/img/id_group_' . $id_group . '.png?v=' . $v;
            $serverfile = dirname(__FILE__) . '/../views/img/id_group_' . $id_group . '.png';
        } else if (is_file(dirname(__FILE__) . '/../views/img/id_group_' . $id_group . '.jpg')) {
            $filename = _MODULE_DIR_ . 'attributewizardpro/views/img/id_group_' . $id_group . '.jpg?v=' . $v;
            $serverfile = dirname(__FILE__) . '/../views/img/id_group_' . $id_group . '.jpg';
        } else if (is_file(dirname(__FILE__) . '/../views/img/id_group_' . $id_group . '.jpeg')) {
            $filename = _MODULE_DIR_ . 'attributewizardpro/views/img/id_group_' . $id_group . '.jpeg?v=' . $v;
            $serverfile = dirname(__FILE__) . '/../views/img/id_group_' . $id_group . '.jpeg';
        }

        if (isset($filename)) {
            list($width, $height, $type, $attr) = getimagesize($serverfile);
            return "<img border=\"0\" src=\"$filename\" width=\"$width\" height=\"$height\" alt=\"$alt\" class=\"awp_gi\" />";
        } else {
            return false;
        }
    }

    public static function getLayeredImageTag($id_attribute)
    {
        if (is_array($id_attribute)) {
            $v = $id_attribute['v'];
        }
        if (is_array($id_attribute)) {
            $id_attribute = $id_attribute['id_attribute'];
        }
        if (is_file(dirname(__FILE__) . '/../views/img/id_attribute_' . $id_attribute . '.gif')) {
            $filename = _MODULE_DIR_ . '/attributewizardpro/views/img/id_attribute_' . $id_attribute . '.gif?v=' . $v;
        } else if (is_file(dirname(__FILE__) . '/../views/img/id_attribute_' . $id_attribute . '.png')) {
            $filename = _MODULE_DIR_ . '/attributewizardpro/views/img/id_attribute_' . $id_attribute . '.png?v=' . $v;
        } else if (is_file(dirname(__FILE__) . '/../views/img/id_attribute_' . $id_attribute . '.jpg')) {
            $filename = _MODULE_DIR_ . '/attributewizardpro/views/img/id_attribute_' . $id_attribute . '.jpg?v=' . $v;
        } else if (is_file(dirname(__FILE__) . '/../views/img/id_attribute_' . $id_attribute . '.jpeg')) {
            $filename = _MODULE_DIR_ . '/attributewizardpro/views/img/id_attribute_' . $id_attribute . '.jpeg?v=' . $v;
        }

        if (isset($filename)) {
            return "$filename";
        } else {
            return false;
        }
    }

    public static function awpConvertPriceWithCurrency($params, &$smarty)
    {
        return Tools::displayPrice((float) $params['price'], $params['currency'], false);
    }

    /**
     * Compare version of prestashop curent with $version2
     *
     */
    protected function comparePSV($operator, $version2)
    {
        return version_compare(substr($this->getRawPSV(), 0, strlen($version2)), $version2, $operator);
    }

    /**
     * get raw version of PrestaShop
     */
    protected function getRawPSV()
    {
        return _PS_VERSION_;
    }

    protected function appendStockSql($query, $use_stock)
    {
        if ($use_stock && $this->shop_group->share_stock)
        {
            $query .= 'AND pa.`id_product_attribute` = sa.`id_product_attribute`
                       AND sa.id_shop = 0';
        }
        elseif($use_stock)
        {
            $query .= 'AND pa.`id_product_attribute` = sa.`id_product_attribute`
                       AND pa_shop.id_shop = sa.id_shop';
        }
        return $query;
    }
}
