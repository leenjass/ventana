<?php
/**
 * Rolige PrestaShop Cleaner Extra
 *
 * @author    Rolige <www.rolige.com>
 * @copyright 2011-2019 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 */

require_once dirname(__FILE__).'/../../../config/config.inc.php';
require_once dirname(__FILE__).'/../rg_pscleanerextra.php';

$module = Module::getInstanceByName('rg_pscleanerextra');

if ($module->active && Tools::getValue('token') == $module->secure_key) {
    $conf = '';

    $logs = RgPSCETools::cleanAndOptimize((int)Tools::getValue('cart_range'), (int)Tools::getValue('cart_rule_range'), (int)Tools::getValue('connections'), (int)Tools::getValue('stats_search'), (int)Tools::getValue('ps_log'), (int)Tools::getValue('mails'));
    if (count($logs)) {
        $conf .= $module->l('The following queries successfully cleaned your database:', 'RgPSCEModuleFormDatabase').'<br /><ul>';
        foreach ($logs as $query => $entries) {
            $conf .= '<li>'.Tools::htmlentitiesUTF8($query).'<br />'.sprintf($module->l('%d line(s)', 'RgPSCEModuleFormDatabase'), $entries).'</li>';
        }
        $conf .= '</ul>';
    } else {
        $conf .= $module->l('Nothing that need to be cleaned', 'RgPSCEModuleFormDatabase').'<br />';
    }

    if ((int)Tools::getValue('integrity')) {
        $logs = RgPSCETools::checkAndFix();
        if (count($logs)) {
            $conf .= $module->l('The following queries successfully fixed broken data:', 'RgPSCEModuleFormDatabase').'<br /><ul>';
            foreach ($logs as $query => $entries) {
                $conf .= '<li>'.Tools::htmlentitiesUTF8($query).'<br />'.sprintf($module->l('%d line(s)', 'RgPSCEModuleFormDatabase'), $entries).'</li>';
            }
            $conf .= '</ul>';
        } else {
            $conf .= $module->l('Nothing that need to be fixed', 'RgPSCEModuleFormDatabase');
        }
    }
    
    die($conf);
}

die(0);
