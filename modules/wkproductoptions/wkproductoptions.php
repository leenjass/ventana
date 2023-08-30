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
if (!defined('_PS_VERSION_')) {
    exit;
}

include_once dirname(__FILE__) . '/classes/WkProductOptionsClasses.php';

class WkProductOptions extends Module
{
    public $secure_key;

    public function __construct()
    {
        $this->name = 'wkproductoptions';
        $this->tab = 'pricing_promotion';
        $this->version = '5.1.0';
        $this->author = 'Webkul';
        if (_PS_VERSION_ >= '1.7') {
            $this->secure_key = Tools::hash($this->name);
        } else {
            $this->secure_key = Tools::encrypt($this->name);
        }
        $this->bootstrap = true;
        $this->ps_versions_compliancy = ['min' => '1.7', 'max' => _PS_VERSION_];
        parent::__construct();
        $this->displayName = $this->l('Custom Product Options');
        $this->description = $this->l('Apply customized options on products.');
        $this->confirmUninstall = $this->l('Are you sure?');
    }

    /**
     * Install module
     *
     * @return bool
     */
    public function install()
    {
        $objModuleDb = new WkProductOptionsDb();
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }
        if (!parent::install()
            || !$objModuleDb->createTables()
            || !$this->callInstallTab()
            || !$this->installConfiguration()
            || !$this->registerModuleHooks()
        ) {
            return false;
        }
        $hookId = Hook::getIdByName('displayHeader');
        if ($hookId) {
            $this->updatePosition($hookId, 0, 1);
        }

        return true;
    }

    /**
     * install configuration
     *
     * @return bool
     */
    public function installConfiguration()
    {
        Configuration::updateValue(
            'WK_PRODUCT_OPTION_DISPLAY_COLOR_PICKER',
            1
        );
        Configuration::updateValue(
            'WK_PRODUCT_OPTION_DISPLAY_POPUP',
            1
        );

        return true;
    }

    /**
     * Function to create tab
     *
     * @return bool
     */
    public function callInstallTab()
    {
        $this->installTab('AdminProductOptions', 'Product Options', 'AdminCatalog');

        return true;
    }

    /**
     * register hooks for module
     *
     * @return bool
     */
    private function registerModuleHooks()
    {
        return $this->registerHook(
            [
                'displayHeader',
                'actionProductSave',
                'actionProductDelete',
                'actionValidateOrder',
                'displayOverrideTemplate',
                'displayAfterProductName',
                'displayAddBpProductName',
                'displayProductPriceBlock',
                'displayOptionMailContent',
                'displayProductOptionCart',
                'displayAdminProductsExtra',
                'displayAfterProductVariant',
                'displayAfterCartProductLine',
                'displayProductAdditionalInfo',
                'actionFrontControllerSetMedia',
                'actionAdminControllerSetMedia',
                'actionAttributeCombinationDelete',
                'actionObjectProductInCartDeleteAfter',
            ]
        );
    }

    public function getContent()
    {
        if (Tools::isSubmit('submitdisplaysetting')) {
            Configuration::updateValue(
                'WK_PRODUCT_OPTION_DISPLAY_COLOR_PICKER',
                Tools::getValue('WK_PRODUCT_OPTION_DISPLAY_COLOR_PICKER')
            );
            Configuration::updateValue(
                'WK_PRODUCT_OPTION_DISPLAY_POPUP',
                Tools::getValue('WK_PRODUCT_OPTION_DISPLAY_POPUP')
            );
            $this->context->controller->confirmations[] = $this->l('Settings updated successfully.');
        }
        /* Start - Code for module promotion banner */
        Media::addJsDef([
            'module_dir' => _MODULE_DIR_,
            'wkModuleAddonKey' => $this->module_key,
            'wkModuleAddonsId' => 88078,
            'wkModuleTechName' => $this->name,
            'wkModuleDoc' => file_exists(_PS_MODULE_DIR_ . $this->name . '/doc_en.pdf'),
        ]);
        $this->context->controller->addJs('https://prestashop.webkul.com/crossselling/wkcrossselling.min.js?t=' . time());
        /* End - Code for module promotion banner */

        return $this->renderForm();
    }

    protected function renderForm()
    {
        $renderFrom = [];
        $renderFrom[] = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Configuration'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => $this->l('Show color picker'),
                        'hint' => $this->l('Show color picker for text and textarea type input field in front end.'),
                        'name' => 'WK_PRODUCT_OPTION_DISPLAY_COLOR_PICKER',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'WK_PRODUCT_OPTION_DISPLAY_COLOR_PICKER_on',
                                'value' => true,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'WK_PRODUCT_OPTION_DISPLAY_COLOR_PICKER_off',
                                'value' => false,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Display options information in pop-up'),
                        'hint' => $this->l('If enabled, options information will be displayed in pop-up.'),
                        'name' => 'WK_PRODUCT_OPTION_DISPLAY_POPUP',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'WK_PRODUCT_OPTION_DISPLAY_POPUP_on',
                                'value' => true,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'WK_PRODUCT_OPTION_DISPLAY_POPUP_off',
                                'value' => false,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                        'desc' => $this->l('This configuration is only applicable for cart, order confirmation and customer order detail page.'),
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
        ];
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitdisplaysetting';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
        . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = [
            'fields_value' => [
                'WK_PRODUCT_OPTION_DISPLAY_COLOR_PICKER' => Configuration::get('WK_PRODUCT_OPTION_DISPLAY_COLOR_PICKER'),
                'WK_PRODUCT_OPTION_DISPLAY_POPUP' => Configuration::get('WK_PRODUCT_OPTION_DISPLAY_POPUP'),
            ],
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm($renderFrom);
    }

    /**
     * Add stylesheet and js for product option
     *
     * @param array $params
     *
     * @return void
     */
    public function hookActionFrontControllerSetMedia()
    {
        $controller = Tools::getValue('controller');
        if ($controller == 'category'
            || $controller == 'index'
            || $controller == 'manufacturer'
            || $controller == 'search'
            || $controller == 'supplier'
            || $controller == 'new-products'
            || $controller == 'best-sales'
            || $controller == 'prices-drop'
            || $controller == 'cart'
        ) {
            $this->loadCommonAssests();
        }
        if ($controller == 'order') {
            $this->context->controller->registerJavascript(
                'product-option-order-js',
                'modules/' . $this->name . '/views/js/wk_option_order.js',
                ['position' => 'bottom', 'priority' => 1001]
            );
        }
        if ($controller == 'orderconfirmation'
            || $controller == 'orderdetail'
            || $controller == 'cart'
            || $controller == 'order'
            || $controller == 'orderopc'
        ) {
            $this->context->controller->registerStylesheet(
                'product-option-page-css',
                'modules/' . $this->name . '/views/css/front_product_option.css'
            );
        }
    }

    public function hookDisplayHeader()
    {
        $controller = Tools::getValue('controller');
        if ($controller == 'product'
            || $controller == 'productoptionajax'
            || $controller == 'ajax'
        ) {
            $this->loadCommonAssests();
        }
    }

    protected function loadCommonAssests()
    {
        $controller = Tools::getValue('controller');
        $this->context->controller->registerStylesheet(
            'wk_select.css',
            'modules/' . $this->name . '/views/css/wk_select.css'
        );
        $this->context->controller->registerStylesheet(
            'product-option-page-css',
            'modules/' . $this->name . '/views/css/front_product_option.css'
        );
        $jsVars = [
            'wk_product_option_ajax' => $this->context->link->getModuleLink(
                'wkproductoptions',
                'productoptionajax'
            ),
            'secure_key' => Tools::getToken(false),
            'maxSizeAllowed' => Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE'),
            'wk_no_file' => $this->l('No selected file'),
            'wk_multi_select_placeholder' => $this->l('Select some options'),
            'wk_controller' => $controller,
            'wk_ps_version' => _PS_VERSION_,
        ];
        $this->datePickerTranslate();
        Media::addJsDef($jsVars);
        $this->context->controller->addJqueryUI(['ui.slider', 'ui.datepicker']);
        $this->context->controller->registerJavascript(
            'wk-product-option-field-timepicker-js',
            'js/jquery/plugins/timepicker/jquery-ui-timepicker-addon.js',
            ['position' => 'bottom', 'priority' => 1000]
        );
        $this->context->controller->registerJavascript(
            'wk_select.js',
            'modules/' . $this->name . '/views/js/wk_select.js',
            ['position' => 'bottom', 'priority' => 148]
        );
        $this->context->controller->registerJavascript(
            'product-option-page-js',
            'modules/' . $this->name . '/views/js/front_product_option.js',
            ['position' => 'bottom', 'priority' => 149]
        );
    }

    /**
     * Date time picker translations
     *
     * @return void
     */
    private function datePickerTranslate()
    {
        $wkMonthNameFull = [
            $this->l('January'),
            $this->l('February'),
            $this->l('March'),
            $this->l('April'),
            $this->l('May'),
            $this->l('June'),
            $this->l('July'),
            $this->l('August'),
            $this->l('September'),
            $this->l('October'),
            $this->l('November'),
            $this->l('December'),
        ];
        $wkMonthNameShort = [
            $this->l('Jan'),
            $this->l('Feb'),
            $this->l('Mar'),
            $this->l('Apr'),
            $this->l('May'),
            $this->l('Jun'),
            $this->l('Jul'),
            $this->l('Aug'),
            $this->l('Sep'),
            $this->l('Oct'),
            $this->l('Nov'),
            $this->l('Dec'),
        ];
        $dayNamesMin = [
            $this->l('Su'),
            $this->l('Mo'),
            $this->l('Tu'),
            $this->l('We'),
            $this->l('Th'),
            $this->l('Fr'),
            $this->l('Sa'),
        ];
        Media::addJsDef([
            'wkMonthNameShort' => $wkMonthNameShort,
            'wkMonthNameFull' => $wkMonthNameFull,
            'wkDaysOfWeek' => $dayNamesMin,
            'wkcloseText' => $this->l('Done'),
            'wkprevText' => $this->l('Prev'),
            'wknextText' => $this->l('Next'),
            'wkcurrentText' => $this->l('Today'),
            'wktimeText' => $this->l('Time'),
            'wkhourText' => $this->l('Hour'),
            'wkminuteText' => $this->l('Minute'),
            'wksecondText' => $this->l('Second'),
            'wkcurrentTimeText' => $this->l('Now'),
            'wktimeOnlyTitle' => $this->l('Choose time'),
        ]);

        return true;
    }

    /**
     * Assign assets admin order page
     *
     * @return void
     */
    public function hookActionAdminControllerSetMedia()
    {
        if ('AdminOrders' == Tools::getValue('controller')) {
            $idOrder = 0;
            $containsOption = 0;
            if (_PS_VERSION_ >= '1.7.7.0') {
                $uri = $_SERVER['REQUEST_URI']; // it will get full url
                $uriArray = explode('/', $uri); // convert string into array with explode
                $orderIndex = 0;
                foreach ($uriArray as $index => $string) {
                    if (strpos($string, '?_token') !== false) {
                        $orderIndex = $index;
                    }
                }
                if ($orderIndex) {
                    $idOrder = $uriArray[$orderIndex - 1];
                }
                $isSymfonyContext = 1;
            } else {
                $idOrder = Tools::getValue('id_order');
                $isSymfonyContext = 0;
            }
            if ($idOrder) {
                $objCustomerOptions = new WkProductCustomerOptions();
                $containsOption = (int) $objCustomerOptions->checkOrderContainsOption($idOrder, true);
                Media::addJsDef(
                    [
                        'contains_options' => $containsOption,
                        'wk_symfony_context' => $isSymfonyContext,
                        'id_order' => $idOrder,
                        'product_option_controller' => $this->context->link->getAdminLink(
                            'AdminProductOptions'
                        ),
                    ]
                );
                $this->context->controller->addJS(
                    [
                        $this->_path . 'views/js/admin_order_page.js',
                    ]
                );
                $this->context->controller->addCSS(
                    [
                        $this->_path . 'views/css/admin_product_option.css',
                    ]
                );
            }
        }
        if ('AdminProducts' == Tools::getValue('controller')) {
            Media::addJsDef(
                [
                    'wk_customization_msg' => $this->l('Product options are allowed in this product so you cannot use customizaton feature for this product.'),
                    'wk_product_option_controller' => $this->context->link->getAdminLink('AdminProductOptions'),
                    'wk_select_comb' => $this->l('Select some combinations.'),
                    'is_new_product_page' => WkProductOptionsConfig::checkNewPSProductPage(),
                ]
            );
            $this->context->controller->addJS(
                [
                    $this->_path . 'views/js/admin_product_wise_option.js',
                ]
            );
        }
        if ('AdminCarts' == Tools::getValue('controller')) {
            $this->context->controller->addJS(
                [
                    $this->_path . 'views/js/admin_option_cart.js',
                ]
            );
        }
    }

    /**
     * Update product data after order
     *
     * @param array $params
     *
     * @return void
     */
    public function hookActionValidateOrder($params)
    {
        $idOrder = $params['order']->id;
        $objOrder = new Order($idOrder);
        if ($idOrder && Validate::isLoadedObject($objOrder)) {
            $objOptionCustomer = new WkProductCustomerOptions();
            $orderedProduct = $objOrder->getProducts();
            if (!empty($orderedProduct)) {
                foreach ($orderedProduct as $order) {
                    $idProduct = $order['product_id'];
                    $idProductAttribute = $order['product_attribute_id'];
                    $idCustomization = $order['id_customization'];
                    $optionInfo = $objOptionCustomer->getSavedProductOptionsBeforeOrder(
                        $idProduct,
                        $idProductAttribute,
                        $idCustomization,
                        $objOrder->id_cart
                    );
                    $objProduct = new Product($idProduct);
                    $productPrice = $objProduct->getPrice(true, $idProductAttribute);
                    $productPrice = Tools::convertPriceFull(
                        $productPrice,
                        new Currency($objOrder->id_currency),
                        new Currency((int) Configuration::get('PS_CURRENCY_DEFAULT'))
                    );
                    if (!empty($optionInfo)) {
                        $objOptionValue = new WkProductOptionsValue();
                        foreach ($optionInfo as $option) {
                            $priceImpact = 0;
                            $objOption = new WkProductOptionsConfig($option['id_option']);
                            if ($option['option_type'] == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_CHECKBOX) {
                                $selectedValues = json_decode($option['option_value']);
                            } elseif ($option['option_type'] == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_DROPDOWN) {
                                if ($option['multiselect'] == 1) {
                                    $selectedValues = json_decode($option['option_value']);
                                } else {
                                    $selectedValues[] = $option['option_value'];
                                }
                            } elseif ($option['option_type'] == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_RADIO) {
                                $selectedValues[] = $option['option_value'];
                            }
                            if ($objOption->option_type == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_DROPDOWN
                                || $objOption->option_type == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_CHECKBOX
                                || $objOption->option_type == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_RADIO
                            ) {
                                if (!empty($selectedValues) && is_array($selectedValues)) {
                                    $selectedValues = array_unique($selectedValues);
                                    $selectedValuesFormattedWithTe = $objOptionValue->getAllDisplayOptionsByValues(
                                        $objOption->id,
                                        $selectedValues,
                                        $this->context->language->id,
                                        $this->context->shop->id,
                                        $idProduct,
                                        true
                                    );
                                    if (!empty($selectedValuesFormattedWithTe)) {
                                        foreach ($selectedValuesFormattedWithTe as $optValue) {
                                            $priceImpactInternal = 0;
                                            if ($option['price_type'] == 1) {
                                                $priceImpactInternal = $productPrice * ($optValue['price_impact'] / 100);
                                            } else {
                                                if ($optValue['tax_type'] == 1) {
                                                    $priceImpactInternal = $optValue['price_impact'];
                                                } else {
                                                    $taxRate = Tax::getProductTaxRate($idProduct);
                                                    $priceImpactInternal = $optValue['price_impact'] * (1 + ($taxRate / 100));
                                                }
                                            }
                                            $priceImpact = $priceImpact + $priceImpactInternal;
                                        }
                                    }
                                }
                            } else {
                                if ($option['price_type'] == 1) {
                                    $priceImpact = $productPrice * ($option['price_impact'] / 100);
                                } else {
                                    if ($option['tax_type'] == 1) {
                                        $priceImpact = $option['price_impact'];
                                    } else {
                                        $taxRate = Tax::getProductTaxRate($idProduct);
                                        $priceImpact = $option['price_impact'] * (1 + ($taxRate / 100));
                                    }
                                }
                            }
                            $priceImpact = Tools::convertPriceFull(
                                $priceImpact,
                                new Currency((int) Configuration::get('PS_CURRENCY_DEFAULT')),
                                new Currency($objOrder->id_currency)
                            );
                            $priceImpact = Tools::ps_round($priceImpact, 6);
                            $objOption = new WkProductCustomerOptions($option['id_wk_product_customer_options']);
                            if (Validate::isLoadedObject($objOption)) {
                                if ($objOption->option_type == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_DATE
                                ) {
                                    $objOption->option_value = Tools::displayDate($objOption->option_value, false);
                                } elseif ($objOption->option_type == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_DATETIME
                                ) {
                                    $objOption->option_value = Tools::displayDate($objOption->option_value, true);
                                }
                                $objOption->id_order = $idOrder;
                                $objOption->in_cart = 0;
                                $objOption->id_shop = $objOrder->id_shop;
                                $objOption->id_customer = $objOrder->id_customer;
                                $objOption->price_impact = $priceImpact;
                                $objOption->update();
                            }
                        }
                        // Remove customization data from core table
                        $objOptionCustomer->deleteCustomiedDataFormCoreTable($idCustomization);
                    }
                }
            }
        }
    }

    /**
     * Display product options information on cart page
     * This is custom hook added by overridding cart.tpl
     *
     * @param array $params
     *
     * @return void
     */
    public function hookDisplayAfterCartProductLine($params)
    {
        if (Tools::getValue('controller') == 'cart') {
            if (Configuration::get('WK_PRODUCT_OPTION_DISPLAY_POPUP')) {
                if ($params['type'] == 2) {
                    return '';
                }
            } else {
                if ($params['type'] == 1) {
                    return '';
                }
            }
            $idProduct = $params['product']->id_product;
            $idProductAttribute = $params['product']->id_product_attribute;
            $idCustomization = $params['product']->id_customization;
            $objOptionCustomer = new WkProductCustomerOptions();
            $optionInfo = $objOptionCustomer->getSavedProductOptionsBeforeOrder(
                $idProduct,
                $idProductAttribute,
                $idCustomization,
                $this->context->cart->id
            );
            $objProduct = new Product($idProduct);
            $productPrice = $objProduct->getPrice(true, $idProductAttribute);
            $productPrice = Tools::convertPriceFull(
                $productPrice,
                $this->context->currency,
                new Currency((int) Configuration::get('PS_CURRENCY_DEFAULT'))
            );
            $objOptionValue = new WkProductOptionsValue();
            foreach ($optionInfo as &$option) {
                $priceImpact = 0;
                $objOption = new WkProductOptionsConfig($option['id_option']);
                if ($option['option_type'] == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_CHECKBOX) {
                    $selectedValues = json_decode($option['option_value']);
                } elseif ($option['option_type'] == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_DROPDOWN) {
                    if ($option['multiselect'] == 1) {
                        $selectedValues = json_decode($option['option_value']);
                    } else {
                        $selectedValues[] = $option['option_value'];
                    }
                } elseif ($option['option_type'] == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_RADIO) {
                    $selectedValues[] = $option['option_value'];
                }
                if ($objOption->option_type == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_DROPDOWN
                    || $objOption->option_type == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_CHECKBOX
                    || $objOption->option_type == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_RADIO
                ) {
                    if (!empty($selectedValues) && is_array($selectedValues)) {
                        $selectedValues = array_unique($selectedValues);
                        $selectedValuesFormattedWithTe = $objOptionValue->getAllDisplayOptionsByValues(
                            $objOption->id,
                            $selectedValues,
                            $this->context->language->id,
                            $this->context->shop->id,
                            $idProduct,
                            true
                        );
                        if (!empty($selectedValuesFormattedWithTe)) {
                            foreach ($selectedValuesFormattedWithTe as $optValue) {
                                $priceImpact = $priceImpact + $optValue['price_impact'];
                            }
                        }
                    }
                } else {
                    if ($option['price_type'] == 1) {
                        $priceImpact = $productPrice * ($option['price_impact'] / 100);
                    } else {
                        if ($option['tax_type'] == 1) {
                            $priceImpact = $option['price_impact'];
                        } else {
                            $taxRate = Tax::getProductTaxRate($idProduct);
                            $priceImpact = $option['price_impact'] * (1 + ($taxRate / 100));
                        }
                    }
                }
                $option['price_impact_formated'] = Tools::displayPrice(
                    Tools::convertPriceFull(
                        $priceImpact,
                        new Currency((int) Configuration::get('PS_CURRENCY_DEFAULT')),
                        $this->context->currency
                    ),
                    $this->context->currency
                );
                if ($option['option_type'] == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_CHECKBOX) {
                    $option['option_value'] = json_decode($option['option_value']);
                } elseif ($option['option_type'] == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_DROPDOWN) {
                    if ($option['multiselect'] == 1) {
                        $option['option_value'] = json_decode($option['option_value']);
                    }
                } elseif ($option['option_type'] == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_FILE) {
                    $optionPath = _PS_MODULE_DIR_ . $this->name . '/views/img/upload/' . $option['option_value'];
                    if (file_exists($optionPath)) {
                        $option['option_value'] = _MODULE_DIR_ . $this->name . '/views/img/upload/' . $option['option_value'];
                    } else {
                        $option['option_value'] = 'not_exists';
                    }
                } elseif ($option['option_type'] == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_DATE
                ) {
                    $option['option_value'] = Tools::displayDate($option['option_value'], false);
                } elseif ($option['option_type'] == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_DATETIME
                ) {
                    $option['option_value'] = Tools::displayDate($option['option_value'], true);
                }
            }
            $modalKey = (int) $idProduct . '_' . (int) $idProductAttribute . '_' . (int) $idCustomization;
            $this->context->smarty->assign(
                [
                    'product_options_info' => $optionInfo,
                    'option_model_key' => $modalKey,
                    'WK_PRODUCT_OPTIONS_TEXT' => WkProductOptionsConfig::WK_PRODUCT_OPTIONS_TEXT,
                    'WK_PRODUCT_OPTIONS_TEXTAREA' => WkProductOptionsConfig::WK_PRODUCT_OPTIONS_TEXTAREA,
                    'WK_PRODUCT_OPTIONS_DROPDOWN' => WkProductOptionsConfig::WK_PRODUCT_OPTIONS_DROPDOWN,
                    'WK_PRODUCT_OPTIONS_CHECKBOX' => WkProductOptionsConfig::WK_PRODUCT_OPTIONS_CHECKBOX,
                    'WK_PRODUCT_OPTIONS_RADIO' => WkProductOptionsConfig::WK_PRODUCT_OPTIONS_RADIO,
                    'WK_PRODUCT_OPTIONS_FILE' => WkProductOptionsConfig::WK_PRODUCT_OPTIONS_FILE,
                    'wk_option_page' => Tools::getValue('controller'),
                    'display_mode' => 'normal',
                ]
            );

            return $this->fetch('module:' . $this->name . '/views/templates/hook/product_option_saved_info.tpl');
        }
    }

    /**
     * This hook is responsible for displaying information on invoice, delivery slip, order slip etc.
     *
     * @param array $params
     *
     * @return void
     */
    public function hookDisplayAddBpProductName($params)
    {
        if ($idOrder = $params['id_order']) {
            $order = new Order($idOrder);
            $idProduct = $params['id_product'];
            $idProductAttribute = $params['id_product_attribute'];
            $idCustomization = $params['id_customization'];
            $objOptionCustomer = new WkProductCustomerOptions();
            $optionInfo = $objOptionCustomer->getSavedProductOptionsAfterOrder(
                $idProduct,
                $idProductAttribute,
                $idCustomization,
                $idOrder
            );
            foreach ($optionInfo as &$option) {
                $option['price_impact_formated'] = Tools::displayPrice(
                    $option['price_impact'],
                    new Currency($order->id_currency)
                );
                if ($option['option_type'] == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_CHECKBOX) {
                    $option['option_value'] = json_decode($option['option_value']);
                } elseif ($option['option_type'] == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_DROPDOWN) {
                    if ($option['multiselect'] == 1) {
                        $option['option_value'] = json_decode($option['option_value']);
                    }
                } elseif ($option['option_type'] == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_FILE) {
                    $optionPath = _PS_MODULE_DIR_ . $this->name . '/views/img/upload/' . $option['option_value'];
                    if (file_exists($optionPath)) {
                        $option['option_value'] = Tools::getShopDomainSsl(true, true) . _MODULE_DIR_ .
                         $this->name . '/views/img/upload/' . $option['option_value'];
                    } else {
                        $option['option_value'] = 'not_exists';
                    }
                }
            }
            $this->context->smarty->assign(
                [
                    'product_options_info' => $optionInfo,
                    'product_name' => $params['product_name'],
                    'WK_PRODUCT_OPTIONS_TEXT' => WkProductOptionsConfig::WK_PRODUCT_OPTIONS_TEXT,
                    'WK_PRODUCT_OPTIONS_TEXTAREA' => WkProductOptionsConfig::WK_PRODUCT_OPTIONS_TEXTAREA,
                    'WK_PRODUCT_OPTIONS_DROPDOWN' => WkProductOptionsConfig::WK_PRODUCT_OPTIONS_DROPDOWN,
                    'WK_PRODUCT_OPTIONS_CHECKBOX' => WkProductOptionsConfig::WK_PRODUCT_OPTIONS_CHECKBOX,
                    'WK_PRODUCT_OPTIONS_RADIO' => WkProductOptionsConfig::WK_PRODUCT_OPTIONS_RADIO,
                    'WK_PRODUCT_OPTIONS_FILE' => WkProductOptionsConfig::WK_PRODUCT_OPTIONS_FILE,
                ]
            );

            return $this->fetch('module:' . $this->name . '/views/templates/hook/pdf_content.tpl');
        }
    }

    /**
     * This hook is responsible for displaying information on mail.
     *
     * @param array $params
     *
     * @return string
     */
    public function hookDisplayOptionMailContent($params)
    {
        $idProduct = $params['id_product'];
        $idProductAttribute = $params['id_product_attribute'];
        $idCustomization = $params['id_customization'];
        $objOptionCustomer = new WkProductCustomerOptions();
        $optionInfo = $objOptionCustomer->getSavedProductOptionsBeforeOrder(
            $idProduct,
            $idProductAttribute,
            $idCustomization,
            $params['id_cart']
        );
        $objProduct = new Product($idProduct);
        $productPrice = $objProduct->getPrice(true, $idProductAttribute);
        $productPrice = Tools::convertPriceFull(
            $productPrice,
            $this->context->currency,
            new Currency((int) Configuration::get('PS_CURRENCY_DEFAULT'))
        );
        $objOptionValue = new WkProductOptionsValue();
        foreach ($optionInfo as &$option) {
            $priceImpact = 0;
            $objOption = new WkProductOptionsConfig($option['id_option']);
            if ($option['option_type'] == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_CHECKBOX) {
                $selectedValues = json_decode($option['option_value']);
            } elseif ($option['option_type'] == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_DROPDOWN) {
                if ($option['multiselect'] == 1) {
                    $selectedValues = json_decode($option['option_value']);
                } else {
                    $selectedValues[] = $option['option_value'];
                }
            } elseif ($option['option_type'] == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_RADIO) {
                $selectedValues[] = $option['option_value'];
            }
            if ($objOption->option_type == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_DROPDOWN
                || $objOption->option_type == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_CHECKBOX
                || $objOption->option_type == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_RADIO
            ) {
                if (!empty($selectedValues) && is_array($selectedValues)) {
                    $selectedValues = array_unique($selectedValues);
                    $selectedValuesFormattedWithTe = $objOptionValue->getAllDisplayOptionsByValues(
                        $objOption->id,
                        $selectedValues,
                        $this->context->language->id,
                        $this->context->shop->id,
                        $idProduct,
                        true
                    );
                    if (!empty($selectedValuesFormattedWithTe)) {
                        foreach ($selectedValuesFormattedWithTe as $optValue) {
                            $priceImpact = $priceImpact + $optValue['price_impact'];
                        }
                    }
                }
            } else {
                if ($option['price_type'] == 1) {
                    $priceImpact = $productPrice * ($option['price_impact'] / 100);
                } else {
                    if ($option['tax_type'] == 1) {
                        $priceImpact = $option['price_impact'];
                    } else {
                        $taxRate = Tax::getProductTaxRate($idProduct);
                        $priceImpact = $option['price_impact'] * (1 + ($taxRate / 100));
                    }
                }
            }
            $option['price_impact_formated'] = Tools::displayPrice(
                Tools::convertPriceFull(
                    $priceImpact,
                    new Currency((int) Configuration::get('PS_CURRENCY_DEFAULT')),
                    $this->context->currency
                ),
                $this->context->currency
            );
            if ($option['option_type'] == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_CHECKBOX) {
                $option['option_value'] = json_decode($option['option_value']);
            } elseif ($option['option_type'] == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_DROPDOWN) {
                if ($option['multiselect'] == 1) {
                    $option['option_value'] = json_decode($option['option_value']);
                }
            } elseif ($option['option_type'] == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_FILE) {
                $optionPath = _PS_MODULE_DIR_ . $this->name . '/views/img/upload/' . $option['option_value'];
                if (file_exists($optionPath)) {
                    $option['option_value'] = Tools::getShopDomainSsl(true, true) . _MODULE_DIR_ .
                     $this->name . '/views/img/upload/' . $option['option_value'];
                } else {
                    $option['option_value'] = 'not_exists';
                }
            } elseif ($option['option_type'] == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_DATE
            ) {
                $option['option_value'] = Tools::displayDate($option['option_value'], false);
            } elseif ($option['option_type'] == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_DATETIME
            ) {
                $option['option_value'] = Tools::displayDate($option['option_value'], true);
            }
        }
        $this->context->smarty->assign(
            [
                'product_options_info' => $optionInfo,
                'WK_PRODUCT_OPTIONS_TEXT' => WkProductOptionsConfig::WK_PRODUCT_OPTIONS_TEXT,
                'WK_PRODUCT_OPTIONS_TEXTAREA' => WkProductOptionsConfig::WK_PRODUCT_OPTIONS_TEXTAREA,
                'WK_PRODUCT_OPTIONS_DROPDOWN' => WkProductOptionsConfig::WK_PRODUCT_OPTIONS_DROPDOWN,
                'WK_PRODUCT_OPTIONS_CHECKBOX' => WkProductOptionsConfig::WK_PRODUCT_OPTIONS_CHECKBOX,
                'WK_PRODUCT_OPTIONS_RADIO' => WkProductOptionsConfig::WK_PRODUCT_OPTIONS_RADIO,
                'WK_PRODUCT_OPTIONS_FILE' => WkProductOptionsConfig::WK_PRODUCT_OPTIONS_FILE,
                'WK_PRODUCT_OPTIONS_DATE' => WkProductOptionsConfig::WK_PRODUCT_OPTIONS_DATE,
                'WK_PRODUCT_OPTIONS_TIME' => WkProductOptionsConfig::WK_PRODUCT_OPTIONS_TIME,
                'WK_PRODUCT_OPTIONS_DATETIME' => WkProductOptionsConfig::WK_PRODUCT_OPTIONS_DATETIME,
                'product_name' => $params['product_name'],
            ]
        );

        return $this->fetch('module:' . $this->name . '/views/templates/hook/mail_content.tpl');
    }

    /**
     * Display product options on order confirmation page
     *
     * @param array $params
     *
     * @return void
     */
    public function hookDisplayProductPriceBlock($params)
    {
        if ('orderconfirmation' == Tools::getValue('controller') && $params['type'] == 'unit_price') {
            if (Tools::getValue('id_order')) {
                return $this->displayProductOptionAfterOrder($params);
            }
        }
        if ('order' == Tools::getValue('controller')) {
            return $this->fetch('module:' . $this->name . '/views/templates/hook/customization_remove.tpl');
        }
    }

    /**
     * Display product options on customer order-details page
     * This is custom hook added bny overriding order-detail page
     *
     * @param array $params
     *
     * @return void
     */
    public function hookDisplayAfterProductName($params)
    {
        if ('orderdetail' == Tools::getValue('controller')) {
            if (Tools::getValue('id_order')) {
                return $this->displayProductOptionAfterOrder($params, $params['theme']);
            }
        }
    }

    /**
     * Function to return template of product options view after order
     *
     * @param array $params
     * @param string $displayMode
     *
     * @return string
     */
    private function displayProductOptionAfterOrder($params, $displayMode = 'normal')
    {
        $idOrder = Tools::getValue('id_order');
        $order = new Order($idOrder);
        $idProduct = $params['product']['id_product'];
        $idProductAttribute = $params['product']['id_product_attribute'];
        $idCustomization = $params['product']['id_customization'];
        $objOptionCustomer = new WkProductCustomerOptions();
        $optionInfo = $objOptionCustomer->getSavedProductOptionsAfterOrder(
            $idProduct,
            $idProductAttribute,
            $idCustomization,
            $idOrder
        );
        foreach ($optionInfo as &$option) {
            $option['price_impact_formated'] = Tools::displayPrice(
                $option['price_impact'],
                new Currency($order->id_currency)
            );
            if ($option['option_type'] == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_CHECKBOX) {
                $option['option_value'] = json_decode($option['option_value']);
            } elseif ($option['option_type'] == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_DROPDOWN) {
                if ($option['multiselect'] == 1) {
                    $option['option_value'] = json_decode($option['option_value']);
                }
            } elseif ($option['option_type'] == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_FILE) {
                $optionPath = _PS_MODULE_DIR_ . $this->name . '/views/img/upload/' . $option['option_value'];
                if (file_exists($optionPath)) {
                    $option['option_value'] = _MODULE_DIR_ . $this->name . '/views/img/upload/' . $option['option_value'];
                } else {
                    $option['option_value'] = 'not_exists';
                }
            }
        }
        $modalKey = (int) $idProduct . '_' . (int) $idProductAttribute . '_' . (int) $idCustomization;
        $this->context->smarty->assign(
            [
                'product_options_info' => $optionInfo,
                'option_model_key' => $modalKey,
                'WK_PRODUCT_OPTIONS_TEXT' => WkProductOptionsConfig::WK_PRODUCT_OPTIONS_TEXT,
                'WK_PRODUCT_OPTIONS_TEXTAREA' => WkProductOptionsConfig::WK_PRODUCT_OPTIONS_TEXTAREA,
                'WK_PRODUCT_OPTIONS_DROPDOWN' => WkProductOptionsConfig::WK_PRODUCT_OPTIONS_DROPDOWN,
                'WK_PRODUCT_OPTIONS_CHECKBOX' => WkProductOptionsConfig::WK_PRODUCT_OPTIONS_CHECKBOX,
                'WK_PRODUCT_OPTIONS_RADIO' => WkProductOptionsConfig::WK_PRODUCT_OPTIONS_RADIO,
                'WK_PRODUCT_OPTIONS_FILE' => WkProductOptionsConfig::WK_PRODUCT_OPTIONS_FILE,
                'wk_option_page' => Tools::getValue('controller'),
                'display_mode' => $displayMode,
            ]
        );

        return $this->fetch('module:' . $this->name . '/views/templates/hook/product_option_saved_info.tpl');
    }

    public function hookDisplayProductOptionCart($params)
    {
        $idCart = $params['id_cart'];
        $objCart = new Cart($idCart);
        if ($idCart && Validate::isLoadedObject($objCart)) {
            $index = 0;
            if (isset($params['is_native'])) {
                $index = $params['pos'];
            } else {
                $index = $params['pos'] - 1;
            }
            $objOptionCustomer = new WkProductCustomerOptions();
            $cartProduct = $objCart->getProducts();
            if (!empty($cartProduct)) {
                foreach ($cartProduct as $key => $cart) {
                    if ($index == $key) {
                        $idProduct = $cart['id_product'];
                        $idProductAttribute = $cart['id_product_attribute'];
                        $idCustomization = $cart['id_customization'];
                        $objOptionCustomer = new WkProductCustomerOptions();
                        $optionInfo = $objOptionCustomer->getSavedProductOptionsByIdCart(
                            $idProduct,
                            $idProductAttribute,
                            $idCustomization,
                            $idCart
                        );
                        if ($optionInfo) {
                            foreach ($optionInfo as &$option) {
                                $option['price_impact_formated'] = Tools::displayPrice(
                                    $option['price_impact'],
                                    new Currency($objCart->id_currency)
                                );
                                if ($option['option_type'] == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_CHECKBOX) {
                                    $option['option_value'] = json_decode($option['option_value']);
                                } elseif ($option['option_type'] == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_DROPDOWN) {
                                    if ($option['multiselect'] == 1) {
                                        $option['option_value'] = json_decode($option['option_value']);
                                    }
                                } elseif ($option['option_type'] == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_FILE) {
                                    $optionPath = _PS_MODULE_DIR_ . $this->name . '/views/img/upload/' .
                                    $option['option_value'];
                                    if (file_exists($optionPath)) {
                                        $option['option_value'] = _MODULE_DIR_ . $this->name . '/views/img/upload/' .
                                        $option['option_value'];
                                    } else {
                                        $option['option_value'] = 'not_exists';
                                    }
                                }
                            }
                            $modalKey = (int) $idProduct . '_' . (int) $idProductAttribute . '_' . (int) $idCustomization;
                            $this->context->smarty->assign(
                                [
                                    'product_options_info' => $optionInfo,
                                    'option_model_key' => $modalKey,
                                    'has_no_view' => $params['no_view'],
                                    'WK_PRODUCT_OPTIONS_TEXT' => WkProductOptionsConfig::WK_PRODUCT_OPTIONS_TEXT,
                                    'WK_PRODUCT_OPTIONS_TEXTAREA' => WkProductOptionsConfig::WK_PRODUCT_OPTIONS_TEXTAREA,
                                    'WK_PRODUCT_OPTIONS_DROPDOWN' => WkProductOptionsConfig::WK_PRODUCT_OPTIONS_DROPDOWN,
                                    'WK_PRODUCT_OPTIONS_CHECKBOX' => WkProductOptionsConfig::WK_PRODUCT_OPTIONS_CHECKBOX,
                                    'WK_PRODUCT_OPTIONS_RADIO' => WkProductOptionsConfig::WK_PRODUCT_OPTIONS_RADIO,
                                    'WK_PRODUCT_OPTIONS_FILE' => WkProductOptionsConfig::WK_PRODUCT_OPTIONS_FILE,
                                    'WK_PRODUCT_OPTIONS_DATE' => WkProductOptionsConfig::WK_PRODUCT_OPTIONS_DATE,
                                    'WK_PRODUCT_OPTIONS_TIME' => WkProductOptionsConfig::WK_PRODUCT_OPTIONS_TIME,
                                    'WK_PRODUCT_OPTIONS_DATETIME' => WkProductOptionsConfig::WK_PRODUCT_OPTIONS_DATETIME,
                                    'wk_option_page' => 'admincart',
                                    'display_mode' => 'normal',
                                ]
                            );

                            return $this->fetch('module:' . $this->name . '/views/templates/hook/product_option_saved_info.tpl');
                        }
                    }
                }
            }
        }
    }

    /**
     * Delete Selected Product optiosn if cart id deleted
     *
     * @param array $params
     *
     * @return void
     */
    public function hookActionObjectProductInCartDeleteAfter($params)
    {
        $objCustomerOption = new WkProductCustomerOptions();
        $objCustomerOption->deleteProductOptionsFromCart(
            $params['id_product'],
            $params['id_product_attribute'],
            $params['customization_id'],
            $this->context->cart->id
        );
    }

    /**
     * To Display warning msg on product page when same product is added in cart
     * if options already added for that product
     *
     * @param array $params
     *
     * @return string
     */
    public function hookDisplayProductAdditionalInfo($params)
    {
        $objCustomerOption = new WkProductCustomerOptions();
        if (isset($params['product']->id_product)) {
            $idProduct = $params['product']->id_product;
        } else {
            $idProduct = $params['product']['id_product'];
        }
        if (isset($params['product']->id_product_attribute)) {
            $idProductAttribute = $params['product']->id_product_attribute;
        } else {
            $idProductAttribute = $params['product']['id_product_attribute'];
        }
        $exists = $objCustomerOption->checkProductExistsInCart(
            $idProduct,
            $idProductAttribute
        );
        $this->context->smarty->assign(
            [
                'product_exist_in_cart' => $exists,
                'id_product_attribute' => $idProductAttribute,
            ]
        );

        return $this->fetch('module:' . $this->name . '/views/templates/hook/cart_warning_msg.tpl');
    }

    /**
     * Overridden front templates
     *
     * @param array $params
     *
     * @return void
     */
    public function hookDisplayOverrideTemplate($params)
    {
        if ($params['template_file'] == 'catalog/product') {
            $extendFilePath = 'catalog/product.tpl';
            $idProduct = $params['id'];
            if (!$idProduct) {
                $idProduct = Tools::getValue('id_product');
            }
            if ($idProduct) {
                $objProduct = new Product($idProduct);
                $idProductAttribute = (int) Product::getDefaultAttribute($idProduct);
                if ($objProduct->hasCombinations()) {
                    if (Tools::getValue('id_product_attribute')) {
                        $idProductAttribute = (int) Tools::getValue('id_product_attribute');
                    } else {
                        $idProductAttribute = (int) Product::getDefaultAttribute($idProduct);
                    }
                }
                // don't return own tpl if product does not contains product options
                $objOption = new WkProductOptionsConfig();
                $options = $objOption->getAvailableOptionByIdProduct($idProduct, $idProductAttribute);
                $customizationField = $objOption->getCustomizations($idProduct);
                $hasOption = false;
                if (!empty($customizationField)) {
                    foreach ($customizationField as $custom) {
                        $customizationFieldLang = $objOption->getCustomizationsLangData($custom['id_customization_field']);
                        if ($customizationFieldLang) {
                            $hasOption = true;
                            break;
                        }
                    }
                }
                if (!empty($options)) {
                    if (!$hasOption) {
                        $objOption->insertIntoPsProduct($idProduct);
                    }
                }
                $objProductConfiguration = new WkProductWiseConfiguration();
                $productConfig = $objProductConfiguration->getProductWiseConfiguration(
                    $idProduct,
                    $this->context->shop->id
                );
                if (!empty($productConfig) && $productConfig['is_native_customization']) {
                    $allowCustomization = 0;
                    $objOption->updateCustomizationFieldToOptional($idProduct);
                } else {
                    $allowCustomization = 1;
                }
                $this->context->smarty->assign(
                    [
                        'extendFilePath' => $extendFilePath,
                        'allow_customization' => $allowCustomization,
                    ]
                );

                return 'module:' . $this->name . '/views/templates/hook/product_override.tpl';
            }

            return $extendFilePath;
        } elseif ($params['template_file'] == 'catalog/_partials/quickview') {
            $extendFilePath = 'catalog/_partials/quickview.tpl';
            $idProduct = $params['id'];
            if (!$idProduct) {
                $idProduct = Tools::getValue('id_product');
            }
            if ($idProduct) {
                $objProduct = new Product($idProduct);
                $idProductAttribute = (int) Product::getDefaultAttribute($idProduct);
                if ($objProduct->hasCombinations()) {
                    if (Tools::getValue('id_product_attribute')) {
                        $idProductAttribute = (int) Tools::getValue('id_product_attribute');
                    } else {
                        $idProductAttribute = (int) Product::getDefaultAttribute($idProduct);
                    }
                }
                // don't return own tpl if product does not contains product options
                $objOption = new WkProductOptionsConfig();
                $options = $objOption->getAvailableOptionByIdProduct($idProduct, $idProductAttribute);
                if (!empty($options)) {
                    $this->context->smarty->assign(
                        [
                            'extendFilePath' => $extendFilePath,
                        ]
                    );

                    return 'module:' . $this->name . '/views/templates/hook/product_quick_view.tpl';
                }
            }

            return $extendFilePath;
        } elseif ($params['template_file'] == 'checkout/_partials/cart-detailed'
            || $params['template_file'] == 'checkout/cart'
        ) {
            $extendFilePath = 'checkout/cart.tpl';
            if ($params['template_file'] == 'checkout/_partials/cart-detailed') {
                $extendFilePath = 'checkout/_partials/cart-detailed.tpl';
            }
            $objCustomerOptions = new WkProductCustomerOptions();
            // don't return own tpl if cart does not contains product options
            if (isset($this->context->cart->id)
                && $objCustomerOptions->checkOrderContainsOption($this->context->cart->id, false)
            ) {
                $this->context->smarty->assign(
                    [
                        'extendFilePath' => $extendFilePath,
                    ]
                );

                return 'module:' . $this->name . '/views/templates/hook/product_option_cart.tpl';
            }

            return $extendFilePath;
        } elseif ($params['template_file'] == 'customer/order-detail') {
            /**
             * Override order detail page if order contains bundle product
             */
            $extendFilePath = 'customer/order-detail.tpl';
            $idOrder = Tools::getValue('id_order');
            if ($idOrder) {
                // don't return own tpl if order does not contains product options
                $objCustomerOptions = new WkProductCustomerOptions();
                if ($objCustomerOptions->checkOrderContainsOption($idOrder, true)) {
                    $this->context->smarty->assign(
                        [
                            'extendFilePath' => $extendFilePath,
                        ]
                    );

                    return 'module:' . $this->name . '/views/templates/hook/customer_order_details.tpl';
                }
            }

            return $extendFilePath;
        }
    }

    /**
     * This is custom hook added in product.tpl and quick-view.tpl by overriding template
     *
     * @param array $params
     *
     * @return void
     */
    public function hookDisplayAfterProductVariant($params)
    {
        if ($idProduct = $params['product']->id_product) {
            $objOption = new WkProductOptionsConfig();
            $objProduct = new Product($idProduct);
            $idProductAttribute = (int) Product::getDefaultAttribute($idProduct);
            if ($objProduct->hasCombinations()) {
                $idProductAttribute = (int) $params['product']->id_product_attribute;
                if (!$idProductAttribute) {
                    $idProductAttribute = (int) Product::getDefaultAttribute($idProduct);
                }
            }
            $options = $objOption->getAvailableOptionByIdProduct($idProduct, $idProductAttribute);
            $this->context->smarty->assign(
                [
                    'product_options' => $options,
                    'WK_PRODUCT_OPTIONS_TEXT' => WkProductOptionsConfig::WK_PRODUCT_OPTIONS_TEXT,
                    'WK_PRODUCT_OPTIONS_TEXTAREA' => WkProductOptionsConfig::WK_PRODUCT_OPTIONS_TEXTAREA,
                    'WK_PRODUCT_OPTIONS_DROPDOWN' => WkProductOptionsConfig::WK_PRODUCT_OPTIONS_DROPDOWN,
                    'WK_PRODUCT_OPTIONS_CHECKBOX' => WkProductOptionsConfig::WK_PRODUCT_OPTIONS_CHECKBOX,
                    'WK_PRODUCT_OPTIONS_RADIO' => WkProductOptionsConfig::WK_PRODUCT_OPTIONS_RADIO,
                    'WK_PRODUCT_OPTIONS_FILE' => WkProductOptionsConfig::WK_PRODUCT_OPTIONS_FILE,
                    'WK_PRODUCT_OPTIONS_DATE' => WkProductOptionsConfig::WK_PRODUCT_OPTIONS_DATE,
                    'WK_PRODUCT_OPTIONS_TIME' => WkProductOptionsConfig::WK_PRODUCT_OPTIONS_TIME,
                    'WK_PRODUCT_OPTIONS_DATETIME' => WkProductOptionsConfig::WK_PRODUCT_OPTIONS_DATETIME,
                ]
            );

            return $this->fetch('module:' . $this->name . '/views/templates/hook/product_options.tpl');
        }
    }

    /**
     * Product Option display on front office
     *
     * @param array $params
     *
     * @return string
     */
    public function hookDisplayAdminProductsExtra($params)
    {
        if (Shop::getContext() != Shop::CONTEXT_SHOP) {
            return $this->display($this->_path, 'shop_warning.tpl');
        }
        $objOptions = new WkProductOptionsConfig();
        $options = $objOptions->getAllOptions(
            $this->context->shop->id,
            $this->context->language->id
        );
        $objProductConfiguration = new WkProductWiseConfiguration();
        $productConfig = $objProductConfiguration->getProductWiseConfiguration(
            $params['id_product'],
            $this->context->shop->id
        );
        $objProduct = new Product($params['id_product']);
        $combinations = [];
        $combProduct = 0;
        if ($objProduct->hasCombinations()) {
            $combinations = $objProduct->getAttributeCombinations();
            $combinations = WkProductOptionsConfig::getCombinationName($combinations);
            if (!empty($combinations)) {
                foreach ($combinations as &$combination) {
                    $combination['option_values'] = $objOptions->getActiveOptionsByCombination(
                        $params['id_product'],
                        $combination['id_product_attribute'],
                        $this->context->shop->id
                    );
                }
            }
            $combProduct = 1;
        } else {
            if (!empty($options)) {
                foreach ($options as &$option) {
                    $value = $objOptions->checkProductWiseEntryExists(
                        $option['id_wk_product_options_config'],
                        $params['id_product'],
                        0,
                        $this->context->shop->id
                    );
                    if (!empty($value)) {
                        $value = $value['active'];
                    } else {
                        $value = 0;
                    }
                    $option['selected_value'] = $value;
                }
            }
        }
        $this->context->smarty->assign(
            [
                'all_options' => $options,
                'attribute_combination' => $combinations,
                'id_ps_product' => $params['id_product'],
                'has_combination' => $combProduct,
                'wk_option_controller_link' => $this->context->link->getAdminLink('AdminProductOptions', true),
                'wk_product_config' => $productConfig,
            ]
        );

        return $this->fetch('module:' . $this->name . '/views/templates/hook/product_option_catalog.tpl');
    }

    /**
     * save Product wise option data
     *
     * @return void
     */
    public function hookActionProductSave($params)
    {
        $isNativeCustomization = Tools::getValue('wk_disable_native_customization');
        $objProductConfiguration = new WkProductWiseConfiguration();
        $productConfig = $objProductConfiguration->getProductWiseConfiguration(
            $params['id_product'],
            $this->context->shop->id
        );
        if (!empty($productConfig)) {
            $objProductConfiguration = new WkProductWiseConfiguration($productConfig['id_wk_product_wise_configuration']);
        }
        $objProductConfiguration->id_product = (int) $params['id_product'];
        $objProductConfiguration->is_native_customization = (int) $isNativeCustomization;
        $objProductConfiguration->id_shop = (int) $this->context->shop->id;
        $objProductConfiguration->save();

        $objOptions = new WkProductOptionsConfig();
        $options = $objOptions->getAllOptions(
            $this->context->shop->id,
            $this->context->language->id
        );
        if (!empty($options)) {
            $objProduct = new Product($params['id_product']);
            if ($objProduct->hasCombinations()) {
                // For combination product
                $combinations = $objProduct->getAttributeCombinations();
                $combinations = WkProductOptionsConfig::getCombinationName($combinations);
                if (!empty($combinations)) {
                    foreach ($combinations as $combination) {
                        foreach ($options as $option) {
                            $objOptions->addUpdateProductWiseOption(
                                $option['id_wk_product_options_config'],
                                Tools::getValue('active_option_' . $combination['id_product_attribute'] . '_' . $option['id_wk_product_options_config']),
                                $params['id_product'],
                                $combination['id_product_attribute'],
                                $this->context->shop->id
                            );
                        }
                    }
                }
            } else {
                // For normal product
                foreach ($options as $option) {
                    $objOptions->addUpdateProductWiseOption(
                        $option['id_wk_product_options_config'],
                        Tools::getValue('active_option_' . $option['id_wk_product_options_config']),
                        $params['id_product'],
                        0,
                        $this->context->shop->id
                    );
                }
            }
        }
    }

    /**
     * Delete product options info of product
     *
     * @param array $params
     *
     * @return void
     */
    public function hookActionProductDelete($params)
    {
        $objOptions = new WkProductOptionsConfig();
        $objOptions->deleteProductWiseOptions($params['id_product']);
        // delete options of product if added in cart
        $objCustomerOption = new WkProductCustomerOptions();
        $objCustomerOption->deleteProductOptionsFromCartByIdProduct($params['id_product'], false);
    }

    /**
     * Delete product options info of product combination
     *
     * @param array $params
     *
     * @return void
     */
    public function hookActionAttributeCombinationDelete($params)
    {
        // delete options of product if added in cart
        $objCustomerOption = new WkProductCustomerOptions();
        $objCustomerOption->deleteProductOptionsFromCartByIdProduct(
            $params['id_product_attribute'],
            true
        );
    }

    /**
     * Tab creation code
     *
     * @param string $className
     * @param string $tabName
     * @param bool $tabParentName
     *
     * @return void
     */
    public function installTab($className, $tabName, $tabParentName = false)
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = $className;
        $tab->name = [];
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $tabName;
        }

        if ($tabParentName) {
            $tab->id_parent = (int) Tab::getIdFromClassName($tabParentName);
        } else {
            $tab->id_parent = 0;
        }

        $tab->module = $this->name;

        return $tab->add();
    }

    /**
     * Delete configuration value
     *
     * @return bool
     */
    public function deleteConfigValues()
    {
        $configKeys = [
            'WK_PRODUCT_OPTION_DISPLAY_COLOR_PICKER',
            'WK_PRODUCT_OPTION_DISPLAY_POPUP',
        ];
        foreach ($configKeys as $key) {
            if (!Configuration::deleteByName($key)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Uninstall module information
     *
     * @return bool true or false
     */
    public function uninstall()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }
        $objModuleDb = new WkProductOptionsDb();
        if (!parent::uninstall()
            || !$this->uninstallTab()
            || !$this->deleteConfigValues()
            || !$objModuleDb->dropTables()
        ) {
            return false;
        }

        return true;
    }

    /**
     * Uninstall created modules tabs
     *
     * @return bool
     */
    public function uninstallTab()
    {
        $moduleTabs = Tab::getCollectionFromModule($this->name);
        if (!empty($moduleTabs)) {
            foreach ($moduleTabs as $moduleTab) {
                $moduleTab->delete();
            }

            return true;
        }

        return false;
    }
}
