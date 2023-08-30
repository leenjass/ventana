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
class AdminProductOptionsController extends ModuleAdminController
{
    protected $can_add_option = true;
    protected $position_identifier = 'id_product_option_to_move';

    public function __construct()
    {
        $this->context = Context::getContext();
        $this->bootstrap = true;
        $this->table = 'wk_product_options_config';
        $this->className = 'WkProductOptionsConfig';
        $this->identifier = 'id_wk_product_options_config';
        $this->list_no_link = true;
        $this->lang = true;
        $this->_defaultOrderBy = 'shopposition';
        parent::__construct();
        $this->toolbar_title = $this->l('Product options');
        $this->_select = ' wkps.`active` AS status, b.`name`, b.`description`, wkps.`position` as `shopposition`,';
        $this->_join .= 'LEFT JOIN `' . _DB_PREFIX_ . 'wk_product_options_config_shop` wkps
            ON (wkps.`id_wk_product_options_config` = a.`id_wk_product_options_config`)';

        if (!Shop::isFeatureActive() || Shop::getContext() !== Shop::CONTEXT_SHOP) {
            // In case of All Shops
            $this->_select .= 'shp.`name` as wk_ps_shop_name';
            $this->_join .= 'JOIN `' . _DB_PREFIX_ . 'shop` shp ON (shp.`id_shop` = wkps.`id_shop`)';
        }
        $this->_where = Shop::addSqlRestriction(false, 'wkps');
        if (Shop::isFeatureActive()
            && (Shop::getContext() == Shop::CONTEXT_ALL || Shop::getContext() == Shop::CONTEXT_GROUP)
        ) {
            $this->can_add_option = false;
        }
        $options = [];
        $objOption = new WkProductOptionsConfig();
        foreach ($objOption->getOptionTypes() as $option) {
            $options[$option['id_option']] = $option['name'];
        }
        $this->fields_list = [
            'id_wk_product_options_config' => [
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
                'having_filter' => true,
                'filter_key' => 'wkps!id_wk_product_options_config',
            ],
            'name' => [
                'title' => $this->l('Option name'),
                'align' => 'center',
                'having_filter' => true,
                'filter_key' => 'b!name',
            ],
            'price' => [
                'title' => $this->l('Price impact'),
                'align' => 'center',
                'having_filter' => true,
                'callback' => 'getPriceImpact',
                'filter_key' => 'wkps!price',
            ],
            'option_type' => [
                'title' => $this->l('Option type'),
                'align' => 'center',
                'having_filter' => true,
                'callback' => 'getOptionName',
                'type' => 'select',
                'filter_key' => 'wkps!option_type',
                'list' => $options,
            ],
        ];
        if (Shop::getContext() == Shop::CONTEXT_SHOP) {
            $this->fields_list['active'] = [
                'title' => $this->l('Status'),
                'active' => 'status',
                'align' => 'center',
                'type' => 'bool',
                'orderby' => false,
                'having_filter' => true,
                'filter_key' => 'wkps!active',
            ];
            $this->fields_list['shopposition'] = [
                'title' => $this->l('Position'),
                'align' => 'center',
                'filter_key' => 'wkps!position',
                'position' => 'position',
                'orderby' => false,
            ];
        } else {
            $this->fields_list['status'] = [
                'title' => $this->l('Status'),
                'align' => 'center',
                'type' => 'bool',
                'orderby' => false,
                'having_filter' => true,
                'filter_key' => 'wkps!active',
                'callback' => 'getStatus',
            ];
        }

        if (Shop::isFeatureActive() && Shop::getContext() !== Shop::CONTEXT_SHOP) {
            // In case of All Shops
            $this->fields_list['wk_ps_shop_name'] = [
                'title' => $this->l('Shop'),
                'align' => 'center',
                'havingFilter' => true,
                'orderby' => false,
            ];
        }
        if ($this->can_add_option) {
            $this->addRowAction('edit');
        }
        $this->addRowAction('delete');
        $this->bulk_actions = [
            'delete' => [
                'text' => $this->l('Delete selected'),
                'icon' => 'icon-trash',
                'confirm' => $this->l('Delete selected items?'),
            ],
        ];
    }

    /**
     * Callback function to display option name on list
     *
     * @param int $optionId
     *
     * @return string
     */
    public function getOptionName($optionId)
    {
        return WkProductOptionsConfig::getOptionNameById($optionId);
    }

    /**
     * Get option status
     *
     * @param int $val
     *
     * @return string
     */
    public function getStatus($val)
    {
        if ($val) {
            return $this->l('Active');
        } else {
            return $this->l('Inactive');
        }

        return '-';
    }

    /**
     * Callback function to display formated price impact on list
     *
     * @param float $price
     * @param array $row
     *
     * @return string
     */
    public function getPriceImpact($price, $row)
    {
        if ($row['option_type'] == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_DROPDOWN
            || $row['option_type'] == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_CHECKBOX
            || $row['option_type'] == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_RADIO
        ) {
            return '-';
        } else {
            if ($row['price_type'] == 2) {
                return Tools::displayPrice($price);
            } elseif ($row['price_type'] == 1) {
                return Tools::ps_round($price, 2) . ' %';
            }
        }

        return '-';
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display) && $this->can_add_option) {
            $this->page_header_toolbar_btn['new'] = [
                'href' => self::$currentIndex . '&add' . $this->table . '&token=' . $this->token,
                'desc' => $this->l('Add new option'),
                'icon' => 'process-icon-new',
            ];
        }
        parent::initPageHeaderToolbar();
    }

    public function processFilter()
    {
        $hasError = false;
        if (isset($this->list_id)) {
            foreach ($_POST as $value) {
                if (!is_array($value)) {
                    if (preg_match('/¤|\|/', $value)) {
                        $hasError = true;
                        continue;
                    }
                }
            }
        }
        if (!$hasError) {
            parent::processFilter();
        }
    }

    public function initToolbar()
    {
        if (empty($this->display) && $this->can_add_option) {
            $this->page_header_toolbar_btn['new'] = [
                'href' => self::$currentIndex . '&add' . $this->table . '&token=' . $this->token,
                'desc' => $this->l('Add new option'),
                'icon' => 'process-icon-new',
            ];
            parent::initToolbar();
        } else {
            $this->toolbar_btn['modules-list'] = [];
        }
    }

    public function initContent()
    {
        if ($this->action == 'select_delete') {
            $this->context->smarty->assign([
                'delete_form' => true,
                'url_delete' => htmlentities($_SERVER['REQUEST_URI']),
                'boxes' => $this->boxes,
            ]);
        }

        if (!$this->can_add_option && !$this->display) {
            $this->informations[] = $this->l('You have to select a shop if you want to create and edit options.');
        }

        parent::initContent();
    }

    /**
     * Create custom form for option management
     *
     * @return void
     */
    public function renderForm()
    {
        if (!$this->can_add_option) {
            $this->informations[] = $this->l('You have to select a shop if you want to create and edit options.');

            return;
        }
        if (!($objOption = $this->loadObject(true))) {
            return;
        }
        $hasCustomOptions = 0;
        $optionValue = [];
        if (!empty(Tools::getValue('categoryBox'))) {
            $selectedCategories = Tools::getValue('categoryBox');
        } else {
            $selectedCategories = false;
        }
        if (!empty(Tools::getValue('groupBox'))) {
            $groupInfo = Tools::getValue('groupBox');
        } else {
            $groupInfo = [];
        }
        if ($this->display == 'edit') {
            $idOption = Tools::getValue('id_wk_product_options_config');
            if ($idOption) {
                $objOption = new $this->className($idOption);
                if (!$objOption->getKeyInfo($idOption, $this->context->shop->id)) {
                    $this->informations[] = $this->l('Data does not belong to this shop.');

                    return;
                }
                $optionProduct = $objOption->getConditionInformation(
                    $objOption->id,
                    $this->context->shop->id
                );
                $customerName = '';
                if ($optionProduct) {
                    $idCustomer = $optionProduct['id_customer'];
                    if ($idCustomer) {
                        $objCustomer = new Customer($idCustomer);
                        if (Validate::isLoadedObject($objCustomer)) {
                            $customerName = $objCustomer->firstname . ' ' . $objCustomer->lastname;
                        }
                    }
                }
                $products = json_decode($optionProduct['products']);
                $productsArray = [];
                if (!empty($products)) {
                    foreach ($products as $key => $prod) {
                        $temp = [];
                        $productImg = Image::getCover($prod);
                        $product = new Product($prod, false, $this->context->language->id);
                        $temp['id_product'] = $prod;
                        if ($productImg['id_image']) {
                            $temp['img_path'] = $this->context->link->getImageLink(
                                $product->link_rewrite,
                                $productImg['id_image'],
                                ImageType::getFormattedName('small')
                            );
                        } else {
                            $temp['img_path'] = _PS_IMG_ . 'p/' . $this->context->language->iso_code . '-default-' .
                            ImageType::getFormattedName('small') . '.jpg';
                        }

                        $temp['product_link'] = $this->context->link->getAdminLink(
                            'AdminProducts',
                            true,
                            ['id_product' => $prod, 'updateproduct' => '1']
                        );
                        $temp['name'] = $product->name;
                        $productsArray[$key] = $temp;
                    }
                }
                if (empty($this->errors)) {
                    $selectedCategories = json_decode($optionProduct['categories']);
                    $groupInfo = json_decode($optionProduct['id_group']);
                }
                $maxOption = 1;
                if ($objOption->option_type == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_DROPDOWN
                    || $objOption->option_type == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_CHECKBOX
                    || $objOption->option_type == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_RADIO
                ) {
                    $objOptionValues = new WkProductOptionsValue();
                    $allOptionValues = $objOptionValues->getAllDisplayOptionByIdOption(
                        $idOption,
                        $this->context->shop->id
                    );
                    $langData = [];
                    if (!empty($allOptionValues)) {
                        foreach ($allOptionValues as &$optValue) {
                            foreach (Language::getLanguages() as $language) {
                                $langData[$language['id_lang']] = $objOptionValues->getOptionValuesLangData(
                                    $optValue['id_wk_product_options_value'],
                                    $language['id_lang'],
                                    $optValue['id_shop']
                                );
                            }
                            $optValue['display_option_value'] = $langData;
                        }
                        $maxOption = @count($allOptionValues);
                        $i = 1;
                        $options = [];
                        for ($x = 0; $x < $maxOption; ++$x) {
                            $options[] = $i;
                            ++$i;
                        }
                        $maxOption = implode(',', $options);
                        $hasCustomOptions = 1;
                    }
                } else {
                    $optionValue = [];
                    $hasCustomOptions = 0;
                    $allOptionValues = [];
                }
                $this->context->smarty->assign(
                    [
                        'id_option' => $objOption->id,
                        'option_info' => (array) $objOption,
                        'option_name' => $objOption->name,
                        'display_name' => $objOption->display_name,
                        'placeholder' => $objOption->placeholder,
                        'option_description' => $objOption->description,
                        'option_values' => $optionValue,
                        'option_product' => $optionProduct,
                        'products' => $productsArray,
                        'customer_name' => $customerName,
                        'max_option' => $maxOption,
                        'all_saved_options' => $allOptionValues,
                    ]
                );
            }
        }

        $this->fields_form = [
            'submit' => [
                'title' => $this->l('Save'),
            ],
        ];
        $objOption = new $this->className();
        if (!$selectedCategories) {
            $selectedCategories = [];
        }
        $root = Category::getRootCategory();
        $treeOption = new HelperTreeCategories('wk-tree-option-categories-tree', $this->l('Associated categories'));
        $treeOption->setRootCategory((int) $root->id)
            ->setUseCheckBox(true)
            ->setUseSearch(false)
            ->setSelectedCategories($selectedCategories);
        $groups = Group::getGroups($this->context->language->id, $this->context->shop->id);
        $currencies = Currency::getCurrencies();
        $countries = Country::getCountries($this->context->language->id);
        $this->context->smarty->assign(
            [
                'languages' => Language::getLanguages(false),
                'total_languages' => count(Language::getLanguages(false)),
                'wk_category_tree' => $treeOption->render(),
                'current_lang' => Language::getLanguage((int) $this->context->language->id),
                'all_groups' => $groups,
                'countries' => $countries,
                'currencies' => $currencies,
                'currency_symbol' => $this->context->currency->sign,
                'option_types' => $objOption->getOptionTypes(),
                'option_groups' => $groupInfo,
                'custom_option_drop' => $hasCustomOptions,
                'option_values' => [],
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

        return parent::renderForm();
    }

    /**
     * Search products by name
     *
     * @return void
     */
    public function ajaxProcessSearchProductsByName()
    {
        $idProducts = Tools::getValue('idProducts');
        if (!$idProducts) {
            $idProducts = [];
        }
        $idProducts = array_unique($idProducts);
        $keyWord = Tools::getValue('productname');
        $objOption = new $this->className();
        if ($result = $objOption->searchProduct(
            $this->context->language->id,
            $keyWord,
            $this->context->shop->id,
            $idProducts
        )) {
            foreach ($result as &$prod) {
                $productImg = Image::getCover($prod['id_product']);
                $product = new Product($prod['id_product'], false, $this->context->language->id);
                if ($productImg && $productImg['id_image']) {
                    $prod['img_path'] = $this->context->link->getImageLink(
                        $product->link_rewrite,
                        $productImg['id_image'],
                        ImageType::getFormattedName('small')
                    );
                } else {
                    $prod['img_path'] = _PS_IMG_ . 'p/' . $this->context->language->iso_code . '-default-' .
                        ImageType::getFormattedName('small') . '.jpg';
                }
                $prod['product_link'] = $this->context->link->getAdminLink(
                    'AdminProducts',
                    true,
                    ['id_product' => $prod['id_product'], 'updateproduct' => '1']
                );
            }
        }
        exit(json_encode($result));
    }

    public function ajaxProcessSearchCustomer()
    {
        $custSearch = Tools::getValue('cust_search');
        if ($custSearch) {
            $searches = explode(' ', Tools::getValue('keywords'));
            $customers = [];
            $searches = array_unique($searches);
            foreach ($searches as $search) {
                if (!empty($search) && $results = Customer::searchByName($search, 50)) {
                    foreach ($results as $result) {
                        if ($result['active']) {
                            $customers[$result['id_customer']] = $result;
                        }
                    }
                }
            }

            if (count($customers)) {
                $toReturn = [
                    'customers' => $customers,
                    'found' => true,
                ];
            } else {
                $toReturn = ['found' => false];
            }

            exit(json_encode($toReturn));
        }
    }

    /**
     * Append product options admin order details product line row
     *
     * @return void
     */
    public function ajaxProcessAppendInProductLine()
    {
        $idOrderDetail = Tools::getValue('id_order_detail');
        $idOrder = Tools::getValue('id_order');
        if ($idOrder && $idOrderDetail) {
            $order = new Order($idOrder);
            $objCustomerOptions = new WkProductCustomerOptions();
            $productInfo = $objCustomerOptions->getInfoFromIdOrderDetail($idOrder, $idOrderDetail);
            if (!empty($productInfo)) {
                $idProduct = $productInfo['product_id'];
                $idProductAttribute = $productInfo['product_attribute_id'];
                $idCustomization = $productInfo['id_customization'];
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
                        $optionPath = _PS_MODULE_DIR_ . $this->module->name . '/views/img/upload/' .
                        $option['option_value'];
                        if (file_exists($optionPath)) {
                            $option['option_value'] = _MODULE_DIR_ . $this->module->name . '/views/img/upload/' .
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
                        'WK_PRODUCT_OPTIONS_TEXT' => WkProductOptionsConfig::WK_PRODUCT_OPTIONS_TEXT,
                        'WK_PRODUCT_OPTIONS_TEXTAREA' => WkProductOptionsConfig::WK_PRODUCT_OPTIONS_TEXTAREA,
                        'WK_PRODUCT_OPTIONS_DROPDOWN' => WkProductOptionsConfig::WK_PRODUCT_OPTIONS_DROPDOWN,
                        'WK_PRODUCT_OPTIONS_CHECKBOX' => WkProductOptionsConfig::WK_PRODUCT_OPTIONS_CHECKBOX,
                        'WK_PRODUCT_OPTIONS_RADIO' => WkProductOptionsConfig::WK_PRODUCT_OPTIONS_RADIO,
                        'WK_PRODUCT_OPTIONS_FILE' => WkProductOptionsConfig::WK_PRODUCT_OPTIONS_FILE,
                        'WK_PRODUCT_OPTIONS_DATE' => WkProductOptionsConfig::WK_PRODUCT_OPTIONS_DATE,
                        'WK_PRODUCT_OPTIONS_TIME' => WkProductOptionsConfig::WK_PRODUCT_OPTIONS_TIME,
                        'WK_PRODUCT_OPTIONS_DATETIME' => WkProductOptionsConfig::WK_PRODUCT_OPTIONS_DATETIME,
                        'wk_option_page' => 'AdminOrders',
                        'display_mode' => 'normal',
                    ]
                );
                $data = $this->context->smarty->fetch(
                    _PS_MODULE_DIR_ . $this->module->name . '/views/templates/hook/product_option_saved_info.tpl'
                );
                exit(json_encode(['data' => $data]));
            }
        }
    }

    /**
     * Ajax to apply bulk action
     *
     * @return void
     */
    public function ajaxProcessOptionBulkAction()
    {
        $combinations = Tools::getValue('wk_select_option');
        if (!empty($combinations) && is_array($combinations)) {
            $idProduct = Tools::getValue('id_product');
            $isNativeCustomization = Tools::getValue('wk_disable_native_customization');
            $objProductConfiguration = new WkProductWiseConfiguration();
            $productConfig = $objProductConfiguration->getProductWiseConfiguration(
                $idProduct,
                $this->context->shop->id
            );
            if (!empty($productConfig)) {
                $objProductConfiguration = new WkProductWiseConfiguration($productConfig['id_wk_product_wise_configuration']);
            }
            $objProductConfiguration->id_product = (int) $idProduct;
            $objProductConfiguration->is_native_customization = (int) $isNativeCustomization;
            $objProductConfiguration->id_shop = (int) $this->context->shop->id;
            $objProductConfiguration->save();
            $objOptions = new WkProductOptionsConfig();
            $options = $objOptions->getAllOptions(
                $this->context->shop->id,
                $this->context->language->id
            );
            if (!empty($options)) {
                $objProduct = new Product($idProduct);
                if ($objProduct->hasCombinations()) {
                    // For combination product
                    if ($combinations && is_array($combinations)) {
                        foreach ($combinations as $combination) {
                            foreach ($options as $option) {
                                $objOptions->addUpdateProductWiseOption(
                                    $option['id_wk_product_options_config'],
                                    Tools::getValue('active_option_' . $option['id_wk_product_options_config']),
                                    $idProduct,
                                    $combination,
                                    $this->context->shop->id
                                );
                            }
                        }
                    }
                }
            }
            $objOptions = new WkProductOptionsConfig();
            $options = $objOptions->getAllOptions(
                $this->context->shop->id,
                $this->context->language->id
            );
            $objProductConfiguration = new WkProductWiseConfiguration();
            $productConfig = $objProductConfiguration->getProductWiseConfiguration(
                $idProduct,
                $this->context->shop->id
            );
            $objProduct = new Product($idProduct);
            $combinations = [];
            $combProduct = 0;
            if ($objProduct->hasCombinations()) {
                $combinations = $objProduct->getAttributeCombinations();
                $combinations = WkProductOptionsConfig::getCombinationName($combinations);
                if (!empty($combinations)) {
                    foreach ($combinations as &$combination) {
                        $combination['option_values'] = $objOptions->getActiveOptionsByCombination(
                            $idProduct,
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
                            $idProduct,
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
                    'id_ps_product' => $idProduct,
                    'has_combination' => $combProduct,
                    'wk_option_controller_link' => $this->context->link->getAdminLink('AdminProductOptions', true),
                    'wk_product_config' => $productConfig,
                ]
            );
            $data = $this->context->smarty->fetch(
                _PS_MODULE_DIR_ . $this->module->name . '/views/templates/hook/product_option_catalog.tpl'
            );
            exit(json_encode(['data' => $data, 'append' => 1, 'msg' => $this->l('Bulk action applied successfully.')]));
        }
    }

    /**
     * Assign media files to controller
     *
     * @param bool $isNewTheme
     *
     * @return void
     */
    public function setMedia($isNewTheme = false)
    {
        $parentMedia = parent::setMedia($isNewTheme);
        // assign variable to js file
        $jsVars = [
            'option_product_ajax' => $this->context->link->getAdminLink('AdminProductOptions'),
            'wkoptiondatatableMessegeEmpty' => $this->l('No product found !!'),
            'wkoptiondatatableNoMatching' => $this->l('No matching records found.'),
            'wkoptiondatatableSearch' => $this->l('Search'),
            'wkoptiondatatableFirstPage' => $this->l('First Page'),
            'wkoptiondatatablePrevious' => $this->l('Previous'),
            'wkoptiondatatableNext' => $this->l('Next'),
            'wkoptiondatatableLastPage' => $this->l('Last Page'),
            'wkoptiondatatableDropdownPrefix' => $this->l('Show'),
            'wkoptiondatatableDropdownSuffix' => $this->l('entries'),
            'wkoptiondatatableInfoPrefix' => $this->l('Showing'),
            'wkoptiondatatableInfoTo' => $this->l('To'),
            'wkoptiondatatableInfoOf' => $this->l('Of'),
            'wkoptiondatatableInfoSuffix' => $this->l('Entries'),
            'product_delete_success' => $this->l('Product successfully deleted.'),
            'product_add_success' => $this->l('Product successfully added.'),
            'Choose' => $this->l('Choose'),
            'no_customers_found' => $this->l('No customers found.'),
            'wk_option_text' => WkProductOptionsConfig::WK_PRODUCT_OPTIONS_TEXT,
            'wk_drop_down' => WkProductOptionsConfig::WK_PRODUCT_OPTIONS_DROPDOWN,
            'wk_checkbox' => WkProductOptionsConfig::WK_PRODUCT_OPTIONS_CHECKBOX,
            'wk_option_textarea' => WkProductOptionsConfig::WK_PRODUCT_OPTIONS_TEXTAREA,
            'wk_radio' => WkProductOptionsConfig::WK_PRODUCT_OPTIONS_RADIO,
            'wk_image_file' => WkProductOptionsConfig::WK_PRODUCT_OPTIONS_FILE,
            'date_format' => WkProductOptionsConfig::WK_PRODUCT_OPTIONS_DATE,
            'time_format' => WkProductOptionsConfig::WK_PRODUCT_OPTIONS_TIME,
            'datetimeformat' => WkProductOptionsConfig::WK_PRODUCT_OPTIONS_DATETIME,
            'wk_languages' => Language::getLanguages(),
            'wk_addtag' => $this->l('Add Option'),
            'wk_no_product' => $this->l('No products found'),
            'wkcurrentText' => $this->l('Now'),
            'wkcloseText' => $this->l('Done'),
            'wktimeOnlyTitle' => $this->l('Choose Time'),
            'wktimeText' => $this->l('Time'),
            'wkhourText' => $this->l('Hour'),
            'wkminuteText' => $this->l('Minute'),
            'wksecondText' => $this->l('Second'),
            'wkmillisecText' => $this->l('Millisecond'),
            'wkmicrosecText' => $this->l('Microsecond'),
            'wktimezoneText' => $this->l('Time Zone'),
            'wk_del_confirm' => $this->l('Are you sure you want to delete this item?'),
            'wk_delete_success' => $this->l('Option value deleted successfully.'),
            'wk_invalid_price_range' => $this->l('Price Impact in percentage must be between 0 and 100.'),
        ];
        Media::addJsDef($jsVars);
        // add datetime picker
        $this->addJqueryPlugin('datetimepicker');
        // tagify
        $this->addJqueryPlugin('tagify');
        $this->addJS(
            [
                _PS_MODULE_DIR_ . '/wkproductoptions/views/js/jquery.dataTables.min.js',
                _PS_MODULE_DIR_ . '/wkproductoptions/views/js/dataTables.bootstrap.js',
                _PS_MODULE_DIR_ . '/wkproductoptions/views/js/admin_product_option.js',
            ]
        );
        $this->addCSS(
            [
                _PS_MODULE_DIR_ . '/wkproductoptions/views/css/admin_product_option.css',
                _PS_MODULE_DIR_ . '/wkproductoptions/views/css/datatable_bootstrap.css',
            ]
        );

        return $parentMedia;
    }

    /**
     * Add new option
     *
     * @return void
     */
    public function ajaxProcessAddNewOption()
    {
        $idOption = Tools::getValue('id_option');
        $price = Tools::getValue('last_price');
        if (!Validate::isPrice($price)) {
            $price = 0.00;
        }
        if ($idOption) {
            $this->context->smarty->assign(
                [
                    'languages' => Language::getLanguages(false),
                    'total_languages' => count(Language::getLanguages(false)),
                    'current_lang' => Language::getLanguage((int) $this->context->language->id),
                    'currency_symbol' => $this->context->currency->sign,
                    'use_same' => Tools::getValue('use_same'),
                    'id_option' => $idOption,
                    'price' => $price,
                    'last_price_type' => Tools::getValue('last_price_type'),
                    'last_tax_type' => Tools::getValue('last_tax_type'),
                ]
            );
            $newOptionTpl = $this->context->smarty->fetch(
                _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/_partials/wk_add_option.tpl'
            );
            exit(json_encode(['data' => $newOptionTpl]));
        }
    }

    /**
     * Delete option value
     *
     * @return void
     */
    public function ajaxProcessDeleteOptionValue()
    {
        $idOptionValue = Tools::getValue('id_option_value');
        if ($idOptionValue) {
            $objOptionValue = new WkProductOptionsValue($idOptionValue);
            if (Validate::isLoadedObject($objOptionValue)) {
                $objOptionValue->delete();
                exit(json_encode(['success' => 1]));
            }
        }
        exit(json_encode(['success' => 0]));
    }

    /**
     * Save Option data in DB
     *
     * @return void
     */
    public function processSave()
    {
        if (Tools::isSubmit('submitAdd' . $this->table) || Tools::isSubmit('submitAdd' . $this->table . 'AndStay')) {
            $idOption = Tools::getValue('id_wk_product_options_config');
            $optionType = Tools::getValue('option_type');
            $currency = Tools::getValue('option_currency');
            if (Tools::getValue('customer')) {
                $customer = Tools::getValue('option_customer');
            } else {
                $customer = 0;
            }
            $country = Tools::getValue('option_country');
            $from = Tools::getValue('option_from');
            $to = Tools::getValue('option_to');
            $price = Tools::getValue('option_price');
            $priceType = Tools::getValue('option_price_type');
            $taxType = Tools::getValue('option_tax_type');
            $status = Tools::getValue('option_status');
            $categories = Tools::getValue('categoryBox');
            $groups = Tools::getValue('groupBox');
            $idProducts = Tools::getValue('idProducts');
            $defaultLangId = (int) $this->context->language->id;
            $objDefaultLanguage = Language::getLanguage((int) $defaultLangId);
            $characterLimit = Tools::getValue('max_character_limit');
            if (!trim(Tools::getValue('option_name_' . $defaultLangId))) {
                $this->errors[] = sprintf(
                    $this->l('Option name is required at least in %s.'),
                    $objDefaultLanguage['name']
                );
            } else {
                foreach (Language::getLanguages() as $language) {
                    $lang = Language::getLanguage((int) $language['id_lang']);
                    if (!$this->isCleanInput(Tools::getValue('option_name_' . $language['id_lang']))
                        || !Validate::isGenericName(Tools::getValue('option_name_' . $language['id_lang']))
                    ) {
                        $this->errors[] = $this->l('Option name is not valid in ') . $lang['name'];
                    } elseif (Tools::strlen(Tools::getValue('option_name_' . $language['id_lang'])) > 128) {
                        $this->errors[] = sprintf(
                            $this->l('Option name is too long. It must have 128 character or less in %s.'),
                            $lang['name']
                        );
                    }
                }
            }
            if (!trim(Tools::getValue('display_name_' . $defaultLangId))) {
                $this->errors[] = sprintf(
                    $this->l('Display name is required at least in %s.'),
                    $objDefaultLanguage['name']
                );
            } else {
                foreach (Language::getLanguages() as $language) {
                    $lang = Language::getLanguage((int) $language['id_lang']);
                    if (!$this->isCleanInput(Tools::getValue('display_name_' . $language['id_lang']))
                        || !Validate::isGenericName(Tools::getValue('display_name_' . $language['id_lang']))
                    ) {
                        $this->errors[] = sprintf(
                            $this->l('Display name is not valid in %s.'),
                            $lang['name']
                        );
                    } elseif (Tools::strlen(Tools::getValue('display_name_' . $language['id_lang'])) > 128) {
                        $this->errors[] = sprintf(
                            $this->l('Display name is too long. It must have 128 character or less in %s.'),
                            $lang['name']
                        );
                    }
                }
            }

            if (!trim(Tools::getValue('option_description_' . $defaultLangId))) {
                $this->errors[] = sprintf(
                    $this->l('Option description is required at least in %s.'),
                    $objDefaultLanguage['name']
                );
            } else {
                foreach (Language::getLanguages() as $language) {
                    $lang = Language::getLanguage((int) $language['id_lang']);
                    if (!$this->isCleanInput(Tools::getValue('option_description_' . $language['id_lang']))
                        || !Validate::isCleanHtml(Tools::getValue('option_description_' . $language['id_lang']))
                        || !Validate::isGenericName(Tools::getValue('option_description_' . $language['id_lang']))
                    ) {
                        $this->errors[] = sprintf(
                            $this->l('Option description is not valid in %s.'),
                            $lang['name']
                        );
                    } elseif (Tools::strlen(Tools::getValue('option_description_' . $language['id_lang'])) > 256) {
                        $this->errors[] = sprintf(
                            $this->l('Option description is too long. It must have 256 character or less in %s.'),
                            $lang['name']
                        );
                    }
                }
            }
            if ($optionType == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_TEXT) {
                $userInput = Tools::getValue('user_input');
            } else {
                $userInput = 0;
            }
            if ($optionType == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_TEXT
                || $optionType == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_TEXTAREA
            ) {
                foreach (Language::getLanguages() as $language) {
                    $lang = Language::getLanguage((int) $language['id_lang']);
                    if (trim(Tools::getValue('placeholder_' . $language['id_lang']))) {
                        if (!$this->isCleanInput(Tools::getValue('placeholder_' . $language['id_lang']))
                            || !Validate::isGenericName(Tools::getValue('placeholder_' . $language['id_lang']))
                        ) {
                            $this->errors[] = sprintf(
                                $this->l('Placeholder is not valid in %s.'),
                                $lang['name']
                            );
                        } elseif (Tools::strlen(Tools::getValue('placeholder_' . $language['id_lang'])) > 128) {
                            $this->errors[] = sprintf(
                                $this->l('Placeholder is too long. It must have 128 character or less in %s.'),
                                $lang['name']
                            );
                        }
                    }
                }
                $validate = false;
                if (($optionType == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_TEXT && $userInput)
                    || ($optionType == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_TEXTAREA)
                ) {
                    $validate = true;
                } else {
                    $validate = false;
                }
                if ($validate) {
                    if (trim($characterLimit) == '') {
                        $this->errors[] = $this->l('Please provide character limit.');
                    } elseif (!Validate::isUnsignedInt($characterLimit)) {
                        $this->errors[] = $this->l('Character limit field is not valid.');
                    }
                } else {
                    $characterLimit = 0;
                }
            } else {
                $characterLimit = 0;
            }
            if ($from) {
                if (!$to) {
                    $this->errors[] = $this->l('Availability date in \'To\' field is not valid.');
                }
            }
            if ($to) {
                if (!$from) {
                    $this->errors[] = $this->l('Availability date in \'From\' field is not valid.');
                }
            }
            if ($from) {
                if (!Validate::isDateFormat($from)) {
                    $this->errors[] = $this->l('Availability date in \'From\' field is not valid.');
                }
            }
            if ($to) {
                if (!Validate::isDateFormat($to)) {
                    $this->errors[] = $this->l('Availability date in \'To\' field is not valid.');
                }
            }
            if (!$from) {
                $from = '0000-00-00 00:00:00';
            }
            if (!$to) {
                $to = '0000-00-00 00:00:00';
            }
            if ($to != '0000-00-00 00:00:00' && strtotime($to) < strtotime($from)) {
                $this->errors[] = $this->l('Please enter a valid date range.');
            }
            if (!Validate::isPrice($price)) {
                $this->errors[] = $this->l('Please enter numeric value in price impact field.');
            } elseif ($priceType == 1) {
                if ($price > 100 || $price < 0) {
                    $this->errors[] = $this->l('Price Impact in percentage must be between 0 and 100.');
                }
            }
            $multiSelect = 0;
            if ($optionType == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_DROPDOWN
                || $optionType == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_CHECKBOX
                || $optionType == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_RADIO
            ) {
                $maxOptions = Tools::getValue('max_options');
                $maxOptionsArray = explode(',', $maxOptions);
                $countOption = Tools::getValue('count_options');
                if (!$countOption) {
                    $this->errors[] = $this->l('Please add some option values for this field.');
                }
                foreach ($maxOptionsArray as $optionIndex) {
                    if (!trim(Tools::getValue('display_value_' . $optionIndex . '_' . $defaultLangId))) {
                        $this->errors[] = sprintf(
                            $this->l('Option value is required at least in %s.'),
                            $objDefaultLanguage['name']
                        );
                    } else {
                        foreach (Language::getLanguages(true) as $language) {
                            if (Tools::getValue('display_value_' . $optionIndex . '_' . $language['id_lang'])) {
                                $this->optionValidation(
                                    Tools::getValue('display_value_' . $optionIndex . '_' . $language['id_lang']),
                                    0
                                );
                            }
                        }
                        $this->sameOptionValidation();
                    }
                }
                if ($optionType == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_DROPDOWN
                ) {
                    $multiSelect = Tools::getValue('multiselect');
                }
            }
            if (empty($this->errors)) {
                if ($idOption) {
                    $edit = 1;
                    $objOption = new $this->className($idOption);
                } else {
                    $edit = 0;
                    $objOption = new $this->className();
                    if ($objOption->position <= 0) {
                        if (Shop::isFeatureActive()) {
                            $objOption->position = WkProductOptionsConfig::getHigherPositionShop();
                        } else {
                            $objOption->position = WkProductOptionsConfig::getHigherPosition();
                        }
                        $objOption->position = WkProductOptionsConfig::getHigherPositionShop();
                    }
                }
                foreach (Language::getLanguages(false) as $language) {
                    $title = $language['id_lang'];
                    if (!Tools::getValue('option_name_' . $language['id_lang'])) {
                        $title = $defaultLangId;
                    }
                    $displayName = $language['id_lang'];
                    if (!Tools::getValue('display_name_' . $language['id_lang'])) {
                        $displayName = $defaultLangId;
                    }
                    $description = $language['id_lang'];

                    if (!Tools::getValue('option_description_' . $language['id_lang'])) {
                        $description = $defaultLangId;
                    }
                    $placeholder = $language['id_lang'];
                    if (!Tools::getValue('placeholder_' . $language['id_lang'])) {
                        $placeholder = $defaultLangId;
                    }
                    $objOption->name[$language['id_lang']] = trim(Tools::getValue('option_name_' . $title));
                    $objOption->display_name[$language['id_lang']] = trim(Tools::getValue('display_name_' . $displayName));
                    $objOption->description[$language['id_lang']] = trim(Tools::getValue('option_description_' . $description));
                    $objOption->placeholder[$language['id_lang']] = trim(Tools::getValue('placeholder_' . $placeholder));
                    $objOption->option_value[$language['id_lang']] = '';
                }
                $objOption->option_type = $optionType;
                $objOption->price = $price;
                $objOption->price_type = $priceType;
                $objOption->tax_type = $taxType;
                $objOption->active = $status;
                $objOption->text_limit = trim($characterLimit);
                $objOption->pre_selected = (int) Tools::getValue('pre_selected');
                $objOption->is_bulk_enabled = (int) Tools::getValue('is_bulk_enabled');
                $objOption->is_required = (int) Tools::getValue('is_required');
                $objOption->user_input = $userInput;
                $objOption->multiselect = $multiSelect;
                $objOption->save();
                if ($objOption->id) {
                    if ($objOption->option_type == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_DROPDOWN
                        || $objOption->option_type == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_CHECKBOX
                        || $objOption->option_type == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_RADIO
                    ) {
                        $objOptionValue = new WkProductOptionsValue();
                        $objOptionValue->deleteDataFromOptionTable($objOption->id, $this->context->shop->id);
                        $objOptionValue->saveOptionValues($objOption->id, $objOption->option_type);
                    }
                    if (!empty($groups)) {
                        $groups = json_encode($groups);
                    } else {
                        $groups = '';
                    }
                    if (!empty($categories)) {
                        $categories = json_encode($categories);
                    } else {
                        $categories = '';
                    }
                    if (!empty($idProducts)) {
                        $idProducts = json_encode($idProducts);
                    } else {
                        $idProducts = '';
                    }
                    if (!$customer) {
                        $customer = 0;
                    }
                    $objOption->deleteDataFromConditionTable($objOption->id);
                    $objOption->addEntriesInConditionTable(
                        $objOption->id,
                        $currency,
                        $country,
                        $groups,
                        $customer,
                        $idProducts,
                        $categories,
                        $from,
                        $to,
                        $this->context->shop->id
                    );
                    if ($objOption->is_bulk_enabled) {
                        $this->applyBulkProducts($objOption->id, json_decode($categories));
                    }
                }
                if (Tools::isSubmit('submitAdd' . $this->table . 'AndStay')) {
                    if ($edit) {
                        Tools::redirectAdmin(
                            self::$currentIndex . '&id_wk_product_options_config=' . (int) $objOption->id .
                            '&update' . $this->table . '&conf=4&token=' . $this->token
                        );
                    } else {
                        Tools::redirectAdmin(
                            self::$currentIndex . '&id_wk_product_options_config=' . (int) $objOption->id .
                            '&update' . $this->table . '&conf=3&token=' . $this->token
                        );
                    }
                } else {
                    if ($edit) {
                        Tools::redirectAdmin(self::$currentIndex . '&conf=4&token=' . $this->token);
                    } else {
                        Tools::redirectAdmin(self::$currentIndex . '&conf=3&token=' . $this->token);
                    }
                }
            }
            if ($idOption) {
                $this->display = 'edit';
            } else {
                $this->display = 'add';
            }
        }
    }

    /**
     * Validation of options from dropdown/checkbox/radio etc.
     *
     * @param string $optionName
     * @param int $flag
     *
     * @return void
     */
    public function optionValidation($optionName, $flag)
    {
        $isValidTag = preg_match('/^[^!<>;?=+#"°{}_$%]*$/u', $optionName);
        if ($isValidTag == 0) {
            $flag = 1;
        }

        if ($flag == 1) {
            $this->errors[] = $this->l('Entered option is invalid. It should not contain special characters.');
        }
    }

    /**
     * Apply option in bulk
     *
     * @param int $idOption
     * @param array $categories
     *
     * @return void
     */
    public function applyBulkProducts($idOption, $categories)
    {
        if (is_array($categories)) {
            foreach ($categories as $category) {
                $objCategory = new Category($category);
                $catProducts = $objCategory->getProducts(
                    $this->context->language->id,
                    0,
                    100000000,
                    null,
                    null,
                    false,
                    true,
                    false,
                    1,
                    false
                );
                if (!empty($catProducts) && is_array($catProducts)) {
                    $objOptions = new WkProductOptionsConfig();
                    foreach ($catProducts as $prod) {
                        $objProduct = new Product($prod['id_product']);
                        $objProductConfiguration = new WkProductWiseConfiguration();
                        $productConfig = $objProductConfiguration->getProductWiseConfiguration(
                            $prod['id_product'],
                            $this->context->shop->id
                        );
                        if (!empty($productConfig)) {
                            $objProductConfiguration = new WkProductWiseConfiguration($productConfig['id_wk_product_wise_configuration']);
                        }
                        $objProductConfiguration->id_product = (int) $prod['id_product'];
                        $objProductConfiguration->is_native_customization = (int) 1;
                        $objProductConfiguration->id_shop = (int) $this->context->shop->id;
                        $objProductConfiguration->save();
                        if ($objProduct->hasCombinations()) {
                            // For combination product
                            $combinations = $objProduct->getAttributeCombinations();
                            $combinations = WkProductOptionsConfig::getCombinationName($combinations);
                            if (!empty($combinations)) {
                                foreach ($combinations as $combination) {
                                    $objOptions->addUpdateProductWiseOption(
                                        $idOption,
                                        1,
                                        $prod['id_product'],
                                        $combination['id_product_attribute'],
                                        $this->context->shop->id
                                    );
                                }
                            }
                        } else {
                            // For normal product
                            $objOptions->addUpdateProductWiseOption(
                                $idOption,
                                1,
                                $prod['id_product'],
                                0,
                                $this->context->shop->id
                            );
                        }
                    }
                }
            }
        }
    }

    /**
     * Ajax process to update status positions
     *
     * @return void
     */
    public function ajaxProcessUpdatePositions()
    {
        $way = (int) Tools::getValue('way');
        $idOption = (int) Tools::getValue('id');
        $positions = Tools::getValue('wk_product_options_config');
        foreach ($positions as $position => $value) {
            $pos = explode('_', $value);
            if (isset($pos[2]) && (int) $pos[2] === $idOption) {
                if ($objOption = new WkProductOptionsConfig((int) $pos[2])) {
                    if (Shop::isFeatureActive()) {
                        if (isset($position)
                            && $objOption->updatePositionShop($way, $position, $this->context->shop->id, $idOption)
                        ) {
                            echo 'ok position ' . (int) $position . ' for status ' . (int) $pos[1] . '\r\n';
                        } else {
                            echo '{"hasError" : true, "errors" : "Can not update status ' .
                            (int) $idOption . ' to position ' . (int) $position . ' "}';
                        }
                    } else {
                        if (isset($position)
                            && $objOption->updatePosition($way, $position, $idOption)
                        ) {
                            $objOption->updatePositionShop($way, $position, $this->context->shop->id, $idOption);
                            echo 'ok position ' . (int) $position . ' for status ' . (int) $pos[1] . '\r\n';
                        } else {
                            echo '{"hasError" : true, "errors" : "Can not update status ' .
                            (int) $idOption . ' to position ' . (int) $position . ' "}';
                        }
                    }
                } else {
                    echo '{"hasError" : true, "errors" : "This status (' . (int) $idOption .
                    ') can t be loaded"}';
                }
                break;
            }
        }
    }

    /**
     * Function to check clean content
     *
     * @param string $input
     *
     * @return bool
     */
    private function isCleanInput($input)
    {
        if (preg_match('/<[\s]*script/ims', $input)
            || preg_match('/.*script\:/ims', $input)
        ) {
            return false;
        }

        return true;
    }

    /**
     * Same option value validation
     *
     * @return void
     */
    public function sameOptionValidation()
    {
        $defaultLangId = (int) $this->context->language->id;
        $maxOptions = Tools::getValue('max_options');
        $maxOptionsArray = explode(',', $maxOptions);
        $optionValueArray = [];
        foreach ($maxOptionsArray as $optionIndex) {
            foreach (Language::getLanguages(true) as $language) {
                $optionidLang = $language['id_lang'];
                if (!Tools::getValue('display_value_' . $optionIndex . '_' . $language['id_lang'])) {
                    $optionidLang = $defaultLangId;
                }
                $optionValueArray[$language['id_lang']][] = Tools::getValue('display_value_' . $optionIndex . '_' . $optionidLang);
            }
        }
        if (!empty($optionValueArray)) {
            foreach ($optionValueArray as $opt) {
                if (count($opt) != count(array_unique($opt))) {
                    $this->errors[] = $this->l('Please check entered option values, some options are duplicate.');
                }
            }
        }
    }
}
