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
class WkProductOptionsValue extends ObjectModel
{
    public $id_wk_product_options_value;
    public $id_option;
    public $option_type;
    public $price;
    public $price_type;
    public $tax_type;

    // multilang properties
    public $option_value;

    public static $definition = [
        'table' => 'wk_product_options_value',
        'primary' => 'id_wk_product_options_value',
        'multilang' => true,
        'multilang_shop' => true,
        'fields' => [
            'id_option' => ['type' => self::TYPE_INT, 'required' => true, 'shop' => true],
            'option_type' => ['type' => self::TYPE_INT, 'required' => true, 'shop' => true],
            'price' => ['type' => self::TYPE_FLOAT, 'required' => true, 'shop' => true],
            'price_type' => ['type' => self::TYPE_INT, 'required' => true, 'shop' => true],
            'tax_type' => ['type' => self::TYPE_INT, 'required' => true, 'shop' => true],
            // multilang
            'option_value' => [
                'type' => self::TYPE_HTML,
                'lang' => true,
            ],
        ],
    ];

    public function __construct($id = null, $idLang = null, $idShop = null)
    {
        Shop::addTableAssociation('wk_product_options_value', ['type' => 'shop']);
        parent::__construct($id, $idLang, $idShop);
    }

    /**
     * Delete entries from option table
     *
     * @param int $idOption
     * @param int $idShop
     *
     * @return bool
     */
    public function deleteDataFromOptionTable($idOption, $idShop = null)
    {
        if (!$idShop) {
            $idShop = (int) Context::getContext()->shop->id;
        }
        $allDisplayValues = $this->getAllDisplayOptionByIdOption($idOption, $idShop);
        if (!empty($allDisplayValues) && is_array($allDisplayValues)) {
            foreach ($allDisplayValues as $value) {
                Db::getInstance()->delete(
                    'wk_product_options_value_lang',
                    'id_wk_product_options_value = ' . (int) $value['id_wk_product_options_value']
                );
                Db::getInstance()->delete(
                    'wk_product_options_value',
                    'id_wk_product_options_value = ' . (int) $value['id_wk_product_options_value']
                );
                Db::getInstance()->delete(
                    'wk_product_options_value_shop',
                    'id_wk_product_options_value = ' . (int) $value['id_wk_product_options_value']
                );
            }
        }

        return true;
    }

    /**
     * Get option value shop information
     *
     * @param int $idOption
     * @param int $idShop
     *
     * @return array
     */
    public function getAllDisplayOptionByIdOption($idOption, $idShop)
    {
        return Db::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'wk_product_options_value_shop`
        WHERE `id_option` = ' . (int) $idOption . ' AND `id_shop` = ' . (int) $idShop);
    }

    /**
     * Get option value lang data
     *
     * @param int $idOptionValue
     * @param int $idLang
     * @param int $idShop
     *
     * @return array
     */
    public function getOptionValuesLangData($idOptionValue, $idLang, $idShop)
    {
        return Db::getInstance()->getRow(
            'SELECT `id_wk_product_options_value`, `option_value`
            FROM `' . _DB_PREFIX_ . 'wk_product_options_value_lang`
            WHERE `id_wk_product_options_value` = ' . (int) $idOptionValue .
            ' AND `id_shop` = ' . (int) $idShop . ' AND `id_lang` = ' . (int) $idLang
        );
    }

    /**
     * Get all options information with lang
     *
     * @param int $idOption
     * @param int $idShop
     *
     * @return array
     */
    public function getAllDisplayOptionByIdOptionWithLang($idOption, $idShop)
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'wk_product_options_value` wpoc';
        $sql .= Shop::addSqlAssociation('wk_product_options_value', 'wpoc');
        $sql .= 'LEFT JOIN `' . _DB_PREFIX_ . 'wk_product_options_value_lang` wpocl
        on (wk_product_options_value_shop.`id_wk_product_options_value` = wpocl.`id_wk_product_options_value`)';
        $sql .= ' WHERE wk_product_options_value_shop.`id_shop` = ' . (int) $idShop . '
         AND wk_product_options_value_shop.`id_option` = ' . (int) $idOption;

        return Db::getInstance()->executeS($sql);
    }

    /**
     * Get all options value associated with shop
     *
     * @param int $idOption
     * @param int $idLang
     * @param int $idShop
     * @param int $idProduct
     *
     * @return array
     */
    public function getAllDisplayOptions($idOption, $idLang, $idShop, $idProduct = false)
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'wk_product_options_value` wpoc';
        $sql .= Shop::addSqlAssociation('wk_product_options_value', 'wpoc');
        $sql .= 'LEFT JOIN `' . _DB_PREFIX_ . 'wk_product_options_value_lang` wpocl
        on (wk_product_options_value_shop.`id_wk_product_options_value` = wpocl.`id_wk_product_options_value`)';
        $sql .= ' WHERE wk_product_options_value_shop.`id_shop` = ' . (int) $idShop . '
         AND wk_product_options_value_shop.`id_option` = ' . (int) $idOption . '  AND wpocl.`id_lang` = ' . (int) $idLang;
        $result = Db::getInstance()->executeS($sql);
        if ($idProduct && !empty($result)) {
            $priceDisplay = Product::getTaxCalculationMethod(
                (int) Context::getContext()->cookie->id_customer
            );
            if (!$priceDisplay || $priceDisplay == 2) {
                $tax = true;
            } else {
                $tax = false;
            }
            $objProduct = new Product($idProduct);
            $productPrice = $objProduct->getPrice($tax);
            foreach ($result as &$res) {
                if ($res['price_type'] == 1) {
                    $productPrice = Tools::convertPriceFull(
                        $productPrice,
                        Context::getContext()->currency,
                        new Currency((int) Configuration::get('PS_CURRENCY_DEFAULT'))
                    );
                    $priceImpact = $productPrice * $res['price'] / 100;
                } else {
                    if ($res['tax_type'] == 1) {
                        $priceImpactTaxIncl = $res['price'];
                        $taxRate = Tax::getProductTaxRate($idProduct);
                        $priceImpactTaxExcl = $res['price'] / (1 + ($taxRate / 100));
                    } else {
                        $priceImpactTaxExcl = $res['price'];
                        $taxRate = Tax::getProductTaxRate($idProduct);
                        $priceImpactTaxIncl = $res['price'] * (1 + ($taxRate / 100));
                    }
                    if ($tax) {
                        $priceImpact = $priceImpactTaxIncl;
                    } else {
                        $priceImpact = $priceImpactTaxExcl;
                    }
                }
                $priceImpact = Tools::convertPriceFull(
                    $priceImpact,
                    new Currency((int) Configuration::get('PS_CURRENCY_DEFAULT')),
                    Context::getContext()->currency
                );
                $res['price_impact'] = $priceImpact;
                $res['price_impact_formated'] = Tools::displayPrice(
                    $priceImpact,
                    Context::getContext()->currency
                );
            }
        }

        return $result;
    }

    /**
     * Get all options values associated with products
     *
     * @param int $idOption
     * @param array $selectedOptions
     * @param int $idLang
     * @param int $idShop
     * @param bool $idProduct
     * @param bool $useTax
     *
     * @return array
     */
    public function getAllDisplayOptionsByIdOptions($idOption, $selectedOptions, $idLang, $idShop, $idProduct = false, $useTax = true)
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'wk_product_options_value` wpoc';
        $sql .= Shop::addSqlAssociation('wk_product_options_value', 'wpoc');
        $sql .= 'LEFT JOIN `' . _DB_PREFIX_ . 'wk_product_options_value_lang` wpocl
        on (wk_product_options_value_shop.`id_wk_product_options_value` = wpocl.`id_wk_product_options_value`)';
        $sql .= ' WHERE wk_product_options_value_shop.`id_shop` = ' . (int) $idShop . '
         AND wk_product_options_value_shop.`id_option` = ' . (int) $idOption . '  AND wpocl.`id_lang` = ' . (int) $idLang;
        $result = Db::getInstance()->executeS($sql);
        if ($idProduct && !empty($result)) {
            $objProduct = new Product($idProduct);
            $productPrice = $objProduct->getPrice($useTax);
            foreach ($result as $key => &$res) {
                if (in_array($res['id_wk_product_options_value'], $selectedOptions)) {
                    if ($res['price_type'] == 1) {
                        $productPrice = Tools::convertPriceFull(
                            $productPrice,
                            Context::getContext()->currency,
                            new Currency((int) Configuration::get('PS_CURRENCY_DEFAULT'))
                        );
                        $priceImpact = $productPrice * $res['price'] / 100;
                    } else {
                        if ($res['tax_type'] == 1) {
                            $priceImpactTaxIncl = $res['price'];
                            $taxRate = Tax::getProductTaxRate($idProduct);
                            $priceImpactTaxExcl = $res['price'] / (1 + ($taxRate / 100));
                        } else {
                            $priceImpactTaxExcl = $res['price'];
                            $taxRate = Tax::getProductTaxRate($idProduct);
                            $priceImpactTaxIncl = $res['price'] * (1 + ($taxRate / 100));
                        }
                        if ($useTax) {
                            $priceImpact = $priceImpactTaxIncl;
                        } else {
                            $priceImpact = $priceImpactTaxExcl;
                        }
                    }
                    $priceImpact = Tools::convertPriceFull(
                        $priceImpact,
                        new Currency((int) Configuration::get('PS_CURRENCY_DEFAULT')),
                        Context::getContext()->currency
                    );
                    $res['price_impact'] = $priceImpact;
                    $res['price_impact_formated'] = Tools::displayPrice(
                        $priceImpact,
                        Context::getContext()->currency
                    );
                } else {
                    unset($result[$key]);
                }
            }

            return $result;
        }

        return false;
    }

    /**
     * Get information with selected option values
     *
     * @param int $idOption
     * @param int $selectedOptions
     * @param int $idLang
     * @param int $idShop
     * @param bool $idProduct
     * @param bool $useTax
     *
     * @return array
     */
    public function getAllDisplayOptionsByValues($idOption, $selectedOptions, $idLang, $idShop, $idProduct = false, $useTax = true)
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'wk_product_options_value` wpoc';
        $sql .= Shop::addSqlAssociation('wk_product_options_value', 'wpoc');
        $sql .= 'LEFT JOIN `' . _DB_PREFIX_ . 'wk_product_options_value_lang` wpocl
        on (wk_product_options_value_shop.`id_wk_product_options_value` = wpocl.`id_wk_product_options_value`)';
        $sql .= ' WHERE wk_product_options_value_shop.`id_shop` = ' . (int) $idShop . '
         AND wk_product_options_value_shop.`id_option` = ' . (int) $idOption . '  AND wpocl.`id_lang` = ' . (int) $idLang;
        $result = Db::getInstance()->executeS($sql);
        if ($idProduct && !empty($result)) {
            $objProduct = new Product($idProduct);
            $productPrice = $objProduct->getPrice($useTax);
            foreach ($result as $key => &$res) {
                if (in_array($res['option_value'], $selectedOptions)) {
                    if ($res['price_type'] == 1) {
                        $productPrice = Tools::convertPriceFull(
                            $productPrice,
                            Context::getContext()->currency,
                            new Currency((int) Configuration::get('PS_CURRENCY_DEFAULT'))
                        );
                        $priceImpact = $productPrice * $res['price'] / 100;
                    } else {
                        if ($res['tax_type'] == 1) {
                            $priceImpactTaxIncl = $res['price'];
                            $taxRate = Tax::getProductTaxRate($idProduct);
                            $priceImpactTaxExcl = $res['price'] / (1 + ($taxRate / 100));
                        } else {
                            $priceImpactTaxExcl = $res['price'];
                            $taxRate = Tax::getProductTaxRate($idProduct);
                            $priceImpactTaxIncl = $res['price'] * (1 + ($taxRate / 100));
                        }
                        if ($useTax) {
                            $priceImpact = $priceImpactTaxIncl;
                        } else {
                            $priceImpact = $priceImpactTaxExcl;
                        }
                    }
                    $priceImpact = Tools::convertPriceFull(
                        $priceImpact,
                        new Currency((int) Configuration::get('PS_CURRENCY_DEFAULT')),
                        Context::getContext()->currency
                    );
                    $res['price_impact'] = $priceImpact;
                    $res['price_impact_formated'] = Tools::displayPrice(
                        $priceImpact,
                        Context::getContext()->currency
                    );
                } else {
                    unset($result[$key]);
                }
            }

            return $result;
        }

        return false;
    }

    /**
     * Save Options Values
     *
     * @param int $idOption
     * @param int $optionType
     *
     * @return void
     */
    public function saveOptionValues($idOption, $optionType)
    {
        $context = Context::getContext();
        $defaultLangId = (int) $context->language->id;
        $maxOptions = Tools::getValue('max_options');
        $maxOptionsArray = explode(',', $maxOptions);
        foreach ($maxOptionsArray as $optionIndex) {
            $objSelf = new self();
            foreach (Language::getLanguages(true) as $language) {
                $optionidLang = $language['id_lang'];
                if (!Tools::getValue('display_value_' . $optionIndex . '_' . $language['id_lang'])) {
                    $optionidLang = $defaultLangId;
                }
                $objSelf->option_value[$language['id_lang']] = trim(Tools::getValue('display_value_' . $optionIndex . '_' . $optionidLang));
            }
            $objSelf->id_option = $idOption;
            $objSelf->option_type = $optionType;
            $objSelf->price = Tools::getValue('option_value_price_' . $optionIndex);
            $objSelf->price_type = Tools::getValue('option_value_price_type_' . $optionIndex);
            $objSelf->tax_type = Tools::getValue('option_value_tax_type_' . $optionIndex);
            $objSelf->save();
            unset($objSelf);
        }
    }
}
