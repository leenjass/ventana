<?php
/**
 * Multishop Color Menu
 *
 * @author    Rolige <www.rolige.com>
 * @copyright 2011-2019 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 */

if (!defined('_PS_VERSION_'))
    exit;

require dirname(__FILE__).'/vendor/autoload.php';

class Rg_MultishopColorMenu extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'rg_multishopcolormenu';
        $this->tab = 'others';
        $this->version = '1.0.0';
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->author = 'Rolige';
        $this->author_link = 'https://www.rolige.com/';
        $this->addons_author_link = 'https://addons.prestashop.com/en/2_community-developer?contributor=99052';
        $this->module_id = 26;
        $this->addons_module_id = 26558; // TODO
        $this->need_instance = 1;
        $this->bootstrap = true;
        $this->secure_key = Tools::encrypt($this->name);
        $this->module_key = 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';
        $this->author_address = '0xbF9c7047a7F061754830f2F4D00BEaC7240E325C';

        parent::__construct();

        $this->displayName = $this->l('Multishop Color Menu');
        $this->description = $this->l('Allows to show different color for multi shop Back Office menu depending on the selected shop context.');

        $this->menu = array(
            array(
                'dashboard' => array(
                    'icon' => 'icon-home',
                    'title' => $this->l('Dashboard'),
                ),
            ),
            array(
                'settings' => array(
                    'icon' => 'icon-cogs',
                    'title' => $this->l('Settings'),
                ),
            ),
        );
    }

    public function install()
    {
        return parent::install() && $this->registerHook('displayBackOfficeHeader') && $this->registerHook('displayBackOfficeFooter');
    }

    public function uninstall()
    {
        return parent::uninstall();
    }

    /**
     * Configuration form
     */
    public function getContent()
    {
        $output = '';

        $menu_selected = Tools::strtolower(trim(Tools::getValue('menu_active')));
        $moduleForm = RgMcmModuleForm::getForm($menu_selected, $this->menu);

        if ($moduleForm->isSubmitForm()) {
            if (!$error = $moduleForm->validateForm()) {
                $confirmation = $moduleForm->processForm();
            }
        }

        if (isset($error)) {
            if (!$error) {
                $output .= $this->displayConfirmation($confirmation);
            } else {
                $output .= $this->displayError($error);
            }
        }

        $this->boSmartyAssign(array(
            'menu' => array(
                'items' => $this->menu,
                'link' => $this->context->link->getAdminLink('AdminModules').'&configure='.$this->name,
                'active' => $moduleForm->menu_active,
            ),
            'form' => $moduleForm->renderForm(),
        ));

        $output .= $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        return $output;
    }

    /**
     * CSS & JavaScript files loaded in the BO
     */
    public function hookDisplayBackOfficeHeader()
    {
        if (Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP) {
            $prefix = 'SETTINGS_SINGLE_';
        } else {
            $prefix = 'SETTINGS_MULTI_';
        }
        Media::addJsDef(array('multishopcolormenu' => array(
            'color' => RgMcmConfig::get($prefix.'COLOR'),
            'back_color' => RgMcmConfig::get($prefix.'BACK_COLOR'),
        )));

        if (method_exists($this->context->controller, 'addJquery')) {
            $this->context->controller->addJquery();
        }

        $controller_name = $this->context->controller->controller_name;
        if ($controller_name == 'AdminModules' && Tools::getValue('configure') == $this->name) {
            $this->context->controller->addCSS($this->_path.'views/libs/slick/slick.css');
            $this->context->controller->addCSS($this->_path.'views/libs/slick/slick-theme.css');
            $this->context->controller->addJS($this->_path.'views/libs/slick/slick.min.js');
            $this->context->controller->addCSS($this->_path.'views/css/module_config.css');
            $this->context->controller->addJS($this->_path.'views/js/module_config.js');
        }
    }

    public function hookDisplayBackOfficeFooter()
    {
        return '<script type="text/javascript" src="'.$this->_path.'views/js/color_menu.js"></script>';
    }

    public function boSmartyAssign($vars = null)
    {
        static $smarty_vars = null;

        if ($smarty_vars === null) {
            $smarty_vars = array(
                '_path' => $this->_path,
                'version' => $this->version,
                'new_version' => RgMcmTools::getNewModuleVersion($this->name, $this->version),
            );
        }

        Media::addJsDef(array($this->name => array(
                '_path' => $this->_path,
                'secure_key' => $this->secure_key,
                'config_prefix' => RgMcmConfig::prefix('config'),
        )));

        if (is_array($vars)) {
            $smarty_vars = array_merge($smarty_vars, $vars);

            return $this->context->smarty->assign($this->name, $smarty_vars);
        }

        return $this->context->smarty->assign($this->name, $smarty_vars);
    }
}
