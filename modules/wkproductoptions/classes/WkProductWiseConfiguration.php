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
class WkProductWiseConfiguration extends ObjectModel
{
    public $id_wk_product_wise_configuration;
    public $is_native_customization;
    public $id_product;
    public $id_shop;

    public static $definition = [
        'table' => 'wk_product_wise_configuration',
        'primary' => 'id_wk_product_wise_configuration',
        'fields' => [
            'id_product' => ['type' => self::TYPE_INT],
            'is_native_customization' => ['type' => self::TYPE_INT],
            'id_shop' => ['type' => self::TYPE_INT],
        ],
    ];

    /**
     * Get product wise configuration
     *
     * @param int $idProduct
     * @param int $idShop
     *
     * @return array
     */
    public function getProductWiseConfiguration($idProduct, $idShop)
    {
        return Db::getInstance()->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'wk_product_wise_configuration`
        WHERE `id_product` = ' . (int) $idProduct . ' AND `id_shop` = ' . (int) $idShop);
    }
}
