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
 * versions in the future. If you wish to customize this module for your needs
 * please refer to CustomizationPolicy.txt file inside our module for more information.
 *
 * @author Webkul IN
 * @copyright Since 2010 Webkul
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_5_0_0($object)
{
    $wkSqlQry = [
        'ALTER TABLE `' . _DB_PREFIX_ . 'wk_product_options_config`
            ADD COLUMN `pre_selected`  tinyint(1) unsigned NOT NULL DEFAULT 0',
        'ALTER TABLE `' . _DB_PREFIX_ . 'wk_product_options_config`
            ADD COLUMN `is_required`  tinyint(1) unsigned NOT NULL DEFAULT 0',
        'ALTER TABLE `' . _DB_PREFIX_ . 'wk_product_options_config_shop`
            ADD COLUMN `pre_selected`  tinyint(1) unsigned NOT NULL DEFAULT 0',
        'ALTER TABLE `' . _DB_PREFIX_ . 'wk_product_options_config_shop`
            ADD COLUMN `is_required`  tinyint(1) unsigned NOT NULL DEFAULT 0',
        'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'wk_product_wise_configuration` (
            `id_wk_product_wise_configuration`  int(10) unsigned NOT NULL AUTO_INCREMENT,
            `is_native_customization` int(10) unsigned NOT NULL DEFAULT 0,
            `id_product` int(10) unsigned NOT NULL DEFAULT 0,
            `id_shop` int(10) unsigned NOT NULL,
            PRIMARY KEY (`id_wk_product_wise_configuration`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8',
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
    ];
    $wkDbInstance = Db::getInstance();
    $wkSuccess = true;
    foreach ($wkSqlQry as $wkQuery) {
        $wkSuccess &= $wkDbInstance->execute(trim($wkQuery));
    }
    if ($wkSuccess) {
        $oldData = Db::getInstance()->executeS(
            'SELECT * FROM `' . _DB_PREFIX_ . 'wk_product_options_config`'
        );
        if (!empty($oldData)) {
            $id = 0;
            foreach ($oldData as $data) {
                if ($data['option_type'] == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_DROPDOWN
                    || $data['option_type'] == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_CHECKBOX
                    || $data['option_type'] == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_RADIO
                ) {
                    $id = $id + 1;
                    $sqlValue = 'INSERT INTO `' . _DB_PREFIX_ . 'wk_product_options_value`
                    (`id_wk_product_options_value`, `id_option`, `option_type`,`price`,`price_type`, `tax_type`)
                    VALUES(' . (int) $id . ',' . (int) $data['id_wk_product_options_config'] . ", '" .
                    (int) $data['option_type'] . "', " .
                    (float) $data['price'] . ', ' . (int) $data['price_type'] . ',' . (int) $data['tax_type'] . ')';
                    $valInserted = $wkDbInstance->execute($sqlValue);
                    if ($valInserted) {
                        $shops = Db::getInstance()->executeS('SELECT `id_shop` FROM `' . _DB_PREFIX_ . 'shop`');
                        foreach ($shops as $shop) {
                            $oldDataShop = Db::getInstance()->executeS(
                                'SELECT * FROM `' . _DB_PREFIX_ . 'wk_product_options_config_shop` WHERE `id_shop` = ' . (int) $shop['id_shop']
                            );
                            if (!empty($oldDataShop) && is_array($oldDataShop)) {
                                $sqlValueShop = 'INSERT INTO `' . _DB_PREFIX_ . 'wk_product_options_value_shop`
                                (`id_wk_product_options_value`, `id_option`, `option_type`,`price`,`price_type`, `tax_type`, `id_shop`)
                                VALUES(' . (int) $id . ',' . (int) $data['id_wk_product_options_config'] . ", '" .
                                (int) $data['option_type'] . "', " .
                                (float) $data['price'] . ', ' . (int) $data['price_type'] . ',' . (int) $data['tax_type'] . ',' . (int) $shop['id_shop'] . ')';
                                $valInsertedShop = $wkDbInstance->execute($sqlValueShop);
                                if ($valInsertedShop) {
                                    $oldDataLang = Db::getInstance()->executeS(
                                        'SELECT * FROM `' . _DB_PREFIX_ . 'wk_product_options_config_lang` 
                                        WHERE `id_wk_product_options_config` = ' . (int) $data['id_wk_product_options_config'] .
                                        ' AND `id_shop` = ' . (int) $shop['id_shop']
                                    );
                                    if (!empty($oldDataLang) && is_array($oldDataLang)) {
                                        foreach ($oldDataLang as $oldLang) {
                                            if ($oldLang['option_value']) {
                                                $optionsValues = json_decode($oldLang['option_value']);
                                                if (!empty($optionsValues) && is_array($optionsValues)) {
                                                    foreach ($optionsValues as $val) {
                                                        $sqlValueLang = 'INSERT INTO `' . _DB_PREFIX_ . 'wk_product_options_value_lang`
                                                        (`id_wk_product_options_value`, `option_value`, `id_lang`, `id_shop`)
                                                        VALUES(' . (int) $id . ',' . pSQL($val) . ", '" .
                                                        (int) $oldLang['id_lang'] . "', " .
                                                        (int) $shop['id_shop'] . ')';
                                                        $wkDbInstance->execute($sqlValueLang);
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $object->uninstallOverrides()
            && $object->installOverrides()
            && $object->registerHook('displayAddBpProductName')
            && $object->registerHook('displayOptionMailContent')
            && Configuration::updateValue(
                'WK_PRODUCT_OPTION_DISPLAY_COLOR_PICKER',
                0
            );
    }

    return true;
}
