<?php
/**
 * Rolige PrestaShop Cleaner Extra
 *
 * @author    Rolige <www.rolige.com>
 * @copyright 2011-2019 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 */

class RgPSCETools
{
    public static function renameDbPrefix($new_db_prefix)
    {
        $db = Db::getInstance();

        $tables_data = $db->executeS('SHOW TABLES LIKE "'._DB_PREFIX_.'%"');
        $tables = array_map('current', $tables_data);
        $sentences = array_map(function ($table) use ($new_db_prefix)
        {
            return $table.' TO '.RgPSCETools::str_replace_first_limit(_DB_PREFIX_, $new_db_prefix, $table);
        }, $tables);
        $query = 'RENAME TABLE '.implode(', ', $sentences).';';

        $res = $db->execute($query);
        if ($res) {
            if (version_compare(_PS_VERSION_, '1.7', '>=')) {
                $config_text = file_get_contents(_PS_CORE_DIR_.'/app/config/parameters.php');
                $db_prefix_pos = (int)strpos($config_text, "'database_prefix'");
                $db_prefix_text = substr($config_text, $db_prefix_pos, strpos($config_text, ',', $db_prefix_pos) - $db_prefix_pos);
                $config_text = str_replace($db_prefix_text, "'database_prefix' => '".$new_db_prefix."'", $config_text);

                file_put_contents(_PS_CORE_DIR_.'/app/config/parameters.php', $config_text);

                Tools::clearSf2Cache('dev');
                Tools::clearSf2Cache('prod');
        } else {
                $config_text = file_get_contents(_PS_CORE_DIR_.'/config/settings.inc.php');
                $db_prefix_pos = (int)strpos($config_text, "'_DB_PREFIX_'");
                $db_prefix_text = substr($config_text, $db_prefix_pos, strpos($config_text, ';', $db_prefix_pos) - $db_prefix_pos);
                $config_text = str_replace($db_prefix_text, "'_DB_PREFIX_', '".$new_db_prefix."')", $config_text);

                file_put_contents(_PS_CORE_DIR_.'/config/settings.inc.php', $config_text);
            }
            return true;
        }

        return false;
    }

    public function str_replace_first_limit($search, $replace, $subject)
    {
        $occurrences = substr_count($subject, $search);
        if ($occurrences === 0) {
            return $subject;
        } else if ($occurrences <= 1) {
            return str_replace($search, $replace, $subject);
        }
        $position = 0;
        $position = strpos($subject, $search, $position) + strlen($search);
        $substring = substr($subject, 0, $position + 1);
        $substring = str_replace($search, $replace, $substring);
        return substr_replace($subject, $substring, 0, $position + 1);
    }

    public static function deleteOldImages()
    {
        $cont = 0;

        $real_images = Tools::scandir(_PS_PROD_IMG_DIR_, 'jpg', '', true);
        $images_data = Image::getAllImages();
        $db_images = array_column($images_data, 'id_image');

        foreach ($real_images as $img) {
            if (($id_image = (int)str_replace('.jpg', '', basename($img))) &&
                    !in_array($id_image, $db_images)) {
                if (@unlink(_PS_PROD_IMG_DIR_.$img)) {
                    $cont++;
                }
            }
        }

        self::deleteEmptyImagesFolder();

        return $cont;
    }

    private static function deleteEmptyImagesFolder($folder = _PS_PROD_IMG_DIR_)
    {
        $files = scandir($folder);
        
        foreach ($files as $file) {
            if ($file != '.' && $file != '..' && is_dir($folder.$file)) {
                self::deleteEmptyImagesFolder($folder.$file.'/');
            }
        }

        if ($folder != _PS_PROD_IMG_DIR_ && count(Tools::scandir($folder, 'jpg', '', true)) == 0) {
            Tools::deleteDirectory($folder, true);
        }
    }

    public static function truncate($case)
    {
        $db = Db::getInstance();
        $db->execute('SET FOREIGN_KEY_CHECKS = 0;');

        switch ($case) {
            case 'catalog':
                $id_home = Configuration::getMultiShopValues('PS_HOME_CATEGORY');
                $id_root = Configuration::getMultiShopValues('PS_ROOT_CATEGORY');
                $db->execute('DELETE FROM `'._DB_PREFIX_.'category` WHERE id_category NOT IN ('.implode(',', array_map('intval', $id_home)).', '.implode(',', array_map('intval', $id_root)).')');
                $max_cat_id = (int)$db->getValue('SELECT MAX(id_category) FROM `'._DB_PREFIX_.'category`') + 1;
                $db->execute('ALTER TABLE `'._DB_PREFIX_.'category` AUTO_INCREMENT='.(int)$max_cat_id);
                $db->execute('DELETE FROM `'._DB_PREFIX_.'category_lang` WHERE id_category NOT IN ('.implode(',', array_map('intval', $id_home)).', '.implode(',', array_map('intval', $id_root)).')');
                $db->execute('DELETE FROM `'._DB_PREFIX_.'category_shop` WHERE id_category NOT IN ('.implode(',', array_map('intval', $id_home)).', '.implode(',', array_map('intval', $id_root)).')');
                foreach (scandir(_PS_CAT_IMG_DIR_) as $dir) {
                    if (preg_match('/^[0-9]+(\-(.*))?\.jpg$/', $dir)) {
                        unlink(_PS_CAT_IMG_DIR_.$dir);
                    }
                }
                $tables = self::getCatalogRelatedTables();
                foreach ($tables as $table) {
                    $db->execute('TRUNCATE TABLE `'._DB_PREFIX_.bqSQL($table).'`');
                }
                $db->execute('DELETE FROM `'._DB_PREFIX_.'address` WHERE id_manufacturer > 0 OR id_supplier > 0 OR id_warehouse > 0');

                Image::deleteAllImages(_PS_PROD_IMG_DIR_);
                if (!file_exists(_PS_PROD_IMG_DIR_)) {
                    mkdir(_PS_PROD_IMG_DIR_);
                }
                foreach (scandir(_PS_MANU_IMG_DIR_) as $dir) {
                    if (preg_match('/^[0-9]+(\-(.*))?\.jpg$/', $dir)) {
                        unlink(_PS_MANU_IMG_DIR_.$dir);
                    }
                }
                foreach (scandir(_PS_SUPP_IMG_DIR_) as $dir) {
                    if (preg_match('/^[0-9]+(\-(.*))?\.jpg$/', $dir)) {
                        unlink(_PS_SUPP_IMG_DIR_.$dir);
                    }
                }
                break;

            case 'sales':
                $tables = self::getSalesRelatedTables();

                $modules_tables = array(
                    'sekeywords' => array('sekeyword'),
                    'pagesnotfound' => array('pagenotfound'),
                );

                foreach ($modules_tables as $name => $module_tables) {
                    if (Module::isInstalled($name)) {
                        $tables = array_merge($tables, $module_tables);
                    }
                }

                foreach ($tables as $table) {
                    $db->execute('TRUNCATE TABLE `'._DB_PREFIX_.bqSQL($table).'`');
                }
                $db->execute('DELETE FROM `'._DB_PREFIX_.'address` WHERE id_customer > 0');
                $db->execute('UPDATE `'._DB_PREFIX_.'employee` SET `id_last_order` = 0,`id_last_customer_message` = 0,`id_last_customer` = 0');

                break;
        }
        self::clearAllCaches();
        $db->execute('SET FOREIGN_KEY_CHECKS = 1;');
    }

    public static function checkOldLanguages($old_lang_id, $new_lang_id, $replace = false)
    {
        $db = Db::getInstance();
        $logs = array();
        $language_ids = Language::getLanguages(false, false, true);
        $language_ids_where = implode(',', $language_ids);

        $tables_data = $db->executeS('SELECT table_name FROM INFORMATION_SCHEMA.COLUMNS
            WHERE COLUMN_NAME = "id_lang" AND table_schema = "'._DB_NAME_.'"');
        $tables = array_column($tables_data, 'table_name');
        if (!count($tables)) {
            $tables = array_column($tables_data, 'TABLE_NAME');
        }
        foreach ($tables as $table) {
            $query = 'SELECT * FROM '.$table.' WHERE id_lang NOT IN ('.$language_ids_where.')';
            if ($results = $db->executeS($query)) {
                if ($replace) {
                    if ($db->update($table, array('id_lang' => $new_lang_id), 'id_lang = '.$old_lang_id, 0, false, true, false)) {
                        $logs[$table] = array(sprintf('%s replacements', $db->Affected_Rows()));
                    }
                } else {
                    $table_keys = $db->executeS('SELECT key_column_usage.column_name FROM INFORMATION_SCHEMA.key_column_usage
                        WHERE constraint_name = "PRIMARY" AND table_name  = "'.$table.'" AND table_schema = "'._DB_NAME_.'"');
                    $table_keys = array_column($table_keys, 'column_name');
                    if (!count($tables)) {
                        $table_keys = array_column($table_keys, 'COLUMN_NAME');
                    }
                    if (!in_array('id_lang', $table_keys)) {
                        $table_keys[] = 'id_lang';
                    }

                    array_walk($results, function (&$row, $index, $table_keys)
                    {
                        $row_text = array();
                        foreach ($table_keys as $column) {
                            $row_text[] = $column.' => '.$row[$column];
                        }
                        $row = implode(', ', $row_text);
                    }, $table_keys);

                    $logs[$table] = $results;
                }
            }
        }

        return $logs;
    }

    public static function checkAndFix()
    {
        $db = Db::getInstance();
        $logs = array();

        // Remove doubles in the configuration
        $filtered_configuration = array();
        $result = $db->executeS('SELECT * FROM '._DB_PREFIX_.'configuration');
        foreach ($result as $row) {
            $key = $row['id_shop_group'].'-|-'.$row['id_shop'].'-|-'.$row['name'];
            if (in_array($key, $filtered_configuration)) {
                $query = 'DELETE FROM '._DB_PREFIX_.'configuration WHERE id_configuration = '.(int)$row['id_configuration'];
                $db->execute($query);
                $logs[$query] = 1;
            } else {
                $filtered_configuration[] = $key;
            }
        }
        unset($filtered_configuration);

        // Remove inexisting or monolanguage configuration value from configuration_lang
        $query = 'DELETE FROM `'._DB_PREFIX_.'configuration_lang`
		WHERE `id_configuration` NOT IN (SELECT `id_configuration` FROM `'._DB_PREFIX_.'configuration`)
		OR `id_configuration` IN (SELECT `id_configuration` FROM `'._DB_PREFIX_.'configuration` WHERE name IS NULL OR name = "")';
        if ($db->execute($query)) {
            if ($affected_rows = $db->Affected_Rows()) {
                $logs[$query] = $affected_rows;
            }
        }

        // Simple Cascade Delete
        $queries = self::getCheckAndFixQueries();

        $queries = self::bulle($queries);
        foreach ($queries as $query_array) {
            // If this is a module and the module is not installed, we continue
            if (isset($query_array[4]) && !Module::isInstalled($query_array[4])) {
                continue;
            }

            $query = 'DELETE FROM `'._DB_PREFIX_.$query_array[0].'` WHERE `'.$query_array[1].'` NOT IN (SELECT `'.$query_array[3].'` FROM `'._DB_PREFIX_.$query_array[2].'`)';
            if ($db->execute($query)) {
                if ($affected_rows = $db->Affected_Rows()) {
                    $logs[$query] = $affected_rows;
                }
            }
        }

        // _lang table cleaning
        $tables = $db->executeS('SHOW TABLES LIKE "'._DB_PREFIX_.'%_lang"');
        foreach ($tables as $table) {
            $table_lang = current($table);
            $table = str_replace('_lang', '', $table_lang);
            $id_table = 'id_'.preg_replace('/^'._DB_PREFIX_.'/', '', $table);

            if (!$db->getInstance()->executeS('SHOW COLUMNS FROM `'.$table.'` LIKE "'.$id_table.'"')) {
                continue;
            }

            $query = 'DELETE FROM `'.bqSQL($table_lang).'` WHERE `'.bqSQL($id_table).'` NOT IN (SELECT `'.bqSQL($id_table).'` FROM `'.bqSQL($table).'`)';
            if ($db->execute($query)) {
                if ($affected_rows = $db->Affected_Rows()) {
                    $logs[$query] = $affected_rows;
                }
            }

            $query = 'DELETE FROM `'.bqSQL($table_lang).'` WHERE `id_lang` NOT IN (SELECT `id_lang` FROM `'._DB_PREFIX_.'lang`)';
            if ($db->execute($query)) {
                if ($affected_rows = $db->Affected_Rows()) {
                    $logs[$query] = $affected_rows;
                }
            }
        }

        // _shop table cleaning
        $tables = $db->executeS('SHOW TABLES LIKE "'._DB_PREFIX_.'%_shop"');
        foreach ($tables as $table) {
            $table_shop = current($table);
            $table = str_replace('_shop', '', $table_shop);
            $id_table = 'id_'.preg_replace('/^'._DB_PREFIX_.'/', '', $table);

            if (in_array($table_shop, array(_DB_PREFIX_.'carrier_tax_rules_group_shop'))) {
                continue;
            }

            if (!$db->getInstance()->executeS('SHOW COLUMNS FROM `'.$table.'` LIKE "'.$id_table.'"')) {
                continue;
            }

            $query = 'DELETE FROM `'.bqSQL($table_shop).'` WHERE `'.bqSQL($id_table).'` NOT IN (SELECT `'.bqSQL($id_table).'` FROM `'.bqSQL($table).'`)';
            if ($db->execute($query)) {
                if ($affected_rows = $db->Affected_Rows()) {
                    $logs[$query] = $affected_rows;
                }
            }

            $query = 'DELETE FROM `'.bqSQL($table_shop).'` WHERE `id_shop` NOT IN (SELECT `id_shop` FROM `'._DB_PREFIX_.'shop`)';
            if ($db->execute($query)) {
                if ($affected_rows = $db->Affected_Rows()) {
                    $logs[$query] = $affected_rows;
                }
            }
        }

        // stock_available
        $query = 'DELETE FROM `'._DB_PREFIX_.'stock_available` WHERE `id_shop` NOT IN (SELECT `id_shop` FROM `'._DB_PREFIX_.'shop`) AND `id_shop_group` NOT IN (SELECT `id_shop_group` FROM `'._DB_PREFIX_.'shop_group`)';
        if ($db->execute($query)) {
            if ($affected_rows = $db->Affected_Rows()) {
                $logs[$query] = $affected_rows;
            }
        }

        //chance engine yo InnoDB
        $cont = 0;
        $tables_data = $db->executeS('SELECT table_name FROM INFORMATION_SCHEMA.TABLES
            WHERE engine = "MyISAM" AND table_schema = "'._DB_NAME_.'"');
        $tables = array_column($tables_data, 'table_name');
        if (!count($tables)) {
            $tables = array_column($tables_data, 'TABLE_NAME');
        }
        foreach ($tables as $table) {
            $query = 'ALTER TABLE '.$table.' ENGINE=InnoDB;';
            if ($db->execute($query)) {
                $cont++;
            }
        }
        if ($cont) {
            $logs['ALTER TABLE to ENGINE=InnoDB;'] = $cont;
        }

        Category::regenerateEntireNtree();

        Image::clearTmpDir();
        self::clearAllCaches();

        return $logs;
    }

    public static function getCheckAndFixQueries()
    {
        $append = array();
        if (version_compare('1.7.0.0', _PS_VERSION_, '>')) {
            $append = array(
                array('access', 'id_tab', 'tab', 'id_tab'),
                array('compare_product', 'id_compare', 'compare', 'id_compare'),
                array('compare_product', 'id_product', 'product', 'id_product'),
                array('compare', 'id_customer', 'customer', 'id_customer'),
                array('module_access', 'id_module', 'module', 'id_module'),
                array('scene_category', 'id_scene', 'scene', 'id_scene'),
                array('scene_category', 'id_category', 'category', 'id_category'),
                array('scene_products', 'id_scene', 'scene', 'id_scene'),
                array('scene_products', 'id_product', 'product', 'id_product'),
                array('theme_specific', 'id_theme', 'theme', 'id_theme'),
                array('theme_specific', 'id_shop', 'shop', 'id_shop'),
            );
        }
        return array_merge($append, array(
            // 0 => DELETE FROM __table__, 1 => WHERE __id__ NOT IN, 2 => NOT IN __table__, 3 => __id__ used in the "NOT IN" table, 4 => module_name
            array('access', 'id_profile', 'profile', 'id_profile'),
            array('accessory', 'id_product_1', 'product', 'id_product'),
            array('accessory', 'id_product_2', 'product', 'id_product'),
            array('address_format', 'id_country', 'country', 'id_country'),
            array('attribute', 'id_attribute_group', 'attribute_group', 'id_attribute_group'),
            array('carrier_group', 'id_carrier', 'carrier', 'id_carrier'),
            array('carrier_group', 'id_group', 'group', 'id_group'),
            array('carrier_zone', 'id_carrier', 'carrier', 'id_carrier'),
            array('carrier_zone', 'id_zone', 'zone', 'id_zone'),
            array('cart_cart_rule', 'id_cart', 'cart', 'id_cart'),
            array('cart_product', 'id_cart', 'cart', 'id_cart'),
            array('cart_rule_carrier', 'id_cart_rule', 'cart_rule', 'id_cart_rule'),
            array('cart_rule_carrier', 'id_carrier', 'carrier', 'id_carrier'),
            array('cart_rule_combination', 'id_cart_rule_1', 'cart_rule', 'id_cart_rule'),
            array('cart_rule_combination', 'id_cart_rule_2', 'cart_rule', 'id_cart_rule'),
            array('cart_rule_country', 'id_cart_rule', 'cart_rule', 'id_cart_rule'),
            array('cart_rule_country', 'id_country', 'country', 'id_country'),
            array('cart_rule_group', 'id_cart_rule', 'cart_rule', 'id_cart_rule'),
            array('cart_rule_group', 'id_group', 'group', 'id_group'),
            array('cart_rule_product_rule_group', 'id_cart_rule', 'cart_rule', 'id_cart_rule'),
            array('cart_rule_product_rule', 'id_product_rule_group', 'cart_rule_product_rule_group', 'id_product_rule_group'),
            array('cart_rule_product_rule_value', 'id_product_rule', 'cart_rule_product_rule', 'id_product_rule'),
            array('category_group', 'id_category', 'category', 'id_category'),
            array('category_group', 'id_group', 'group', 'id_group'),
            array('category_product', 'id_category', 'category', 'id_category'),
            array('category_product', 'id_product', 'product', 'id_product'),
            array('cms', 'id_cms_category', 'cms_category', 'id_cms_category'),
            array('cms_block', 'id_cms_category', 'cms_category', 'id_cms_category', 'blockcms'),
            array('cms_block_page', 'id_cms', 'cms', 'id_cms', 'blockcms'),
            array('cms_block_page', 'id_cms_block', 'cms_block', 'id_cms_block', 'blockcms'),
            array('connections', 'id_shop_group', 'shop_group', 'id_shop_group'),
            array('connections', 'id_shop', 'shop', 'id_shop'),
            array('connections_page', 'id_connections', 'connections', 'id_connections'),
            array('connections_page', 'id_page', 'page', 'id_page'),
            array('connections_source', 'id_connections', 'connections', 'id_connections'),
            array('customer', 'id_shop_group', 'shop_group', 'id_shop_group'),
            array('customer', 'id_shop', 'shop', 'id_shop'),
            array('customer_group', 'id_group', 'group', 'id_group'),
            array('customer_group', 'id_customer', 'customer', 'id_customer'),
            array('customer_message', 'id_customer_thread', 'customer_thread', 'id_customer_thread'),
            array('customer_thread', 'id_shop', 'shop', 'id_shop'),
            array('customization', 'id_cart', 'cart', 'id_cart'),
            array('customization_field', 'id_product', 'product', 'id_product'),
            array('customized_data', 'id_customization', 'customization', 'id_customization'),
            array('delivery', 'id_shop', 'shop', 'id_shop'),
            array('delivery', 'id_shop_group', 'shop_group', 'id_shop_group'),
            array('delivery', 'id_carrier', 'carrier', 'id_carrier'),
            array('delivery', 'id_zone', 'zone', 'id_zone'),
            array('editorial', 'id_shop', 'shop', 'id_shop', 'editorial'),
            array('favorite_product', 'id_product', 'product', 'id_product', 'favoriteproducts'),
            array('favorite_product', 'id_customer', 'customer', 'id_customer', 'favoriteproducts'),
            array('favorite_product', 'id_shop', 'shop', 'id_shop', 'favoriteproducts'),
            array('feature_product', 'id_feature', 'feature', 'id_feature'),
            array('feature_product', 'id_product', 'product', 'id_product'),
            array('feature_value', 'id_feature', 'feature', 'id_feature'),
            array('group_reduction', 'id_group', 'group', 'id_group'),
            array('group_reduction', 'id_category', 'category', 'id_category'),
            array('homeslider', 'id_shop', 'shop', 'id_shop', 'homeslider'),
            array('homeslider', 'id_homeslider_slides', 'homeslider_slides', 'id_homeslider_slides', 'homeslider'),
            array('hook_module', 'id_hook', 'hook', 'id_hook'),
            array('hook_module', 'id_module', 'module', 'id_module'),
            array('hook_module_exceptions', 'id_hook', 'hook', 'id_hook'),
            array('hook_module_exceptions', 'id_module', 'module', 'id_module'),
            array('hook_module_exceptions', 'id_shop', 'shop', 'id_shop'),
            array('image', 'id_product', 'product', 'id_product'),
            array('message', 'id_cart', 'cart', 'id_cart'),
            array('message_readed', 'id_message', 'message', 'id_message'),
            array('message_readed', 'id_employee', 'employee', 'id_employee'),
            array('module_access', 'id_profile', 'profile', 'id_profile'),
            array('module_country', 'id_module', 'module', 'id_module'),
            array('module_country', 'id_country', 'country', 'id_country'),
            array('module_country', 'id_shop', 'shop', 'id_shop'),
            array('module_currency', 'id_module', 'module', 'id_module'),
            //array('module_currency', 'id_currency', 'currency', 'id_currency'),
            array('module_currency', 'id_shop', 'shop', 'id_shop'),
            array('module_group', 'id_module', 'module', 'id_module'),
            array('module_group', 'id_group', 'group', 'id_group'),
            array('module_group', 'id_shop', 'shop', 'id_shop'),
            array('module_preference', 'id_employee', 'employee', 'id_employee'),
            array('orders', 'id_shop', 'shop', 'id_shop'),
            array('orders', 'id_shop_group', 'group_shop', 'id_shop_group'),
            array('order_carrier', 'id_order', 'orders', 'id_order'),
            array('order_cart_rule', 'id_order', 'orders', 'id_order'),
            array('order_detail', 'id_order', 'orders', 'id_order'),
            array('order_detail_tax', 'id_order_detail', 'order_detail', 'id_order_detail'),
            array('order_history', 'id_order', 'orders', 'id_order'),
            array('order_invoice', 'id_order', 'orders', 'id_order'),
            array('order_invoice_payment', 'id_order', 'orders', 'id_order'),
            array('order_invoice_tax', 'id_order_invoice', 'order_invoice', 'id_order_invoice'),
            array('order_return', 'id_order', 'orders', 'id_order'),
            array('order_return_detail', 'id_order_return', 'order_return', 'id_order_return'),
            array('order_slip', 'id_order', 'orders', 'id_order'),
            array('order_slip_detail', 'id_order_slip', 'order_slip', 'id_order_slip'),
            array('pack', 'id_product_pack', 'product', 'id_product'),
            array('pack', 'id_product_item', 'product', 'id_product'),
            array('page', 'id_page_type', 'page_type', 'id_page_type'),
            array('page_viewed', 'id_shop', 'shop', 'id_shop'),
            array('page_viewed', 'id_shop_group', 'shop_group', 'id_shop_group'),
            array('page_viewed', 'id_date_range', 'date_range', 'id_date_range'),
            array('product_attachment', 'id_attachment', 'attachment', 'id_attachment'),
            array('product_attachment', 'id_product', 'product', 'id_product'),
            array('product_attribute', 'id_product', 'product', 'id_product'),
            array('product_attribute_combination', 'id_product_attribute', 'product_attribute', 'id_product_attribute'),
            array('product_attribute_combination', 'id_attribute', 'attribute', 'id_attribute'),
            array('product_attribute_image', 'id_image', 'image', 'id_image'),
            array('product_attribute_image', 'id_product_attribute', 'product_attribute', 'id_product_attribute'),
            array('product_carrier', 'id_product', 'product', 'id_product'),
            array('product_carrier', 'id_shop', 'shop', 'id_shop'),
            array('product_carrier', 'id_carrier_reference', 'carrier', 'id_reference'),
            array('product_country_tax', 'id_product', 'product', 'id_product'),
            array('product_country_tax', 'id_country', 'country', 'id_country'),
            array('product_country_tax', 'id_tax', 'tax', 'id_tax'),
            array('product_download', 'id_product', 'product', 'id_product'),
            array('product_group_reduction_cache', 'id_product', 'product', 'id_product'),
            array('product_group_reduction_cache', 'id_group', 'group', 'id_group'),
            array('product_sale', 'id_product', 'product', 'id_product'),
            array('product_supplier', 'id_product', 'product', 'id_product'),
            array('product_supplier', 'id_supplier', 'supplier', 'id_supplier'),
            array('product_tag', 'id_product', 'product', 'id_product'),
            array('product_tag', 'id_tag', 'tag', 'id_tag'),
            array('range_price', 'id_carrier', 'carrier', 'id_carrier'),
            array('range_weight', 'id_carrier', 'carrier', 'id_carrier'),
            array('referrer_cache', 'id_referrer', 'referrer', 'id_referrer'),
            array('referrer_cache', 'id_connections_source', 'connections_source', 'id_connections_source'),
            array('search_index', 'id_product', 'product', 'id_product'),
            array('search_word', 'id_lang', 'lang', 'id_lang'),
            array('search_word', 'id_shop', 'shop', 'id_shop'),
            array('shop_url', 'id_shop', 'shop', 'id_shop'),
            array('specific_price_priority', 'id_product', 'product', 'id_product'),
            array('stock', 'id_warehouse', 'warehouse', 'id_warehouse'),
            array('stock', 'id_product', 'product', 'id_product'),
            array('stock_available', 'id_product', 'product', 'id_product'),
            array('stock_mvt', 'id_stock', 'stock', 'id_stock'),
            array('tab_module_preference', 'id_employee', 'employee', 'id_employee'),
            array('tab_module_preference', 'id_tab', 'tab', 'id_tab'),
            array('tax_rule', 'id_country', 'country', 'id_country'),
            array('warehouse_carrier', 'id_warehouse', 'warehouse', 'id_warehouse'),
            array('warehouse_carrier', 'id_carrier', 'carrier', 'id_carrier'),
            array('warehouse_product_location', 'id_product', 'product', 'id_product'),
            array('warehouse_product_location', 'id_warehouse', 'warehouse', 'id_warehouse'),
        ));
    }

    public static function cleanAndOptimize($cart_range, $cart_rule_range, $connections_range, $stats_search, $ps_log, $mails)
    {
        $db = Db::getInstance();
        $logs = array();
        if (!$cart_range) {
            $cart_range = 30;
        }
        if (!$cart_rule_range) {
            $cart_rule_range = 30;
        }
        if (!$connections_range) {
            $connections_range = 30;
        }
        if (!$stats_search) {
            $stats_search = 60;
        }
        if (!$ps_log) {
            $ps_log = 180;
        }
        if (!$mails) {
            $mails = 180;
        }

        $query = '
		DELETE FROM `'._DB_PREFIX_.'cart`
		WHERE id_cart NOT IN (SELECT id_cart FROM `'._DB_PREFIX_.'orders`)
		AND date_add < "'.pSQL(date('Y-m-d', strtotime('-'.(int)$cart_range.' day'))).'"';
        if ($db->execute($query)) {
            if ($affected_rows = $db->Affected_Rows()) {
                $logs[$query] = $affected_rows;
            }
        }

        $query = '
		DELETE FROM `'._DB_PREFIX_.'cart_rule`
		WHERE (
			active = 0
			OR quantity = 0
			OR date_to < "'.pSQL(date('Y-m-d')).'"
		)
		AND date_upd < "'.pSQL(date('Y-m-d', strtotime('-'.(int)$cart_rule_range.' day'))).'"';
        if ($db->execute($query)) {
            if ($affected_rows = $db->Affected_Rows()) {
                $logs[$query] = $affected_rows;
            }
        }

        $query = '
		DELETE FROM `'._DB_PREFIX_.'connections`
		WHERE date_add < "'.pSQL(date('Y-m-d', strtotime('-'.(int)$connections_range.' day'))).'"';
        if ($db->execute($query)) {
            if ($affected_rows = $db->Affected_Rows()) {
                $logs[$query] = $affected_rows;
            }
        }
        $query = '
		DELETE FROM `'._DB_PREFIX_.'connections_page`
		WHERE id_connections NOT IN (SELECT id_connections FROM `'._DB_PREFIX_.'connections`)';
        if ($db->execute($query)) {
            if ($affected_rows = $db->Affected_Rows()) {
                $logs[$query] = $affected_rows;
            }
        }
        $query = '
		DELETE FROM `'._DB_PREFIX_.'connections_source`
		WHERE id_connections NOT IN (SELECT id_connections FROM `'._DB_PREFIX_.'connections`)';
        if ($db->execute($query)) {
            if ($affected_rows = $db->Affected_Rows()) {
                $logs[$query] = $affected_rows;
            }
        }

        $query = '
        DELETE FROM `'._DB_PREFIX_.'statssearch`
        WHERE date_add < "'.pSQL(date('Y-m-d', strtotime('-'.(int)$stats_search.' day'))).'"';
        if ($db->execute($query)) {
            if ($affected_rows = $db->Affected_Rows()) {
                $logs[$query] = $affected_rows;
            }
        }

        $query = '
        DELETE FROM `'._DB_PREFIX_.'log`
        WHERE date_add < "'.pSQL(date('Y-m-d', strtotime('-'.(int)$ps_log.' day'))).'"';
        if ($db->execute($query)) {
            if ($affected_rows = $db->Affected_Rows()) {
                $logs[$query] = $affected_rows;
            }
        }

        $query = '
        DELETE FROM `'._DB_PREFIX_.'mail`
        WHERE date_add < "'.pSQL(date('Y-m-d', strtotime('-'.(int)$mails.' day'))).'"';
        if ($db->execute($query)) {
            if ($affected_rows = $db->Affected_Rows()) {
                $logs[$query] = $affected_rows;
            }
        }

        $query = '
        DELETE FROM `'._DB_PREFIX_.'guest`
        WHERE (id_customer = 0 OR id_customer NOT IN (SELECT id_customer FROM `'._DB_PREFIX_.'customer`))
            AND id_guest NOT IN (SELECT id_guest FROM `'._DB_PREFIX_.'cart`)
            AND id_guest NOT IN (SELECT id_guest FROM `'._DB_PREFIX_.'connections`)';
        if ($db->execute($query)) {
            if ($affected_rows = $db->Affected_Rows()) {
                $logs[$query] = $affected_rows;
            }
        }

        $parents = $db->executeS('SELECT DISTINCT id_parent FROM '._DB_PREFIX_.'tab');
        foreach ($parents as $parent) {
            $children = $db->executeS('SELECT id_tab FROM '._DB_PREFIX_.'tab WHERE id_parent = '.(int)$parent['id_parent'].' ORDER BY IF(class_name IN ("AdminHome", "AdminDashboard"), 1, 2), position ASC');
            $i = 1;
            foreach ($children as $child) {
                $query = 'UPDATE '._DB_PREFIX_.'tab SET position = '.(int)($i++).' WHERE id_tab = '.(int)$child['id_tab'].' AND id_parent = '.(int)$parent['id_parent'];
                if ($db->execute($query)) {
                    if ($affected_rows = $db->Affected_Rows()) {
                        $logs[$query] = $affected_rows;
                    }
                }
            }
        }

        return $logs;
    }

    protected static function bulle($array)
    {
        $sorted = false;
        $size = count($array);
        while (!$sorted) {
            $sorted = true;
            for ($i = 0; $i < $size - 1; ++$i) {
                for ($j = $i + 1; $j < $size; ++$j) {
                    if ($array[$i][2] == $array[$j][0]) {
                        $tmp = $array[$i];
                        $array[$i] = $array[$j];
                        $array[$j] = $tmp;
                        $sorted = false;
                    }
                }
            }
        }
        return $array;
    }

    public static function getCatalogRelatedTables()
    {
        $append = array();
        if (version_compare('1.7.0.0', _PS_VERSION_, '>')) {
            $append = array(
                'compare_product',
                'scene_products',
                'scene',
                'scene_category',
                'scene_lang',
                'scene_products',
                'scene_shop',
            );
        }
        return array_merge($append, array(
            'product',
            'product_shop',
            'feature_product',
            'product_lang',
            'category_product',
            'product_tag',
            'tag',
            'image',
            'image_lang',
            'image_shop',
            'specific_price',
            'specific_price_priority',
            'product_carrier',
            'cart_product',
            'product_attachment',
            'product_country_tax',
            'product_download',
            'product_group_reduction_cache',
            'product_sale',
            'product_supplier',
            'warehouse_product_location',
            'stock',
            'stock_available',
            'stock_mvt',
            'customization',
            'customization_field',
            'supply_order_detail',
            'attribute_impact',
            'product_attribute',
            'product_attribute_shop',
            'product_attribute_combination',
            'product_attribute_image',
            'attribute_impact',
            'attribute_lang',
            'attribute_group',
            'attribute_group_lang',
            'attribute_group_shop',
            'attribute_shop',
            'product_attribute',
            'product_attribute_shop',
            'product_attribute_combination',
            'product_attribute_image',
            'stock_available',
            'manufacturer',
            'manufacturer_lang',
            'manufacturer_shop',
            'supplier',
            'supplier_lang',
            'supplier_shop',
            'customization',
            'customization_field',
            'customization_field_lang',
            'customized_data',
            'feature',
            'feature_lang',
            'feature_product',
            'feature_shop',
            'feature_value',
            'feature_value_lang',
            'pack',
            'search_index',
            'search_word',
            'specific_price',
            'specific_price_priority',
            'specific_price_rule',
            'specific_price_rule_condition',
            'specific_price_rule_condition_group',
            'stock',
            'stock_available',
            'stock_mvt',
            'warehouse',
        ));
    }

    public static function getSalesRelatedTables()
    {
        return array(
            'customer',
            'cart',
            'cart_product',
            'connections',
            'connections_page',
            'connections_source',
            'customer_group',
            'customer_message',
            'customer_message_sync_imap',
            'customer_thread',
            'guest',
            'message',
            'message_readed',
            'orders',
            'order_carrier',
            'order_cart_rule',
            'order_detail',
            'order_detail_tax',
            'order_history',
            'order_invoice',
            'order_invoice_payment',
            'order_invoice_tax',
            'order_message',
            'order_message_lang',
            'order_payment',
            'order_return',
            'order_return_detail',
            'order_slip',
            'order_slip_detail',
            'page',
            'page_type',
            'page_viewed',
            'product_sale',
            'referrer_cache',
        );
    }

    protected static function clearAllCaches()
    {
        $index = file_exists(_PS_TMP_IMG_DIR_.'index.php') ? file_get_contents(_PS_TMP_IMG_DIR_.'index.php') : '';
        Tools::deleteDirectory(_PS_TMP_IMG_DIR_, false);
        file_put_contents(_PS_TMP_IMG_DIR_.'index.php', $index);
        Context::getContext()->smarty->clearAllCache();
    }

    public static function getLink($type, $module = null)
    {
        switch ($type) {
            case 'author':
                return isset($module->module_key) && $module->module_key ? $module->addons_author_link : $module->author_link;
            case 'module':
                return isset($module->module_key) && $module->module_key
                    ? 'https://addons.prestashop.com/product.php?id_product=' . $module->addons_module_id
                    : 'https://www.rolige.com/index.php?controller=product&id_product=' . $module->module_id;
            case 'partner':
                return Context::getContext()->language->iso_code === 'es'
                    ? 'https://www.prestashop.com/es/expertos/rolige'
                    : 'https://www.prestashop.com/en/experts/rolige';
            case 'support':
                return isset($module->module_key) && $module->module_key
                    ? 'https://addons.prestashop.com/contact-form.php?id_product=' . $module->addons_module_id
                    : 'https://www.rolige.com/index.php?controller=contact&id_product=' . $module->module_id;
            case 'rate':
                return isset($module->module_key) && $module->module_key
                    ? 'https://addons.prestashop.com/ratings.php'
                    : 'https://www.rolige.com/index.php?controller=product&id_product=' . $module->module_id;
        }

        return false;
    }

    public static function getProductsMarketing($module_name, $source)
    {
        static $response = null;

        if ($response !== null) {
            return $response;
        }

        $config = 'RG_MARKETING_' . Tools::strtoupper($source) . '_REQUEST';
        $data = Configuration::getGlobalValue($config);
        $json = Tools::jsonDecode($data, true);

        if (!isset($json['next_request']) || (int) $json['next_request'] < time()) {
            $params = array(
                'key' => '764438a9bd64fdae8e5b1065d4741eab',
                'params' => array(
                    'module' => $module_name,
                    'domain' => Tools::getServerName(),
                    'source' => $source,
                    'country_iso_code' => Country::getIsoById((int) Configuration::get('PS_COUNTRY_DEFAULT')),
                    'currency_iso_code' => Currency::getCurrencyInstance((int) Configuration::get('PS_CURRENCY_DEFAULT'))->iso_code,
                    'lang_iso_code' => Context::getContext()->language->iso_code,
                ),
            );

            $curl = self::rgCurl('productsmarketing', $params);
            $json = Tools::jsonDecode($curl, true);

            if (isset($json['products']) && count($json['products'])) {
                Configuration::updateGlobalValue($config, $curl);
            }
        }

        return $response = isset($json['products']) ? $json['products'] : array();
    }

    public static function getNewModuleVersion($module_name, $current_version)
    {
        static $response = null;

        if ($response !== null) {
            return $response;
        }

        $data = Configuration::getGlobalValue('LAST_VERSION');
        $json = Tools::jsonDecode($data, true);

        if (isset($json['version']) &&
            version_compare($json['version'], $current_version, '>') &&
            (time() - $json['checked_on']) < 86400
        ) {
            return $response = $json['version'];
        }

        $params = array(
            'key' => '77919fabe4f694c3aeed566b529c5a60',
            'params' => array(
                'module' => $module_name,
            ),
        );

        $curl = self::rgCurl('moduleinfo', $params);
        $json = Tools::jsonDecode($curl, true);

        if (isset($json['version'])) {
            Configuration::updateGlobalValue(
                'LAST_VERSION',
                Tools::jsonEncode(array('version' => $json['version'], 'checked_on' => time()))
            );

            if (version_compare($json['version'], $current_version, '>')) {
                return $response = $json['version'];
            }
        }

        return $response = false;
    }

    public static function rgCurl($service, $params)
    {
        $ch = curl_init('https://www.rolige.com/modules/rg_webservice/api/' . $service);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, Tools::jsonEncode($params));

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }
}
