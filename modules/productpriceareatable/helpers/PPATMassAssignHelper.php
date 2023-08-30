<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 * @author    Musaffar Patel
 * @copyright 2016-2017 Musaffar Patel
 * @license   LICENSE.txt
 */

class PPATMassAssignHelper
{
    /**
     * Delete all module data associated the provided product
     * @param $id_product
     * @param $id_shop
     */
    public static function deleteProduct($id_product, $id_shop)
    {
        PPATProductModel::deleteByProduct($id_product, $id_shop);
        PPATProductPriceTableHelper::deleteByProduct($id_product);
        PPATProductOptionLabelLangModel::deleteByProduct($id_product, $id_shop);
        PPATProductTableOptionHelper::deleteByProduct($id_product, $id_shop);
    }

    /**
     * Duplicate PPAT product settings for another product
     * @param $id_product_old
     * @param $id_product
     * @param $id_shop
     */
    public static function duplicateProduct($id_product_old, $id_product, $id_shop)
    {
        $collection_options = array();
        self::deleteProduct($id_product, $id_shop);

        $ppat_product_model = new PPATProductModel();
        $ppat_product_model->load($id_product_old, $id_shop);
        $ppat_product_new_model = new PPATProductModel();
        $ppat_product_new_model->id_product = (int)$id_product;
        $ppat_product_new_model->id_shop = (int)$ppat_product_model->id_shop;
        $ppat_product_new_model->enabled = (int)$ppat_product_model->enabled;
        $ppat_product_new_model->min_row = (float)$ppat_product_model->min_row;
        $ppat_product_new_model->max_row = (float)$ppat_product_model->max_row;
        $ppat_product_new_model->min_col = (float)$ppat_product_model->min_col;
        $ppat_product_new_model->max_col = (float)$ppat_product_model->max_col;
        $ppat_product_new_model->add();

        // Copy option labels
        $ppat_product_option_label_lang_model = new PPATProductOptionLabelLangModel();
        $collection = $ppat_product_option_label_lang_model->loadByProduct($id_product_old, $id_shop);

        foreach ($collection['text'] as $key=>$value) {
            $ppat_product_option_label_lang_new_model = new PPATProductOptionLabelLangModel();
            $ppat_product_option_label_lang_new_model->id_product = (int)$id_product;
            $ppat_product_option_label_lang_new_model->id_shop = (int)$id_shop;
            $ppat_product_option_label_lang_new_model->id_lang = (int)$key;
            $ppat_product_option_label_lang_new_model->text = pSQL($value);
            $ppat_product_option_label_lang_new_model->add();
        }
        unset($collection);

        //copy the table options associated with the product
        $ppat_product_table_option_model = new PPATProductTableOptionModel();
        $collection = $ppat_product_table_option_model->loadByProduct($id_product_old, $id_shop);

        foreach ($collection as $item) {
            $ppat_product_table_option_new_model = new PPATProductTableOptionModel();
            $ppat_product_table_option_new_model->id_product = (int)$id_product;
            $ppat_product_table_option_new_model->position = (int)$item->position;
            $ppat_product_table_option_new_model->enabled = (int)$item->enabled;
            $ppat_product_table_option_new_model->lookup_rounding_mode = pSQL($item->lookup_rounding_mode);
            $ppat_product_table_option_new_model->default_col = pSQL($item->default_col);
            $ppat_product_table_option_new_model->default_row = pSQL($item->default_row);

            $ppat_product_table_options_lang_model = new PPATProductTableOptionsLangModel();
            $collection_lang = $ppat_product_table_options_lang_model->loadByOption($item->id_option);

            foreach ($collection_lang as $item_lang) {
                $ppat_product_table_option_new_model->option_text[$item_lang->id_lang] = pSQL($item_lang->option_text);
            }
            $ppat_product_table_option_new_model->add();

            $collection_options[] = array(
                'id_option_old' => $item->id_option,
                'id_option_new' => $ppat_product_table_option_new_model->id
            );
        }

        // now duplicate the price table
        foreach ($collection_options as $collection_option) {
            $price_table_rows = PPATProductPriceTableHelper::getPriceTableByOption($collection_option['id_option_old']);

            foreach ($price_table_rows as $price_table_row) {
                $ppat_product_price_table_model = new PPATProductPriceTableModel();
                $ppat_product_price_table_model->row = pSQL($price_table_row['row']);
                $ppat_product_price_table_model->col = pSQL($price_table_row['col']);
                $ppat_product_price_table_model->row_max = pSQL($price_table_row['row_max']);
                $ppat_product_price_table_model->col_max = pSQL($price_table_row['col_max']);
                $ppat_product_price_table_model->price = (float)$price_table_row['price'];
                $ppat_product_price_table_model->id_option = (int)$collection_option['id_option_new'];
                $ppat_product_price_table_model->add();
            }
        }
        unset($collection_options);
        unset($collection);
        unset($collection_lang);
    }
}