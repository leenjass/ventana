<?php
/**
 * Multishop Color Menu
 *
 * @author    Rolige <www.rolige.com>
 * @copyright 2011-2019 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 */

class RgMcmConfig
{
    const PREFIX = 'RgMcm';

    public static function get($key, $idLang = null, $idShopGroup = null, $idShop = null, $default = false)
    {
        return Configuration::get(self::prefix('config').$key, $idLang, $idShopGroup, $idShop, $default);
    }

    public static function getGlobal($key, $idLang = null)
    {
        return self::get($key, $idLang, 0, 0);
    }

    public static function update($key, $values, $html = false, $idShopGroup = null, $idShop = null)
    {
        return Configuration::updateValue(self::prefix('config').$key, $values, $html, $idShopGroup, $idShop);
    }

    public static function updateGlobal($key, $values, $html = false)
    {
        return self::update($key, $values, $html, 0, 0);
    }

    public static function delete($key)
    {
        return Configuration::deleteByName(self::prefix('config').$key);
    }

    /**
     * Gets all the configuration including the prefix, except the lang fields
     *
     * @return array
     */
    public static function getAll()
    {
        static $cache = null;

        if ($cache === null) {
            $sql = new DbQuery();
            $sql->select('`name`');
            $sql->from('configuration');
            $sql->where('`name` LIKE "'.pSQL(self::prefix('config')).'%"');
            $sql->groupBy('`name`');

            $keys = array_column(Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql), 'name');
            $cache = Configuration::getMultiple($keys);
        }

        return $cache;
    }

    public static function install($module)
    {
        RgMcmConfig::update('SETTINGS_SINGLE_COLOR', '#ffff00');
        RgMcmConfig::update('SETTINGS_SINGLE_BACK_COLOR', '#ff0000');

        return true;
    }

    public static function uninstall($module)
    {
        foreach (array_keys(self::getAll()) as $key) {
            Configuration::deleteByName($key);
        }

        return true;
    }

    public static function prefix($type = 'class')
    {
        static $cache = array();

        if (isset($cache[$type])) {
            return $cache[$type];
        }

        switch ($type) {
            case 'class':
                return $cache[$type] = self::PREFIX;
            case 'config':
                return $cache[$type] = Tools::strtoupper(self::PREFIX.'_');
            case 'db':
                return $cache[$type] = Tools::strtolower(self::PREFIX.'_');
            case 'dbfull':
                return $cache[$type] = _DB_PREFIX_.Tools::strtolower(self::PREFIX.'_');
        }

        return false;
    }
}
