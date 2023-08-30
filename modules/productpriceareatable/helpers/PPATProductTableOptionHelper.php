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

class PPATProductTableOptionHelper
{

    /**
     * Get table option name
     * @param $id_option
     * @param $id_lang
     */
    public static function getTableName($id_option, $id_lang)
    {
		$sql = new DbQuery();
		$sql->select('*');
		$sql->from('ppat_product_table_options_lang');
		$sql->where('id_option = '.(int)$id_option);
        $sql->where('id_lang = ' . (int)$id_lang);
		$row = DB::getInstance()->getRow($sql);
		return $row['option_text'];
    }

	/**
	 * Get all options available for a product
	 * @param $id_product
	 * @param $id_lang
	 * @return array|false|mysqli_result|null|PDOStatement|resource
	 */
	public static function getProductTableOptions($id_product, $id_lang = null)
	{
        $ps_lang_default = Configuration::get('PS_LANG_DEFAULT');

        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('ppat_product_table_options');
        $sql->where('id_product = ' . (int)$id_product);
        $sql->orderBy('position');
		$result = DB::getInstance()->executeS($sql);

		if (empty($result)) {
		    return array();
        }

		if ($id_lang == null) {
		    return $result;
        }

        foreach ($result as &$row) {
            $sql = new DbQuery();
            $sql->select('*');
            $sql->from('ppat_product_table_options_lang');
            $sql->where('id_option = ' . (int)$row['id_option']);
            $sql->where('id_lang = ' . (int)$id_lang);
            $row_lang = DB::getInstance()->getRow($sql);

            if (empty($row_lang['option_text'])) {
                $sql = new DbQuery();
                $sql->select('*');
                $sql->from('ppat_product_table_options_lang');
                $sql->where('id_option = ' . (int)$row_lang['id_option']);
                $sql->where('id_lang = ' . (int)$ps_lang_default);
                $row_lang = DB::getInstance()->getRow($sql);
            }

            $row['option_text'] = $row_lang['option_text'];
            $row['id_lang'] = $row_lang['id_lang'];
        }
        return $result;
    }

    /**
     * Delete all options associated with a product
     * @param $id_product
     */
    public static function deleteByProduct($id_product, $id_shop)
    {
        $ppat_product_table_option_model = new PPATProductTableOptionModel();
        $collection = $ppat_product_table_option_model->loadByProduct($id_product, $id_shop);
        $collection_options = array();

        foreach ($collection as $item) {
            $collection_options[] = $item->id_option;
        }

        DB::getInstance()->delete(PPATProductTableOptionModel::$definition['table'], 'id_product=' . (int)$id_product);

        if (!empty($collection_options)) {
            DB::getInstance()->delete(PPATProductTableOptionsLangModel::$definition['table'], 'id_option IN (' . implode(',', $collection_options) . ')');
        }
    }
}
