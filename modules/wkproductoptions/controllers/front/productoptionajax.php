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
class WkProductOptionsProductOptionAjaxModuleFrontController extends ModuleFrontController
{
    public function init()
    {
        parent::init();
        $this->display_header = false;
        $this->display_footer = false;
    }

    public function initContent()
    {
        if (!$this->isTokenValid()) {
            return false;
        }
        if (Tools::getValue('action') == 'addProductOption') {
            $idProduct = Tools::getValue('id_product');
            $idProductAttribute = Tools::getValue('id_product_attribute');
            $idCustomization = Tools::getValue('id_customization');
            if ($idProduct) {
                $objOption = new WkProductOptionsConfig();
                $options = $objOption->getAvailableOptionByIdProduct($idProduct, $idProductAttribute);
                $optionsArray = [];
                $objCustomerOption = new WkProductCustomerOptions();
                $objCustomerOption->deleteProductOptionsFromCart(
                    $idProduct,
                    $idProductAttribute,
                    $idCustomization,
                    $this->context->cart->id
                );
                if (!empty($options)) {
                    foreach ($options as $option) {
                        $id = $option['id_wk_product_options_config'];
                        $idCustomerOption = WkProductCustomerOptions::checkProductWiseEntryExists(
                            $id,
                            $idProduct,
                            $idProductAttribute,
                            $idCustomization,
                            $this->context->shop->id,
                            $this->context->cart->id
                        );
                        $objCustomerOption = new WkProductCustomerOptions($idCustomerOption);
                        if (Tools::getValue('wk_option_' . $id)) {
                            $optionsArray[] = $id;
                            if (isset($this->context->customer->id)) {
                                $objCustomerOption->id_customer = $this->context->customer->id;
                            } else {
                                $objCustomerOption->id_customer = 0; // if guest, will replace on order
                            }
                            $objCustomerOption->id_cart = $this->context->cart->id;
                            $objCustomerOption->id_product = $idProduct;
                            $objCustomerOption->id_product_attribute = $idProductAttribute;
                            $objCustomerOption->price_impact = $option['price'];
                            $objCustomerOption->id_customization = $idCustomization;
                            $objCustomerOption->id_order = 0;
                            $objCustomerOption->price_type = $option['price_type'];
                            $objCustomerOption->tax_type = $option['tax_type'];
                            $objCustomerOption->in_cart = 1;
                            $objCustomerOption->option_type = $option['option_type'];
                            $objCustomerOption->id_option = $id;
                            $objCustomerOption->option_title = $option['display_name'];
                            if ($option['option_type'] == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_TEXT) {
                                if ($option['user_input']) {
                                    $objCustomerOption->option_value = Tools::getValue('wk_option_text_area_' . $id);
                                    $objCustomerOption->input_color = Tools::getValue('input_color_' . $id);
                                } else {
                                    $objCustomerOption->option_value = '';
                                }
                                $objCustomerOption->user_input = $option['user_input'];
                            } elseif ($option['option_type'] == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_TEXTAREA) {
                                $objCustomerOption->option_value = Tools::getValue('wk_option_text_area_new_' . $id);
                                $objCustomerOption->input_color = Tools::getValue('input_color_' . $id);
                            } elseif ($option['option_type'] == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_DROPDOWN) {
                                if ($option['multiselect']) {
                                    $optionValueId = Tools::getValue('wk_option_dropdown_' . $id);
                                    $optvalues = [];
                                    if (!empty($optionValueId)) {
                                        foreach ($optionValueId as $dVal) {
                                            $obOptVal = new WkProductOptionsValue(
                                                $dVal,
                                                $this->context->language->id,
                                                $this->context->shop->id
                                            );
                                            if (Validate::isLoadedObject($obOptVal)) {
                                                $optvalues[] = $obOptVal->option_value;
                                            }
                                        }
                                        $objCustomerOption->option_value = json_encode($optvalues);
                                    }
                                } else {
                                    $objCustomerOption->option_value = Tools::getValue('wk_option_dropdown_' . $id);
                                }
                                $objCustomerOption->multiselect = $option['multiselect'];
                            } elseif ($option['option_type'] == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_CHECKBOX) {
                                $selectedOptions = [];
                                if ($option['options_value_arr']) {
                                    foreach ($option['options_value_arr'] as $opt) {
                                        if (Tools::getValue('wk_option_' . $id . '_' . $opt['id_wk_product_options_value'])) {
                                            $selectedOptions[] = $opt['option_value'];
                                        }
                                    }
                                }
                                $objCustomerOption->option_value = json_encode($selectedOptions);
                            } elseif ($option['option_type'] == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_RADIO) {
                                $objCustomerOption->option_value = Tools::getValue('wk_option_radio_' . $id);
                            } elseif ($option['option_type'] == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_FILE) {
                                $name = 'wk_option_file' . $id;
                                if ($_FILES[$name]) {
                                    $objCustomerOption->id_shop = $this->context->shop->id;
                                    $objCustomerOption->option_value = 'dummy';
                                    $objCustomerOption->save();
                                    $imageName = md5($_FILES[$name]['name'] . date('Y-m-d H:i:s') . $objCustomerOption->id);
                                    $objCustomerOption->option_value = $imageName;
                                    $objCustomerOption->uploadOptionImage($_FILES[$name], $objCustomerOption->id, $imageName);
                                }
                            } elseif ($option['option_type'] == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_DATE) {
                                $date = Tools::getValue('wk_option_date_' . $id);
                                $objCustomerOption->option_value = trim($date);
                            } elseif ($option['option_type'] == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_TIME) {
                                $objCustomerOption->option_value = trim(Tools::getValue('wk_option_time_' . $id));
                            } elseif ($option['option_type'] == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_DATETIME) {
                                $datetime = Tools::getValue('wk_option_datetime_' . $id);
                                $objCustomerOption->option_value = trim($datetime);
                            } else {
                                $objCustomerOption->option_value = '';
                            }
                            $objCustomerOption->id_shop = $this->context->shop->id;
                            $objCustomerOption->save();
                        }
                        unset($objCustomerOption);
                    }
                }
                exit(json_encode(['success' => 1]));
            }
        }
        if (Tools::getValue('action') == 'changeCatalogPrice') {
            $idProduct = Tools::getValue('id_product');
            $idProductAttribute = Tools::getValue('id_product_attribute');
            if (!$idProductAttribute) {
                $idProductAttribute = 0;
            }
            $optionsArray = Tools::getValue('selectedOption');
            $objProduct = new Product($idProduct);
            $productPriceTe = $objProduct->getPrice(false, $idProductAttribute);
            $productPriceTi = $objProduct->getPrice(true, $idProductAttribute);
            $productPriceTe = Tools::convertPriceFull(
                $productPriceTe,
                $this->context->currency,
                new Currency((int) Configuration::get('PS_CURRENCY_DEFAULT'))
            );
            $productPriceTi = Tools::convertPriceFull(
                $productPriceTi,
                $this->context->currency,
                new Currency((int) Configuration::get('PS_CURRENCY_DEFAULT'))
            );
            $objCustomerOption = new WkProductCustomerOptions();
            $optionPriceTe = 0;
            $optionPriceTi = 0;
            if (!empty($optionsArray)) {
                $objOptionValue = new WkProductOptionsValue();
                foreach ($optionsArray as $option) {
                    $optPriceTe = 0;
                    $optPriceTi = 0;
                    $objOption = new WkProductOptionsConfig($option);
                    if ($objOption->option_type == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_DROPDOWN
                        || $objOption->option_type == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_CHECKBOX
                        || $objOption->option_type == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_RADIO
                    ) {
                        $selectedValues = Tools::getValue('id_values');
                        if (!empty($selectedValues) && is_array($selectedValues)) {
                            $selectedValues = array_unique($selectedValues);
                            $selectedValuesFormattedWithTi = $objOptionValue->getAllDisplayOptionsByIdOptions(
                                $objOption->id,
                                $selectedValues,
                                $this->context->language->id,
                                $this->context->shop->id,
                                $idProduct,
                                true
                            );
                            $selectedValuesFormattedWithTe = $objOptionValue->getAllDisplayOptionsByIdOptions(
                                $objOption->id,
                                $selectedValues,
                                $this->context->language->id,
                                $this->context->shop->id,
                                $idProduct,
                                false
                            );
                            if (!empty($selectedValuesFormattedWithTi)) {
                                foreach ($selectedValuesFormattedWithTi as $optValue) {
                                    $optPriceTi = $optPriceTi + $optValue['price_impact'];
                                }
                            }
                            if (!empty($selectedValuesFormattedWithTe)) {
                                foreach ($selectedValuesFormattedWithTe as $optValue) {
                                    $optPriceTe = $optPriceTe + $optValue['price_impact'];
                                }
                            }
                        }
                    } else {
                        $optPriceTe = $objCustomerOption->getPriceByOptionObject(
                            $objOption,
                            $idProduct,
                            $idProductAttribute,
                            false
                        );
                        $optPriceTi = $objCustomerOption->getPriceByOptionObject(
                            $objOption,
                            $idProduct,
                            $idProductAttribute,
                            true
                        );
                    }
                    $optionPriceTe = $optionPriceTe + $optPriceTe;
                    $optionPriceTi = $optionPriceTi + $optPriceTi;
                    unset($objOption);
                }
            }
            $productPriceTe = $productPriceTe + $optionPriceTe;
            $productPriceTi = $productPriceTi + $optionPriceTi;
            $productPriceTe = Tools::convertPriceFull(
                $productPriceTe,
                new Currency((int) Configuration::get('PS_CURRENCY_DEFAULT')),
                $this->context->currency
            );
            $productPriceTi = Tools::convertPriceFull(
                $productPriceTi,
                new Currency((int) Configuration::get('PS_CURRENCY_DEFAULT')),
                $this->context->currency
            );
            $priceDisplay = Product::getTaxCalculationMethod(
                (int) Context::getContext()->cookie->id_customer
            );
            if (!$priceDisplay || $priceDisplay == 2) {
                $displayType = 1;
            } else {
                $displayType = 0;
            }
            $productsInfo = WkProductOptionsConfig::getProductProp($idProduct, $idProductAttribute, $this->context->language->id);
            $productsInfo = Product::getProductProperties(
                (int) $this->context->language->id,
                $productsInfo
            );
            $reductionType = 'amount';
            if ($productsInfo['reduction'] > 0) {
                $reductionType = $productsInfo['specific_prices']['reduction_type'];
            }
            if ($displayType) {
                if ($reductionType == 'percentage') {
                    $productsInfo['price_tax_exc'] = $productPriceTe;
                    $productsInfo['price'] = $productPriceTi - ($optionPriceTi * $productsInfo['specific_prices']['reduction']);
                    $productsInfo['price_without_reduction'] = $productPriceTi + $productsInfo['reduction'];
                    $productsInfo['price_without_reduction_without_tax'] = $productPriceTe + $productsInfo['reduction_without_tax'];
                } else {
                    $productsInfo['price_tax_exc'] = $productPriceTe;
                    $productsInfo['price'] = $productPriceTi;
                    $productsInfo['price_without_reduction'] = $productPriceTi + $productsInfo['reduction'];
                    $productsInfo['price_without_reduction_without_tax'] = $productPriceTe + $productsInfo['reduction_without_tax'];
                }
            } else {
                if ($reductionType == 'percentage') {
                    $productsInfo['price_tax_exc'] = $productPriceTe - ($optionPriceTe * $productsInfo['specific_prices']['reduction']);
                    $productsInfo['price'] = $productPriceTe - ($optionPriceTe * $productsInfo['specific_prices']['reduction']);
                    $productsInfo['price_without_reduction_without_tax'] = $productPriceTe + $productsInfo['reduction_without_tax'];
                    $productsInfo['price_without_reduction'] = $productPriceTi + $productsInfo['reduction'];
                } else {
                    $productsInfo['price_tax_exc'] = $productPriceTe;
                    $productsInfo['price'] = $productPriceTe;
                    $productsInfo['price_without_reduction_without_tax'] = $productPriceTe + $productsInfo['reduction_without_tax'];
                    $productsInfo['price_without_reduction'] = $productPriceTi + $productsInfo['reduction'];
                }
            }
            $factory = new ProductPresenterFactory(Context::getContext(), new TaxConfiguration());
            $productSettings = $factory->getPresentationSettings();
            $presenter = $factory->getPresenter();
            $productsInfo = $presenter->present(
                $productSettings,
                $productsInfo,
                new Language($this->context->language->id)
            );
            $this->context->smarty->assign('product', $productsInfo);

            $priceTpl = $this->context->smarty->fetch(
                _PS_MODULE_DIR_ . $this->module->name . '/views/templates/hook/product_prices.tpl'
            );
            exit(
                json_encode(
                    [
                        'product_price_tax_te' => $productPriceTe,
                        'product_price_tax_ti' => $productPriceTi,
                        'price_tpl' => $priceTpl,
                        'product_price_tax_te_formated' => Tools::displayPrice(
                            $productPriceTe,
                            $this->context->currency
                        ),
                        'product_price_tax_ti_formated' => Tools::displayPrice(
                            $productPriceTi,
                            $this->context->currency
                        ),
                        'wk_tax_rule' => $displayType,
                    ]
                )
            );
        }
        if (Tools::getValue('action') == 'validateImage') {
            $knownMimeType = [
                'image/gif',
                'image/jpg',
                'image/jpeg',
                'image/pjpeg',
                'image/png',
                'image/x-png',
            ];
            $fieldName = Tools::getValue('field_name');
            if (isset($_FILES[$fieldName])) {
                if (function_exists('mime_content_type')) {
                    $mimeType = @mime_content_type($_FILES[$fieldName]['tmp_name']);
                    if (!in_array($mimeType, $knownMimeType)) {
                        $data = [
                            'status' => 'fail',
                            'message' => $this->module->l('Invalid file', 'productoptionajax'),
                        ];
                        exit(json_encode($data));
                    } else {
                        $data = [
                            'status' => 'success',
                        ];
                        exit(json_encode($data));
                    }
                }
            }
        }

        if (Tools::getValue('action') == 'changeVariantTemplate') {
            if ($idProduct = Tools::getValue('id_product')) {
                $objOption = new WkProductOptionsConfig();
                $objProduct = new Product($idProduct);
                $idProductAttribute = (int) Product::getDefaultAttribute($idProduct);
                if ($objProduct->hasCombinations()) {
                    $idProductAttribute = (int) Tools::getValue('id_product_attribute');
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
                $template = $this->context->smarty->fetch(
                    _PS_MODULE_DIR_ . 'wkproductoptions/views/templates/hook/product_options.tpl'
                );
                exit(json_encode(['data' => $template]));
            }
        }
    }
}
