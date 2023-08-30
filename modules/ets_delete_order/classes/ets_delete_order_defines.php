<?php
/**
 * 2007-2022 ETS-Soft
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 wesite only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please contact us for extra customization service at an affordable price
 *
 * @author ETS-Soft <etssoft.jsc@gmail.com>
 * @copyright  2007-2022 ETS-Soft
 * @license    Valid for 1 website (or project) for each purchase of license
 *  International Registered Trademark & Property of ETS-Soft
 */
 
 if (!defined('_PS_VERSION_'))
    exit;
class Ets_delete_order_defines
{
    public static $instance;
    public function __construct()
    {
        $this->context = Context::getContext();
        if (is_object($this->context->smarty)) {
            $this->smarty = $this->context->smarty;
        }
    }
    public static function getInstance()
    {
        if (!(isset(self::$instance)) || !self::$instance) {
            self::$instance = new Ets_delete_order_defines();
        }
        return self::$instance;
    }
    public static function installDb()
    {
        $fieldsorders = Db::getInstance()->ExecuteS('DESCRIBE ' . _DB_PREFIX_ . 'orders');
        $check_add_deleted = true;
        foreach ($fieldsorders as $field) {
            if ($field['Field'] == 'deleted') {
                $check_add_deleted = false;
                break;
            }
        }
        if ($check_add_deleted)
            Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'orders` ADD  `deleted` INT(1) default "0"');
        return true;
    }
    public static function deleteordertrash($id_order)
    {
        if(is_array($id_order))
        {
            Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'orders` SET deleted=1 WHERE id_order IN ('.implode(',',array_map('intval',$id_order)).')');
        }
        else
            Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'orders` set deleted=1 WHERE id_order=' . (int)$id_order);
    }
    public static function restoreorder($id_order)
    {
        if(is_array($id_order))
        {
            Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'orders` SET deleted=0 WHERE id_order IN ('.implode(',',array_map('intval',$id_order)).')');
        }
        else
            Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'orders` set deleted=0 WHERE id_order=' . (int)$id_order);
    }
}