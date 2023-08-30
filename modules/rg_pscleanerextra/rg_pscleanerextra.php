<?php
/**
 * Rolige PrestaShop Cleaner Extra
 *
 * @author    Rolige <www.rolige.com>
 * @copyright 2011-2019 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 */

if (!defined('_CAN_LOAD_FILES_')) {
    exit;
}

require dirname(__FILE__).'/vendor/autoload.php';

class rg_PsCleanerExtra extends Module
{
    private $menu;

    public function __construct()
    {
        $this->name = 'rg_pscleanerextra';
        $this->tab = 'administration';
        $this->version = '1.0.1';
        $this->author = 'Rolige';
        $this->author_link = 'https://www.rolige.com/';
        $this->addons_author_link = 'https://addons.prestashop.com/en/2_community-developer?contributor=99052';
        $this->module_id = 24;
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->multishop_context = Shop::CONTEXT_ALL;

        parent::__construct();

        $this->displayName = $this->l('Rolige PrestaShop Cleaner Extra');
        $this->description = $this->l('Clean obsolete data, check and fix functional integrity constraints, rename DB prefix, chance tables to InnoDB engines, clear obsolete product images and remove default data.');
        $this->secure_key = Tools::encrypt($this->name);

        $this->ps_versions_compliancy = array('min' => '1.6.0.0', 'max' => _PS_VERSION_);

        $this->menu = array(
            array(
                'dashboard' => array(
                    'icon' => 'icon-home',
                    'title' => $this->l('Dashboard'),
                ),
            ),
            array(
                'catalog' => array(
                    'icon' => 'icon-th-large',
                    'title' => $this->l('Catalog'),
                ),
                'orderscustomers' => array(
                    'icon' => 'icon-shopping-cart',
                    'title' => $this->l('Orders and Customers'),
                ),
                'database' => array(
                    'icon' => 'icon-database',
                    'title' => $this->l('Database'),
                ),
                'images' => array(
                    'icon' => 'icon-image',
                    'title' => $this->l('Images'),
                ),
            ),
            array(
                'cron' => array(
                    'icon' => 'icon-clock-o',
                    'title' => $this->l('Cron Jobs'),
                ),
            ),
        );
    }

    public function install()
    {
        return parent::install() && $this->registerHook('displayBackOfficeHeader');
    }

    public function getContent()
    {
        $menu_selected = Tools::strtolower(trim(Tools::getValue('menu_active')));
        $moduleForm = RgPSCEModuleForm::getForm($menu_selected);
        $output = '';

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

        $menu_active = $moduleForm->menu_active;
        $form = $moduleForm->renderForm();

        $this->boSmartyAssign(array(
            'menu' => array(
                'items' => $this->menu,
                'link' => $this->context->link->getAdminLink('AdminModules').'&configure='.$this->name,
                'active' => $menu_active,
            ),
            'form' => $form,
        ));

        $output .= $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        return $output;
    }

    public function boSmartyAssign($vars = null)
    {
        static $smarty_vars = null;

        if ($smarty_vars === null) {
            $smarty_vars = array(
                '_path' => $this->_path,
                'version' => $this->version,
                'new_version' => RgPSCETools::getNewModuleVersion($this->name, $this->version),
            );
        }

        if (is_array($vars)) {
            $smarty_vars = array_merge($smarty_vars, $vars);

            return $this->context->smarty->assign($this->name, $smarty_vars);
        }

        return $this->context->smarty->assign($this->name, $smarty_vars);
    }

    public function hookDisplayBackOfficeHeader()
    {
        $controller_name = $this->context->controller->controller_name;

        if ($controller_name == 'AdminModules' && Tools::getValue('configure') == $this->name) {
            if (method_exists($this->context->controller, 'addJquery')) {
                $this->context->controller->addJquery();
            }

            $this->context->controller->addCSS($this->_path.'views/libs/slick/slick.css');
            $this->context->controller->addCSS($this->_path.'views/libs/slick/slick-theme.css');
            $this->context->controller->addJS($this->_path.'views/libs/slick/slick.min.js');
            $this->context->controller->addCSS($this->_path.'views/css/config.css');
            $this->context->controller->addJS($this->_path.'views/js/config.js');
        }
    }
}
