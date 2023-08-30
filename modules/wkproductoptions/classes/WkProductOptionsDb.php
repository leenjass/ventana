<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.txt
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to a newer
 * versions in the future. If you wish to customize this module for your
 * needs please refer to CustomizationPolicy.txt file inside our module for more information.
 *
 * @author Webkul IN
 * @copyright Since 2010 Webkul
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */
class WkProductOptionsDb
{
    public function getModuleSql()
    {
        return [
            'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . "wk_product_options_config` (
                `id_wk_product_options_config` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `option_type` int(10) unsigned NOT NULL,
                `price` decimal(20,6) NOT NULL,
                `price_type` int(10) unsigned NOT NULL,
                `tax_type` int(10) unsigned NOT NULL,
                `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
                `is_bulk_enabled` tinyint(1) unsigned NOT NULL DEFAULT '0',
                `user_input` tinyint(1) unsigned NOT NULL DEFAULT '0',
                `pre_selected` tinyint(1) unsigned NOT NULL DEFAULT '0',
                `text_limit` int(11) unsigned NOT NULL DEFAULT '0',
                `is_required` tinyint(1) unsigned NOT NULL DEFAULT '0',
                `multiselect` tinyint(1) unsigned NOT NULL DEFAULT '0',
                `position` int(10) unsigned NOT NULL DEFAULT '0',
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                PRIMARY KEY (`id_wk_product_options_config`)
            ) ENGINE=" . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;',
            'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . "wk_product_options_config_shop` (
                `id_wk_product_options_config` int(10) unsigned NOT NULL,
                `option_type` int(10) unsigned NOT NULL,
                `price` decimal(20,6) NOT NULL,
                `price_type` int(10) unsigned NOT NULL,
                `tax_type` int(10) unsigned NOT NULL,
                `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
                `is_bulk_enabled` tinyint(1) unsigned NOT NULL DEFAULT '0',
                `user_input` tinyint(1) unsigned NOT NULL DEFAULT '0',
                `pre_selected` tinyint(1) unsigned NOT NULL DEFAULT '0',
                `is_required` tinyint(1) unsigned NOT NULL DEFAULT '0',
                `text_limit` int(11) unsigned NOT NULL DEFAULT '0',
                `multiselect` tinyint(1) unsigned NOT NULL DEFAULT '0',
                `position` int(10) unsigned NOT NULL DEFAULT '0',
                `id_shop` int(10) unsigned NOT NULL,
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                PRIMARY KEY (`id_wk_product_options_config`, `id_shop`)
            ) ENGINE=" . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;',
            'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'wk_product_options_config_lang` (
                `id_wk_product_options_config` int(10) unsigned NOT NULL,
                `name` text NOT NULL,
                `display_name` text NOT NULL,
                `description` text  NOT NULL,
                `placeholder` text,
                `option_value` text,
                `id_lang` int(10) unsigned NOT NULL,
                `id_shop` int(10) unsigned NOT NULL,
                PRIMARY KEY (`id_wk_product_options_config`, `id_lang`, `id_shop`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;',
            'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'wk_product_options_value` (
                `id_wk_product_options_value` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `option_type` int(10) unsigned NOT NULL,
                `id_option` int(10) unsigned NOT NULL,
                `price` decimal(20,6) NOT NULL,
                `price_type` int(10) unsigned NOT NULL,
                `tax_type` int(10) unsigned NOT NULL,
                PRIMARY KEY (`id_wk_product_options_value`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;',
            'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'wk_product_options_value_shop` (
                `id_wk_product_options_value` int(10) unsigned NOT NULL,
                `option_type` int(10) unsigned NOT NULL,
                `id_option` int(10) unsigned NOT NULL,
                `price` decimal(20,6) NOT NULL,
                `price_type` int(10) unsigned NOT NULL,
                `tax_type` int(10) unsigned NOT NULL,
                `id_shop` int(10) unsigned NOT NULL,
                PRIMARY KEY (`id_wk_product_options_value`, `id_shop`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;',
            'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'wk_product_options_value_lang` (
                `id_wk_product_options_value` int(10) unsigned NOT NULL,
                `option_value` text,
                `id_lang` int(10) unsigned NOT NULL,
                `id_shop` int(10) unsigned NOT NULL,
                PRIMARY KEY (`id_wk_product_options_value`, `id_lang`, `id_shop`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;',
            'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'wk_product_options_conditions` (
                `id_wk_product_options_config` int(10) unsigned NOT NULL,
                `id_currency` int(10) unsigned NOT NULL,
                `id_country` int(10) unsigned NOT NULL,
                `id_group` text NOT NULL,
                `id_customer` int(10) unsigned NOT NULL,
                `products` text NOT NULL,
                `categories` text NOT NULL,
                `from` DATETIME NOT NULL,
                `to` DATETIME NOT NULL,
                `id_shop` int(10) unsigned NOT NULL
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8',
            'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . "wk_product_customer_options` (
                `id_wk_product_customer_options` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `id_customer` int(10) unsigned NOT NULL,
                `id_option` int(10) unsigned NOT NULL,
                `id_cart`  int(10) unsigned NOT NULL,
                `id_order` int(10) unsigned NOT NULL DEFAULT 0,
                `in_cart` int(10) unsigned NOT NULL DEFAULT 0,
                `id_product`  int(10) unsigned NOT NULL,
                `id_product_attribute` int NOT NULL,
                `price_impact` decimal(20,6) NOT NULL,
                `id_customization` int(10) unsigned NOT NULL DEFAULT 0,
                `option_type` int(10) unsigned NOT NULL,
                `price_type` int(10) unsigned NOT NULL,
                `tax_type` int(10) unsigned NOT NULL,
                `user_input` tinyint(1) unsigned NOT NULL DEFAULT '0',
                `multiselect` tinyint(1) unsigned NOT NULL DEFAULT '0',
                `input_color` text NOT NULL,
                `option_value` text NOT NULL,
                `option_title` text NOT NULL,
                `id_shop` int(10) unsigned NOT NULL,
                PRIMARY KEY (`id_wk_product_customer_options`)
            ) ENGINE=" . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8',
            'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'wk_product_wise_options` (
                `id_wk_product_options_config`  int(10) unsigned NOT NULL,
                `active`  int(10) unsigned NOT NULL,
                `id_product` int(10) unsigned NOT NULL DEFAULT 0,
                `id_product_attribute` int(10) unsigned NOT NULL DEFAULT 0,
                `id_shop` int(10) unsigned NOT NULL
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8',
            'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'wk_product_wise_configuration` (
                `id_wk_product_wise_configuration`  int(10) unsigned NOT NULL AUTO_INCREMENT,
                `is_native_customization` int(10) unsigned NOT NULL DEFAULT 0,
                `id_product` int(10) unsigned NOT NULL DEFAULT 0,
                `id_shop` int(10) unsigned NOT NULL,
                PRIMARY KEY (`id_wk_product_wise_configuration`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8',
        ];
    }

    public function createTables()
    {
        if ($sql = $this->getModuleSql()) {
            $objDb = Db::getInstance();
            foreach ($sql as $query) {
                if ($query) {
                    if (!$objDb->execute(trim($query))) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    public function dropTables()
    {
        return Db::getInstance()->execute(
            'DROP TABLE IF EXISTS
            `' . _DB_PREFIX_ . 'wk_product_options_config`,
            `' . _DB_PREFIX_ . 'wk_product_options_config_shop`,
            `' . _DB_PREFIX_ . 'wk_product_options_config_lang`,
            `' . _DB_PREFIX_ . 'wk_product_customer_options`,
            `' . _DB_PREFIX_ . 'wk_product_wise_options`,
            `' . _DB_PREFIX_ . 'wk_product_options_value`,
            `' . _DB_PREFIX_ . 'wk_product_options_value_shop`,
            `' . _DB_PREFIX_ . 'wk_product_options_value_lang`,
            `' . _DB_PREFIX_ . 'wk_product_wise_configuration`,
            `' . _DB_PREFIX_ . 'wk_product_options_conditions`'
        );
    }
}
