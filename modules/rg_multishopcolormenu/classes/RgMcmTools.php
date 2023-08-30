<?php
/**
 * Multishop Color Menu
 *
 * @author    Rolige <www.rolige.com>
 * @copyright 2011-2019 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 */

class RgMcmTools
{

    public static function getProducts($id_product, $field, $compare_value)
    {
        $sql = new DbQuery();
        $sql->select('id_product');
        $sql->from('product');
        $sql->where('`active` = 1 AND `'.pSQL($field).'` LIKE "'.pSQL($compare_value).'" AND `id_product` != '.(int)$id_product);

        $result = Db::getInstance()->executeS($sql);

        return array_column($result, 'id_product');
    }

    public static function getPageName()
    {
        $page = 'unknown';

        if (method_exists(Context::getContext()->controller, 'getPageName')) {
            $page = Context::getContext()->controller->getPageName();
        } elseif (method_exists(Context::getContext()->smarty, 'getTemplateVars')) {
            $page = Context::getContext()->smarty->getTemplateVars('page_name');
        } elseif (Context::getContext()->controller->php_self) {
            $page = Context::getContext()->controller->php_self;
        }

        return $page;
    }

    public static function getLink($type, $module = null)
    {
        switch ($type) {
            case 'author':
                return isset($module->module_key) && $module->module_key ? $module->addons_author_link : $module->author_link;
            case 'module':
                return isset($module->module_key) && $module->module_key ? 'https://addons.prestashop.com/product.php?id_product='.$module->addons_module_id : 'https://www.rolige.com/index.php?controller=product&id_product='.$module->module_id;
            case 'partner':
                return Context::getContext()->language->iso_code === 'es' ? 'https://www.prestashop.com/es/expertos/agencias-web/rolige' : 'https://www.prestashop.com/en/experts/web-agencies/rolige';
            case 'support':
                return isset($module->module_key) && $module->module_key ? 'https://addons.prestashop.com/contact-form.php?id_product='.$module->addons_module_id : 'https://www.rolige.com/index.php?controller=contact&id_product='.$module->module_id;
            case 'rate':
                return isset($module->module_key) && $module->module_key ? 'https://addons.prestashop.com/ratings.php' : 'https://www.rolige.com/index.php?controller=product&id_product='.$module->module_id;
        }

        return false;
    }

    public static function getProductsMarketing($module_name, $source)
    {
        static $response = null;

        if ($response !== null) {
            return $response;
        }

        $config = 'RG_MARKETING_'.Tools::strtoupper($source).'_REQUEST';
        $data = Configuration::getGlobalValue($config);
        $json = Tools::jsonDecode($data, true);

        if (!isset($json['next_request']) || (int)$json['next_request'] < time()) {
            $params = array(
                'key' => '764438a9bd64fdae8e5b1065d4741eab',
                'params' => array(
                    'module' => $module_name,
                    'domain' => Tools::getServerName(),
                    'source' => $source,
                    'country_iso_code' => Country::getIsoById((int)Configuration::get('PS_COUNTRY_DEFAULT')),
                    'currency_iso_code' => Currency::getCurrencyInstance((int)Configuration::get('PS_CURRENCY_DEFAULT'))->iso_code,
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

        $data = RgMcmConfig::getGlobal('LAST_VERSION');
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
            RgMcmConfig::updateGlobal(
                    'LAST_VERSION', Tools::jsonEncode(array('version' => $json['version'], 'checked_on' => time()))
            );

            if (version_compare($json['version'], $current_version, '>')) {
                return $response = $json['version'];
            }
        }

        return $response = false;
    }

    public static function rgCurl($service, $params)
    {
        $ch = curl_init('https://www.rolige.com/modules/rg_webservice/api/'.$service);
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
