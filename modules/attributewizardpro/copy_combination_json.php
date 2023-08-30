<?php
/**
 * 2008 - 2017 Presto-Changeo
 *
 * MODULE Attribute Wizard Pro
 *
 * @version   2.0.0
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

include(dirname(__FILE__) . '/../../config/config.inc.php');
include(dirname(__FILE__) . '/attributewizardpro.php');

$awp = new AttributeWizardPro();
if ($awp->awp_random != Tools::getValue('awp_random')) {
    print 'No Permissions';
    exit;
}

$return = array();

function array2object($array)
{
    if (is_array($array)) {
        $obj = new StdClass();
        foreach ($array as $key => $val) {
            $obj->$key = $val;
        }
    } else {
        $obj = $array;
    }
    return $obj;
}

function copyProductAttributes($id_src, $id_tgt)
{
    $ps_version = (float) (Tools::substr(_PS_VERSION_, 0, 3));
    $psv3 = (int) str_replace(".", "", Tools::substr(_PS_VERSION_, 0, 5) . (Tools::substr(_PS_VERSION_, 5, 1) != '.' ? Tools::substr(_PS_VERSION_, 5, 1) : ''));

    $awp_shops = Tools::getValue('awp_shops');

    $shopsAvailable = explode(',', $awp_shops);
    $shopsAvailables = array();
    foreach ($shopsAvailable as $shops) {
        if (isset($shops['id_shop'])) {
            $shopsAvailables[] = $shops['id_shop'];
        } else {
            $shopsAvailables[] = 1;
        }
    }
    Db::getInstance()->Execute('DELETE FROM ' . _DB_PREFIX_ . 'stock_available
            WHERE id_product = ' . (int) ($id_tgt) . ' and
                  id_product_attribute NOT IN (SELECT id_product_attribute 
                                                FROM ' . _DB_PREFIX_ . 'product_attribute '
        . '                                     WHERE id_product = ' . (int) ($id_tgt) . ' ) ');
    foreach ($shopsAvailables as $shop) {
        $pa = Db::getInstance()->ExecuteS('
            SELECT * FROM `' . _DB_PREFIX_ . 'product_attribute` pa
            LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_shop` pas ON pas.id_product_attribute = pa.id_product_attribute
            WHERE pas.`id_shop` = ' . (int) ($shop) . ' and pa.`id_product` = ' . (int) ($id_src));

        $advStock = Db::getInstance()->ExecuteS('SELECT advanced_stock_management '
            . 'FROM `' . _DB_PREFIX_ . 'product_shop`
            WHERE id_product = ' . (int) ($id_src) . ' and id_shop = ' . (int) ($shop));

        Db::getInstance()->Execute('UPDATE `' . _DB_PREFIX_ . 'product_shop`
            SET advanced_stock_management = ' . (int) ($advStock[0]['advanced_stock_management']) . '
            WHERE id_product = ' . (int) ($id_tgt) . ' and '
            . 'id_shop = ' . (int) ($shop));
        $defaultStockAvailable = Db::getInstance()->ExecuteS('SELECT * FROM `' . _DB_PREFIX_ . 'stock_available`
               WHERE id_product = ' . (int) ($id_src) . ' and id_shop = ' . (int) ($shop) . ' and id_product_attribute = 0');
        if (count($defaultStockAvailable) > 0) {
            $query = 'INSERT INTO `' . _DB_PREFIX_ . 'stock_available` 
                        (`id_product`, 
                        `id_product_attribute`, 
                        `id_shop`,
                        `id_shop_group`, 
                        `quantity`,
                        `depends_on_stock`, 
                        `out_of_stock`)
                    VALUES (' . (int) $id_tgt . ', '
                . '0 ,'
                . (int) $shop . ','
                . (int) $defaultStockAvailable[0]['id_shop_group'] . ', '
                . (int) $defaultStockAvailable[0]['quantity'] . ', '
                . (int) ($defaultStockAvailable[0]['depends_on_stock']) . ','
                . (int) ($defaultStockAvailable[0]['out_of_stock']) . ')';
            Db::getInstance()->Execute($query);
        }
        foreach ($pa as $srow) {
            $query = 'INSERT INTO `' . _DB_PREFIX_ . 'product_attribute`
                ('
                . 'id_product,'
                . 'reference,'
                . 'supplier_reference,'
                . 'location,'
                . 'ean13,'
                . 'wholesale_price,'
                . 'price,'
                . 'ecotax,'
                . 'quantity,'
                . 'weight,'
                . 'default_on,'
                . 'upc,'
                . 'unit_price_impact,'
                . 'minimal_quantity, '
                . 'available_date)
                VALUES
                ("'
                . (int) $id_tgt . '","'
                . pSQL($srow['reference']) . '","'
                . pSQL($srow['supplier_reference']) . '","'
                . pSQL($srow['location']) . '","'
                . pSQL($srow['ean13']) . '","'
                . pSQL($srow['wholesale_price']) . '","'
                . (float) $srow['price'] . '","'
                . pSQL($srow['ecotax']) . '","'
                . (int) $srow['quantity'] . '","'
                . pSQL($srow['weight']) . '",'
                . ($srow['default_on'] ? '"' . (int) $srow['default_on'] . '"' : 'NULL') . '' . ',"'
                . pSQL($srow['upc']) . '","'
                . pSQL($srow['unit_price_impact']) . '","'
                . (int) $srow['minimal_quantity'] . '",  "'
                . pSQL($srow['available_date']) . '"' . ')';

            Db::getInstance()->Execute($query);
            $id_pa = Db::getInstance()->Insert_ID();
            $pac = Db::getInstance()->ExecuteS('
                            SELECT * FROM `' . _DB_PREFIX_ . 'product_attribute_combination`
                            WHERE `id_product_attribute` = ' . (int) $srow['id_product_attribute']);

            foreach ($pac as $pacrow) {
                Db::getInstance()->Execute('INSERT INTO `' . _DB_PREFIX_ . 'product_attribute_combination` (`id_product_attribute`, `id_attribute`) VALUES (' . (int) $id_pa . ',' . (int) $pacrow['id_attribute'] . ')');
            }

            $exists = Db::getInstance()->ExecuteS('SELECT * FROM  `' . _DB_PREFIX_ . 'product_attribute_shop` WHERE `id_product_attribute` = ' . (int) $id_pa . ' and  `id_shop` = ' . (int) $shop . '');
            if (count($exists) > 0) {
                Db::getInstance()->Execute('DELETE FROM  `' . _DB_PREFIX_ . 'product_attribute_shop` WHERE `id_product_attribute` = ' . (int) $id_pa . ' and  `id_shop` = ' . (int) $shop);
            }

            Db::getInstance()->Execute('
            INSERT INTO `' . _DB_PREFIX_ . 'product_attribute_shop` (
                `id_product`,
                `id_product_attribute`,
                `id_shop`,
                `wholesale_price`,
                `price`,
                `ecotax`, 
                `weight`,
                `unit_price_impact`, 
                `default_on`,
                `minimal_quantity`,
                `available_date`)
            VALUES (' .
                (int) $id_tgt . ',' .
                (int) $id_pa . ',' .
                (int) $shop . ',
                "' . pSQL($srow['wholesale_price']) . '", '
                . '"' . (float) $srow['price'] . '",
                "' . pSQL($srow['ecotax']) . '",'
                . '"' . pSQL($srow['weight']) . '",
                "' . pSQL($srow['unit_price_impact']) . '",'
                . '' . ($srow['default_on'] ? '"' . (int) $srow['default_on'] . '"' : 'NULL' ) . ',
                "' . (int) $srow['minimal_quantity'] . '",  '
                . '"' . pSQL($srow['available_date']) . '"  )');

            $query = 'SELECT * '
                . 'FROM  `' . _DB_PREFIX_ . 'stock_available` '
                . 'WHERE   `id_product` = ' . (int) $id_src . ' and  '
                . '`id_product_attribute` = ' . (int) $srow['id_product_attribute']
                . ' and  `id_shop` = ' . (int) $shop;

            $stockAvailable = Db::getInstance()->ExecuteS($query);
            $org_shop = $shop;
            if (is_array($stockAvailable) && count($stockAvailable) == 0) {
                $shop = 0;
                $query = 'SELECT * '
                    . 'FROM  `' . _DB_PREFIX_ . 'stock_available` '
                    . 'WHERE   `id_product` = ' . (int) $id_src . ' and  '
                    . '`id_product_attribute` = ' . (int) $srow['id_product_attribute'] . ' and  '
                    . '`id_shop` = ' . (int) $shop;
                $stockAvailable = Db::getInstance()->ExecuteS($query);
            }
            Db::getInstance()->Execute('DELETE '
                . 'FROM  `' . _DB_PREFIX_ . 'stock_available` '
                . 'WHERE  `id_product` = ' . (int) $id_tgt . ' and '
                . '`id_product_attribute` = ' . (int) ($id_pa) . ' and '
                . '`id_shop` = ' . (int) $shop);

            foreach ($stockAvailable as $stock) {
                $query = '
                INSERT INTO `' . _DB_PREFIX_ . 'stock_available` 
                    (`id_product`, 
                    `id_product_attribute`, 
                    `id_shop`,
                    `id_shop_group`, 
                    `quantity`,
                    `depends_on_stock`, 
                    `out_of_stock`)
                VALUES ('
                    . (int) $id_tgt . ', '
                    . (int) $id_pa . ','
                    . (int) $shop . ',
                    "' . (int) $stock['id_shop_group'] . '", "'
                    . (int) $stock['quantity'] . '",
                    "' . pSQL($stock['depends_on_stock']) . '","'
                    . pSQL($stock['out_of_stock']) . '")';
                Db::getInstance()->Execute($query);
            }
            $stockAB = Db::getInstance()->ExecuteS('SELECT * '
                . 'FROM  `' . _DB_PREFIX_ . 'stock`
                WHERE   `id_product` = ' . (int) $id_src . ' and  '
                . '`id_product_attribute` = ' . (int) $srow['id_product_attribute'] . ' ');

            Db::getInstance()->Execute('DELETE FROM  `' . _DB_PREFIX_ . 'stock` WHERE  `id_product` = ' . (int) $id_tgt . ' and `id_product_attribute` = ' . (int) ($id_pa));

            foreach ($stockAB as $stockRow) {
                Db::getInstance()->Execute('INSERT INTO `' . _DB_PREFIX_ . 'stock` 
                    (`id_warehouse`, 
                    `id_product`, 
                    `id_product_attribute`, 
                    `reference`,
                    `ean13`, 
                    `upc`,
                    `physical_quantity`, 
                    `usable_quantity` , 
                    `price_te`)
                    VALUES ("' .
                    (int) $stockRow['id_warehouse'] . '", ' .
                    (int) $id_tgt . ', ' .
                    (int) $id_pa . ',"' .
                    pSQL($stockRow['reference']) . '", "' .
                    pSQL($stockRow['ean13']) . '", "' .
                    pSQL($stockRow['upc']) . '","' .
                    pSQL($stockRow['physical_quantity']) . '","' .
                    pSQL($stockRow['usable_quantity']) . '","' .
                    pSQL($stockRow['price_te']) . '")');
            }

            $warehouse = Db::getInstance()->ExecuteS('SELECT * '
                . 'FROM  `' . _DB_PREFIX_ . 'warehouse_product_location`
                WHERE   `id_product` = ' . (int) $id_src . ' and  '
                . '`id_product_attribute` = ' . (int) $srow['id_product_attribute'] . ' ');

            Db::getInstance()->Execute('DELETE '
                . 'FROM  `' . _DB_PREFIX_ . 'warehouse_product_location` '
                . 'WHERE  `id_product` = ' . (int) $id_tgt . ' and '
                . '`id_product_attribute` = ' . (int) ($id_pa));

            foreach ($warehouse as $warehouseRow) {
                Db::getInstance()->Execute('INSERT INTO `' . _DB_PREFIX_ . 'warehouse_product_location` 
                    (`id_product`,
                    `id_product_attribute`, 
                    `id_warehouse`,
                    `location`)
                    VALUES ( ' .
                    (int) $id_tgt . ', ' .
                    (int) $id_pa . ',"' .
                    (int) $warehouseRow['id_warehouse'] . '","' .
                    pSQL($warehouseRow['location']) . '")');
            }
            $shop = $org_shop;
        }
    }
    $advStock = Db::getInstance()->ExecuteS('SELECT * FROM `' . _DB_PREFIX_ . 'product_shop`
                                            WHERE id_product = ' . (int) ($id_tgt));



    Db::getInstance()->Execute('INSERT IGNORE INTO `' . _DB_PREFIX_ . 'attribute_impact`
                            (id_product,id_attribute,weight,price)
                                SELECT "' . (int) ($id_tgt) . '" as id_product,id_attribute,weight,price
                                FROM `' . _DB_PREFIX_ . 'attribute_impact` as ai
                                WHERE ai.id_product = ' . (int) ($id_src));
}
if (Tools::getValue('action') == 'validate') {
    $invalid_src = 0;
    $invalid_tgt = 0;
    $p = 0;
    $src = new Product((int) (Tools::getValue('id_product_src')));
    if (Tools::getValue('type') == 'p') {
        $tgt = new Product((int) (Tools::getValue('id_product_tgt')));
    } else if (Tools::getValue('type') == 'm') {
        $tgt = new Manufacturer((int) (Tools::getValue('id_product_tgt')));
    } else if (Tools::getValue('type') == 's') {
        $tgt = new Supplier((int) (Tools::getValue('id_product_tgt')));
    } else {
        $tgt = new Category((int) (Tools::getValue('id_product_tgt')));
        if ($tgt->name) {
            $query = 'SELECT CONCAT(COUNT(id_product_attribute), SUM(price), SUM(weight), SUM(quantity)) AS concat FROM `' . _DB_PREFIX_ . 'product_attribute` WHERE id_product  = ' . (int) (Tools::getValue('id_product_src'));
            $src_hash = Db::getInstance()->getRow($query);
            $query = 'SELECT id_product FROM `' . _DB_PREFIX_ . 'category_product` WHERE id_category = ' . (int) Tools::getValue('id_product_tgt');
            $products = Db::getInstance()->executeS($query);
            foreach ($products as $product) {
                if ((int) (Tools::getValue('id_product_src')) == $product['id_product']) {
                    continue;
                }
                $query = 'SELECT CONCAT(COUNT(id_product_attribute), SUM(price), SUM(weight), SUM(quantity)) AS concat FROM `' . _DB_PREFIX_ . 'product_attribute` WHERE id_product  = ' . (int) ($product['id_product']);
                $tgt_hash = Db::getInstance()->getRow($query);
                if ($src_hash['concat'] != $tgt_hash['concat']) {
                    $p++;
                }
            }
        }
    }
    if (!$src->name) {
        $invalid_src = 1;
    }
    if (!$tgt->name) {
        $invalid_tgt = 1;
    }
    if ($invalid_src == 1 || $invalid_tgt == 1) {
        $return = array('invalid_src' => $invalid_src, 'invalid_tgt' => $invalid_tgt);
    } else {
        $return = array('product_src' => $src->name, 'product_tgt' => $tgt->name, 'copy_products' => $p);
    }
}

if (Tools::getValue('action') == 'copy') {
    $awp_shops = Tools::getValue('awp_shops');
    $awp_shopsList = explode(',', $awp_shops);
    $awp_shopsList = array_map('intval', $awp_shopsList);


    $query = 'SELECT CONCAT(COUNT(pa.id_product_attribute), GROUP_CONCAT(pa.default_on), '
        . 'SUM(pa.price), SUM(pa.weight)' . (Configuration::get('PS_STOCK_MANAGEMENT') ? ',  '
            . 'SUM(stock.quantity)' : '') . ') AS concat
        FROM `' . _DB_PREFIX_ . 'product_attribute` pa
        ' . 'INNER JOIN ' . _DB_PREFIX_ . 'product_attribute_shop product_attribute_shop ON product_attribute_shop.id_product_attribute = pa.id_product_attribute AND product_attribute_shop.id_shop IN(' . pSQL(implode(',', $awp_shopsList)) . ') 
        ' . (Configuration::get('PS_STOCK_MANAGEMENT') ? 'INNER JOIN ' . _DB_PREFIX_ . 'stock_available stock ON stock.id_product_attribute = pa.id_product_attribute AND  stock.id_product =' . (int) (Tools::getValue('id_product_src')) . ' ' : '') . '
        WHERE pa.id_product  = ' . (int) (Tools::getValue('id_product_src'));

    $src_hash = Db::getInstance()->getRow($query);

    if (Tools::getValue('type') == 'p') {
        $query = 'SELECT CONCAT(COUNT(pa.id_product_attribute), GROUP_CONCAT(pa.default_on), SUM(pa.price), SUM(pa.weight)' . (Configuration::get('PS_STOCK_MANAGEMENT') ? ',  ' . 'SUM(stock.quantity)' : '') . ') AS concat
                    FROM `' . _DB_PREFIX_ . 'product_attribute` pa
                    ' . 'INNER JOIN ' . _DB_PREFIX_ . 'product_attribute_shop product_attribute_shop
                        ON product_attribute_shop.id_product_attribute = pa.id_product_attribute
                        AND product_attribute_shop.id_shop IN(' . pSQL($awp_shops) . ')
                    ' . (Configuration::get('PS_STOCK_MANAGEMENT') ? 'INNER JOIN ' . _DB_PREFIX_ . 'stock_available stock
                        ON stock.id_product_attribute = pa.id_product_attribute
                        AND  stock.id_product =' . (int) (Tools::getValue('id_product_tgt')) . ' ' : '') . '
                    WHERE pa.id_product  = ' . (int) (Tools::getValue('id_product_tgt'));

        $tgt_hash = Db::getInstance()->getRow($query);

        if ($src_hash['concat'] != $tgt_hash['concat']) {
            $product_tgt = new Product((int) (Tools::getValue('id_product_tgt')));


            $shopsAvailable = explode(',', $awp_shops);
            foreach ($shopsAvailable as $shops) {
                $product_tgt = new Product((int) (Tools::getValue('id_product_tgt')), false, Context::getContext()->language->id, $shops);
                $result = true;
                $combinations = new Collection('Combination');
                $combinations->where('id_product', '=', (int) (Tools::getValue('id_product_tgt')));

                foreach ($combinations as $combination) {
                    $comb = new Combination($combination->id, null, $shops);
                    $comb->id_shop_list = array($shops);
                    $comb->delete();
                }
                SpecificPriceRule::applyAllRules(array((int) (Tools::getValue('id_product_tgt'))));
            }


            copyProductAttributes((int) (Tools::getValue('id_product_src')), (int) (Tools::getValue('id_product_tgt')));
        }
    } else if (Tools::getValue('type') == 'c') {
        $query = 'SELECT id_product FROM `' . _DB_PREFIX_ . 'category_product` WHERE id_category = ' . (int) Tools::getValue('id_product_tgt');
        $products = Db::getInstance()->executeS($query);
        foreach ($products as $product) {
            if ((int) (Tools::getValue('id_product_src')) == $product['id_product']) {
                continue;
            }
            $query = 'SELECT CONCAT(COUNT(pa.id_product_attribute), SUM(pa.price), SUM(pa.weight)' . (Configuration::get('PS_STOCK_MANAGEMENT') ? ',  ' . 'SUM(stock.quantity)' : '') . ') AS concat
                    FROM `' . _DB_PREFIX_ . 'product_attribute` pa
                    ' . 'INNER JOIN ' . _DB_PREFIX_ . 'product_attribute_shop product_attribute_shop
                        ON product_attribute_shop.id_product_attribute = pa.id_product_attribute
                        AND product_attribute_shop.id_shop IN(' . $awp_shops . ')' . '
                    ' . (Configuration::get('PS_STOCK_MANAGEMENT') ? 'INNER JOIN ' . _DB_PREFIX_ . 'stock_available stock
                        ON stock.id_product_attribute = pa.id_product_attribute
                        AND  stock.id_product =' . (int) ($product['id_product']) . ' ' : '') . '
                    WHERE pa.id_product  = ' . (int) ($product['id_product']);

            $tgt_hash = Db::getInstance()->getRow($query);

            if ($src_hash['concat'] != $tgt_hash['concat']) {
                $product_tgt = new Product((int) $product['id_product']);

                $shopsAvailable = explode(',', $awp_shops);
                foreach ($shopsAvailable as $shops) {
                    $product_tgt = new Product((int) ($product['id_product']), false, Context::getContext()->language->id, $shops);

                    $result = true;
                    $combinations = new Collection('Combination');
                    $combinations->where('id_product', '=', (int) ($product['id_product']));
                    foreach ($combinations as $combination) {
                        $comb = new Combination($combination->id, null, $shops);
                        $comb->id_shop_list = array($shops);
                        $comb->delete();
                    }
                    SpecificPriceRule::applyAllRules(array((int) ($product['id_product'])));
                }


                copyProductAttributes((int) (Tools::getValue('id_product_src')), (int) $product['id_product']);
            }
        }
    } else if (Tools::getValue('type') == 'm') {
        $query = 'SELECT id_product FROM `' . _DB_PREFIX_ . 'product` WHERE id_manufacturer = ' . (int) Tools::getValue('id_product_tgt');
        $products = Db::getInstance()->executeS($query);
        foreach ($products as $product) {
            if ((int) (Tools::getValue('id_product_src')) == $product['id_product']) {
                continue;
            }
            $query = 'SELECT CONCAT(COUNT(pa.id_product_attribute), SUM(pa.price), SUM(pa.weight)' . (Configuration::get('PS_STOCK_MANAGEMENT') ? ',  ' . 'SUM(stock.quantity)' : '') . ') AS concat
                    FROM `' . _DB_PREFIX_ . 'product_attribute` pa
                    ' . 'INNER JOIN ' . _DB_PREFIX_ . 'product_attribute_shop product_attribute_shop
                        ON product_attribute_shop.id_product_attribute = pa.id_product_attribute
                        AND product_attribute_shop.id_shop IN(' . pSQL($awp_shops) . ')' . '
                    ' . (Configuration::get('PS_STOCK_MANAGEMENT') ? 'INNER JOIN ' . _DB_PREFIX_ . 'stock_available stock
                        ON stock.id_product_attribute = pa.id_product_attribute
                        AND  stock.id_product =' . (int) ($product['id_product']) . ' ' : '') . '
                    WHERE pa.id_product  = ' . (int) ($product['id_product']);

            $tgt_hash = Db::getInstance()->getRow($query);

            if ($src_hash['concat'] != $tgt_hash['concat']) {
                $product_tgt = new Product($product['id_product']);

                $shopsAvailable = explode(',', $awp_shops);
                foreach ($shopsAvailable as $shops) {
                    $product_tgt = new Product((int) ($product['id_product']), false, Context::getContext()->language->id, $shops);
                    $result = true;
                    $combinations = new Collection('Combination');
                    $combinations->where('id_product', '=', (int) ($product['id_product']));
                    foreach ($combinations as $combination) {
                        $comb = new Combination($combination->id, null, $shops);
                        $comb->id_shop_list = array($shops);
                        $comb->delete();
                    }
                    SpecificPriceRule::applyAllRules(array((int) ($product['id_product'])));
                }


                copyProductAttributes((int) (Tools::getValue('id_product_src')), $product['id_product']);
            }
        }
    } else if (Tools::getValue('type') == 's') {
        $query = 'SELECT id_product FROM `' . _DB_PREFIX_ . 'product` WHERE id_supplier = ' . (int) Tools::getValue('id_product_tgt');

        $query = 'SELECT p.id_product FROM `' . _DB_PREFIX_ . 'product`  p
                        left join `' . _DB_PREFIX_ . 'product_supplier` ps on ps.id_product = p.id_product
                        WHERE ps.id_product_attribute = 0 AND ps.id_supplier = ' . (int) Tools::getValue('id_product_tgt');

        $products = Db::getInstance()->executeS($query);
        foreach ($products as $product) {
            if ((int) (Tools::getValue('id_product_src')) == $product['id_product']) {
                continue;
            }
            $query = 'SELECT CONCAT(COUNT(pa.id_product_attribute), SUM(pa.price), SUM(pa.weight)' . (Configuration::get('PS_STOCK_MANAGEMENT') ? ',  ' . 'SUM(stock.quantity)' : '') . ') AS concat
                    FROM `' . _DB_PREFIX_ . 'product_attribute` pa
                    ' . 'INNER JOIN ' . _DB_PREFIX_ . 'product_attribute_shop product_attribute_shop
                        ON product_attribute_shop.id_product_attribute = pa.id_product_attribute
                        AND product_attribute_shop.id_shop IN(' . pSQL($awp_shops) . ')' . '
                    ' . (Configuration::get('PS_STOCK_MANAGEMENT') ? 'INNER JOIN ' . _DB_PREFIX_ . 'stock_available stock
                        ON stock.id_product_attribute = pa.id_product_attribute
                        AND  stock.id_product =' . (int) ($product['id_product']) . ' ' : '') . '
                    WHERE pa.id_product  = ' . (int) ($product['id_product']);

            $tgt_hash = Db::getInstance()->getRow($query);

            if ($src_hash['concat'] != $tgt_hash['concat']) {
                $product_tgt = new Product((int) $product['id_product']);

                $shopsAvailable = explode(',', $awp_shops);
                foreach ($shopsAvailable as $shops) {
                    $product_tgt = new Product((int) ($product['id_product']), false, Context::getContext()->language->id, $shops);
                    $result = true;
                    $combinations = new Collection('Combination');
                    $combinations->where('id_product', '=', (int) ($product['id_product']));
                    foreach ($combinations as $combination) {
                        $comb = new Combination($combination->id, null, $shops);
                        $comb->id_shop_list = array($shops);
                        $comb->delete();
                    }
                    SpecificPriceRule::applyAllRules(array((int) ($product['id_product'])));
                }


                copyProductAttributes((int) (Tools::getValue('id_product_src')), (int) $product['id_product']);
            }
        }
    }
    $return = array('complete' => '1', 'err' => Db::getInstance()->getMsgError());
}

@ob_end_clean();
print Tools::jsonEncode($return);
