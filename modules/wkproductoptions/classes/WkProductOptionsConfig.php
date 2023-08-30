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
class WkProductOptionsConfig extends ObjectModel
{
    public $option_type;
    public $price;
    public $price_type;
    public $tax_type;
    public $active;
    public $is_bulk_enabled;
    public $user_input;
    public $multiselect;
    public $text_limit;
    public $pre_selected;
    public $is_required;
    public $position;
    public $date_add;
    public $date_upd;

    // Multilang properties
    public $name;
    public $description;
    public $option_value;
    public $display_name;
    public $placeholder;

    // Option type constant
    const WK_PRODUCT_OPTIONS_TEXT = 1;
    const WK_PRODUCT_OPTIONS_DROPDOWN = 2;
    const WK_PRODUCT_OPTIONS_CHECKBOX = 3;
    const WK_PRODUCT_OPTIONS_RADIO = 4;
    const WK_PRODUCT_OPTIONS_FILE = 5;
    const WK_PRODUCT_OPTIONS_TEXTAREA = 6;
    const WK_PRODUCT_OPTIONS_DATE = 7;
    const WK_PRODUCT_OPTIONS_TIME = 8;
    const WK_PRODUCT_OPTIONS_DATETIME = 9;

    public static $definition = [
        'table' => 'wk_product_options_config',
        'primary' => 'id_wk_product_options_config',
        'multilang' => true,
        'multilang_shop' => true,
        'fields' => [
            'option_type' => ['type' => self::TYPE_INT, 'required' => true, 'shop' => true],
            'price' => ['type' => self::TYPE_FLOAT, 'required' => true, 'shop' => true],
            'price_type' => ['type' => self::TYPE_INT, 'required' => true, 'shop' => true],
            'tax_type' => ['type' => self::TYPE_INT, 'required' => true, 'shop' => true],
            'pre_selected' => ['type' => self::TYPE_INT, 'required' => true, 'shop' => true],
            'is_required' => ['type' => self::TYPE_INT, 'required' => true, 'shop' => true],
            'active' => ['type' => self::TYPE_INT, 'required' => true, 'shop' => true],
            'is_bulk_enabled' => ['type' => self::TYPE_INT, 'required' => true, 'shop' => true],
            'user_input' => ['type' => self::TYPE_INT, 'required' => true, 'shop' => true],
            'text_limit' => ['type' => self::TYPE_INT, 'shop' => true],
            'multiselect' => ['type' => self::TYPE_INT, 'required' => true, 'shop' => true],
            'position' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'shop' => true],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'shop' => true],
            'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'shop' => true],
            // multilang
            'name' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
                'lang' => true,
            ],
            'display_name' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
                'lang' => true,
            ],
            'description' => [
                'type' => self::TYPE_HTML,
                'lang' => true,
                'validate' => 'isCleanHtml',
            ],
            'placeholder' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
                'lang' => true,
            ],
            'option_value' => [
                'type' => self::TYPE_HTML,
                'lang' => true,
            ],
        ],
    ];

    public function __construct($id = null, $idLang = null, $idShop = null)
    {
        Shop::addTableAssociation('wk_product_options_config', ['type' => 'shop']);
        parent::__construct($id, $idLang, $idShop);
    }

    /**
     * se product with name
     *
     * @param int $idLang
     * @param array $search {keyword for search}
     * @param array $exclude {product to be exclude}
     *
     * @return array
     */
    public function searchProduct($idLang, $search, $idShop, $exclude = [])
    {
        $sql = 'SELECT p.`id_product`, pl.`name`
        FROM `' . _DB_PREFIX_ . 'product` p
        LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (pl.`id_product` = p.`id_product`)';
        $sql .= ' WHERE pl.`id_lang` =' . (int) $idLang . ' AND pl.`id_shop` = ' . (int) $idShop;
        if (!empty($exclude)) {
            $sql .= ' AND pl.`id_product` NOT IN (' . pSQL(implode(',', $exclude)) . ')';
        }
        if ($search) {
            $sql .= ' AND pl.`name` LIKE \'%' . pSQL($search) . '%\'';
        }
        $sql .= ' group by p.`id_product`';
        $result = Db::getInstance()->executeS($sql);

        return $result;
    }

    /**
     * Get All Lang Information By IdOption
     *
     * @param int $idOption
     * @param int $idShop
     *
     * @return array
     */
    public function getLanguageOptionInfo($idOption, $idShop)
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'wk_product_options_config_lang`';
        $sql .= ' WHERE `id_shop` = ' . (int) $idShop . '
        AND `id_wk_product_options_config` = ' . (int) $idOption;

        return Db::getInstance()->executeS($sql);
    }

    /**
     * Function to return all available option types
     *
     * @return array
     */
    public function getOptionTypes()
    {
        $moduleInstance = new WkProductOptions();
        $options = [
            'txt' => [
                'id_option' => self::WK_PRODUCT_OPTIONS_TEXT,
                'name' => $moduleInstance->l('Text', 'WkProductOptionsConfig'),
            ],
            'txtarea' => [
                'id_option' => self::WK_PRODUCT_OPTIONS_TEXTAREA,
                'name' => $moduleInstance->l('Textarea', 'WkProductOptionsConfig'),
            ],
            'dropdown' => [
                'id_option' => self::WK_PRODUCT_OPTIONS_DROPDOWN,
                'name' => $moduleInstance->l('Dropdown', 'WkProductOptionsConfig'),
            ],
            'checkbox' => [
                'id_option' => self::WK_PRODUCT_OPTIONS_CHECKBOX,
                'name' => $moduleInstance->l('Checkbox', 'WkProductOptionsConfig'),
            ],
            'radio' => [
                'id_option' => self::WK_PRODUCT_OPTIONS_RADIO,
                'name' => $moduleInstance->l('Radio', 'WkProductOptionsConfig'),
            ],
            'image_file' => [
                'id_option' => self::WK_PRODUCT_OPTIONS_FILE,
                'name' => $moduleInstance->l('Image file', 'WkProductOptionsConfig'),
            ],
            'date' => [
                'id_option' => self::WK_PRODUCT_OPTIONS_DATE,
                'name' => $moduleInstance->l('Date', 'WkProductOptionsConfig'),
            ],
            'time' => [
                'id_option' => self::WK_PRODUCT_OPTIONS_TIME,
                'name' => $moduleInstance->l('Time', 'WkProductOptionsConfig'),
            ],
            'datetime' => [
                'id_option' => self::WK_PRODUCT_OPTIONS_DATETIME,
                'name' => $moduleInstance->l('Datetime', 'WkProductOptionsConfig'),
            ],
        ];

        return $options;
    }

    /**
     * Get Option name by option id
     *
     * @param int $id
     *
     * @return string
     */
    public static function getOptionNameById($id)
    {
        $moduleInstance = new WkProductOptions();
        $name = '';
        switch ($id) {
            case self::WK_PRODUCT_OPTIONS_TEXT:
                $name = $moduleInstance->l('Text', 'WkProductOptionsConfig');
                break;
            case self::WK_PRODUCT_OPTIONS_TEXTAREA:
                $name = $moduleInstance->l('Textarea', 'WkProductOptionsConfig');
                break;
            case self::WK_PRODUCT_OPTIONS_DROPDOWN:
                $name = $moduleInstance->l('Dropdown', 'WkProductOptionsConfig');
                break;
            case self::WK_PRODUCT_OPTIONS_CHECKBOX:
                $name = $moduleInstance->l('Checkbox', 'WkProductOptionsConfig');
                break;
            case self::WK_PRODUCT_OPTIONS_RADIO:
                $name = $moduleInstance->l('Radio', 'WkProductOptionsConfig');
                break;
            case self::WK_PRODUCT_OPTIONS_FILE:
                $name = $moduleInstance->l('Image file', 'WkProductOptionsConfig');
                break;
            case self::WK_PRODUCT_OPTIONS_DATE:
                $name = $moduleInstance->l('Date', 'WkProductOptionsConfig');
                break;
            case self::WK_PRODUCT_OPTIONS_TIME:
                $name = $moduleInstance->l('Time', 'WkProductOptionsConfig');
                break;
            case self::WK_PRODUCT_OPTIONS_DATETIME:
                $name = $moduleInstance->l('Datetime', 'WkProductOptionsConfig');
                break;
        }

        return $name;
    }

    /**
     * Insert entries in option condition table
     *
     * @param int $idOption
     * @param int $idCurrency
     * @param int $idCountry
     * @param string $idGroup json_encoded idGroup ids
     * @param int $idCustomer
     * @param string $products json_encoded products ids
     * @param string $categories json_encoded categories ids
     * @param int $from
     * @param int $to
     * @param int $idShop
     *
     * @return bool
     */
    public function addEntriesInConditionTable(
        $idOption,
        $idCurrency,
        $idCountry,
        $idGroup,
        $idCustomer,
        $products,
        $categories,
        $from,
        $to,
        $idShop
    ) {
        $data = [
            'id_wk_product_options_config' => (int) $idOption,
            'id_currency' => (int) $idCurrency,
            'id_country' => (int) $idCountry,
            'id_group' => pSQL($idGroup),
            'id_customer' => (int) $idCustomer,
            'products' => pSQL($products),
            'categories' => pSQL($categories),
            'from' => pSQL($from),
            'to' => pSQL($to),
            'id_shop' => (int) $idShop,
        ];

        return Db::getInstance()->insert('wk_product_options_conditions', $data);
    }

    /**
     * delete entreis from option condition tabke
     *
     * @param int $idOption
     *
     * @return bool
     */
    public function deleteDataFromConditionTable($idOption)
    {
        return Db::getInstance()->delete(
            'wk_product_options_conditions',
            'id_wk_product_options_config = ' . (int) $idOption
        );
    }

    /**
     * Get condition information By id option
     *
     * @param int $idOption
     * @param int $idShop
     *
     * @return array
     */
    public function getConditionInformation($idOption, $idShop)
    {
        return Db::getInstance()->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'wk_product_options_conditions`
        WHERE `id_wk_product_options_config` = ' . (int) $idOption . ' AND `id_shop` = ' . (int) $idShop);
    }

    /**
     * Get information by field key
     *
     * @param int $idOption
     * @param int $idShop
     * @param string $key
     *
     * @return int
     */
    public function getKeyInfo($idOption, $idShop, $key = 'id_wk_product_options_config')
    {
        $key = 'wk_product_options_config_shop.`' . pSQL($key) . '`';
        $sql = 'SELECT ' . pSQL($key) . ' FROM `' . _DB_PREFIX_ . 'wk_product_options_config` wpoc';
        $sql .= Shop::addSqlAssociation('wk_product_options_config', 'wpoc');
        $sql .= ' WHERE wk_product_options_config_shop.`id_shop` = ' . (int) $idShop . '
        AND wk_product_options_config_shop.`id_wk_product_options_config` = ' . (int) $idOption;

        return Db::getInstance()->getValue($sql);
    }

    /**
     * Get All Options
     *
     * @param int $idShop
     * @param int $idLang
     *
     * @return array
     */
    public function getAllOptions($idShop, $idLang, $onlyActive = true)
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'wk_product_options_config` wpoc';
        $sql .= Shop::addSqlAssociation('wk_product_options_config', 'wpoc');
        $sql .= 'LEFT JOIN `' . _DB_PREFIX_ . 'wk_product_options_config_lang` wpocl
        on (wk_product_options_config_shop.`id_wk_product_options_config` = wpocl.`id_wk_product_options_config`)';
        $sql .= ' WHERE wk_product_options_config_shop.`id_shop` = ' . (int) $idShop . '
         AND wpocl.`id_lang` = ' . (int) $idLang;
        if ($onlyActive) {
            $sql .= ' AND wk_product_options_config_shop.`active` = 1';
        }
        $sql .= ' ORDER BY wk_product_options_config_shop.`position` ASC';

        return Db::getInstance()->executeS($sql);
    }

    /**
     * Update Position
     *
     * @param int $way
     * @param int $position
     *
     * @return bool
     */
    public function updatePosition($way, $position)
    {
        if (!$res = Db::getInstance()->executeS(
            'SELECT wkpo.`id_wk_product_options_config`, wkpo.`position`
            FROM `' . _DB_PREFIX_ . 'wk_product_options_config` wkpo
            WHERE wkpo.`id_wk_product_options_config` = ' . (int) $this->id . ' ORDER BY `position` ASC'
        )) {
            return false;
        }

        $movedOption = false;
        foreach ($res as $option) {
            if ((int) $option['id_wk_product_options_config'] == (int) $this->id) {
                $movedOption = $option;
            }
        }

        if ($movedOption === false) {
            return false;
        }

        return Db::getInstance()->execute(
            'UPDATE `' . _DB_PREFIX_ . 'wk_product_options_config` SET `position`= `position` ' . ($way ? '- 1' : '+ 1') .
                ' WHERE `position`' . ($way ? '> ' .
                    (int) $movedOption['position'] . ' AND `position` <= ' . (int) $position : '< '
                    . (int) $movedOption['position'] . ' AND `position` >= ' . (int) $position)
        ) && Db::getInstance()->execute(
            'UPDATE `' . _DB_PREFIX_ . 'wk_product_options_config`
            SET `position` = ' . (int) $position . '
            WHERE `id_wk_product_options_config`=' . (int) $movedOption['id_wk_product_options_config']
        );
    }

    /**
     * Update Position for shop
     *
     * @param int $way
     * @param int $position
     *
     * @return bool
     */
    public function updatePositionShop($way, $position, $idShop, $idOptions)
    {
        if (!$res = Db::getInstance()->executeS(
            'SELECT wkpo.`id_wk_product_options_config`, wkpo.`position`
            FROM `' . _DB_PREFIX_ . 'wk_product_options_config_shop` wkpo
            WHERE wkpo.`id_wk_product_options_config` = ' . (int) $this->id . ' AND wkpo.`id_shop` = ' . (int) $idShop . ' ORDER BY `position` ASC'
        )) {
            return false;
        }

        $movedOption = false;
        foreach ($res as $option) {
            if ((int) $option['id_wk_product_options_config'] == (int) $this->id) {
                $movedOption = $option;
            }
        }

        if ($movedOption === false) {
            return false;
        }

        return Db::getInstance()->execute(
            'UPDATE `' . _DB_PREFIX_ . 'wk_product_options_config_shop` SET `position`= `position` ' . ($way ? '- 1' : '+ 1') .
                ' WHERE `id_shop` =' . (int) $idShop . ' AND `position`' . ($way ? '> ' .
                    (int) $movedOption['position'] . ' AND `position` <= ' . (int) $position : '< '
                    . (int) $movedOption['position'] . ' AND `position` >= ' . (int) $position)
        ) && Db::getInstance()->execute(
            'UPDATE `' . _DB_PREFIX_ . 'wk_product_options_config_shop`
            SET `position` = ' . (int) $position . '
            WHERE `id_wk_product_options_config`=' . (int) $movedOption['id_wk_product_options_config'] . ' AND `id_shop`= ' . (int) $idShop
        );
    }

    /**
     * Get heigher position
     *
     * @return int
     */
    public static function getHigherPosition()
    {
        $position = Db::getInstance()->getValue('SELECT MAX(`position`)
        FROM `' . _DB_PREFIX_ . 'wk_product_options_config`');
        $result = (is_numeric($position)) ? $position : -1;

        return $result + 1;
    }

    /**
     * Get heigher position for shop
     *
     * @return int
     */
    public static function getHigherPositionShop()
    {
        $position = Db::getInstance()->getValue('SELECT MAX(`position`)
        FROM `' . _DB_PREFIX_ . 'wk_product_options_config_shop` WHERE `id_shop` = ' . (int) Context::getContext()->shop->id);
        $result = (is_numeric($position)) ? $position : -1;

        return $result + 1;
    }

    /**
     * Reorder status position
     * Call it after deleting a status.
     *
     * @return bool $return
     */
    public static function cleanPositions()
    {
        Db::getInstance()->execute('SET @i = -1', false);
        $sql = 'UPDATE `' . _DB_PREFIX_ . 'wk_product_options_config` SET `position` = @i:=@i+1 ORDER BY `position` ASC';

        return (bool) Db::getInstance()->execute($sql);
    }

    /**
     * Reorder status position for shop
     * Call it after deleting a status.
     *
     * @return bool $return
     */
    public static function cleanPositionsShop()
    {
        Db::getInstance()->execute('SET @i = -1', false);
        $sql = 'UPDATE `' . _DB_PREFIX_ . 'wk_product_options_config_shop` SET `position` = @i:=@i+1
        WHERE `id_shop` = ' . (int) Context::getContext()->shop->id . ' ORDER BY `position` ASC';

        return (bool) Db::getInstance()->execute($sql);
    }

    /**
     * Add/Update Product Wise option settings
     *
     * @param int $idOption
     * @param int $active
     * @param int $idProduct
     * @param int $idProductAttribute
     * @param int $idShop
     *
     * @return bool
     */
    public function addUpdateProductWiseOption($idOption, $active, $idProduct, $idProductAttribute, $idShop)
    {
        if (empty($this->checkProductWiseEntryExists($idOption, $idProduct, $idProductAttribute, $idShop))) {
            $data = [
                'id_wk_product_options_config' => (int) $idOption,
                'active' => (int) $active,
                'id_product' => (int) $idProduct,
                'id_product_attribute' => (int) $idProductAttribute,
                'id_shop' => (int) $idShop,
            ];

            return Db::getInstance()->insert(
                'wk_product_wise_options',
                $data
            );
        } else {
            $data = [
                'active' => (int) $active,
            ];

            return Db::getInstance()->update(
                'wk_product_wise_options',
                $data,
                'id_product =' . (int) $idProduct . ' AND id_product_attribute =' . (int) $idProductAttribute .
                    ' AND id_shop = ' . (int) $idShop . ' AND `id_wk_product_options_config` = ' . (int) $idOption
            );
        }
    }

    /**
     * Check product wise entry exists or not
     *
     * @param int $idOption
     * @param int $idProduct
     * @param int $idProductAttribute
     * @param int $idShop
     *
     * @return array
     */
    public function checkProductWiseEntryExists($idOption, $idProduct, $idProductAttribute, $idShop)
    {
        return Db::getInstance()->getRow(
            'SELECT * FROM `' . _DB_PREFIX_ . 'wk_product_wise_options`
            WHERE `id_wk_product_options_config` = ' . (int) $idOption .
                ' AND `id_product` = ' . (int) $idProduct . ' AND `id_shop` = ' . (int) $idShop . ' AND `id_product_attribute` = ' . (int) $idProductAttribute
        );
    }

    /**
     * Get all active options in combinations
     *
     * @param int $idProduct
     * @param int $idShop
     *
     * @return array
     */
    public function getActiveOptionsByCombination($idProduct, $idProductAttribute, $idShop)
    {
        $results = Db::getInstance()->executeS(
            'SELECT `id_wk_product_options_config` FROM `' . _DB_PREFIX_ . 'wk_product_wise_options`
            WHERE `active`= 1 AND `id_product` = ' . (int) $idProduct . ' AND `id_product_attribute` = ' . (int) $idProductAttribute . ' AND `id_shop` = ' . (int) $idShop
        );

        if (!empty($results) && is_array($results)) {
            return array_column($results, 'id_wk_product_options_config');
        }

        return [];
    }

    /**
     * Get Product wise options
     *
     * @param int $idProduct
     * @param int $idShop
     *
     * @return array
     */
    public function getProductWiseOptions($idProduct, $idShop)
    {
        return Db::getInstance()->executeS(
            'SELECT * FROM `' . _DB_PREFIX_ . 'wk_product_wise_options`
            WHERE `id_product` = ' . (int) $idProduct . ' AND `id_shop` = ' . (int) $idShop
        );
    }

    /**
     * Delete Product wise option
     *
     * @param int $idProduct
     *
     * @return bool
     */
    public function deleteProductWiseOptions($idProduct)
    {
        return Db::getInstance()->execute(
            'DELETE FROM `' . _DB_PREFIX_ . 'wk_product_wise_options`
            WHERE `id_product` = ' . (int) $idProduct
        );
    }

    /**
     * re-initialize positions on option delete
     *
     * @return bool
     */
    public function delete()
    {
        $return = parent::delete();
        /* Reinitializing position */
        if (Shop::isFeatureActive()) {
            $this->cleanPositionsShop();
        } else {
            $this->cleanPositions();
            $this->cleanPositionsShop();
        }
        $this->deleteDataFromConditionTable($this->id);
        $objOptionValue = new WkProductOptionsValue();
        $objOptionValue->deleteDataFromOptionTable($this->id);

        return $return;
    }

    /**
     * Get All Available option for product
     * This function is only front end
     *
     * @param int $idProduct
     * @param int $idProductAttribute
     *
     * @return array
     */
    public function getAvailableOptionByIdProduct($idProduct, $idProductAttribute)
    {
        $objOptionValue = new WkProductOptionsValue();
        $optionArray = [];
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'wk_product_options_config` wpoc';
        $sql .= Shop::addSqlAssociation('wk_product_options_config', 'wpoc');
        $sql .= 'LEFT JOIN `' . _DB_PREFIX_ . 'wk_product_options_config_lang` wpocl
        on (wk_product_options_config_shop.`id_wk_product_options_config` = wpocl.`id_wk_product_options_config`)';
        $sql .= 'LEFT JOIN `' . _DB_PREFIX_ . 'wk_product_options_conditions` wpfc
        on (wpoc.`id_wk_product_options_config` = wpfc.`id_wk_product_options_config`)';
        $sql .= ' WHERE wk_product_options_config_shop.`id_shop` = ' . (int) Context::getContext()->shop->id . '
        AND wk_product_options_config_shop.`active` = 1 AND wpocl.`id_lang` = ' .
            (int) Context::getContext()->language->id . ' ORDER BY wk_product_options_config_shop.`position` ASC';
        $allOptions = Db::getInstance()->executeS($sql);
        foreach ($allOptions as &$option) {
            if ($option['products']) {
                if (in_array($idProduct, json_decode($option['products']))) {
                    continue;
                }
            }
            if ($option['id_group']) {
                if (isset(Context::getContext()->customer->id)) {
                    $customerGroups = Customer::getGroupsStatic((int) Context::getContext()->customer->id);
                    if (empty(array_intersect($customerGroups, json_decode($option['id_group'])))) {
                        continue;
                    }
                } else {
                    if (!in_array(
                        Configuration::get('PS_UNIDENTIFIED_GROUP'),
                        json_decode($option['id_group'])
                    )) {
                        continue;
                    }
                }
            }
            if ($option['id_customer']) {
                if (isset(Context::getContext()->customer->id)) {
                    if ($option['id_customer'] != Context::getContext()->customer->id) {
                        continue;
                    }
                } else {
                    continue;
                }
            }
            if ($option['id_country']) {
                if (
                    isset(Context::getContext()->country->id)
                    && Context::getContext()->country->id != $option['id_country']
                ) {
                    continue;
                }
            }
            if ($option['id_currency']) {
                if (
                    isset(Context::getContext()->currency->id)
                    && Context::getContext()->currency->id != $option['id_currency']
                ) {
                    continue;
                }
            }
            if (($option['from'] != '0000-00-00 00:00:00')
                && ($option['to'] != '0000-00-00 00:00:00')
            ) {
                $currentTime = date('Y-m-d H:i:s');
                if (
                    strtotime($currentTime) < strtotime($option['from'])
                    || strtotime($currentTime) > strtotime($option['to'])
                ) {
                    continue;
                }
            }
            if ($option['categories']) {
                $productCategories = Product::getProductCategories($idProduct);
                if (empty(array_intersect($productCategories, json_decode($option['categories'])))) {
                    continue;
                }
            }
            $optionEntry = $this->checkProductWiseEntryExists(
                $option['id_wk_product_options_config'],
                $idProduct,
                $idProductAttribute,
                Context::getContext()->shop->id
            );
            if (!is_array($optionEntry) || (is_array($optionEntry) && !$optionEntry['active'])) {
                continue;
            }
            if (
                $option['option_type'] == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_DROPDOWN
                || $option['option_type'] == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_CHECKBOX
                || $option['option_type'] == WkProductOptionsConfig::WK_PRODUCT_OPTIONS_RADIO
            ) {
                $allOptionsValues = $objOptionValue->getAllDisplayOptions(
                    $option['id_wk_product_options_config'],
                    $option['id_lang'],
                    $option['id_shop'],
                    $idProduct
                );
                $option['options_value_arr'] = $allOptionsValues;
            } else {
                $option['options_value_arr'] = '';
            }
            $priceDisplay = Product::getTaxCalculationMethod(
                (int) Context::getContext()->cookie->id_customer
            );
            if (!$priceDisplay || $priceDisplay == 2) {
                $tax = true;
            } else {
                $tax = false;
            }
            if ($option['price_type'] == 1) {
                $objProduct = new Product($idProduct);
                $productPrice = $objProduct->getPrice($tax);
                $productPrice = Tools::convertPriceFull(
                    $productPrice,
                    Context::getContext()->currency,
                    new Currency((int) Configuration::get('PS_CURRENCY_DEFAULT'))
                );
                $priceImpact = $productPrice * $option['price'] / 100;
            } else {
                if ($option['tax_type'] == 1) {
                    $priceImpactTaxIncl = $option['price'];
                    $taxRate = Tax::getProductTaxRate($idProduct);
                    $priceImpactTaxExcl = $option['price'] / (1 + ($taxRate / 100));
                } else {
                    $priceImpactTaxExcl = $option['price'];
                    $taxRate = Tax::getProductTaxRate($idProduct);
                    $priceImpactTaxIncl = $option['price'] * (1 + ($taxRate / 100));
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
            $option['price_impact'] = $priceImpact;
            $option['price_impact_formated'] = Tools::displayPrice(
                $priceImpact,
                Context::getContext()->currency
            );
            $optionArray[] = $option;
        }

        return $optionArray;
    }

    /**
     * Get Product wise options
     *
     * @param int $idProduct
     * @param int $idShop
     *
     * @return array
     */
    public function getProductWiseOptionsActive($idProduct, $idShop)
    {
        return Db::getInstance()->executeS(
            'SELECT * FROM `' . _DB_PREFIX_ . 'wk_product_wise_options`
            WHERE `active` = 1 AND `id_product` = ' . (int) $idProduct . ' AND `id_shop` = ' . (int) $idShop
        );
    }

    /**
     * Get customizaion
     *
     * @param int $idProduct
     *
     * @return array
     */
    public function getCustomizations($idProduct)
    {
        return Db::getInstance()->executeS(
            'SELECT * FROM `' . _DB_PREFIX_ . 'customization_field`
            WHERE `id_product`=' . (int) $idProduct . ' AND `is_deleted` = 0'
        );
    }

    /**
     * Get customization lang data
     *
     * @param int $idCustomization
     *
     * @return int
     */
    public function getCustomizationsLangData($idCustomization)
    {
        return Db::getInstance()->getValue(
            'SELECT `name` FROM `' . _DB_PREFIX_ . 'customization_field_lang`
            WHERE `name`=\'Product options\' AND `id_customization_field` = ' .
                (int) $idCustomization
        );
    }

    /**
     * Update customization field to optional field
     *
     * @param int $idProduct
     *
     * @return bool
     */
    public function updateCustomizationFieldToOptional($idProduct)
    {
        return Db::getInstance()->execute(
            'UPDATE `' . _DB_PREFIX_ . 'customization_field` SET required=0
            WHERE `id_product`=' . (int) $idProduct
        );
    }

    /**
     * Make option product as customizable product to add multiple options in single cart
     *
     * @param int $idPsProduct
     *
     * @return void
     */
    public function insertIntoPsProduct($idPsProduct)
    {
        if ($idPsProduct) {
            $objProductConfiguration = new WkProductWiseConfiguration();
            $productConfig = $objProductConfiguration->getProductWiseConfiguration(
                $idPsProduct,
                Context::getContext()->shop->id
            );
            if (!empty($productConfig) && $productConfig['is_native_customization']) {
                $objProduct = new Product($idPsProduct);
                if ($objProduct->customizable == 0) {
                    $objProduct->uploadable_files = 0;
                    $objProduct->text_fields = 1;
                    $customizationField = new CustomizationField();
                    $customizationField->id_product = $idPsProduct;
                    $customizationField->type = 1;
                    $customizationField->required = 0;
                    foreach (Language::getLanguages() as $language) {
                        $customizationField->name[$language['id_lang']] = 'Product options'; // don't translate its hidden
                    }
                    $customizationField->save();
                    Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'product`
                        SET `customizable` = 1 WHERE `id_product` = ' . (int) $idPsProduct);
                    Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'product_shop`
                        SET `customizable` = 1 WHERE `id_product` = ' . (int) $idPsProduct);
                }
            }
        }
    }

    /**
     * Check whether option product contains required products or not
     *
     * @param int $idProduct
     *
     * @return bool
     */
    public function isRequiredOptionExits($idProduct)
    {
        $isExist = false;
        $objProductConfiguration = new WkProductWiseConfiguration();
        $productConfig = $objProductConfiguration->getProductWiseConfiguration(
            $idProduct,
            Context::getContext()->shop->id
        );
        if (!empty($productConfig) && $productConfig['is_native_customization']) {
            $idProductAttribute = (int) Product::getDefaultAttribute($idProduct); // only for listing
            $options = $this->getAvailableOptionByIdProduct($idProduct, $idProductAttribute);
            if (!empty($options)) {
                foreach ($options as $option) {
                    if ($option['is_required']) {
                        $isExist = true;
                        break;
                    }
                }
            }
        }

        return $isExist;
    }

    /**
     * get Combination ids
     *
     * @param array $combs
     *
     * @return array
     */
    public static function getCombinationName($combs)
    {
        $combArray = [];
        $groups = [];
        if (is_array($combs)) {
            foreach ($combs as $comb) {
                $combArray[$comb['id_product_attribute']]['id_product_attribute'] = $comb['id_product_attribute'];
                $combArray[$comb['id_product_attribute']]['attributes'][] = [
                    $comb['group_name'],
                    $comb['attribute_name'],
                    $comb['id_attribute'],
                ];
                $combArray[$comb['id_product_attribute']]['quantity'] = $comb['quantity'];
                if ($comb['is_color_group']) {
                    $groups[$comb['id_attribute_group']] = $comb['group_name'];
                }
            }
            foreach ($combArray as $idProductAttribute => $productAttribute) {
                $list = '';
                /* In order to keep the same attributes order */
                asort($productAttribute['attributes']);
                foreach ($productAttribute['attributes'] as $attribute) {
                    $list .= $attribute[0] . ' - ' . $attribute[1] . ', ';
                }

                $list = rtrim($list, ', ');
                $combArray[$idProductAttribute]['attributes'] = $list;
                $combArray[$idProductAttribute]['name'] = $list;
            }
        }

        return $combArray;
    }

    /**
     * Get Products info
     *
     * @param int $idProduct
     * @param int $idProductAttribute
     * @param int $idLang
     *
     * @return array
     */
    public static function getProductProp($idProduct, $idProductAttribute, $idLang)
    {
        $sql = 'SELECT p.*,  pl.* FROM `' . _DB_PREFIX_ . 'product` p';
        $sql .= ' INNER JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (
            p.`id_product` = pl.`id_product`
            AND pl.`id_lang` = ' . (int) $idLang . ' AND p.`id_product`= ' . (int) $idProduct . ')';

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
        $result['id_product_attribute'] = $idProductAttribute;

        return $result;
    }

    public static function checkNewPSProductPage()
    {
        return Db::getInstance()->getValue(
            'SELECT `state` FROM `' . _DB_PREFIX_ . 'feature_flag`
            WHERE `name`="product_page_v2"'
        );
    }
}
