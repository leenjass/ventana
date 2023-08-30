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

function upgrade_module_5_1_0($object)
{
    $wkSqlQry = [
        'ALTER TABLE `' . _DB_PREFIX_ . 'wk_product_options_config_lang`
            ADD COLUMN `placeholder` text',
        'ALTER TABLE `' . _DB_PREFIX_ . 'wk_product_options_config`
            ADD COLUMN  `text_limit` int(11) unsigned NOT NULL DEFAULT 0',
        'ALTER TABLE `' . _DB_PREFIX_ . 'wk_product_options_config_shop`
            ADD COLUMN  `text_limit` int(11) unsigned NOT NULL DEFAULT 0',
        'ALTER TABLE `' . _DB_PREFIX_ . 'wk_product_options_config`
            ADD COLUMN  `is_bulk_enabled` tinyint(1) unsigned NOT NULL DEFAULT 0',
        'ALTER TABLE `' . _DB_PREFIX_ . 'wk_product_options_config_shop`
            ADD COLUMN  `is_bulk_enabled` tinyint(1) unsigned NOT NULL DEFAULT 0',
        'ALTER TABLE `' . _DB_PREFIX_ . 'wk_product_wise_options`
            ADD COLUMN  `id_product_attribute` int(11) unsigned NOT NULL DEFAULT 0',
    ];
    $wkDbInstance = Db::getInstance();
    $wkSuccess = true;
    foreach ($wkSqlQry as $wkQuery) {
        $wkSuccess &= $wkDbInstance->execute(trim($wkQuery));
    }
    if ($wkSuccess) {
        $object->uninstallOverrides()
        && $object->installOverrides()
        && $object->registerHook('displayProductOptionCart')
        && $object->registerHook('displayHeader');
        $hookId = Hook::getIdByName('displayHeader');
        if ($hookId) {
            $object->updatePosition($hookId, 0, 1);
        }
    }

    return true;
}
