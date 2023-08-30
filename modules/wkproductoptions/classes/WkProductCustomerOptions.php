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
class WkProductCustomerOptions extends ObjectModel
{
    public $id_wk_product_customer_options;
    public $id_customer;
    public $id_cart;
    public $id_product;
    public $id_product_attribute;
    public $price_impact;
    public $id_customization;
    public $id_order;
    public $in_cart;
    public $option_type;
    public $price_type;
    public $tax_type;
    public $user_input;
    public $multiselect;
    public $input_color;
    public $id_option;
    public $option_value;
    public $option_title;
    public $id_shop;

    public static $definition = [
        'table' => 'wk_product_customer_options',
        'primary' => 'id_wk_product_customer_options',
        'fields' => [
            'id_customer' => ['type' => self::TYPE_INT, 'required' => true],
            'id_cart' => ['type' => self::TYPE_INT, 'required' => true],
            'id_product' => ['type' => self::TYPE_FLOAT, 'required' => true],
            'id_product_attribute' => ['type' => self::TYPE_INT, 'required' => true],
            'id_customization' => ['type' => self::TYPE_INT, 'required' => true],
            'id_order' => ['type' => self::TYPE_INT, 'required' => true],
            'in_cart' => ['type' => self::TYPE_INT, 'required' => true],
            'price_impact' => ['type' => self::TYPE_FLOAT],
            'option_type' => ['type' => self::TYPE_INT],
            'price_type' => ['type' => self::TYPE_INT],
            'tax_type' => ['type' => self::TYPE_INT],
            'id_option' => ['type' => self::TYPE_INT],
            'user_input' => ['type' => self::TYPE_INT],
            'input_color' => ['type' => self::TYPE_STRING],
            'multiselect' => ['type' => self::TYPE_INT],
            'option_value' => ['type' => self::TYPE_HTML],
            'option_title' => ['type' => self::TYPE_HTML],
            'id_shop' => ['type' => self::TYPE_INT],
        ],
    ];

    /**
     * Upload custom image from front end
     *
     * @param int $files
     * @param int $idOption
     *
     * @return string|bool
     */
    public static function uploadOptionImage($files, $idOption, $imageName)
    {
        if (!empty($files['name']) && $files['size'] > 0 && $idOption) {
            $uploadPath = _PS_MODULE_DIR_ . 'wkproductoptions/views/img/upload/';
            ImageManager::resize($files['tmp_name'], $uploadPath . $imageName, 800, 800);

            return $imageName;
        }

        return false;
    }

    /**
     * Check whether entry exists in the cart or not
     *
     * @param int $idOption
     * @param int $idProduct
     * @param int $idProductAttribute
     * @param int $idCustomization
     * @param int $idShop
     * @param int $idCart
     *
     * @return int
     */
    public static function checkProductWiseEntryExists(
        $idOption,
        $idProduct,
        $idProductAttribute,
        $idCustomization,
        $idShop,
        $idCart
    ) {
        return Db::getInstance()->getValue(
            'SELECT `id_wk_product_customer_options`
            FROM `' . _DB_PREFIX_ . 'wk_product_customer_options`
            WHERE `id_option` = ' . (int) $idOption .
                ' AND `id_product` = ' . (int) $idProduct .
                ' AND `id_product_attribute` = ' . (int) $idProductAttribute .
                ' AND `id_customization` = ' . (int) $idCustomization .
                ' AND `id_cart` = ' . (int) $idCart .
                ' AND `id_shop` = ' . (int) $idShop
        );
    }

    /**
     * Check Product wise entry exists in cart or not
     *
     * @param int $idProduct
     * @param int $idProductAttribute
     * @param int $idCustomization
     * @param int $idShop
     * @param int $idCart
     *
     * @return int|bool
     */
    public static function checkProductEntry(
        $idProduct,
        $idProductAttribute,
        $idCustomization,
        $idShop,
        $idCart
    ) {
        $sql = 'SELECT `id_wk_product_customer_options`
        FROM `' . _DB_PREFIX_ . 'wk_product_customer_options`
        WHERE `id_product` = ' . (int) $idProduct .
            ' AND `id_product_attribute` = ' . (int) $idProductAttribute .
            ' AND `id_cart` = ' . (int) $idCart .
            ' AND `id_shop` = ' . (int) $idShop;
        if ($idCustomization) {
            $sql .= '  AND `id_customization` = ' . (int) $idCustomization;
        }

        return Db::getInstance()->getValue($sql);
    }

    /**
     * Check product exits in current cart or not
     *
     * @param int $idProduct
     * @param int $idProductAttribute
     *
     * @return bool
     */
    public function checkProductExistsInCart(
        $idProduct,
        $idProductAttribute
    ) {
        $inCart = false;
        $context = Context::getContext();
        $cartProducts = $context->cart->getProducts();
        if (!empty($cartProducts)) {
            foreach ($cartProducts as $product) {
                if (($product['id_product'] == $idProduct)
                    && ($product['id_product_attribute'] == $idProductAttribute)
                ) {
                    if (self::checkProductEntry(
                        $idProduct,
                        $idProductAttribute,
                        false,
                        $context->shop->id,
                        $context->cart->id
                    )) {
                        $inCart = true;
                        break;
                    }
                }
            }
        }

        return $inCart;
    }

    /**
     * Get price by Option
     *
     * @param object $option
     * @param int $idProduct
     * @param int $idProduct
     *
     * @return float
     */
    public function getPriceByOptionObject($option, $idProduct, $idProductAttribute, $useTax = false)
    {
        if (Validate::isLoadedObject($option)) {
            $objProduct = new Product($idProduct);
            if ($option->price_type == 1) {
                $productPrice = $objProduct->getPrice($useTax, $idProductAttribute);
                $productPrice = Tools::convertPriceFull(
                    $productPrice,
                    Context::getContext()->currency,
                    new Currency((int) Configuration::get('PS_CURRENCY_DEFAULT'))
                );
                $priceImpact = $productPrice * ($option->price / 100);
            } else {
                if ($option->tax_type == 1) {
                    $priceImpactTaxIncl = $option->price;
                    $taxRate = Tax::getProductTaxRate($idProduct);
                    $priceImpactTaxExcl = $option->price / (1 + ($taxRate / 100));
                } else {
                    $priceImpactTaxExcl = $option->price;
                    $taxRate = Tax::getProductTaxRate($idProduct);
                    $priceImpactTaxIncl = $option->price * (1 + ($taxRate / 100));
                }
                if ($useTax) {
                    $priceImpact = $priceImpactTaxIncl;
                } else {
                    $priceImpact = $priceImpactTaxExcl;
                }
            }

            return $priceImpact;
        }

        return 0;
    }

    /**
     * Get Saved Product option info
     *
     * @param int $idProduct
     * @param int $idProductAttribute
     * @param int $idCustomization
     * @param int $idCart
     *
     * @return array
     */
    public function getSavedProductOptionsBeforeOrder(
        $idProduct,
        $idProductAttribute,
        $idCustomization,
        $idCart
    ) {
        return Db::getInstance()->executeS(
            'SELECT co.*, pos.`price` as price
            FROM `' . _DB_PREFIX_ . 'wk_product_customer_options` co
            LEFT JOIN `' . _DB_PREFIX_ . 'wk_product_options_config_shop` pos
            ON (pos.id_wk_product_options_config = co.id_option)
            WHERE co.`id_product` = ' . (int) $idProduct .
                ' AND co.`id_product_attribute` = ' . (int) $idProductAttribute .
                ' AND co.`id_customization` = ' . (int) $idCustomization .
                ' AND co.`id_cart` = ' . (int) $idCart . ' AND co.`id_order` = 0 AND co.`in_cart` = 1'
        );
    }

    /**
     * Get Saved Product option info
     *
     * @param int $idProduct
     * @param int $idProductAttribute
     * @param int $idCustomization
     * @param int $idCart
     *
     * @return array
     */
    public function getSavedProductOptionsByIdCart(
        $idProduct,
        $idProductAttribute,
        $idCustomization,
        $idCart
    ) {
        return Db::getInstance()->executeS(
            'SELECT co.*, pos.`price` as price
            FROM `' . _DB_PREFIX_ . 'wk_product_customer_options` co
            LEFT JOIN `' . _DB_PREFIX_ . 'wk_product_options_config_shop` pos
            ON (pos.id_wk_product_options_config = co.id_option)
            WHERE co.`id_product` = ' . (int) $idProduct .
                ' AND co.`id_product_attribute` = ' . (int) $idProductAttribute .
                ' AND co.`id_customization` = ' . (int) $idCustomization .
                ' AND co.`id_cart` = ' . (int) $idCart
        );
    }

    /**
     * Get Saved Product option info
     *
     * @param int $idProduct
     * @param int $idProductAttribute
     * @param int $idCustomization
     * @param int $idCart
     *
     * @return array
     */
    public function getSavedProductOptionsAfterOrder(
        $idProduct,
        $idProductAttribute,
        $idCustomization,
        $idOrder
    ) {
        return Db::getInstance()->executeS(
            'SELECT co.*, pos.`price` as price
            FROM `' . _DB_PREFIX_ . 'wk_product_customer_options` co
            LEFT JOIN `' . _DB_PREFIX_ . 'wk_product_options_config_shop` pos
            ON (pos.id_wk_product_options_config = co.id_option)
            WHERE co.`id_product` = ' . (int) $idProduct .
                ' AND co.`id_product_attribute` = ' . (int) $idProductAttribute .
                ' AND co.`id_customization` = ' . (int) $idCustomization .
                ' AND co.`id_order` = ' . (int) $idOrder . ' AND co.`in_cart` = 0'
        );
    }

    /**
     * Add Data in errors during add to cart
     *
     * @param int $idProduct
     * @param int $idProductAttribute
     *
     * @return array
     */
    public function addToCartValidations($idProduct, $idProductAttribute)
    {
        $errors = [];
        $errors['error'] = [];
        $customerSelectedOption = 0;
        $errors['selected'] = 0;
        if ($idProduct) {
            $objOption = new WkProductOptionsConfig();
            $moduleInstance = new WkProductOptions();
            $options = $objOption->getAvailableOptionByIdProduct($idProduct, $idProductAttribute);
            $selectedOptions = [];
            if (!empty($options)) {
                if (!isset(Context::getContext()->cart->id)) {
                    if (Context::getContext()->cookie->id_guest) {
                        $guest = new Guest(Context::getContext()->cookie->id_guest);
                        Context::getContext()->cart->mobile_theme = $guest->mobile_theme;
                    }
                    Context::getContext()->cart->add();
                    if (Context::getContext()->cart->id) {
                        Context::getContext()->cookie->id_cart = (int) Context::getContext()->cart->id;
                        Context::getContext()->cookie->write();
                    }
                }
                foreach ($options as $option) {
                    $id = $option['id_wk_product_options_config'];
                    if ($option['is_required'] && !Tools::getValue('wk_option_' . $id)) {
                        $errors['error'][] = sprintf(
                            $moduleInstance->l('Please select %s option. It is required field.', 'WkProductCustomerOptions'),
                            $option['display_name']
                        );
                    } else {
                        if (Tools::getValue('wk_option_' . $id)) {
                            $customerSelectedOption = 1;
                            $selectedOptions[] = $id;
                            if ($option['option_type'] == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_TEXT) {
                                if ($option['user_input']) {
                                    if (!Tools::getValue('wk_option_text_area_' . $id)) {
                                        $errors['error'][] = sprintf(
                                            $moduleInstance->l('Fill value for option : %s', 'WkProductCustomerOptions'),
                                            $option['display_name']
                                        );
                                    } elseif (!Validate::isCleanHtml(Tools::getValue('wk_option_text_area_' . $id))) {
                                        $errors['error'][] = sprintf(
                                            $moduleInstance->l('Please enter valid value for : %s', 'WkProductCustomerOptions'),
                                            $option['display_name']
                                        );
                                    } elseif ($option['text_limit'] && Tools::strlen(Tools::getValue('wk_option_text_area_' . $id)) > $option['text_limit']) {
                                        $errors['error'][] = sprintf(
                                            $moduleInstance->l('Maximum character limit for : (%s) is %d.', 'WkProductCustomerOptions'),
                                            $option['display_name'],
                                            $option['text_limit']
                                        );
                                    }
                                }
                            } elseif ($option['option_type'] == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_TEXTAREA) {
                                if (!Tools::getValue('wk_option_text_area_new_' . $id)) {
                                    $errors['error'][] = sprintf(
                                        $moduleInstance->l('Fill value for option : %s', 'WkProductCustomerOptions'),
                                        $option['display_name']
                                    );
                                } elseif (!Validate::isCleanHtml(Tools::getValue('wk_option_text_area_new_' . $id))) {
                                    $errors['error'][] = sprintf(
                                        $moduleInstance->l('Please enter valid value for : %s', 'WkProductCustomerOptions'),
                                        $option['display_name']
                                    );
                                } elseif ($option['text_limit'] && Tools::strlen(Tools::getValue('wk_option_text_area_new_' . $id)) > $option['text_limit']) {
                                    $errors['error'][] = sprintf(
                                        $moduleInstance->l('Maximum character limit for : (%s) is %d.', 'WkProductCustomerOptions'),
                                        $option['display_name'],
                                        $option['text_limit']
                                    );
                                }
                            } elseif ($option['option_type'] == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_DROPDOWN) {
                                if (!Tools::getValue('wk_option_dropdown_' . $id)) {
                                    $errors['error'][] = sprintf(
                                        $moduleInstance->l('Select value for option : %s', 'WkProductCustomerOptions'),
                                        $option['display_name']
                                    );
                                }
                            } elseif ($option['option_type'] == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_CHECKBOX) {
                                if ($option['options_value_arr']) {
                                    $selectedOptions = [];
                                    foreach ($option['options_value_arr'] as $opt) {
                                        if (Tools::getValue('wk_option_' . $id . '_' . $opt['id_wk_product_options_value'])) {
                                            $selectedOptions[] = $opt['option_value'];
                                        }
                                    }
                                }
                                if (empty($selectedOptions)) {
                                    $errors['error'][] = sprintf(
                                        $moduleInstance->l('Select value for option : %s', 'WkProductCustomerOptions'),
                                        $option['display_name']
                                    );
                                }
                            } elseif ($option['option_type'] == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_RADIO) {
                                if (!Tools::getValue('wk_option_radio_' . $id)) {
                                    $errors['error'][] = sprintf(
                                        $moduleInstance->l('Select value for option : %s', 'WkProductCustomerOptions'),
                                        $option['display_name']
                                    );
                                }
                            } elseif ($option['option_type'] == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_DATE) {
                                if (!Tools::getValue('wk_option_date_' . $id)) {
                                    $errors['error'][] = sprintf(
                                        $moduleInstance->l('Select value for option : %s', 'WkProductCustomerOptions'),
                                        $option['display_name']
                                    );
                                } else {
                                    if ((trim(Tools::getValue('wk_option_date_' . $id)) != '') && !Validate::isDateFormat(Tools::getValue('wk_option_date_' . $id))) {
                                        $errors['error'][] = sprintf(
                                            $moduleInstance->l('The value for option : %s is not valid.', 'WkProductCustomerOptions'),
                                            $option['display_name']
                                        );
                                    }
                                }
                            } elseif ($option['option_type'] == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_TIME) {
                                if (!Tools::getValue('wk_option_time_' . $id)) {
                                    $errors['error'][] = sprintf(
                                        $moduleInstance->l('Select value for option : %s', 'WkProductCustomerOptions'),
                                        $option['display_name']
                                    );
                                } else {
                                    if ((trim(Tools::getValue('wk_option_time_' . $id)) != '')
                                    && !preg_match(
                                        '/^(?:2[0-4]|[01][1-9]|10):([0-5][0-9]):([0-5][0-9])$/',
                                        Tools::getValue('wk_option_time_' . $id)
                                    )) {
                                        $errors['error'][] = sprintf(
                                            $moduleInstance->l('The value for option : %s is not valid.', 'WkProductCustomerOptions'),
                                            $option['display_name']
                                        );
                                    }
                                }
                            } elseif ($option['option_type'] == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_DATETIME) {
                                if (!Tools::getValue('wk_option_datetime_' . $id)) {
                                    $errors['error'][] = sprintf(
                                        $moduleInstance->l('Select value for option : %s', 'WkProductCustomerOptions'),
                                        $option['display_name']
                                    );
                                } else {
                                    if ((trim(Tools::getValue('wk_option_datetime_' . $id)) != '') && !Validate::isDateFormat(Tools::getValue('wk_option_datetime_' . $id))) {
                                        $errors['error'][] = sprintf(
                                            $moduleInstance->l('The value for option : %s is not valid.', 'WkProductCustomerOptions'),
                                            $option['display_name']
                                        );
                                    }
                                }
                            } elseif ($option['option_type'] == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_FILE) {
                                if (!Tools::getValue('wk_option_file_contain_' . $id)) {
                                    $errors['error'][] = sprintf(
                                        $moduleInstance->l('Upload valid image for option: %s', 'WkProductCustomerOptions'),
                                        $option['display_name']
                                    );
                                } elseif (Tools::getValue('wk_option_file_contain_' . $id) == 2) {
                                    $errors['error'][] = sprintf(
                                        $moduleInstance->l('Image size exceeded from allowed upload limit %s MB for option: %s', 'WkProductCustomerOptions'),
                                        (int) Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE'),
                                        $option['display_name']
                                    );
                                } elseif (Tools::getValue('wk_option_file_contain_' . $id) == 3) {
                                    $errors['error'][] = sprintf(
                                        $moduleInstance->l('Please upload valid image for option: %s', 'WkProductCustomerOptions'),
                                        $option['display_name']
                                    );
                                }
                            }
                        }
                    }
                }
            }
            $errors['selected'] = $customerSelectedOption;
        }

        return $errors;
    }

    /**
     * Delete Product option data from cart
     *
     * @param int $idProduct
     * @param int $idProductAttribute
     * @param int $idCustomization
     * @param int $idCart
     *
     * @return bool
     */
    public function deleteProductOptionsFromCart(
        $idProduct,
        $idProductAttribute,
        $idCustomization,
        $idCart
    ) {
        return Db::getInstance()->execute(
            'DELETE FROM `' . _DB_PREFIX_ . 'wk_product_customer_options`
            WHERE `id_product` = ' . (int) $idProduct .
                ' AND `id_product_attribute` = ' . (int) $idProductAttribute .
                ' AND `id_customization` = ' . (int) $idCustomization .
                ' AND `id_cart` = ' . (int) $idCart . ' AND `id_order` = 0 AND `in_cart` = 1'
        );
    }

    /**
     * Check whether order contains product options or not
     *
     * @param int $where | id_order or id_cart
     *
     * @return bool
     */
    public function checkOrderContainsOption($where, $afterOrder = true)
    {
        if ($afterOrder) {
            $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'wk_product_customer_options`
                WHERE `id_order` = ' . (int) $where . ' AND `in_cart` = 0';
        } else {
            $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'wk_product_customer_options`
                WHERE `id_cart` = ' . (int) $where . ' AND `in_cart` = 1 AND `id_order` = 0';
        }
        $result = Db::getInstance()->executeS($sql);
        if (!empty($result)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Delete product option data by id_product
     *
     * @param int $where
     * @param bool $isCombination
     *
     * @return bool
     */
    public function deleteProductOptionsFromCartByIdProduct($where, $isCombination = false)
    {
        $sql = 'DELETE FROM `' . _DB_PREFIX_ . 'wk_product_customer_options`
            WHERE `id_product` = ' . (int) $where . ' AND `id_order` = 0 AND `in_cart` = 1
            AND `id_shop` = ' . (int) Context::getContext()->shop->id;
        if ($isCombination) {
            $sql = 'DELETE FROM `' . _DB_PREFIX_ . 'wk_product_customer_options`
            WHERE `id_product_attribute` = ' . (int) $where . ' AND `id_order` = 0 AND `in_cart` = 1
            AND `id_shop` = ' . (int) Context::getContext()->shop->id;
        }

        return Db::getInstance()->execute($sql);
    }

    /**
     * Get Product info from core ps tables related to id_order_details
     *
     * @param int $idOrder
     * @param int $idOrderDetail
     *
     * @return array
     */
    public function getInfoFromIdOrderDetail($idOrder, $idOrderDetail)
    {
        return Db::getInstance()->getRow('SELECT * FROM
         `' . _DB_PREFIX_ . 'order_detail` WHERE `id_order_detail` = ' . (int) $idOrderDetail . '
          AND `id_order` = ' . (int) $idOrder);
    }

    /**
     * Check whether order contains product options or not
     *
     * @param int $idCart
     * @param int $idProduct
     * @param int $idProductAttribute
     * @param int $idCustomization
     *
     * @return bool
     */
    public static function checkOrderContainsOptionBeforeOrder(
        $idCart,
        $idProduct,
        $idProductAttribute,
        $idCustomization
    ) {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'wk_product_customer_options`
            WHERE `id_cart` = ' . (int) $idCart . ' AND `in_cart` = 1 AND `id_order` = 0
            AND `id_product` = ' . (int) $idProduct . ' AND `id_product_attribute` = ' . (int) $idProductAttribute .
            ' AND `id_customization`=' . (int) $idCustomization;
        $result = Db::getInstance()->executeS($sql);
        if (!empty($result)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check whether product has option or not in cart
     *
     * @param int $idCart
     * @param int $idProduct
     * @param int $idProductAttribute
     * @param int $idCustomization
     *
     * @return bool
     */
    public static function checkOrderContainsOptionEntry(
        $idCart,
        $idProduct,
        $idProductAttribute,
        $idCustomization
    ) {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'wk_product_customer_options`
            WHERE `id_cart` = ' . (int) $idCart . ' AND `id_product` = ' . (int) $idProduct .
            ' AND `id_product_attribute` = ' . (int) $idProductAttribute .
            ' AND `id_customization`=' . (int) $idCustomization;
        $result = Db::getInstance()->executeS($sql);
        if (!empty($result)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check saved option by id customization
     *
     * @param int $idCart
     * @param int $idProduct
     * @param int $idProductAttribute
     * @param int $idCustomization
     *
     * @return array|bool
     */
    public static function checkSavedOptionByIdCustomizaton(
        $idCart,
        $idProduct,
        $idProductAttribute,
        $idCustomization
    ) {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'wk_product_customer_options`
            WHERE `id_cart` = ' . (int) $idCart . ' AND `id_product` = ' . (int) $idProduct .
            ' AND `id_product_attribute` = ' . (int) $idProductAttribute .
            ' AND `id_customization`=' . (int) $idCustomization;
        $result = Db::getInstance()->executeS($sql);
        if (!empty($result)) {
            return $result;
        } else {
            return false;
        }
    }

    /**
     * Get price imapct
     *
     * @param float $price
     * @param int $idCart
     * @param int $idProduct
     * @param int $isProductAttribute
     * @param int $idCustomization
     *
     * @return float
     */
    public static function getPriceImpact($price, $idCart, $idProduct, $isProductAttribute, $idCustomization)
    {
        $context = Context::getContext();

        $hasCustomOptions = self::checkSavedOptionByIdCustomizaton(
            $idCart,
            $idProduct,
            $isProductAttribute,
            $idCustomization
        );
        if ($hasCustomOptions && $idCustomization) {
            $objOptionValue = new WkProductOptionsValue();
            $totalPriceImpact = 0;
            $taxRate = Tax::getProductTaxRate($idProduct);
            foreach ($hasCustomOptions as $option) {
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
                if (
                    $objOption->option_type == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_DROPDOWN
                    || $objOption->option_type == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_CHECKBOX
                    || $objOption->option_type == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_RADIO
                ) {
                    if (!empty($selectedValues) && is_array($selectedValues)) {
                        $selectedValues = array_unique($selectedValues);
                        $selectedValuesFormattedWithTe = $objOptionValue->getAllDisplayOptionsByValues(
                            $objOption->id,
                            $selectedValues,
                            $context->language->id,
                            $context->shop->id,
                            $idProduct,
                            true
                        );
                        if (!empty($selectedValuesFormattedWithTe)) {
                            foreach ($selectedValuesFormattedWithTe as $optValue) {
                                $priceImpactInternal = 0;
                                if ($optValue['price_type'] == 1) {
                                    $priceImpactInternal = $price * ($optValue['price_impact'] / 100);
                                } else {
                                    if ($optValue['tax_type'] == 1) {
                                        $priceImpactInternal = ($optValue['price_impact'] * 100) / (100 + $taxRate);
                                    } else {
                                        $priceImpactInternal = $optValue['price_impact'];
                                    }
                                }
                                $priceImpact = $priceImpact + $priceImpactInternal;
                            }
                        }
                    }
                } else {
                    if ($option['price_type'] == 1) {
                        $priceImpact = $price * ($option['price_impact'] / 100);
                    } else {
                        if ($option['tax_type'] == 1) {
                            $priceImpact = ($option['price_impact'] * 100) / (100 + $taxRate);
                        } else {
                            $priceImpact = $option['price_impact'];
                        }
                    }
                }
                $priceImpact = Tools::ps_round($priceImpact, 6);
                $totalPriceImpact = $totalPriceImpact + $priceImpact;
            }
            $price = $totalPriceImpact + $price;
        }

        return $price;
    }

    /**
     * Delete customization data from core table
     *
     * @param int $idCustomization
     *
     * @return bool
     */
    public function deleteCustomiedDataFormCoreTable($idCustomization)
    {
        $sql = 'DELETE FROM `' . _DB_PREFIX_ . 'customized_data`
        WHERE `id_customization` = ' . (int) $idCustomization;

        return Db::getInstance()->execute($sql);
    }
}
