<?php
/**
 * 2008 - 2017 Presto-Changeo
 *
 * MODULE Attribute Wizard Pro
 *
 * @version   2.0.9
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
// FILES MODIFIED
// 1. \themes\classic\templates\checkout\_partials\order-confirmation-table.tpl
// 2. \themes\classic\templates\checkout\_partials\cart-detailed-product-line.tpl
// 3. \themes\classic\templates\customer\_partials\order-detail-no-return.tpl
// 4. \themes\classic\templates\customer\_partials\order-detail-return.tpl
// 5. \themes\classic\modules\ps_shoppingcart\modal.tpl
if (!defined('_PS_VERSION_')) {
    exit;
}

if (!in_array('AWPAPI', get_declared_classes())) {
    require_once(dirname(__FILE__) . '/PrestoChangeoClasses/AWPAPI.php');
}

class AttributeWizardPro extends AWPAPI
{

    protected $html = '';
    protected $postErrors = array();
    protected $full_version = 20900;
    private $awp_width;
    private $awp_image_resize;
    private $awp_layered_image;
    private $awp_thumbnail_size;
    private $awp_upload_size;
    private $awp_add_to_cart;
    private $awp_out_of_stock;
    private $awp_pi_display;
    private $awp_second_add;
    private $awp_no_customize;
    private $awp_popup;
    private $awp_fade;
    private $awp_opacity;
    private $awp_popup_width;
    private $awp_popup_top;
    private $awp_popup_left;
    private $awp_popup_image;
    private $awp_popup_image_type;
    private $awp_display_wizard;
    private $awp_display_wizard_field;
    private $awp_display_wizard_value;
    private $awp_disable_all;
    private $awp_disable_hide;
    private $awp_no_tax_impact;
    private $awp_adc_no_attribute;
    private $awp_gd_popup;
    private $awp_collapse_block;
    private $awp_disable_url_hash;
    public $awp_default_group;
    public $awp_default_item;
    public $awp_random;
    public $awp_attributes;
    public $awp_sort_attributes_alphab;
    public $enable_parent_group = false;

    public function __construct()
    {
        $this->name = 'attributewizardpro';
        $this->tab = 'front_office_features';
        $this->version = '2.0.9';

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
        $this->author = 'Presto-Changeo';
        $this->is_eu_compatible = 1;

        $this->bootstrap = true;
        parent::__construct();

        $this->refreshProperties();

        $this->displayName = $this->l('Attribute Wizard Pro');
        $this->description = $this->l('Customized the displays of product attributes, override product combination and create unlimited custom attributes.');
    }

    public function install()
    {
        return (parent::install() &&
            $this->registerHook([
                'backOfficeHeader',
                'header',
                'productfooter',
                'newOrder',
                'footer',
                'awpProduct',
                'actionProductUpdate',
            ]) &&
            $this->registerHook('displayAdminProductsExtra') &&
            $this->installTables() &&
            $this->setConfigValues()
            );
    }

    public function uninstall()
    {
        if (!parent::uninstall()) {
            return false;
        }
        return true;
    }

    private function installTables()
    {
        $installResult = true;

        $query = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'awp_attribute_wizard_pro` (
  			`awp_attributes` MEDIUMTEXT NOT NULL
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;';

        Db::getInstance()->Execute(trim($query));

        $result = Db::getInstance()->ExecuteS("SELECT * FROM `" . _DB_PREFIX_ . "awp_attribute_wizard_pro`");
        if (sizeof($result) == 0) {
            Db::getInstance()->Execute('INSERT INTO `' . _DB_PREFIX_ . 'awp_attribute_wizard_pro` (`awp_attributes`) VALUES ("")');
        }
        $result = Db::getInstance()->ExecuteS("show keys from `" . _DB_PREFIX_ . "product_attribute_combination`");
        if (sizeof($result) == 2) {
            Db::getInstance()->Execute("ALTER TABLE `" . _DB_PREFIX_ . "product_attribute_combination` ADD INDEX ( `id_attribute` ) ");
            Db::getInstance()->Execute("ALTER TABLE `" . _DB_PREFIX_ . "product_attribute_combination` ADD INDEX ( `id_product_attribute` ) ");
        }

        $this->getDbOrderedAttributes();

        $cols = Db::getInstance()->ExecuteS('describe ' . _DB_PREFIX_ . 'cart_product');
        $installed = false;
        $upgraded = false;

        foreach ($cols as $col) {
            if ($col['Field'] == "instructions") {
                $installed = true;
            } elseif ($col['Field'] == "instructions_id") {
                $upgraded = true;
            }
        }
        if (!$installed) {
            Db::getInstance()->Execute("ALTER TABLE `" . _DB_PREFIX_ . "cart_product` ADD `instructions` TEXT  NOT NULL AFTER `quantity` ,ADD `instructions_valid` varchar(50) NOT NULL AFTER `instructions`, ADD `instructions_id` TEXT NOT NULL AFTER `instructions_valid`");
            Db::getInstance()->Execute("ALTER TABLE `" . _DB_PREFIX_ . "order_detail` CHANGE `product_name` `product_name` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
        } else if (!$upgraded) {
            Db::getInstance()->Execute("ALTER TABLE `" . _DB_PREFIX_ . "cart_product` ADD `instructions_id` TEXT NOT NULL AFTER `instructions_valid`");
        }

        $res = Db::getInstance()->ExecuteS('SHOW KEYS FROM ' . _DB_PREFIX_ . 'cart_product');

        foreach ($res as $val) {
            if ($val['Key_name'] == 'PRIMARY') {
                Db::getInstance()->Execute('ALTER TABLE `' . _DB_PREFIX_ . 'cart_product` DROP INDEX `PRIMARY`');
                Db::getInstance()->Execute('
					ALTER TABLE `' . _DB_PREFIX_ . 'cart_product`
					ADD PRIMARY KEY (`id_cart`, `id_product`, `id_product_attribute`, id_address_delivery, `instructions_valid` (50))');
                break;
            }
        }
        $this->createTempAttributes();
        return $installResult;
    }

    private function setConfigValues()
    {
        $result = true;

        $result &=
            Configuration::updateValue('AWP_INSTALLED', 1) &
            Configuration::updateValue('AWP_INSTALL', 'block') &
            Configuration::updateValue('AWP_INSTRUCTIONS', 'block') &
            Configuration::updateValue('AWP_THUMBNAIL_SIZE', '60') &
            Configuration::updateValue('AWP_UPLOAD_SIZE', '2000') &
            Configuration::updateValue('AWP_PI_DISPLAY', 'diff') &
            Configuration::updateValue('AWP_SECOND_ADD', '10') &
            Configuration::updateValue('AWP_SECOND_ADD', '10') &
            Configuration::updateValue('AWP_NO_CUSTOMIZE', '0') &
            Configuration::updateValue('AWP_POPUP', '0') &
            Configuration::updateValue('AWP_FADE', '0') &
            Configuration::updateValue('AWP_OPACITY', '40') &
            Configuration::updateValue('AWP_POPUP_WIDTH', '700') &
            Configuration::updateValue('AWP_POPUP_TOP', '200') &
            Configuration::updateValue('AWP_POPUP_LEFT', '-100') &
            Configuration::updateValue('AWP_DISPLAY_WIZARD', '1') &
            Configuration::updateValue('AWP_DISPLAY_WIZARD_VALUE', '1') &
            Configuration::updateValue('AWP_NO_TAX_IMPACT', '0') &
            Configuration::updateValue('AWP_ADCC_NO_ATTRIBUTE', '1') &
            Configuration::updateValue('PRESTO_CHANGEO_UC', time()) &
            Configuration::updateValue('AWP_RANDOM', md5(mt_rand() . time())) &
            Configuration::updateValue('AWP_SORT_BY_NAME_APLHAB', '0');
            Configuration::updateValue('AWP_GROUPS_COUNT', 10);

        return $result;
    }

    private function refreshProperties()
    {
        if (!Configuration::get('AWP_INSTALLED')) {
            $query = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'awp_attribute_wizard_pro` (
	  			`awp_attributes` MEDIUMTEXT NOT NULL
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
            Db::getInstance()->Execute(trim($query));
            $result = Db::getInstance()->ExecuteS("SELECT * FROM `" . _DB_PREFIX_ . "awp_attribute_wizard_pro`");
            if (!is_array($result) || sizeof($result) == 0) {
                Db::getInstance()->Execute('INSERT INTO `' . _DB_PREFIX_ . 'awp_attribute_wizard_pro` (`awp_attributes`) VALUES ("")');
                $result = '';
            }
            Configuration::updateValue('AWP_INSTALLED', 1);
        }
        $result = Db::getInstance()->ExecuteS("SELECT * FROM `" . _DB_PREFIX_ . "awp_attribute_wizard_pro`");
        if (!is_array($result) || sizeof($result) == 0) {
            $query = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'awp_attribute_wizard_pro` (
	  			`awp_attributes` MEDIUMTEXT NOT NULL
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
            Db::getInstance()->Execute(trim($query));
            Db::getInstance()->Execute('INSERT INTO `' . _DB_PREFIX_ . 'awp_attribute_wizard_pro` (`awp_attributes`) VALUES ("")');
            $result = "";
        } else {
            $result = $result[0]['awp_attributes'];
        }
        $this->awp_attributes = $result != "" ? unserialize($result) : $this->getDbOrderedAttributes();
        $this->awp_width = Configuration::get('AWP_IMAGE_RESIZE_WIDTH');
        $this->awp_image_resize = Configuration::get('AWP_IMAGE_RESIZE');
        $this->awp_layered_image = Configuration::get('AWP_LAYERED_IMAGE');
        $this->awp_thumbnail_size = Configuration::get('AWP_THUMBNAIL_SIZE');
        $this->awp_upload_size = Configuration::get('AWP_UPLOAD_SIZE');
        $this->awp_add_to_cart = Configuration::get('AWP_ADD_TO_CART');
        $this->awp_out_of_stock = Configuration::get('AWP_OUT_OF_STOCK');
        $this->awp_pi_display = Configuration::get('AWP_PI_DISPLAY');
        $this->awp_second_add = (int) (Configuration::get('AWP_SECOND_ADD'));
        $this->awp_no_customize = (int) (Configuration::get('AWP_NO_CUSTOMIZE'));
        $this->awp_popup = (int) (Configuration::get('AWP_POPUP'));
        $this->awp_fade = (int) (Configuration::get('AWP_FADE'));
        $this->awp_opacity = (int) (Configuration::get('AWP_OPACITY'));
        $this->awp_popup_width = (int) (Configuration::get('AWP_POPUP_WIDTH'));
        $this->awp_popup_top = (int) (Configuration::get('AWP_POPUP_TOP'));
        $this->awp_popup_left = (int) (Configuration::get('AWP_POPUP_LEFT'));
        $this->awp_popup_image = (int) (Configuration::get('AWP_POPUP_IMAGE'));
        $this->awp_popup_image_type = Configuration::get('AWP_POPUP_IMAGE_TYPE');
        $this->awp_default_group = Configuration::get('AWP_DEFAULT_GROUP');
        $this->awp_default_item = Configuration::get('AWP_DEFAULT_ITEM');
        $this->awp_display_wizard = Configuration::get('AWP_DISPLAY_WIZARD');
        $this->awp_display_wizard_field = Configuration::get('AWP_DISPLAY_WIZARD_FIELD');
        $this->awp_display_wizard_value = Configuration::get('AWP_DISPLAY_WIZARD_VALUE');
        $this->awp_disable_all = (int) (Configuration::get('AWP_DISABLE_ALL'));
        $this->awp_disable_hide = (int) (Configuration::get('AWP_DISABLE_HIDE'));
        $this->awp_no_tax_impact = 1;
        $this->awp_adc_no_attribute = (int) (Configuration::get('AWP_ADC_NO_ATTRIBUTE'));
        $this->awp_gd_popup = (int) (Configuration::get('AWP_GD_POPUP'));
        $this->awp_collapse_block = (int) (Configuration::get('AWP_COLLAPSE_BLOCK'));
        $this->awp_disable_url_hash = (int) (Configuration::get('AWP_DISABLE_URL_HASH'));
        $this->last_updated = Configuration::get('PRESTO_CHANGEO_UC');
        $this->awp_sort_attributes_alphab = Configuration::get('AWP_SORT_BY_NAME_APLHAB');
        $random = Configuration::get('AWP_RANDOM');
        if ($random != '') {
            $this->awp_random = $random;
        } else {
            $random = md5(mt_rand() . time());
            Configuration::updateValue('AWP_RANDOM', $random);
            $this->awp_random = $random;
        }

        if(!Configuration::getIdByName('AWP_GROUPS_COUNT')) {
            Configuration::updateValue('AWP_GROUPS_COUNT', 10);
        }
    }

    /**
     * Method for register hook for installed module
     */
    public function registerHookWithoutInstall($hookname, $module_prefix)
    {
        $varName = $module_prefix . '_' . Tools::strtoupper($hookname) . '_ADDED';

        if (Configuration::get($varName) != 1) {
            $hookId = Hook::getIdByName($hookname);
            $isExistModule = Hook::getModulesFromHook($hookId, $this->id);

            if (empty($isExistModule)) {
                if ($this->registerHook($hookname)) {
                    Configuration::updateValue($varName, '1');
                }
            } else {
                // if module already istalled just set variable = 1
                Configuration::updateValue($varName, '1');
            }
        }
    }

    protected function applyUpdates()
    {
        $this->registerHookWithoutInstall('backOfficeHeader', 'AWP');
    }

    public function getContent()
    {
        $this->postProcess();
        $output = $this->displayForm();
        return $this->html . $output;
    }

    private function displayForm()
    {
        $this->applyUpdates();
        $this->prepareAdminVars();

        /* Display Warning Domain Name */
        $getWarningDomainName = $this->getWarningDomainName();
        if ($getWarningDomainName != '') {
            $this->html .= $this->displayWarning($getWarningDomainName);
        }

        /* Display Max_input_vars warning */
        $displayLimitPostWarning = $this->displayLimitPostWarning();
        if ($displayLimitPostWarning != '') {
            $this->html .= $this->displayWarning($displayLimitPostWarning);
        }

        $this->prepareInstallationInstructionsVars();

        /* Include TinyMCE if necesarry */
        $this->prepareTinyMCE();
        $tinyMCEView = $this->display(__FILE__, 'views/templates/admin/tiny_mce.tpl');

        $topMenuDisplay = $this->display(__FILE__, 'views/templates/admin/top_menu.tpl');
        $leftMenuDisplay = $this->display(__FILE__, 'views/templates/admin/left_menu.tpl');

        $basicSettingsDisplay = $this->display(__FILE__, 'views/templates/admin/basic_settings.tpl');
        $advancedSettingsDisplay = $this->display(__FILE__, 'views/templates/admin/advanced_settings.tpl');

        $installationInstructionsDisplay = $this->display(__FILE__, 'views/templates/admin/installation_instructions.tpl');

        $attributeInformationDisplay = $this->display(__FILE__, 'views/templates/admin/attribute_information.tpl');

        $copyAttributesDisplay = $this->display(__FILE__, 'views/templates/admin/copy_attributes.tpl');
        $bottomSettingsDisplay = $this->display(__FILE__, 'views/templates/admin/bottom_menu.tpl');

        return $tinyMCEView . $topMenuDisplay . $leftMenuDisplay . $basicSettingsDisplay . $advancedSettingsDisplay . $installationInstructionsDisplay . $attributeInformationDisplay . $copyAttributesDisplay . $bottomSettingsDisplay;
    }

    private function prepareTinyMCE()
    {
        $iso = Language::getIsoById((int) ($this->context->cookie->id_lang));
        $isoTinyMCE = (file_exists(_PS_ROOT_DIR_ . '/js/tiny_mce/langs/' . $iso . '.js') ? $iso : 'en');
        $ad = dirname($_SERVER["PHP_SELF"]);
        $this->context->smarty->assign(array(
            'iso' => $iso,
            'isoTinyMCE' => $isoTinyMCE,
            'ad' => $ad
        ));

        $this->context->smarty->assign(array(
            'base_uri' => __PS_BASE_URI__,
            'theme_name' => _THEME_NAME_,
            'theme_css_dir' => _THEME_CSS_DIR_,
            'ps_root_dir' => _PS_ROOT_DIR_,
            'iso_1' => (file_exists(_PS_ROOT_DIR_ . '/js/tinymce/jscripts/tiny_mce/langs/' . $iso . '.js') ? $iso : 'en'),
        ));
    }

    private function prepareInstallationInstructionsVars()
    {
        $ps_version = $this->getPSV();
        $admin_dir = Tools::substr(dirname($_SERVER['REQUEST_URI']), strrpos(dirname($_SERVER['REQUEST_URI']), "/"));
        $res = [];

        $ps_version_array = explode('.', _PS_VERSION_);
        $ps_version_id = 10000 * intval($ps_version_array[0]) + 100 * intval($ps_version_array[1]);
        if (count($ps_version_array) >= 3)
            $ps_version_id += (int) ($ps_version_array[2]);


        $frontCheck = NULL;
        if ($ps_version_id >= 10705) {
            $frontCheck = [
                'file' => '/override/classes/controller/FrontController.php',
                'lines' => array('32-66'),
                'context' => 'override'
            ];
        }


        if(version_compare('1.7.7.0', $this->getRawPSV()) !== 1)
            $ps_version = '1.7.7';

        $checks = array(
            $frontCheck,
            [
                'file' => '/override/classes/Cart.php',
                'lines' => array('30-36', '83-90', 308, '326-328', 357, '501-503', 544, 593, 613, '655-656', '750-751', '756-757', 776, '789-790', '798-807', '810-823', '834-835', '840-847', 921),
                'context' => 'override'
            ],
            [
                'file' => '/override/classes/PaymentModule.php',
                'lines' => array(27, 348),
                'context' => 'override'
            ],
            [
                'file' => '/override/classes/Product.php',
                'lines' => array('39-46', 72, 118),
                'context' => 'override'
            ],
            [
                'file' => '/override/classes/order/OrderDetail.php',
                'lines' => array(43, '119-122'),
                'context' => 'override'
            ],
            [
                'file' => '/override/controllers/front/CartController.php',
                'lines' => array('30-32', '40-45', 89, 189),
                'context' => 'override'
            ],
            [
                'file' => '/themes/placeholder/modules/ps_shoppingcart/modal.tpl',
                'lines' => array('25-27'),
                'context' => 'classic'
            ],
            [
                'file' => '/themes/placeholder/modules/ps_shoppingcart/ps_shoppingcart-product-line.tpl',
                'lines' => array(6, 8),
                'context' => 'classic'
            ],
            [
                'file' => '/themes/placeholder/templates/checkout/_partials/cart-detailed-product-line.tpl',
                'lines' => array('26-42', 54, 74, '134-137', 164, 167),
                'context' => 'classic'
            ],
            [
                'file' => '/themes/placeholder/templates/checkout/_partials/order-confirmation-table.tpl',
                'lines' => array('43-46'),
                'context' => 'classic'
            ],
            [
                'file' => '/themes/placeholder/templates/checkout/_partials/cart-summary-product-line.tpl',
                'lines' => array('36-38'),
                'context' => 'classic'
            ],
            [
                'file' => '/themes/placeholder/templates/customer/_partials/order-detail-no-return.tpl',
                'lines' => array('41-43'),
                'context' => 'classic'
            ],
            [
                'file' => '/themes/placeholder/templates/customer/_partials/order-detail-return.tpl',
                'lines' => array('56'),
                'context' => 'classic'
            ],
            [
                'file' => '/themes/placeholder/templates/customer/_partials/order-messages.tpl',
                'lines' => array('61'),
                'context' => 'classic'
            ],
            [
                'file' => '/themes/placeholder/mails/en/order_conf_product_list.tpl',
                'lines' => array('46'),
                'context' => 'classic'
            ],
        );

        if(version_compare('1.7.7.0', $this->getRawPSV()) == 1)
        {
            $checks[] = [
                'file' => 'placeholder/themes/default/template/controllers/carts/helpers/view/view.tpl',
                'lines' => array('155'),
                'context' => 'admin'
            ];
        }

        foreach ($checks as $key => $check) {
            if (isset($check)) {
                switch ($check['context']) {
                    case 'override':
                        $mfile = $lfile = $check['file'];
                        break;
                    case 'classic':
                        $lfile = str_replace('placeholder', _THEME_NAME_, $check['file']);
                        $mfile = str_replace('placeholder', 'classic', $check['file']);
                        break;
                    case 'admin':
                        $lfile = str_replace('placeholder', $admin_dir, $check['file']);
                        $mfile = str_replace('placeholder', '/admin', $check['file']);
                        break;
                }

                $result = $this->fileCheckLines($lfile, $mfile, $check['lines'], $ps_version);
                // prepare checks arr to have all results and fit current instrs look
                // todo rewrite fileCheckLines
                $result = reset($result);
                $checks[$key]['file'] = $mfile;
                $checks[$key]['file_not_found'] = $result['file_not_found'];
                $checks[$key]['file_installed'] = $result['file_installed'];
                unset($result['file_not_found'], $result['file_installed']);
                $checks[$key]['lines'] = $result;
            }
        }

        if(version_compare('1.7.7.0', $this->getRawPSV()) !== 1)
            $ps_version = '1.7.7';

        $this->context->smarty->assign(array(
            'awp_ps_version' => $ps_version,
            'checks' => $checks,
            'admin_dir' => $admin_dir,
            'theme_folder' => _THEME_NAME_,
            'admin_view_tpl' => $admin_dir . '/themes/default/template/controllers/carts/helpers/view/view.tpl'
        ));
    }

    /**
     * get version of PrestaShop
     * return float value version
     */
    protected function getPSV()
    {
        return (float) Tools::substr($this->getRawPSV(), 0, 3);
    }

    private function fileCheckLines($lfile, $mfile, $lines, $ps_version, $extra = "")
    {
        $return = array();

        if (!file_exists(_PS_ROOT_DIR_ . $lfile)) {
            $return[$lfile]['file_not_found'] = false;
        } else {
            $return[$lfile]['file_not_found'] = true;
        }

        $return[$lfile]['file_installed'] = false;

        $server_file = Tools::file_get_contents(_PS_ROOT_DIR_ . $lfile);
        $server_file = preg_replace('/\s+/', '', $server_file); // strip all whitespaces

        $all_good = true;
        $module_lines = file(_PS_ROOT_DIR_ . "/modules/attributewizardpro/modified_" . $ps_version . $mfile);
        $fullyInstalled = true;

        foreach ($lines as $line) {
            if (sizeof($module_lines) <= 1) {
                $all_good = false;
                $line_good = false;

                break;
            }
            $start = "";
            $end = "";
            if (strpos($line, "-") === false) {
                $start = max($line - 1, 0);
                $end = min($line + 1, sizeof($module_lines) - 1);
            } else {
                $tmp_arr = explode("-", $line);
                $start = max((int) ($tmp_arr[0]) - 1, 0);
                $end = min((int) ($tmp_arr[1] + 1), sizeof($module_lines) - 1);
            }
            $line_good = true;

            for ($i = $start; $i <= $end; $i++) {
                $module_lines[$i] = preg_replace('/\s+/', '', $module_lines[$i]);  // strip all whitespaces
                if ($module_lines[$i] == "" || strpos($server_file, $module_lines[$i]) !== false) {
                    if ($module_lines[$i] != "") {
                        $server_file = Tools::substr($server_file, strpos($server_file, $module_lines[$i])); // removed (strpos + 1) because in some cases it discarded a first valid char of a string
                    }
                } else {
                    $all_good = false;
                    $line_good = false;
                    break;
                }
            }
            if ($fullyInstalled && $all_good) {
                $fullyInstalled = true;
            } else {
                $fullyInstalled = false;
            }
            $return[$lfile][$line] = $line_good;
        }
        $return[$lfile]['file_installed'] = $fullyInstalled;

        return $return;
    }

    private function prepareCopyAttributeVars()
    {
        $ps_shops = Shop::getContextListShopID();
        $shops = implode(',', $ps_shops);

        $this->context->smarty->assign(
            array(
                'awp_shops' => Tools::safeOutput($shops)
            )
        );
    }

    private function prepareAdminVars()
    {
        $displayUpgradeCheck = '';
        if (file_exists(dirname(__FILE__) . '/PrestoChangeoClasses/PrestoChangeoUpgrade.php')) {
            if (!in_array('PrestoChangeoUpgrade', get_declared_classes()))
                require_once(dirname(__FILE__) . '/PrestoChangeoClasses/PrestoChangeoUpgrade.php');
            $initFile = new PrestoChangeoUpgrade($this, $this->_path, $this->full_version);

            $upgradeCheck = $initFile->displayUpgradeCheck('AWP2');
            if (isset($upgradeCheck) && !empty($upgradeCheck))
                $displayUpgradeCheck = $upgradeCheck;
        }

        $getModuleRecommendations = '';
        if (file_exists(dirname(__FILE__) . '/PrestoChangeoClasses/PrestoChangeoUpgrade.php')) {

            if (!in_array('PrestoChangeoUpgrade', get_declared_classes()))
                require_once(dirname(__FILE__) . '/PrestoChangeoClasses/PrestoChangeoUpgrade.php');
            $initFile = new PrestoChangeoUpgrade($this, $this->_path, $this->full_version);

            $getModuleRecommendations = $initFile->getModuleRecommendations('ADN');
        }

        $logoPrestoChangeo = '';
        $contactUsLinkPrestoChangeo = '';
        if (file_exists(dirname(__FILE__) . '/PrestoChangeoClasses/PrestoChangeoUpgrade.php')) {
            if (!in_array('PrestoChangeoUpgrade', get_declared_classes()))
                require_once(dirname(__FILE__) . '/PrestoChangeoClasses/PrestoChangeoUpgrade.php');
            $initFile = new PrestoChangeoUpgrade($this, $this->_path, $this->full_version);


            $logoPrestoChangeo = $initFile->getPrestoChangeoLogo();
            $contactUsLinkPrestoChangeo = $initFile->getContactUsOnlyLink();
        }

        $this->createTempAttributes();
        Configuration::updateValue('PS_USE_HTMLPURIFIER', 0);

        $ps_shops = Shop::getContextListShopID();
        $shops = implode(",", $ps_shops);
        if (isset($ps_shops[0]) && !empty($ps_shops[0])) {
            $shopUrl = new Shop($ps_shops[0]);
        } elseif (isset($ps_shops[1]) && !empty($ps_shops[1])) {
            $shopUrl = new Shop($ps_shops[1]);
        }

        $virtual_uri = $shopUrl->virtual_uri;
        $states = OrderState::getOrderStates((int) ($this->context->cookie->id_lang));

        $languages = Language::getLanguages();
        $features = Feature::getFeatures((int) ($this->context->cookie->id_lang));
        $image_formats = ImageType::getImagesTypes('products');
        $image_formats_options = '';
        foreach ($image_formats as $format) {
            $image_formats_options .= '<option value="' . $format['name'] . '|||' . $format['width'] . 'x' . $format['height'] . '"' . (Tools::getValue('awp_popup_image_type', $this->awp_popup_image_type) == $format['name'] . '|||' . $format['width'] . 'x' . $format['height'] ? ' selected="selected"' : '') . '">' . $format['name'] . '  (' . $format['width'] . 'x' . $format['height'] . ')</option>';
        }
        $iso = Language::getIsoById((int) ($this->context->cookie->id_lang));
        $ipr_arr = array("checkbox", "radio", "textbox", "quantity", "calculation", "image", "images");
        $size_arr = array("dropdown", "file", "hidden");
        $hin_arr = array("checkbox", "radio", "textbox", "textarea", "file", "quantity", "calculation", "image", "images");
        $ml_arr = array("textbox", "textarea");
        $req_arr = array("textbox", "textarea", "file", "image", "images", "dropdown", "radio");
        $ale_arr = array("hidden");

        $ps_version3 = Tools::substr(_PS_VERSION_, 0, 5) . (Tools::substr(_PS_VERSION_, 5, 1) != "." ? Tools::substr(_PS_VERSION_, 5, 1) : "");
        $ps_version3_array = array("1.7.0", "1.7.1", "1.7.2", "1.7.3");

        //if (!in_array($ps_version3, $ps_version3_array)) {
        //    $this->html .= $this->displayError($this->l('The module is not yet compatible with this version of Prestashop'));
        //}

        if(Tools::getIsset('n')) {
            Configuration::updateValue('AWP_GROUPS_COUNT', (int)Tools::getValue('n'));
        }

        $n = (int)Configuration::get('AWP_GROUPS_COUNT');
        $p = (int)Tools::getValue('p');

        if($p < 1){
            $p = 1;
        }

        $attribute_groups_count = $this->getDbAttributesCount();
        $ordered_groups = $this->getDbOrderedAttributes($n, $p);

        if((int)$n > 0){
            $pages_count = ceil($attribute_groups_count / $n);
        } else {
            $pages_count = 1;
        }

        $ipr_arr = array("checkbox", "radio", "textbox", "quantity", "calculation", "image", "images");
        $size_arr = array("checkbox", "radio", "textbox", "quantity", "calculation", "image", "images", "textarea");
        $hin_arr = array("checkbox", "radio", "textbox", "textarea", "file", "quantity", "calculation", "image", "images");
        $ml_arr = array("textbox", "textarea");
        $req_arr = array("textbox", "textarea", "file", "image", "images", "dropdown", "radio");
        $ale_arr = array("checkbox", "radio", "textbox", "quantity", "calculation", "image", "images", "textarea", "file", "dropdown");

        $this->context->smarty->assign(array(
            'ipr_arr' => $ipr_arr,
            'size_arr' => $size_arr,
            'hin_arr' => $hin_arr,
            'ml_arr' => $ml_arr,
            'req_arr' => $req_arr,
            'ale_arr' => $ale_arr,
            'ipr_arr_class' => implode("Opt ", $ipr_arr) . "Opt ",
            'size_arr_class' => implode("Opt ", $size_arr) . "Opt ",
            'hin_arr_class' => implode("Opt ", $hin_arr) . "Opt ",
            'ml_arr_class' => implode("Opt ", $ml_arr) . "Opt ",
            'req_arr_class' => implode("Opt ", $req_arr) . "Opt ",
            'ale_arr_class' => implode("Opt ", $ale_arr) . "Opt ",
            'awp_random' => Tools::safeOutput($this->awp_random),
            'languages' => $languages,
            'theme_lang_dir' => Tools::safeOutput(_THEME_LANG_DIR_),
            'states' => $states,
            'displayUpgradeCheck' => $displayUpgradeCheck,
            'getModuleRecommendations' => $getModuleRecommendations,
            'id_lang' => (int) $this->context->cookie->id_lang,
            'id_employee' => (int) $this->context->cookie->id_employee,
            'path' => Tools::safeOutput($this->_path),
            'module_name' => Tools::safeOutput($this->displayName),
            'module_dir' => Tools::safeOutput(_MODULE_DIR_),
            'module_basedir' => Tools::safeOutput(_MODULE_DIR_ . 'attributewizardpro/'),
            'request_uri' => Tools::safeOutput($_SERVER['REQUEST_URI']),
            'module_uri' => $this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name,
            'mod_version' => Tools::safeOutput($this->version),
            'upgradeCheck' => (isset($upgradeCheck) && !empty($upgradeCheck) ? true : false),
            'logoPrestoChangeo' => $logoPrestoChangeo,
            'ordered_groups' => $ordered_groups != "" ? $ordered_groups : $this->getDbOrderedAttributes($n, $p),
            'awp_width' => (float) $this->awp_width,
            'awp_image_resize' => (bool) $this->awp_image_resize,
            'awp_layered_image' => (int) $this->awp_layered_image,
            'awp_thumbnail_size' => (int) $this->awp_thumbnail_size,
            'awp_upload_size' => (int) $this->awp_upload_size,
            'ini_max_upload_filesize' => (Tools::substr(ini_get('upload_max_filesize'), 0, -1) * 1024),
            'awp_add_to_cart' => Tools::safeOutput($this->awp_add_to_cart),
            'awp_out_of_stock' => Tools::safeOutput($this->awp_out_of_stock),
            'awp_pi_display' => Tools::safeOutput($this->awp_pi_display),
            'awp_second_add' => (int) $this->awp_second_add,
            'awp_no_customize' => (int) $this->awp_no_customize,
            'awp_popup' => (int) $this->awp_popup,
            'awp_fade' => (int) $this->awp_fade,
            'awp_opacity' => (int) $this->awp_opacity,
            'awp_popup_width' => (int) $this->awp_popup_width,
            'awp_popup_top' => (int) $this->awp_popup_top,
            'awp_popup_left' => (int) $this->awp_popup_left,
            'awp_popup_image' => (int) $this->awp_popup_image,
            'awp_popup_image_type' => $this->awp_popup_image_type,
            'awp_default_group' => $this->awp_default_group,
            'awp_default_item' => $this->awp_default_item,
            'awp_display_wizard' => $this->awp_display_wizard,
            'awp_display_wizard_field' => $this->awp_display_wizard_field,
            'awp_display_wizard_value' => $this->awp_display_wizard_value,
            'awp_disable_all' => (int) $this->awp_disable_all,
            'awp_disable_hide' => (int) $this->awp_disable_hide,
            'awp_no_tax_impact' => (int) 1,
            'awp_adc_no_attribute' => (int) $this->awp_adc_no_attribute,
            'awp_gd_popup' => (int) $this->awp_gd_popup,
            'awp_collapse_block' => (int) $this->awp_collapse_block,
            'awp_disable_url_hash' => (int) $this->awp_disable_url_hash,
            'image_formats_options' => $image_formats_options,
            'contactUsLinkPrestoChangeo' => $contactUsLinkPrestoChangeo,
            'awp_shis' => Configuration::get('AWP_INSTRUCTIONS'),
            'tiny_mce_all' => Configuration::get('AWP_TINY_MCE_ALL') ? : 0,
            'awp_sort_attributes_alphab' => (int) $this->awp_sort_attributes_alphab,
            'awp_enable_parent_group' => $this->enable_parent_group,
            'n' => $n,
            'p' => $p,
            'pages_count' => $pages_count
        ));

        Media::addJsDef([
            'current_page' => $p,
            'attributes_per_page' => $n,
        ]);

        $this->prepareCopyAttributeVars();
    }

    private function validateBasicSettings()
    {
        $errors = false;
        $awp_display_wizard = Tools::getValue('awp_display_wizard');

        if (!Validate::isInt($awp_display_wizard)) {
            $this->html .= $this->displayWarning($this->l('Invalid Display Wizard'));
            $errors = true;
        }

        $awp_display_wizard_field = Tools::getValue('awp_display_wizard_field');
        if (!Validate::isString($awp_display_wizard_field)) {
            $this->html .= $this->displayWarning($this->l('Invalid Display Wizard Field'));
            $errors = true;
        }

        $awp_display_wizard_value = Tools::getValue('awp_display_wizard_value');
        if (!Validate::isString($awp_display_wizard_value)) {
            $this->html .= $this->displayWarning($this->l('Invalid Display Wizard Value'));
            $errors = true;
        }

        $awp_popup = Tools::getValue('awp_popup');
        if (!Validate::isInt($awp_popup)) {
            $this->html .= $this->displayWarning($this->l('Invalid Wizard Location'));
            $errors = true;
        }

        $awp_fade = Tools::getValue('awp_fade');
        if (!Validate::isInt($awp_fade)) {
            $this->html .= $this->displayWarning($this->l('Invalid Fade Background'));
            $errors = true;
        }

        $awp_opacity = Tools::getValue('awp_opacity');
        if (!Validate::isInt($awp_opacity)) {
            $this->html .= $this->displayWarning($this->l('Invalid Opacity Background'));
            $errors = true;
        }
        $awp_popup_image = Tools::getValue('awp_popup_image');
        if (!Validate::isInt($awp_popup_image)) {
            $this->html .= $this->displayWarning($this->l('Invalid Include Popup Product Image'));
            $errors = true;
        }
        $awp_popup_image_type = Tools::getValue('awp_popup_image_type');
        if (!Validate::isString($awp_popup_image_type)) {
            $this->html .= $this->displayWarning($this->l('Invalid Include Popup Image Type'));
            $errors = true;
        }
        $awp_image_resize = Tools::getValue('awp_image_resize');
        if (!Validate::isInt($awp_image_resize)) {
            $this->html .= $this->displayWarning($this->l('Invalid Group Image'));
            $errors = true;
        }
        $awp_image_resize_width = Tools::getValue('awp_image_resize_width');
        if (!Validate::isInt($awp_image_resize_width)) {
            $this->html .= $this->displayWarning($this->l('Invalid Group Image - max width'));
            $errors = true;
        }

        $awp_layered_image = Tools::getValue('awp_layered_image');
        if (!Validate::isInt($awp_layered_image)) {
            $this->html .= $this->displayWarning($this->l('Invalid Layered Images Option'));
            $errors = true;
        }

        $awp_thumbnail_size = Tools::getValue('awp_thumbnail_size');
        if (!Validate::isInt($awp_thumbnail_size)) {
            $this->html .= $this->displayWarning($this->l('Invalid File Upload Setting - Thumbnail Width/Height'));
            $errors = true;
        }

        $awp_upload_size = Tools::getValue('awp_upload_size');
        if (!Validate::isInt($awp_upload_size)) {
            $this->html .= $this->displayWarning($this->l('Invalid File Upload Setting - Max Upload Size'));
            $errors = true;
        }

        $awp_add_to_cart = Tools::getValue('awp_add_to_cart');
        if (!Validate::isString($awp_add_to_cart)) {
            $this->html .= $this->displayWarning($this->l('Invalid Add to Cart Display'));
            $errors = true;
        }
        $awp_second_add = Tools::getValue('awp_second_add');
        if (!Validate::isInt($awp_second_add)) {
            $this->html .= $this->displayWarning($this->l('Invalid Add to Cart button - Display additional buttons'));
            $errors = true;
        }
        $awp_no_customize = Tools::getValue('awp_no_customize');
        if (!Validate::isInt($awp_no_customize)) {
            $this->html .= $this->displayWarning($this->l('Invalid Add to Cart button -  Do not replace with Customize (In page)'));
            $errors = true;
        }
        $awp_out_of_stock = Tools::getValue('awp_out_of_stock');
        if (!Validate::isString($awp_out_of_stock)) {
            $this->html .= $this->displayWarning($this->l('Invalid Unavailable / Out of Stock'));
            $errors = true;
        }
        $awp_pi_display = Tools::getValue('awp_pi_display');
        if (!Validate::isString($awp_pi_display)) {
            $this->html .= $this->displayWarning($this->l('Invalid Price Impact Display'));
            $errors = true;
        }
        $awp_disable_hide = Tools::getValue('awp_disable_hide');
        if (!Validate::isInt($awp_disable_hide)) {
            $this->html .= $this->displayWarning($this->l('Invalid Not in Product Page Option'));
            $errors = true;
        }
        $awp_disable_all = Tools::getValue('awp_disable_all');
        if (!Validate::isInt($awp_disable_all)) {
            $this->html .= $this->displayWarning($this->l('Invalid Not in Product Page Value'));
            $errors = true;
        }
        $awp_adc_no_attribute = Tools::getValue('awp_adc_no_attribute');
        if (isset($awp_adc_no_attribute) && $awp_adc_no_attribute != '' && !Validate::isInt($awp_adc_no_attribute)) {
            $this->html .= $this->displayWarning($this->l('Invalid No Attribute Selection '));
            $errors = true;
        }
        $awp_gd_popup = Tools::getValue('awp_gd_popup');
        if (!Validate::isInt($awp_gd_popup)) {
            $this->html .= $this->displayWarning($this->l('Invalid Group Description Display'));
            $errors = true;
        }
        $awp_collapse_block = Tools::getValue('awp_collapse_block');
        if (!Validate::isInt($awp_collapse_block)) {
            $this->html .= $this->displayWarning($this->l('Invalid Expand / Collapse AWP blocks'));
            $errors = true;
        }

        $awp_disable_url_hash = Tools::getValue('awp_disable_url_hash');
        if (!Validate::isInt($awp_disable_url_hash)) {
            $this->html .= $this->displayWarning($this->l('Invalid Disable AWP URL hash'));
            $errors = true;
        }

        return $errors;
    }

    private function submitBasicSettings()
    {
        if (!Configuration::updateValue('AWP_ADD_TO_CART', Tools::getValue('awp_add_to_cart')) || !Configuration::updateValue('AWP_SECOND_ADD', Tools::getValue('awp_second_add')) || !Configuration::updateValue('AWP_OUT_OF_STOCK', Tools::getValue('awp_out_of_stock')) || !Configuration::updateValue('AWP_NO_CUSTOMIZE', Tools::getValue('awp_no_customize')) || !Configuration::updateValue('AWP_PI_DISPLAY', Tools::getValue('awp_pi_display')) || !Configuration::updateValue('AWP_LAYERED_IMAGE', Tools::getValue('awp_layered_image')) || !Configuration::updateValue('AWP_POPUP', Tools::getValue('awp_popup')) || !Configuration::updateValue('AWP_THUMBNAIL_SIZE', Tools::getValue('awp_thumbnail_size')) || !Configuration::updateValue('AWP_UPLOAD_SIZE', Tools::getValue('awp_upload_size')) || !Configuration::updateValue('AWP_DISPLAY_WIZARD', Tools::getValue('awp_display_wizard')) || !Configuration::updateValue('AWP_DISPLAY_WIZARD_FIELD', Tools::getValue('awp_display_wizard_field')) || !Configuration::updateValue('AWP_DISPLAY_WIZARD_VALUE', Tools::getValue('awp_display_wizard_value')) || !Configuration::updateValue('AWP_FADE', Tools::getValue('awp_fade')) || !Configuration::updateValue('AWP_OPACITY', Tools::getValue('awp_opacity')) || !Configuration::updateValue('AWP_NO_TAX_IMPACT', Tools::getValue('awp_no_tax_impact')) || !Configuration::updateValue('AWP_ADC_NO_ATTRIBUTE', Tools::getValue('awp_adc_no_attribute')) || !Configuration::updateValue('AWP_GD_POPUP', Tools::getValue('awp_gd_popup')) || !Configuration::updateValue('AWP_DISABLE_URL_HASH', Tools::getValue('awp_disable_url_hash')) || !Configuration::updateValue('AWP_COLLAPSE_BLOCK', Tools::getValue('awp_collapse_block')) || !Configuration::updateValue('AWP_POPUP_WIDTH', Tools::getValue('awp_popup_width')) || !Configuration::updateValue('AWP_POPUP_TOP', Tools::getValue('awp_popup_top')) || !Configuration::updateValue('AWP_POPUP_LEFT', Tools::getValue('awp_popup_left')) || !Configuration::updateValue('AWP_POPUP_IMAGE', Tools::getValue('awp_popup_image')) || !Configuration::updateValue('AWP_POPUP_IMAGE_TYPE', Tools::getValue('awp_popup_image_type')) || !Configuration::updateValue('AWP_DISABLE_ALL', Tools::getValue('awp_disable_all')) || !Configuration::updateValue('AWP_DISABLE_HIDE', Tools::getValue('awp_disable_hide')) || !Configuration::updateValue('AWP_SORT_BY_NAME_APLHAB', Tools::getValue('awp_sort_attributes_alphab'))) {
            $this->html .= $this->displayError($this->l('Cannot update settings') . Db::getInstance()->getMsgError());
        } else {
            $this->html .= $this->displayConfirmation($this->l('Settings updated'));
        }
    }

    private function validateAdvancedSettings()
    {
        $errors = false;
        /* $awp_display_wizard = Tools::getValue('awp_display_wizard');

          if (!Validate::isInt($awp_display_wizard)) {
          $this->html .= $this->displayWarning($this->l('Invalid Display Wizard'));
          $errors = true;
          } */
        
        foreach ($this->awp_attributes as $key => $att) {
            foreach ($att['attributes'] as $attr) {               
               
                $idProduct = (int) Tools::getValue("attr_product_" . $attr['id_attribute']);
                if (isset($idProduct) && $idProduct > 0) {
                    $res = Db::getInstance()->ExecuteS('SELECT id_product FROM `' . _DB_PREFIX_ . 'product` WHERE id_product = "' . (int) $idProduct . '"');

                    if (isset($res) && !empty($res)) {

                    } else {
                        $errors = true;
                        $this->html .= $this->displayWarning($this->l('Invalid Product id for attribute id ' . $attr['id_attribute']));
                    }
                }
            }
        }
        return $errors;
    }

    private function submitAdvancedSettings()
    {
        Configuration::updateValue('AWP_TINY_MCE_ALL', Tools::getValue('tiny_mce_all'));

        $languages = Language::getLanguages();

        foreach ($this->awp_attributes as $key => $att) {
            if(!Tools::getIsset("group_type_" . $att['id_attribute_group'])) {
                continue;
            }

            $this->awp_attributes[$key]["group_type"] = Tools::getValue("group_type_" . $att['id_attribute_group']);
            if (isset($_POST["group_required_" . $att['id_attribute_group']]))
                $this->awp_attributes[$key]["group_required"] = Tools::getValue("group_required_" . $att['id_attribute_group']);
            else
                $this->awp_attributes[$key]["group_required"] = "";
            if (isset($_POST["group_max_limit_" . $att['id_attribute_group']]))
                $this->awp_attributes[$key]["group_max_limit"] = Tools::getValue("group_max_limit_" . $att['id_attribute_group']);
            else
                $this->awp_attributes[$key]["group_max_limit"] = "0";
            if (isset($_POST["group_width_" . $att['id_attribute_group']]))
                $this->awp_attributes[$key]["group_width"] = Tools::getValue("group_width_" . $att['id_attribute_group']);
            else
                $this->awp_attributes[$key]["group_width"] = "";
            if (isset($_POST["group_height_" . $att['id_attribute_group']]))
                $this->awp_attributes[$key]["group_height"] = Tools::getValue("group_height_" . $att['id_attribute_group']);
            else
                $this->awp_attributes[$key]["group_height"] = "";
            if (isset($_POST["group_resize_" . $att['id_attribute_group']]))
                $this->awp_attributes[$key]["group_resize"] = Tools::getValue("group_resize_" . $att['id_attribute_group']);
            else
                $this->awp_attributes[$key]["group_resize"] = "";
            if (isset($_POST["group_layout_" . $att['id_attribute_group']]))
                $this->awp_attributes[$key]["group_layout"] = Tools::getValue("group_layout_" . $att['id_attribute_group']);
            if (isset($_POST["group_per_row_" . $att['id_attribute_group']]))
                $this->awp_attributes[$key]["group_per_row"] = Tools::getValue("group_per_row_" . $att['id_attribute_group']);
            else
                $this->awp_attributes[$key]["group_per_row"] = "1";
            if (isset($_POST["group_hide_name_" . $att['id_attribute_group']]))
                $this->awp_attributes[$key]["group_hide_name"] = Tools::getValue("group_hide_name_" . $att['id_attribute_group']);
            else
                $this->awp_attributes[$key]["group_hide_name"] = "";

            /* Impact per char */
            if (isset($_POST["group_min_limit_" . $att['id_attribute_group']]))
                $this->awp_attributes[$key]["group_min_limit"] = Tools::getValue("group_min_limit_" . $att['id_attribute_group']);
            else
                $this->awp_attributes[$key]["group_min_limit"] = 0;

            if (isset($_POST["price_impact_per_char_" . $att['id_attribute_group']]))
                $this->awp_attributes[$key]["price_impact_per_char"] = Tools::getValue("price_impact_per_char_" . $att['id_attribute_group']);
            else
                $this->awp_attributes[$key]["price_impact_per_char"] = 0;

            if (isset($_POST["exceptions_" . $att['id_attribute_group']]))
                $this->awp_attributes[$key]["exceptions"] = Tools::getValue("exceptions_" . $att['id_attribute_group']);
            else
                $this->awp_attributes[$key]["exceptions"] = "";
            /* End impact per char */

            if (isset($_POST["group_url_" . $att['id_attribute_group']]))
                $this->awp_attributes[$key]["group_url"] = Tools::getValue("group_url_" . $att['id_attribute_group']);
            else
                $this->awp_attributes[$key]["group_url"] = "";
            if (isset($_POST["group_file_ext_" . $att['id_attribute_group']]))
                $this->awp_attributes[$key]["group_file_ext"] = Tools::getValue("group_file_ext_" . $att['id_attribute_group']);
            if (isset($_POST["group_quantity_zero_" . $att['id_attribute_group']]))
                $this->awp_attributes[$key]["group_quantity_zero"] = Tools::getValue("group_quantity_zero_" . $att['id_attribute_group']);
            else
                $this->awp_attributes[$key]["group_quantity_zero"] = "";

            /* Connected Attributes - Save DO NOT HIDE VALUE */
            if (isset($_POST["connected_do_not_hide_" . $att['id_attribute_group']]))
                $this->awp_attributes[$key]["connected_do_not_hide"] = Tools::getValue("connected_do_not_hide_" . $att['id_attribute_group']);
            else
                $this->awp_attributes[$key]["connected_do_not_hide"] = "";
            /* END Connected Attributes - Save DO NOT HIDE VALUE */

            if (isset($_POST["chk_limit_min_" . $att['id_attribute_group']]))
                $this->awp_attributes[$key]["chk_limit_min"] = Tools::getValue("chk_limit_min_" . $att['id_attribute_group']);
            else
                $this->awp_attributes[$key]["chk_limit_min"] = "0";

            if (isset($_POST["chk_limit_max_" . $att['id_attribute_group']]))
                $this->awp_attributes[$key]["chk_limit_max"] = Tools::getValue("chk_limit_max_" . $att['id_attribute_group']);
            else
                $this->awp_attributes[$key]["chk_limit_max"] = "0";
            if (isset($_POST["group_calc_min_" . $att['id_attribute_group']]))
                $this->awp_attributes[$key]["group_calc_min"] = Tools::getValue("group_calc_min_" . $att['id_attribute_group']);
            else
                $this->awp_attributes[$key]["group_calc_min"] = "";
            if (isset($_POST["group_calc_max_" . $att['id_attribute_group']]))
                $this->awp_attributes[$key]["group_calc_max"] = Tools::getValue("group_calc_max_" . $att['id_attribute_group']);
            else
                $this->awp_attributes[$key]["group_calc_max"] = "";
            if (isset($_POST["group_calc_multiply_" . $att['id_attribute_group']]) && $_POST["group_type_" . $att['id_attribute_group']] == "calculation")
                $this->awp_attributes[$key]["group_calc_multiply"] = Tools::getValue("group_calc_multiply_" . $att['id_attribute_group']);
            else
                $this->awp_attributes[$key]["group_calc_multiply"] = "";
            foreach ($languages as $language) {
                $idl = $language['id_lang'];
                $this->awp_attributes[$key]["group_description_" . $idl] = htmlspecialchars(Tools::stripslashes(Tools::getValue("description_" . $att['id_attribute_group'] . "_" . $idl)));
                $this->awp_attributes[$key]["group_header_" . $idl] = htmlspecialchars(Tools::stripslashes(Tools::getValue("group_header_" . $att['id_attribute_group'] . "_" . $idl)));
                
                $this->awp_attributes[$key]['parent_group_name_' . $idl] = Tools::getValue("parent_group_name_" . $att['id_attribute_group'] . "_" . $idl);
            }

            if (isset($_POST["delete_image_" . $att['id_attribute_group']]) && $_POST["delete_image_" . $att['id_attribute_group']]) {
                $filename = $this->getGroupImage($att['id_attribute_group'], true);
                unlink(dirname(__FILE__) . '/img/' . $filename);
            }
            foreach ($att['attributes'] as $attr) {
                
                foreach ($languages as $language) {
                    $idl = $language['id_lang'];
                    $this->awp_attributes[$key][$attr['id_attribute']]["attr_description_" . $idl] = htmlspecialchars(Tools::stripslashes(Tools::getValue("attr_description_" . $attr['id_attribute'] . "_" . $idl)));
                }
                $this->awp_attributes[$key][$attr['id_attribute']]["attr_product"] = (int) Tools::getValue("attr_product_" . $attr['id_attribute']);
            
                if (isset($_POST["delete_image_l" . $attr['id_attribute']]) && $_POST["delete_image_l" . $attr['id_attribute']]) {
                    $filename = $this->getLayeredImage($attr['id_attribute'], true, $key);
                    unlink(dirname(__FILE__) . '/img/' . $filename);
                }
            }

            $this->awp_attributes[$key]['group_tinymce'] = Tools::getValue('tiny_mce_' . $att['id_attribute_group'], '');
        }
        $attr = Db::getInstance()->_escape(serialize($this->awp_attributes));
        Db::getInstance()->Execute('UPDATE `' . _DB_PREFIX_ . 'awp_attribute_wizard_pro` SET awp_attributes = "' . $attr . '"');
    }

    private function submitResetData()
    {
        Db::getInstance()->Execute('DELETE FROM `' . _DB_PREFIX_ . 'awp_attribute_wizard_pro`');
        $this->awp_attributes = array();
    }

    private function submitDeleteAttributes()
    {
        $result = Db::getInstance()->ExecuteS("SELECT * "
            . "FROM " . _DB_PREFIX_ . "product_attribute_combination "
            . "WHERE id_attribute = '" . (int) $this->awp_default_item . "'");
        foreach ($result as $row) {
            Db::getInstance()->Execute("DELETE FROM " . _DB_PREFIX_ . "product_attribute "
                . "WHERE id_product_attribute = '" . (int) $row['id_product_attribute'] . "'");

            Db::getInstance()->Execute("DELETE FROM " . _DB_PREFIX_ . "product_attribute_shop "
                . "WHERE id_product_attribute = '" . (int) $row['id_product_attribute'] . "'");
            Db::getInstance()->Execute("DELETE FROM " . _DB_PREFIX_ . "stock_available "
                . "WHERE id_product_attribute = '" . (int) $row['id_product_attribute'] . "'");

            $query = "SELECT cp.id_cart FROM `" . _DB_PREFIX_ . "cart_product` AS cp "
                . "LEFT JOIN " . _DB_PREFIX_ . "orders AS o ON o.id_cart = cp.id_cart"
                . " WHERE id_product_attribute = '" . (int) $row['id_product_attribute'] . "' "
                . "AND o.id_order is null";
            $result1 = Db::getInstance()->ExecuteS($query);
            if (is_array($result1)) {
                foreach ($result1 as $row1) {
                    Db::getInstance()->Execute("DELETE FROM `" . _DB_PREFIX_ . "cart_product` "
                        . "WHERE id_product_attribute = '" . (int) $row['id_product_attribute']
                        . "' AND id_cart = '" . (int) $row1['id_cart'] . "'");
                }
            }
        }
        Db::getInstance()->Execute("DELETE FROM " . _DB_PREFIX_ . "product_attribute_combination "
            . "WHERE id_attribute = '" . (int) $this->awp_default_item . "'");
    }

    private function postProcess()
    {
        if (Tools::getValue('awp_shis') != "") {
            if (Tools::getValue('awp_shis') == "block") {
                Configuration::updateValue('AWP_INSTRUCTIONS', "none");
            } else {
                Configuration::updateValue('AWP_INSTRUCTIONS', "block");
            }
        }

        if (Tools::isSubmit('deleteAttributes')) {
            $this->submitDeleteAttributes();
            $this->html .= $this->displayConfirmation($this->l('Temp awp_details attributes deleted.'));
        }
        if (Tools::isSubmit('resetData')) {
            $this->submitResetData();
            $this->html .= $this->displayConfirmation($this->l('Data reset succesfully.'));
        }

        if (Tools::isSubmit('submitChanges')) {
            $errors = $this->validateBasicSettings();

            if ($errors) {
                $this->html .= $this->displayError($this->l('Cannot update settings'));
                return false;
            } else {
                $this->submitBasicSettings();
            }
        }

        if (Tools::isSubmit('submitAdvancedChanges')) {
            $errors = $this->validateAdvancedSettings();

            if ($errors) {
                $this->html .= $this->displayError($this->l('Cannot update advanced settings'));
                return false;
            } else {
                $this->submitAdvancedSettings();
            }
        }
        $this->refreshProperties();
    }

    /**
     * Add the CSS & JavaScript files you want to be loaded in the BO.
     */
    public function hookBackOfficeHeader()
    {
        $controller = Tools::getValue('controller');

        if (Tools::getValue('configure') == $this->name || $controller == 'AdminProducts') {
            $this->context->controller->addCSS($this->_path . 'views/css/globalBack.css');
            $this->context->controller->addCSS($this->_path . 'views/css/specificBack.css');
        }

        if(Tools::getValue('configure') == $this->name) {
            $this->context->controller->addCSS($this->_path . 'views/css/bootstrap-tokenfield.min.css');
            $this->context->controller->addCSS($this->_path . 'views/css/jquery-ui.min.css');
        }

        if ($controller == 'AdminProducts') {
            Media::addJsDef(array(
                'awp_path' => $this->_path,
                'awp_table_col1' => $this->l('Combination'),
                'awp_table_col2' => $this->l('Price Impact'),
                'awp_table_col3' => $this->l('Weight Impact'),
                'awp_table_col4' => $this->l('Qty'),
                'awp_confirm_delete' => $this->l('Are you sure you want to delete all combinations?'),
            ));
            $this->context->controller->addJS(
                [
                    $this->_path . 'views/js/awpcombinationgenerator.js',
                ]
            );
        }
    }

    public function hookAwpProduct($params)
    {
        return ('product' == Dispatcher::getInstance()->getController()) ? $this->hookProductFooter($params) : false;
    }

    protected function assignAttributesCombinations($id_product)
    {
        $attributes_combinations = Product::getAttributesInformationsByProduct($id_product);
        if (is_array($attributes_combinations) && count($attributes_combinations)) {
            foreach ($attributes_combinations as &$ac) {
                foreach ($ac as &$val) {
                    $val = str_replace(Configuration::get('PS_ATTRIBUTE_ANCHOR_SEPARATOR'), '_', Tools::link_rewrite(str_replace(array(',', '.'), '-', $val)));
                }
            }
        } else {
            $attributes_combinations = array();
        }
        $this->context->smarty->assign(array(
            'attributesCombinations' => $attributes_combinations,
            'attribute_anchor_separator' => Configuration::get('PS_ATTRIBUTE_ANCHOR_SEPARATOR')
            )
        );
    }

    public function hookProductFooter($params)
    {
        //global $smarty, $cookie, $cart, $link;

        $smarty = $this->context->smarty;
        $cookie = $this->context->cookie;
        $cart = $this->context->cart;
        $link = $this->context->link;
        $currency = array_key_exists('id_currency', $params) ? $_REQUEST['id_currency'] : ($cookie->id_currency ? $cookie->id_currency : $params['id_currency']);
        if (!$currency) {
            $currency = Configuration::get('PS_CURRENCY_DEFAULT');
        }

        $action = Tools::getValue('action');
        $isQuickView = false;
        if (isset($action) && $action == 'quickview') {
            $isQuickView = true;
        }

        $id_product = (int) Tools::getValue('id_product');

        $this->assignAttributesCombinations($id_product);
        $product = new Product($id_product, true, (int) $cookie->id_lang);
        $query = 'SELECT SUM(`quantity`)
			FROM `' . _DB_PREFIX_ . 'cart_product`
			WHERE `id_product` = ' . (int) $id_product . ' AND `id_cart` = ' . (int) ($cart->id);

        $cart_quantity = !$cart ? 0 : Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
        $product_features = Product::getFrontFeaturesStatic((int) $cookie->id_lang, (int) $id_product);
        $quantity = $cart_quantity ? $cart_quantity : 1;
        if ($product->hasAttributes() <= 0) {
            return;
        }

        if ($this->awp_display_wizard != 1) {
            if ($this->awp_display_wizard_field == "Reference" && $this->awp_display_wizard_value != $product->reference) {
                return;
            }
            if ($this->awp_display_wizard_field == "Supplier Reference" && $this->awp_display_wizard_value != $product->supplier_reference) {
                return;
            }
            if ($this->awp_display_wizard_field == "EAN13" && $this->awp_display_wizard_value != $product->ean13) {
                return;
            }
            if ($this->awp_display_wizard_field == "UPC" && $this->awp_display_wizard_value != $product->upc) {
                return;
            }
            if ($this->awp_display_wizard_field == "Location" && $this->awp_display_wizard_value != $product->location) {
                return;
            }
        }
        $use_stock = Configuration::get('PS_STOCK_MANAGEMENT');

        /* Filter using id_dhop */

        $query = '
			SELECT ag.`id_attribute_group`, agl.`name` AS group_name, agl.`public_name` AS public_group_name, a.`id_attribute`, al.`name` AS attribute_name,
			a.`color` AS attribute_color, pa.`id_product_attribute`, ' . ($use_stock ? 'stock.`quantity`,' : '') . ' pa.`price`, pa.`ecotax`, pa.`weight`, pa.`default_on`, pa.`reference`
			FROM `' . _DB_PREFIX_ . 'product_attribute` pa
			' . Shop::addSqlAssociation('product_attribute', 'pa') . '
			' . Product::sqlStock('pa', 'pa') . '
			LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac ON pac.`id_product_attribute` = pa.`id_product_attribute`
			LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_shop` pas ON pas.`id_product_attribute` = pa.`id_product_attribute`
			LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a ON a.`id_attribute` = pac.`id_attribute`
			LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
			LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON a.`id_attribute` = al.`id_attribute`
			LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl ON ag.`id_attribute_group` = agl.`id_attribute_group`
			WHERE pa.`id_product` = ' . (int) ($product->id) . '
			AND pas.id_shop = ' . (int) $this->context->shop->id . '
			AND al.`id_lang` = ' . (int) $this->context->cookie->id_lang . '
			AND agl.`id_lang` = ' . (int) $this->context->cookie->id_lang . '
			ORDER BY agl.`public_name`, pa.id_product_attribute DESC, default_on ASC';

        /* Connected attributes */
        $id_lang = (int) $cookie->id_lang;


        /* get all attributes */
        $sqlConnectedAttributes = 'SELECT pa.*, product_attribute_shop.*, ag.`id_attribute_group`,
            ag.`is_color_group`, agl.`name` AS group_name, al.`name` AS attribute_name,
                a.`id_attribute`, pa.`unit_price_impact`, IFNULL(stock.quantity, 0) as quantity
            FROM `' . _DB_PREFIX_ . 'product_attribute` pa
            ' . Shop::addSqlAssociation('product_attribute', 'pa') . '
            ' . Product::sqlStock('pa', 'pa') . '
            LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac ON pac.`id_product_attribute` = pa.`id_product_attribute`
            LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a ON a.`id_attribute` = pac.`id_attribute`
            LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
            LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = ' . (int) $id_lang . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = ' . (int) $id_lang . ')
            WHERE pa.`id_product` = ' . (int) ($product->id) . '
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
            $connectedAttributesArray[$row['id_product_attribute']]['price'] = (float) ($row['price']);
            $connectedAttributesArray[$row['id_product_attribute']]['quantity'] = (int) $row['quantity'];
            $connectedAttributesArray[$row['id_product_attribute']]['minimal_quantity'] = (int) $row['minimal_quantity'];

            $connectedAttributesArray[$row['id_product_attribute']]['default_on'] = (int) $row['default_on'];
            if ($row['default_on']) {
                $defAttribute = (int) $row['id_product_attribute'];
            }
        }
        /* Remove simple attributes - connected attributes must contain a fixed number of groups */
        $notConnectedGroups = array();
        $connectedGroups = array();

        $connectedAttributeValuesAll = array();
        $notConnectedAttributeValuesAll = array();

        $allConnected = true;

        $result = Db::getInstance()->getValue("SELECT id_attribute_group "
            . "FROM " . _DB_PREFIX_ . "attribute_group_lang "
            . "WHERE name = 'awp_details' "
            . "ORDER BY id_attribute_group DESC");
        $awpDetailsIdGroup = $result;

        /* Remove all simple attributes (which have a one or multiple attributes from a single group) */
        /* Compute the not connected attribute groups */
        /* Compute the not connected attribute values */
        /* Check if all combinations are connected */
        foreach ($connectedAttributesArray as $k => $row) {
            $row['id_attribute_groups'] = array_unique($row['id_attribute_groups']);

            if (count($row['id_attribute_groups']) == 1) {
                $notConnectedGroups[] = $row['id_attribute_groups'][0];

                foreach ($row['attributes'] as $id_attribute) {
                    if ($awpDetailsIdGroup != $row['id_attribute_groups'][0]) {
                        $notConnectedAttributeValuesAll[] = $id_attribute;
                    }
                }
                unset($connectedAttributesArray[$k]);

                if ($awpDetailsIdGroup != $row['id_attribute_groups'][0]) {
                    $allConnected = false;
                }
            }
        }

        if (isset($connectedAttributesArray[$defAttribute])) {
            $defaultConnectedAttribute = $connectedAttributesArray[$defAttribute];
        } else {
            $defaultConnectedAttribute = null;
        }
        /* If all combinations are connected keep the default combination */
        if (!$allConnected) {
            unset($connectedAttributesArray[$defAttribute]);
        }
        /* Compute all connected groups & attributes */
        foreach ($connectedAttributesArray as $k => $row) {
            foreach ($row['id_attribute_groups'] as $idAttributeGroup) {
                $connectedGroups[] = $idAttributeGroup;
            }
            foreach ($row['attributes'] as $id_attribute) {
                $connectedAttributeValuesAll[] = $id_attribute;
            }
        }
        $connectedGroups = array_unique($connectedGroups);
        $notConnectedGroups = array_unique($notConnectedGroups);

        /* Compute different types of connected attributes */
        $notConnectedAttributeValuesAll = array_unique($notConnectedAttributeValuesAll);
        $connectedAttributeValuesAll = array_unique($connectedAttributeValuesAll);


        /* containsSearchAll means that the product has the same attribute groups for both connected and simple attributes */
        /* If so, then only price will be applied & show / hide functionality will be disabled */
        if (empty($notConnectedAttributeValuesAll)) {
            $bothConnectedAttributes = $connectedAttributeValuesAll;
        } else {
            $bothConnectedAttributes = array_intersect($notConnectedAttributeValuesAll, $connectedAttributeValuesAll);
        }
        $bothConnectedAttributes = array_merge($bothConnectedAttributes, $connectedAttributeValuesAll);
        $bothConnectedAttributes = array_unique($bothConnectedAttributes);

        $awpDetailsIdGroupArrp = array();
        $awpDetailsIdGroupArrp[] = $awpDetailsIdGroup;
        $connectedGroups = array_diff($connectedGroups, $awpDetailsIdGroupArrp);
        $notConnectedGroups = array_diff($notConnectedGroups, $awpDetailsIdGroupArrp);
        if (empty($notConnectedAttributeValuesAll)) {
            $containsSearchAll = false;
        } else {
            $containsSearchAll = count($bothConnectedAttributes) == count($connectedAttributeValuesAll);
        }

        if (!empty($notConnectedGroups)) {
            $bothConnectedGroups = array_intersect($connectedGroups, $notConnectedGroups);
            if (empty($bothConnectedGroups)) {
                $containsSearchAll = false;
            }
        }
        $notConnectedGroups = array_filter($notConnectedGroups);
        $singleAttributeGroup = false;
        if (empty($connectedGroups) && sizeof($notConnectedGroups) == 1) {
            $singleAttributeGroup = true;
        }
        $smarty->assign("singleAttributeGroup", $singleAttributeGroup);
        $smarty->assign("bothConnectedAttributes", $bothConnectedAttributes);

        $smarty->assign("containsSearchAll", $containsSearchAll);
        $smarty->assign("defaultConnectedAttribute", $defaultConnectedAttribute);
        $smarty->assign("connectedGroups", $connectedGroups);
        $smarty->assign("notConnectedGroups", $notConnectedGroups);
        $smarty->assign("connectedAttributes", $connectedAttributesArray);
        /* End connected attributes */

        $attributesGroups = Db::getInstance()->ExecuteS($query);
        if (Db::getInstance()->numRows()) {
            $groups = array();
            $master = false;
            $default_on = array();
            foreach ($attributesGroups AS $k => $row) {
                /* Color management */
                if (isset($row['attribute_color']) && $row['attribute_color'] && $row['id_attribute_group'] == $product->id_color_default) {
                    $colors[$row['id_attribute']]['value'] = $row['attribute_color'];
                    $colors[$row['id_attribute']]['name'] = $row['attribute_name'];
                }
                $group_order = $this->isInGroup($row['id_attribute_group'], $this->awp_attributes);
                if ($group_order == -1) {
                    continue;
                }
                $groups[$group_order]['id_group'] = $row['id_attribute_group'];
                $groups[$group_order]['group_type'] = $this->awp_attributes[$group_order]['group_type'];
                if ($this->awp_attributes[$group_order]['group_type'] == "checkbox") {
                    $groups[$group_order]['group_header'] = htmlspecialchars_decode($this->awp_attributes[$group_order]['group_header_' . $cookie->id_lang]);
                }
                if (isset($this->awp_attributes[$group_order]['group_max_limit'])) {
                    $groups[$group_order]['group_max_limit'] = (int) $this->awp_attributes[$group_order]['group_max_limit'];
                }
                if (isset($this->awp_attributes[$group_order]['group_required'])) {
                    $groups[$group_order]['group_required'] = (int) $this->awp_attributes[$group_order]['group_required'];
                }
                if (isset($this->awp_attributes[$group_order]['image_upload'])) {
                    $groups[$group_order]['image_upload'] = $this->awp_attributes[$group_order]['image_upload'];
                }
                if (isset($this->awp_attributes[$group_order]['group_url'])) {
                    $groups[$group_order]['group_url'] = $this->awp_attributes[$group_order]['group_url'];
                }
                if (isset($this->awp_attributes[$group_order]['group_color'])) {
                    $groups[$group_order]['group_color'] = $this->awp_attributes[$group_order]['group_color'];
                }
                if (isset($this->awp_attributes[$group_order]['group_width'])) {
                    $groups[$group_order]['group_width'] = (int) $this->awp_attributes[$group_order]['group_width'];
                }
                if (isset($this->awp_attributes[$group_order]['group_height'])) {
                    $groups[$group_order]['group_height'] = (int) $this->awp_attributes[$group_order]['group_height'];
                }
                if (isset($this->awp_attributes[$group_order]['group_resize'])) {
                    $groups[$group_order]['group_resize'] = $this->awp_attributes[$group_order]['group_resize'];
                }
                if (isset($this->awp_attributes[$group_order]['group_layout'])) {
                    $groups[$group_order]['group_layout'] = $this->awp_attributes[$group_order]['group_layout'];
                }
                if (isset($this->awp_attributes[$group_order]['group_per_row'])) {
                    $groups[$group_order]['group_per_row'] = (int) $this->awp_attributes[$group_order]['group_per_row'];
                }
                if (isset($this->awp_attributes[$group_order]['group_hide_name'])) {
                    $groups[$group_order]['group_hide_name'] = (int) $this->awp_attributes[$group_order]['group_hide_name'];
                }
                if (isset($this->awp_attributes[$group_order]['group_calc_min'])) {
                    $groups[$group_order]['group_calc_min'] = $this->awp_attributes[$group_order]['group_calc_min'];
                }
                if (isset($this->awp_attributes[$group_order]['group_calc_max'])) {
                    $groups[$group_order]['group_calc_max'] = $this->awp_attributes[$group_order]['group_calc_max'];
                }
                if (isset($this->awp_attributes[$group_order]['group_calc_multiply']) && $this->awp_attributes[$group_order]['group_type'] == "calculation") {
                    $groups[$group_order]['group_calc_multiply'] = $this->getFeatureVal($cookie->id_lang, $product->id, $this->awp_attributes[$group_order]['group_calc_multiply']);
                }
                if (isset($this->awp_attributes[$group_order]['group_quantity_zero'])) {
                    $groups[$group_order]['group_quantity_zero'] = $this->awp_attributes[$group_order]['group_quantity_zero'];
                }
                if (isset($this->awp_attributes[$group_order]['chk_limit_min'])) {
                    $groups[$group_order]['chk_limit_min'] = (int) $this->awp_attributes[$group_order]['chk_limit_min'];
                }
                if (isset($this->awp_attributes[$group_order]['chk_limit_max'])) {
                    $groups[$group_order]['chk_limit_max'] = (int) $this->awp_attributes[$group_order]['chk_limit_max'];
                }
                /* Connected attributes - send DO NOT HIDE OPTION */
                if (isset($this->awp_attributes[$group_order]['connected_do_not_hide'])) {
                    $groups[$group_order]['connected_do_not_hide'] = (int) $this->awp_attributes[$group_order]['connected_do_not_hide'];
                }
                /* END Connected attributes - send DO NOT HIDE OPTION */

                if (isset($this->awp_attributes[$group_order]['group_description_' . $cookie->id_lang])) {
                    $groups[$group_order]['group_description'] = htmlspecialchars_decode($this->awp_attributes[$group_order]['group_description_' . $cookie->id_lang]);
                    if (substr_count($groups[$group_order]['group_description'], "<") < 2) {
                        $groups[$group_order]['group_description'] = nl2br($groups[$group_order]['group_description']);
                    }
                }
                if (isset($this->awp_attributes[$group_order]['group_file_ext'])) {
                    $groups[$group_order]['group_file_ext'] = $this->awp_attributes[$group_order]['group_file_ext'];
                }

                if (isset($this->awp_attributes[$group_order]['price_impact_per_char']) && $this->awp_attributes[$group_order]['price_impact_per_char'] == 1) {
                    $groups[$group_order]['price_impact_per_char'] = $this->awp_attributes[$group_order]['price_impact_per_char'];
                    $groups[$group_order]['group_min_limit'] = $this->awp_attributes[$group_order]['group_min_limit'];
                    $groups[$group_order]['exceptions'] = $this->awp_attributes[$group_order]['exceptions'];
                }

                $attribute_order = $this->isInAttribute($row['id_attribute'], $this->awp_attributes[$group_order]["attributes"]);
                $idProductAttr = $this->awp_attributes[$group_order][$row['id_attribute']]['attr_product'];
                $idProductDefAttribute = 0;
                if ($idProductAttr > 0) {
                    $idProductDefAttribute = Product::getDefaultAttribute($idProductAttr);
                }
                $groups[$group_order]['attributes'][$attribute_order] = array(
                        $row['id_attribute'], 
                        $row['attribute_name'], 
                        ((isset($this->awp_attributes[$group_order]['group_color']) && $this->awp_attributes[$group_order]['group_color'] == 1) ? $row['attribute_color'] : ""), 
                        (isset($this->awp_attributes[$group_order]['attributes'][$attribute_order]['image_upload_attr']) ? $this->awp_attributes[$group_order]['attributes'][$attribute_order]['image_upload_attr'] : ''),
                    (isset($this->awp_attributes[$group_order][$row['id_attribute']]['attr_product']) ? $this->awp_attributes[$group_order][$row['id_attribute']]['attr_product'] : ''),
                    (isset($this->awp_attributes[$group_order][$row['id_attribute']]['attr_description_' . $id_lang]) ? $this->awp_attributes[$group_order][$row['id_attribute']]['attr_description_' . $id_lang] : ''),
                    $idProductDefAttribute
                    
                    );
                
                
                $groups[$group_order]['name'] = $row['public_group_name'];
                if ($row['default_on']) {
                    if (isset($groups[$group_order]['default'])) {
                        array_push($groups[$group_order]['default'], (int) ($row['id_attribute']));
                    } else {
                        $groups[$group_order]['default'] = array((int) ($row['id_attribute']));
                    }
                    if (!isset($groups[$group_order]['attributes_quantity'][$row['id_attribute']])) {
                        $groups[$group_order]['attributes_quantity'][$row['id_attribute']] = $use_stock ? (int) ($row['quantity']) : 0;
                        $default_on[$row['id_attribute']] = 1;
                    }
                } else {
                    if (!isset($groups[$group_order]['attributes_quantity'][$row['id_attribute']]) || array_key_exists($row['id_attribute'], $default_on)) {
                        $groups[$group_order]['attributes_quantity'][$row['id_attribute']] = 0;
                        unset($default_on[$row['id_attribute']]);
                    }
                    $groups[$group_order]['attributes_quantity'][$row['id_attribute']] += $use_stock ? (int) ($row['quantity']) : 0;
                }

                if (isset($this->awp_attributes[$group_order]['parent_group_name_' . $cookie->id_lang])) {
                    $groups[$group_order]['parent_group_name'] = $this->awp_attributes[$group_order]['parent_group_name_' . $cookie->id_lang];
                }


            }
            if(isset($this->awp_sort_attributes_alphab) && !empty($this->awp_sort_attributes_alphab) && $this->awp_sort_attributes_alphab){
                foreach($groups as &$group){
                    $names = array_column($group['attributes'], '1');
                    array_multisort($names, SORT_ASC, $group['attributes']);
                }
            }
            $ins = Tools::getValue('ins');
            $awp_qty_edit = 0;
            $awp_is_edit = 0;
            $awp_edit_special_values = array();
            if ($ins != '') {
                //print 'SELECT * FROM `'._DB_PREFIX_.'cart_product` WHERE id_product = '.$product->id.' AND id_product_attribute = '.Tools::getValue('ipa').' AND instructions_valid = "'.$ins.'"';
                $ids = Db::getInstance()->ExecuteS('SELECT * FROM `' . _DB_PREFIX_ . 'cart_product` WHERE id_product = ' . (int) $product->id . ' AND id_product_attribute = ' . (int) Tools::getValue('ipa') . ' AND instructions_valid = "' . pSQL($ins) . '"');
                if (is_array($ids) && sizeof($ids) > 0) {
                    $ids_array = explode(",", Tools::substr($ids[0]['instructions_id'], 1));
                    $awp_text = $ids[0]['instructions'];
                    $awp_qty_edit = $ids[0]['quantity'];
                    $awp_is_edit = 1;
                    foreach ($groups as $key => $val) {
                        $arr = $val['attributes'];
                        if (!is_array($arr)) {
                            $arr = array();
                        }
                        ksort($arr);
                        $groups[$key]['attributes'] = $arr;
                        $groups[$key]['default'] = array();
                        foreach ($ids_array as $ids) {
                            if ($this->isInAttribute($ids, $this->awp_attributes[$key]["attributes"]) != -1) {
                                if (in_array($val['group_type'], array("textbox", "textarea", "file"))) {
                                    //print "$awp_text<br />";
                                    if ($text_val = $this->getAttributeValue($ids, $awp_text)) {
                                        $awp_edit_special_values[$ids] = $text_val;
                                    }
                                    if ($text_val = $this->getAttributeFileValue($ids, $awp_text)) {
                                        $awp_edit_special_values[$ids . '_file'] = $text_val;
                                    }
                                }
                                //$text = explode($this->_awp_attributes[$key]["attributes"]['attribute_name'], $string)
                                $groups[$key]['default'][] = $ids;
                            }
                        }
                    }
                }
            } else {
                foreach ($groups as $key => $val) {
                    $arr = $val['attributes'];
                    if (!is_array($arr)) {
                        $arr = array();
                    }
                    ksort($arr);
                    $groups[$key]['attributes'] = $arr;
                    //print_r($arr);
                    //if ($groups[$key]['group_type'] != "checkbox" && $groups[$key]['group_type'] != "quantity" && !array_key_exists('default',$val) && array_key_exists('0',$arr))
                    if (($val['group_type'] == "radio" || $val['group_type'] == "dropdown") && !isset($val['default']) && isset($arr['0'])) {
                        $groups[$key]['default'] = array($arr[0][0]);
                    } elseif (!isset($val['default'])) {
                        $groups[$key]['default'] = array();
                    }
                }
            }
            $smarty->assign("awp_is_edit", $awp_is_edit);
            $smarty->assign("awp_qty_edit", $awp_qty_edit);
            ksort($groups);

            if (!isset($smarty->registered_plugins['function']['getGroupImageTag'])) {
                $smarty->registerPlugin('function', 'getGroupImageTag', array('AttributeWizardPro', 'getGroupImageTag')); // or keep a backward compatibility if PHP version < 5.1.2
            }
            if (!isset($smarty->registered_plugins['function']['getLayeredImageTag'])) {
                $smarty->registerPlugin('function', 'getLayeredImageTag', array('AttributeWizardPro', 'getLayeredImageTag')); // or keep a backward compatibility if PHP version < 5.1.2
            }
            /* Filter using id_dhop */
            $query = 'SELECT pa.id_product, pas.price, pas.weight, pas.minimal_quantity, pac.id_attribute '
                . 'FROM `' . _DB_PREFIX_ . 'product_attribute` AS pa,`'
                . _DB_PREFIX_ . 'product_attribute_shop` AS pas, `'
                . _DB_PREFIX_ . 'product_attribute_combination` AS pac '
                . 'WHERE pas.id_shop = ' . (int) $this->context->shop->id
                . ' AND (pas.default_on = 0 OR pas.default_on IS NULL) '
                . 'AND pas.id_product_attribute = pac.id_product_attribute '
                . 'AND pas.id_product_attribute = pa.id_product_attribute '
                . 'AND pa.id_product = ' . (int) $id_product
                . ' GROUP BY pac.id_attribute';
            $attribute_impact = Db::getInstance()->ExecuteS($query);
            $query = 'SELECT pa.id_product, pas.price, pas.weight, pas.minimal_quantity, pac.id_attribute '
                . 'FROM `' . _DB_PREFIX_ . 'product_attribute` AS pa,`'
                . _DB_PREFIX_ . 'product_attribute_shop` AS pas, `'
                . _DB_PREFIX_ . 'product_attribute_combination` AS pac '
                . 'WHERE pas.id_shop = ' . (int) $this->context->shop->id
                . ' AND pas.default_on = 1'
                . ' AND pas.id_product_attribute = pac.id_product_attribute'
                . ' AND pas.id_product_attribute = pa.id_product_attribute'
                . ' AND pa.id_product = ' . (int) $id_product
                . ' GROUP BY pac.id_attribute';
            $attribute_impact_default = Db::getInstance()->ExecuteS($query);

            foreach ($attribute_impact_default as $drow) {
                $found = false;
                foreach ($attribute_impact as $row) {
                    if ($drow['id_attribute'] == $row['id_attribute']) {
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    array_push($attribute_impact, $drow);
                }
            }
            //	print_r($product);
            //print_r($attribute_impact_default);
            //print_r($this->_awp_attributes);
            //print_r($groups);
            $awp_currency = Currency::getCurrency($currency);
            //print_r($awp_edit_special_values);
            $smarty->assign("awp_add_to_cart", $this->awp_add_to_cart);
            $smarty->assign("awp_out_of_stock", $this->awp_out_of_stock);
            $smarty->assign("awp_ins", Tools::getValue('ins'));
            $smarty->assign("awp_ipa", Tools::getValue('ipa'));
            // Check for popup image display selection
            if ($this->awp_popup_image) {
                $tmp_pit = explode('|||', $this->awp_popup_image_type);
                $cover = Product::getCover($product->id);
                if (is_array($cover) && sizeof($cover) == 1) {
                    $img_src = $link->getImageLink($product->link_rewrite, $product->id . '-' . $cover['id_image'], $tmp_pit[0]);
                    $awp_product_image = array('src' => $img_src);
                    $tmp_pit = explode('x', $tmp_pit[1]);
                    $awp_product_image['width'] = $tmp_pit[0];
                    $awp_product_image['height'] = $tmp_pit[1];
                    $smarty->assign("awp_product_image", $awp_product_image);
                }
            }

            $currency = $cookie->id_currency;
            if (!$currency) {
                $currency = Configuration::get('PS_CURRENCY_DEFAULT');
            }
            $currencyObj = new Currency((int) $currency);
            $awp_currency = $currencyObj;
            if ($product->specificPrice && $product->specificPrice['reduction'] && $product->specificPrice['reduction_type'] == 'percentage') {
                $reduction_percent = $product->specificPrice['reduction'] * 100;
            } else {
                $reduction_percent = 0;
            }
            //print_r($currencyObj);
            $this->assignAttributesGroups($product);
            $address = new Address($this->context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
            $tax = (float) $product->getTaxesRate(new Address((int) $this->context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')}));


            if (!isset($smarty->registered_plugins['function']['awpConvertPriceWithCurrency'])) {
//                $smarty->registerPlugin('function', 'awpConvertPriceWithCurrency', array('AttributeWizardPro', 'awpConvertPriceWithCurrency')); // or keep a backward compatibility if PHP version < 5.1.2
                smartyRegisterFunction($smarty, 'function', 'awpConvertPriceWithCurrency', array('Product', 'convertPriceWithCurrency'));
            }

            // prepare currency format characters array based on pre-formatted price as an example
            $currency_chars = [];
            $example_price_org = mb_ereg_replace("[\s]","",Tools::displayPrice(1111.11)); // dummy value
            $example_price = mb_ereg_replace("[".$currencyObj->sign."]","",$example_price_org); // dummy value

            if (mb_strpos($example_price_org, '1') === 0) {
                $currency_chars['sign'] = mb_substr($example_price_org, mb_strrpos($example_price_org, '1') + 1);
                $currency_chars['sign_position'] = 'end';
            } else {
                $currency_chars['sign'] = mb_substr($example_price_org, 0, mb_strpos($example_price_org, '1'));
                $currency_chars['sign_position'] = 'start';
            }
            //$currency_chars['sign'] = mb_substr($example_price, 0, mb_strpos($example_price, '1'));
            $example_price = mb_ereg_replace(preg_quote($currency_chars['sign']), '', $example_price); // with escaping regex special chars
            $currency_chars['thousands'] = mb_substr($example_price, 1, 1);
            $currency_chars['thousands'] = Validate::isInt($currency_chars['thousands']) ? null : $currency_chars['thousands'];
            $currency_chars['decimal'] = mb_substr($example_price, mb_strlen($example_price) - 3, 1);
            $currency_chars['sign'] = $currencyObj->sign;
            if(version_compare('1.7.7.0', $this->getRawPSV()) !== 1)
            {
                $smarty->assign("priceDisplayPrecision", $this->context->getComputingPrecision());
            }
            else
            {
                $smarty->assign("priceDisplayPrecision", Configuration::get('PS_PRICE_DISPLAY_PRECISION', 2));
            }

            $smarty->assign("awp_add_to_cart", $this->awp_add_to_cart);
            $smarty->assign(array(
                'col_img_dir' => _PS_COL_IMG_DIR_,
                'img_col_dir' => _THEME_COL_DIR_,
                'this_wizard_path' => __PS_BASE_URI__ . 'modules/attributewizardpro/',
                'awp_allow_oosp' => $product->isAvailableWhenOutOfStock((int) $product->out_of_stock),
                'awp_stock' => (!Configuration::get('PS_DISPLAY_QTIES') || $product->quantity <= 0 || !$product->available_for_order || Configuration::get('PS_CATALOG_MODE') ? '' : 1),
                'awp_display_qty' => Configuration::get('PS_DISPLAY_QTIES'),
                'awp_pi_display' => $this->awp_pi_display,
                'awp_layered_image' => $this->awp_layered_image,
                'groups' => $groups,
                'awp_ajax' => Configuration::get('PS_BLOCK_CART_AJAX'),
                'awp_no_customize' => (int) ($this->awp_no_customize),
                'awp_second_add' => (int) ($this->awp_second_add),
                'awp_popup' => (int) ($this->awp_popup),
                'awp_fade' => (int) ($this->awp_fade),
                'awp_opacity' => (int) ($this->awp_opacity),
                'awp_popup_width' => (int) ($this->awp_popup_width),
                'awp_popup_top' => (int) ($this->awp_popup_top),
                'awp_popup_left' => (int) ($this->awp_popup_left),
                'awp_no_tax_impact' => (int) ($this->awp_no_tax_impact),
                'awp_adc_no_attribute' => (int) ($this->awp_adc_no_attribute),
                'awp_gd_popup' => (int) ($this->awp_gd_popup),
                'awp_collapse_block' => (int) $this->awp_collapse_block,
                'awp_disable_url_hash' => (int) $this->awp_disable_url_hash,
                'awp_edit_special_values' => $awp_edit_special_values,
                'attributeImpacts' => $attribute_impact,
                'awp_currency' => $awp_currency,
                'awp_converted_price' => 0,
                'productBasePriceTaxIncl' => (float) $product->getPrice(false, false, 6, null, false, false),
                'productPriceTaxExcluded' => (float) $product->getPriceWithoutReduct(true, false),
                'displayPrice' => Product::getTaxCalculationMethod((int) $this->context->cookie->id_customer),
                'reduction_percent' => (float) $reduction_percent,
                'awp_currency_chars' => json_encode($currency_chars),
                'currencyFormat' => $currencyObj->format,
                'currencySign' => $currencyObj->sign,
                'currencyBlank' => $currencyObj->blank,
                'currencyRate' => (float) $currencyObj->conversion_rate,
                'roundMode' => (int) Configuration::get('PS_PRICE_ROUND_MODE'),
                'noTaxForThisProduct' => Tax::excludeTaxeOption() || !$product->getTaxesRate($address),
                'allowBuyWhenOutOfStock' => $product->isAvailableWhenOutOfStock((int) $product->out_of_stock),
                'isQuickView' => $isQuickView,
                'quickViewProductLink' => $this->context->link->getProductLink($product),
                'taxRate' => (float) $tax,
                'awp_psv' => (float) (Tools::substr(_PS_VERSION_, 0, 3)),
                'awp_psv3' => Tools::substr(_PS_VERSION_, 0, 5) . (Tools::substr(_PS_VERSION_, 5, 1) != "." ? Tools::substr(_PS_VERSION_, 5, 1) : ""),
                'awp_reload_page' => Configuration::get('PS_CART_REDIRECT') == 0 ? 1 : 0,
                'awp_currency_rate' => $awp_currency->conversion_rate));
            return $this->display(__FILE__, 'attributewizardpro.tpl');
        }
    }

    /**
     * Assign template vars related to attribute groups and colors
     */
    protected function assignAttributesGroups($product)
    {
        $colors = array();
        $groups = array();

        // @todo (RM) should only get groups and not all declination ?
        $attributes_groups = $product->getAttributesGroups($this->context->language->id);
        if (is_array($attributes_groups) && $attributes_groups) {
            $combination_images = $product->getCombinationImages($this->context->language->id);
            $combination_prices_set = array();
            foreach ($attributes_groups as $k => $row) {
                // Color management
                if (isset($row['is_color_group']) && $row['is_color_group'] && (isset($row['attribute_color']) && $row['attribute_color']) || (file_exists(_PS_COL_IMG_DIR_ . $row['id_attribute'] . '.jpg'))) {
                    $colors[$row['id_attribute']]['value'] = $row['attribute_color'];
                    $colors[$row['id_attribute']]['name'] = $row['attribute_name'];
                    if (!isset($colors[$row['id_attribute']]['attributes_quantity'])) {
                        $colors[$row['id_attribute']]['attributes_quantity'] = 0;
                    }
                    $colors[$row['id_attribute']]['attributes_quantity'] += (int) $row['quantity'];
                }
                if (!isset($groups[$row['id_attribute_group']])) {
                    $groups[$row['id_attribute_group']] = array(
                        'group_name' => $row['group_name'],
                        'name' => $row['public_group_name'],
                        'group_type' => $row['group_type'],
                        'default' => -1,
                    );
                }

                $groups[$row['id_attribute_group']]['attributes'][$row['id_attribute']] = $row['attribute_name'];
                if ($row['default_on'] && $groups[$row['id_attribute_group']]['default'] == -1) {
                    $groups[$row['id_attribute_group']]['default'] = (int) $row['id_attribute'];
                }
                if (!isset($groups[$row['id_attribute_group']]['attributes_quantity'][$row['id_attribute']])) {
                    $groups[$row['id_attribute_group']]['attributes_quantity'][$row['id_attribute']] = 0;
                }
                $groups[$row['id_attribute_group']]['attributes_quantity'][$row['id_attribute']] += (int) $row['quantity'];

                $combinations[$row['id_product_attribute']]['attributes_values'][$row['id_attribute_group']] = $row['attribute_name'];
                $combinations[$row['id_product_attribute']]['attributes'][] = (int) $row['id_attribute'];
                $combinations[$row['id_product_attribute']]['price'] = (float) Tools::convertPriceFull($row['price'], null, Context::getContext()->currency, false);

                // Call getPriceStatic in order to set $combination_specific_price
                if (!isset($combination_prices_set[(int) $row['id_product_attribute']])) {
                    Product::getPriceStatic((int) $product->id, false, $row['id_product_attribute'], 6, null, false, true, 1, false, null, null, null, $combination_specific_price);
                    $combination_prices_set[(int) $row['id_product_attribute']] = true;
                    $combinations[$row['id_product_attribute']]['specific_price'] = $combination_specific_price;
                }
                $combinations[$row['id_product_attribute']]['ecotax'] = (float) $row['ecotax'];
                $combinations[$row['id_product_attribute']]['weight'] = (float) $row['weight'];
                $combinations[$row['id_product_attribute']]['quantity'] = (int) $row['quantity'];
                $combinations[$row['id_product_attribute']]['reference'] = $row['reference'];
                $combinations[$row['id_product_attribute']]['unit_impact'] = Tools::convertPriceFull($row['unit_price_impact'], null, Context::getContext()->currency, false);
                $combinations[$row['id_product_attribute']]['minimal_quantity'] = (int) $row['minimal_quantity'];
                if ($row['available_date'] != '0000-00-00' && Validate::isDate($row['available_date'])) {
                    $combinations[$row['id_product_attribute']]['available_date'] = $row['available_date'];
                    $combinations[$row['id_product_attribute']]['date_formatted'] = Tools::displayDate($row['available_date']);
                } else {
                    $combinations[$row['id_product_attribute']]['available_date'] = $combinations[$row['id_product_attribute']]['date_formatted'] = '';
                }

                if (!isset($combination_images[$row['id_product_attribute']][0]['id_image'])) {
                    $combinations[$row['id_product_attribute']]['id_image'] = -1;
                } else {
                    $combinations[$row['id_product_attribute']]['id_image'] = $id_image = (int) $combination_images[$row['id_product_attribute']][0]['id_image'];
                    if ($row['default_on']) {
                        $current_cover['id_image'] = 0;
                        if (isset($this->context->smarty->tpl_vars['cover']->value)) {
                            $current_cover = $this->context->smarty->tpl_vars['cover']->value;
                        }

                        if (is_array($combination_images[$row['id_product_attribute']])) {
                            foreach ($combination_images[$row['id_product_attribute']] as $tmp) {
                                if ($tmp['id_image'] == $current_cover['id_image']) {
                                    $combinations[$row['id_product_attribute']]['id_image'] = $id_image = (int) $tmp['id_image'];
                                    break;
                                }
                            }
                        }

                        if ($id_image > 0) {
                            if (isset($this->context->smarty->tpl_vars['images']->value)) {
                                $product_images = $this->context->smarty->tpl_vars['images']->value;
                            }
                            if (isset($product_images) && is_array($product_images) && isset($product_images[$id_image])) {
                                $product_images[$id_image]['cover'] = 1;
                                $this->context->smarty->assign('mainImage', $product_images[$id_image]);
                                if (count($product_images)) {
                                    $this->context->smarty->assign('images', $product_images);
                                }
                            }
                            if (isset($this->context->smarty->tpl_vars['cover']->value)) {
                                $cover = $this->context->smarty->tpl_vars['cover']->value;
                            }
                            if (isset($cover) && is_array($cover) && isset($product_images) && is_array($product_images)) {
                                $product_images[$cover['id_image']]['cover'] = 0;
                                if (isset($product_images[$id_image])) {
                                    $cover = $product_images[$id_image];
                                }
                                $cover['id_image'] = (Configuration::get('PS_LEGACY_IMAGES') ? ($this->product->id . '-' . $id_image) : (int) $id_image);
                                $cover['id_image_only'] = (int) $id_image;
                                $this->context->smarty->assign('cover', $cover);
                            }
                        }
                    }
                }
            }

            // wash attributes list (if some attributes are unavailables and if allowed to wash it)
            if (!Product::isAvailableWhenOutOfStock($product->out_of_stock) && Configuration::get('PS_DISP_UNAVAILABLE_ATTR') == 0) {
                foreach ($groups as &$group) {
                    foreach ($group['attributes_quantity'] as $key => &$quantity) {
                        if ($quantity <= 0) {
                            unset($group['attributes'][$key]);
                        }
                    }
                }

                foreach ($colors as $key => $color) {
                    if ($color['attributes_quantity'] <= 0) {
                        unset($colors[$key]);
                    }
                }
            }
            foreach ($combinations as $id_product_attribute => $comb) {
                $attribute_list = '';
                foreach ($comb['attributes'] as $id_attribute) {
                    $attribute_list .= '\'' . (int) $id_attribute . '\',';
                }
                $attribute_list = rtrim($attribute_list, ',');
                $combinations[$id_product_attribute]['list'] = $attribute_list;
            }

            $newComb = array();
            $newComb['combinations'] = $combinations;
            $arrSpecificPrice = array();
            if ($product->specificPrice && is_array($product->specificPrice)) {
                $arrSpecificPrice['product_specific_price'] = $product->specificPrice;
            }
            $this->context->smarty->assign(array(
                'groups' => $groups,
                'colors' => (count($colors)) ? $colors : false,
                'combinations' => $newComb,
                'product_specific_price' => $arrSpecificPrice,
                'combinationImages' => $combination_images
            ));
        }
    }

    public function hookHeader()
    {
        $smarty = $this->context->smarty;
        $cookie = $this->context->cookie;

        $smarty->assign("awp_url_rewrite", (int) Configuration::get('PS_REWRITING_SETTINGS'));

        // id_product could come in GET or POST
        if(!Tools::getValue('id_product')){
            return;
        }
        $product = new Product((int) Tools::getValue('id_product'), true, (int) $cookie->id_lang);
        if ($product->hasAttributes() <= 0) {
            return;
        }
        if ($this->awp_display_wizard != 1) {
            if ($this->awp_display_wizard_field == "Reference" && $this->awp_display_wizard_value != $product->reference) {
                return;
            }
            if ($this->awp_display_wizard_field == "Supplier Reference" && $this->awp_display_wizard_value != $product->supplier_reference) {
                return;
            }
            if ($this->awp_display_wizard_field == "EAN13" && $this->awp_display_wizard_value != $product->ean13) {
                return;
            }
            if ($this->awp_display_wizard_field == "UPC" && $this->awp_display_wizard_value != $product->upc) {
                return;
            }
            if ($this->awp_display_wizard_field == "Location" && $this->awp_display_wizard_value != $product->location) {
                return;
            }
        }

        $this->context->controller->addCSS(($this->_path) . 'views/css/awp.css', 'all');
//        $this->context->controller->addJS(($this->_path) . 'views/js/awp_product.js');
        $this->context->controller->registerJavascript('modules-awp', 'modules/'.$this->name.'/views/js/awp_product.js', ['position' => 'bottom', 'priority' => 500]);
        if ($this->awp_gd_popup) {
            $this->context->controller->addCSS(($this->_path) . 'views/css/tooltipster.css', 'all');
            $this->context->controller->addJS(($this->_path) . 'views/js/jquery.tooltipster.min.js');
        }

        $awpAvailableTxt = '';
        $stock = Product::getQuantity(Tools::getValue('id_product'), Tools::getValue('id_product_attribute'));
        if(isset($product->available_now) && $stock > 0)
            $awpAvailableTxt = $product->available_now;
        elseif(isset($product->available_later))
            $awpAvailableTxt = $product->available_later;

        Media::addJsDef([
            'awpAvailableTxt' => $awpAvailableTxt,
        ]);
        $smarty->assign("awp_add_to_cart", $this->awp_add_to_cart);
        return $this->display(__FILE__, 'header.tpl');
    }

    public function prepareAWPCombinationGeneratorTab()
    {
        $attribute_groups = AttributeGroup::getAttributesGroups($this->context->language->id);

        $awpDetailsIdGroup = Db::getInstance()->getValue("SELECT id_attribute_group FROM " . _DB_PREFIX_ . "attribute_group_lang WHERE name = 'awp_details' ORDER BY id_attribute_group DESC");

        foreach ($attribute_groups as $key => $group) {
            // exclude awp_details
            if($group['id_attribute_group'] == $awpDetailsIdGroup) {
                unset($attribute_groups[$key]);
                continue;
            }

            $attribute_values = AttributeGroup::getAttributes($this->context->language->id, $group['id_attribute_group']);

            // Merge duplicated attributes for CONTEXT_ALL
            $merged_attributes = [];
            foreach ($attribute_values as $attribute_value)
            {
                if (!isset($merged_attributes[$attribute_value['id_attribute']]) || $attribute_value['id_shop'] === $this->context->shop->id)
                {
                    $merged_attributes[$attribute_value['id_attribute']] = $attribute_value;
                }
            }
            $attribute_values = array_values($merged_attributes);

            $attribute_groups[$key]['attributes'] = $attribute_values; //AttributeGroup::getAttributes($this->context->language->id, $group['id_attribute_group']);
            if (count($attribute_values) == 0){
                unset($attribute_groups[$key]);
            }
        }

        $this->context->smarty->assign(array(
            'awp_path' => $this->_path,
            'attribute_groups' => $attribute_groups,
            'awpDetailsIdGroup' => $awpDetailsIdGroup,
            'languages' => $this->context->controller->_languages,
            'default_language' => (int) Configuration::get('PS_LANG_DEFAULT')
        ));
    }

    function generate_combinations(array $data, array &$all = array(), array $group = array(), $value = null, $i = 0)
    {
        $keys = array_keys($data);
        if (isset($value) === true) {
            array_push($group, $value);
        }

        if ($i >= count($data)) {
            array_push($all, $group);
        } else {
            $currentKey = $keys[$i];
            $currentElement = $data[$currentKey];
            foreach ($currentElement as $val) {
                $this->generate_combinations($data, $all, $group, $val, $i + 1);
            }
        }

        return $all;
    }

    public function hookActionProductUpdate($params)
    {
        // get all languages
        // for each of them, store the new field
        $id_product = (int) Tools::getValue('id_product');

        $id_new_combinations = array();

        $product = new Product($id_product);
        $awpCombinationsOptions = Tools::getValue('awp');

        $awpGroupType = $awpCombinationsOptions['awp_group_type'];

        if (isset($awpCombinationsOptions['awp_attribute'])) {
            $awpAttributeSelected = $awpCombinationsOptions['awp_attribute'];
        } else {
            $awpAttributeSelected = array();
        }


        if (isset($awpCombinationsOptions['awp_group_shared'])) {
            $awpAttributeShared = $awpCombinationsOptions['awp_group_shared'];
        } else {
            $awpAttributeShared = array();
        }

        $awpAttributePrice = $awpCombinationsOptions['awp_attribute_price'];
        $awpAttributeQty = $awpCombinationsOptions['awp_attribute_qty'];
        $awpAttributeWeight = $awpCombinationsOptions['awp_attribute_weight'];

        $arrConnectedCombinations = array();

        $arrSharedConnectedCombinations = array();

        foreach ($awpAttributeSelected as $attributeGroup => $attributeValueArr) {
            $groupShared = false;
            if (isset($awpAttributeShared[$attributeGroup]) && !empty($awpAttributeShared[$attributeGroup])) {
                $groupShared = true;
            }
            // Connected or Non-connected
            $groupType = $awpGroupType[$attributeGroup];

            if ($groupType == 'separated') {
                if ($groupShared) {
                    // check if combination exists
                    $attributes = array();
                    $totalQty = 0;
                    $totalPriceImpact = 0;
                    $totalWeight = 0;
                    foreach ($attributeValueArr as $id_attribute => $value) {
                        $attributes[] = $id_attribute;

                        $totalPriceImpact += (float) $awpAttributePrice[$attributeGroup][$id_attribute];
                        $totalQty += (int) $awpAttributeQty[$attributeGroup][$id_attribute];
                        $totalWeight += (float) $awpAttributeWeight[$attributeGroup][$id_attribute];
                    }
                    $id_combination = (int) $product->productAttributeExists($attributes, false, null, true, true);

                    $obj = new Combination($id_combination);
                    $obj->id_product = $id_product;
                    $obj->minimal_quantity = 1;
                    $obj->quantity = $totalQty;
                    $obj->price = $totalPriceImpact;
                    $obj->ecotax = 0;
                    $obj->weight = $totalWeight;
                    $obj->available_date = '0000-00-00';
                    $obj->save();

                    StockAvailable::setQuantity($id_product, $obj->id, $totalQty);

                    if (isset($id_combination) && $id_combination > 0) {
                        //Db::getInstance()->delete('product_attribute_combination', 'id_product_attribute = ' . (int) $id_combination);
                    } else {
                        $attribute_list = array();
                        foreach ($attributeValueArr as $id_attribute => $value) {
                            $attribute_list[] = array(
                                'id_product_attribute' => (int) $obj->id,
                                'id_attribute' => (int) $id_attribute
                            );
                        }
                        $res = Db::getInstance()->insert('product_attribute_combination', $attribute_list);
                    }
                } else {
                    // A combination for each attribute
                    foreach ($attributeValueArr as $attributeValue => $value) {
                        // check if combination exists
                        $attributes = array();
                        $attributes[] = $attributeValue;

                        $id_combination = (int) $product->productAttributeExists($attributes, false, null, true, true);


                        $priceImpact = (float) $awpAttributePrice[$attributeGroup][$attributeValue];
                        $qty = (int) $awpAttributeQty[$attributeGroup][$attributeValue];

                        $weight = (float) $awpAttributeWeight[$attributeGroup][$attributeValue];
                        $obj = new Combination($id_combination);
                        $obj->id_product = $id_product;
                        $obj->minimal_quantity = 1;
                        $obj->quantity = $qty;
                        $obj->price = $priceImpact;
                        $obj->ecotax = 0;
                        $obj->weight = $weight;
                        $obj->available_date = '0000-00-00';
                        $obj->save();

                        StockAvailable::setQuantity($id_product, $obj->id, $qty);
                        if (isset($id_combination) && $id_combination > 0) {
                            //Db::getInstance()->delete('product_attribute_combination', 'id_product_attribute = ' . (int) $id_combination);
                        } else {
                            if ($obj->id) {
                                $attribute_list = array();
                                $attribute_list[] = array(
                                    'id_product_attribute' => (int) $obj->id,
                                    'id_attribute' => (int) $attributeValue
                                );
                                $res = Db::getInstance()->insert('product_attribute_combination', $attribute_list);
                            }
                        }
                    }
                }
            } else if ($groupType == 'connected') {
                if ($groupShared) {
                    foreach ($attributeValueArr as $id_attribute => $value) {
                        $arrSharedConnectedCombinations[] = (int) $id_attribute;
                    }
                } else {
                    foreach ($attributeValueArr as $id_attribute => $value) {
                        $arrConnectedCombinations[$attributeGroup][] = (int) $id_attribute;
                    }
                }
            }
        }
        // combination only connected non-shared combinations
        $arrConnectedAttributes = $this->generate_combinations($arrConnectedCombinations);

        // combine connected combinations with connected shared combinations
        $connectedCombinations = array();
        foreach ($arrConnectedAttributes as $k => $id_attribute_connected) {
            $connectedAttributes = array();
            foreach ($id_attribute_connected as $conn_id_attr) {
                $connectedAttributes[] = $conn_id_attr;
            }
            // Add plain connected and shared id_attributes
            foreach ($arrSharedConnectedCombinations as $i => $id_attribute_connected_shared) {
                $connectedAttributes[] = $id_attribute_connected_shared;
            }
            $connectedCombinations[] = $connectedAttributes;
        }

        if (isset($connectedCombinations) && sizeof($connectedCombinations) > 0 && sizeof($connectedCombinations[0]) > 0) {
            foreach ($connectedCombinations as $attributes) {
                $id_combination = (int) $product->productAttributeExists($attributes, false, null, true, true);
                $priceImpactTotal = 0;
                $qtyTotal = 0;
                $weightTotal = 0;
                foreach ($attributes as $attributeValue) {
                    $attribute = new Attribute((int) $attributeValue);
                    $attributeGroup = $attribute->id_attribute_group;
                    $priceImpactTotal += (float) $awpAttributePrice[$attributeGroup][$attributeValue];
                    $qtyTotal += (int) $awpAttributeQty[$attributeGroup][$attributeValue];
                    $weightTotal += (float) $awpAttributeWeight[$attributeGroup][$attributeValue];
                }
                $obj = new Combination($id_combination);
                $obj->id_product = (int) $id_product;
                $obj->minimal_quantity = 1;

                $obj->ecotax = 0;
                $obj->available_date = '0000-00-00';
                $obj->quantity = (int) $qtyTotal;
                $obj->price = (float) $priceImpactTotal;
                $obj->weight = (float) $weightTotal;
                $obj->save();

                StockAvailable::setQuantity($id_product, $obj->id, $qtyTotal);
                if (isset($id_combination) && $id_combination > 0) {
                    //Db::getInstance()->delete('product_attribute_combination', 'id_product_attribute = ' . (int) $id_combination);
                } else {
                    $obj->save();

                    $priceImpact = 0;
                    $qty = 0;
                    if ($obj->id > 0) {
                        $attribute_list = array();
                        foreach ($attributes as $attributeValue) {
                            //$priceImpact += (float) $awpAttributePrice[$attributeGroup][$attributeValue];
                            //$qty += (int) $awpAttributeQty[$attributeGroup][$attributeValue];
                            $attribute_list[] = array(
                                'id_product_attribute' => (int) $obj->id,
                                'id_attribute' => (int) $attributeValue
                            );
                        }
                        $res = Db::getInstance()->insert('product_attribute_combination', $attribute_list);
                    }
                }
            }
        }

        if (isset($awpCombinationsOptions['awp_attribute_default'])) {
            $awpDefault = $awpCombinationsOptions['awp_attribute_default'];
            $attribute_list = array();
            $priceImpactTotal = 0;
            $qtyTotal = 0;
            $weightTotal = 0;
            foreach ($awpDefault as $id_group => $attributes) {
                foreach ($attributes as $id_attribute => $sel) {
                    $attribute_list[] = $id_attribute;
                    $priceImpactTotal += (float) $awpAttributePrice[$id_group][$id_attribute];
                    $qtyTotal += (int) $awpAttributeQty[$id_group][$id_attribute];
                    $weightTotal += (float) $awpAttributeWeight[$id_group][$id_attribute];
                }
            }

            $product->deleteDefaultAttributes();
            $id_combination = (int) $product->productAttributeExists($attribute_list, false, null, true, true);
            $obj = new Combination($id_combination);
            $obj->id_product = $id_product;
            $obj->minimal_quantity = 1;
            $obj->quantity = $qtyTotal;
            $obj->price = $priceImpactTotal;
            $obj->ecotax = 0;
            $obj->weight = $weightTotal;
            $obj->available_date = '0000-00-00';
            $obj->default = true;
            $obj->save();

            StockAvailable::setQuantity($id_product, $obj->id, $qtyTotal);
            if (isset($id_combination) && $id_combination > 0) {
                $product->cache_default_attribute = (int) $id_combination;
                $product->setDefaultAttribute($id_combination);
                //Db::getInstance()->delete('product_attribute_combination', 'id_product_attribute = ' . (int) $id_combination);
            } else {
                if ($obj->id) {
                    $product->setDefaultAttribute($obj->id);
                    $product->cache_default_attribute = (int) $obj->id;
                    $attribute_list_val = array();
                    foreach ($attribute_list as $attributeValue) {
                        $attribute_list_val[] = array(
                            'id_product_attribute' => (int) $obj->id,
                            'id_attribute' => (int) $attributeValue
                        );
                    }
                    $res = Db::getInstance()->insert('product_attribute_combination', $attribute_list_val);
                }
            }
        }
    }

    public function hookDisplayAdminProductsExtra($params)
    {
        $id_product = (int) $params['id_product'];
        if (Validate::isLoadedObject($product = new Product($id_product))) {
            $this->prepareAWPCombinationGeneratorTab();
            return $this->display(__FILE__, 'views/templates/admin/awpcombinationgenerator.tpl');
        }
    }

    function hookNewOrder($params)
    {
        global $cookie;
        $cart = $params['cart'];
        foreach ($cart->getProducts() AS $product) {
            if ($this->awp_display_wizard != 1) {
                $productO = new Product($product['id_product'], true, intval($cookie->id_lang));
                if ($this->awp_display_wizard_field == "Reference" && $this->awp_display_wizard_value != $productO->reference)
                    continue;
                if ($this->awp_display_wizard_field == "Supplier Reference" && $this->awp_display_wizard_value != $productO->supplier_reference)
                    continue;
                if ($this->awp_display_wizard_field == "EAN13" && $this->awp_display_wizard_value != $productO->ean13)
                    continue;
                if ($this->awp_display_wizard_field == "UPC" && $this->awp_display_wizard_value != $productO->upc)
                    continue;
                if ($this->awp_display_wizard_field == "Location" && $this->awp_display_wizard_value != $productO->location)
                    continue;
            }
            if ($product['instructions_id'] == "" || Configuration::get('PS_STOCK_MANAGEMENT') != 1)
                continue;

                $query = 'SELECT pa.id_product_attribute FROM `' . _DB_PREFIX_ . 'product_attribute_combination` AS pac, ' . _DB_PREFIX_ . 'product_attribute AS pa
					WHERE
					pac.id_attribute IN (' . substr($product['instructions_id'], 1) . ') AND pac.id_product_attribute = pa.id_product_attribute AND
					pa.id_product = ' . $product['id_product'] . ' ' . (substr_count($product['instructions_id'], ",") > 111111 ? 'AND pa.default_on = 0' : '');
                $res = Db::getInstance()->ExecuteS($query);



                $id_lang = (int) $cookie->id_lang;
                $connectedAttributesGroups = array();
                /* get all attributes */
                    $sqlConnectedAttributes = 'SELECT pa.*, product_attribute_shop.*, ag.`id_attribute_group`,
							ag.`is_color_group`, agl.`name` AS group_name, al.`name` AS attribute_name,
								a.`id_attribute`, pa.`unit_price_impact`, IFNULL(stock.quantity, 0) as quantity
							FROM `' . _DB_PREFIX_ . 'product_attribute` pa
							' . Shop::addSqlAssociation('product_attribute', 'pa') . '
							' . Product::sqlStock('pa', 'pa') . '
							LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac ON pac.`id_product_attribute` = pa.`id_product_attribute`
							LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a ON a.`id_attribute` = pac.`id_attribute`
							LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
							LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = ' . (int) $id_lang . ')
							LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = ' . (int) $id_lang . ')
							WHERE pa.`id_product` = ' . intval($product['id_product']) . ' 
							
							GROUP BY pa.`id_product_attribute`, a.`id_attribute`
							ORDER BY pa.`id_product_attribute`';

                $connectedAttributesSql = Db::getInstance()->ExecuteS($sqlConnectedAttributes);
                //echo $sqlConnectedAttributes;
                $connectedAttributesArray = array();
                /* construct array with all attributes, groups & prices */
                $defAttribute = 0;
                foreach ($connectedAttributesSql as $row) {

                    $connectedAttributesArray[$row['id_product_attribute']]['id_attribute_groups'][] = $row['id_attribute_group'];
                    $connectedAttributesArray[$row['id_product_attribute']]['attributes_values'][] = $row['attribute_name'];
                    $connectedAttributesArray[$row['id_product_attribute']]['attributes'][] = (int) $row['id_attribute'];



                    $connectedAttributesArray[$row['id_product_attribute']]['attributes_to_groups'][$row['id_attribute_group']][] = (int) $row['id_attribute'];

                    $connectedAttributesArray[$row['id_product_attribute']]['price'] = (float) ($row['price']); //Tools::convertPriceFull($row['price'], null, Context::getContext()->currency);

                    $connectedAttributesArray[$row['id_product_attribute']]['quantity'] = (int) $row['quantity'];
                    $connectedAttributesArray[$row['id_product_attribute']]['weight'] = (int) $row['weight'];
                    $connectedAttributesArray[$row['id_product_attribute']]['minimal_quantity'] = $row['minimal_quantity'];

                    $connectedAttributesArray[$row['id_product_attribute']]['reference'] = $row['reference'];

                    if ($row['default_on'])
                        $defAttribute = $row['id_product_attribute'];
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
                        $notConnectedGroups[] = $row['id_attribute_groups'][0];

                        foreach ($row['attributes'] as $id_attribute) {
                            if ($row['id_attribute_groups'][0] != $awpDetailsIdGroup)
                                $notConnectedAttributeValuesAll[] = $id_attribute;
                        }

                        unset($connectedAttributesArray[$k]);

                        if ($awpDetailsIdGroup != $row['id_attribute_groups'][0])
                            $allConnected = false;
                    }
                }
                if (!$allConnected)
                    unset($connectedAttributesArray[$defAttribute]);
                foreach ($connectedAttributesArray as $k => $row) {
                    $row['id_attribute_groups'] = array_unique($row['id_attribute_groups']);

                    if (count($row['id_attribute_groups']) == 1) {
                        
                    } else {


                        foreach ($row['id_attribute_groups'] as $groups)
                            $connectedAttributesGroups[] = $groups;
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

                if (empty($notConnectedAttributeValuesAll))
                    $bothConnectedAttributes = $connectedAttributeValuesAll;
                else
                    $bothConnectedAttributes = array_intersect($notConnectedAttributeValuesAll, $connectedAttributeValuesAll);
                $bothConnectedAttributes = array_merge($bothConnectedAttributes, $connectedAttributeValuesAll);
                $bothConnectedAttributes = array_unique($bothConnectedAttributes);



                //substr($product['instructions_id'],1)
                $connected_ids = explode(',', substr($product['instructions_id'], 1));
                if ($connected_ids[0] == '')
                    unset($connected_ids[0]);

                $connected_ids = array_intersect($connected_ids, $bothConnectedAttributes);

                $containsSearch = false;
                $connectedAttributeAvailable = null;

                $connectedCombId = null;

                $connectedIgnore = array();



                foreach ($connectedAttributesArray as $k => $connectedAttribute) {

                    $containsSearch = count(array_intersect($connected_ids, $connectedAttribute['attributes'])) == count($connected_ids);
                    if ($containsSearch) {
                        $connectedCombId = $k;
                        $connectedAttributeAvailable = $connectedAttributesArray[$k];
                    } else {
                        $connectedIgnore[] = $k;
                    }
                }

                $res = Db::getInstance()->ExecuteS($query);


                // connected

                $newRes = array();
                $addedConnected = false;
                foreach ($res as $id) {
                    if ($id['id_product_attribute'] == $connectedCombId) {
                        if (!$addedConnected) {
                            $newRes[] = $id;
                            $addedConnected = true;
                        }
                    } elseif (in_array($id['id_product_attribute'], $connectedIgnore)) {
                        // ignore and do not add to removestock
                    } else {
                        $newRes[] = $id;
                    }
                }


                //$connectedIdAttributes = $connectedAttributesArray[$connectedCombId]['attributes'];
                foreach ($newRes as $id) {
                    $current_stock = StockAvailable::getQuantityAvailableByProduct($product['id_product'], $id['id_product_attribute'], Context::getContext()->shop->id);

                    $this->removeStock15(new Product($product['id_product'], true), $id['id_product_attribute'], Context::getContext()->shop->id, ($current_stock - $product['cart_quantity']));
                }
        }
    }
}

