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
 * @copyright 2015-2017 Musaffar Patel
 * @license   LICENSE.txt
 */

if (!defined('_PS_VERSION_'))
	exit;

class PPATProductPriceTableModel extends ObjectModel
{
	/** @var integer Unique ID */
	public $id_price;

	/** @var string Row */
	public $row;

	/** @var string Col */
	public $col;

	/** @var string Row Max */
	public $row_max;

	/** @var string Col Max */
	public $col_max;

	/** @var string Price */
	public $price;

	/** @var integer Option ID */
	public $id_option;

	/**
	 * @see ObjectModel::$definition
	 */

	public static $definition = array(
		'table' => 'ppat_product_price_table',
		'primary' => 'id_price',
		'multilang' => false,
		'fields' => array(
			'row' => array('type' => self::TYPE_STRING),
			'col' => array('type' => self::TYPE_STRING),
			'row_max' => array('type' => self::TYPE_STRING),
			'col_max' => array('type' => self::TYPE_STRING),
			'price' => array('type' => self::TYPE_STRING),
			'id_option' => array('type' => self::TYPE_INT)
		)
	);

	/**
	 * Get all the column headers pertaining to a particular options price table based on all the col values in the table
	 * @param $id_option
	 * @return array
	 * @throws PrestaShopDatabaseException
	 */
	public static function getOptionGridColumns($id_option)
	{
		$grid_cols_collection = array();
		$sql = 'SELECT
					DISTINCT col FROM '._DB_PREFIX_.'ppat_product_price_table
				WHERE id_option = '.(int)$id_option.'
				ORDER BY CAST(`col` AS DECIMAL(10,2)) ASC
				';
		$result = DB::getInstance()->executeS($sql);

		if ($result)
		{
			foreach ($result as $row)
			{
				$grid_col = [];
				$grid_col['align'] = 'right';
				$grid_col['dataType'] = 'float';
				$grid_col['title'] = $row['col'];
				$grid_cols_collection[] = $grid_col;
			}
		}
		return $grid_cols_collection;
	}

	/**
	 * Get the grid data for a particular options price table
	 * @param $id_option
	 * @param bool $add_col_keys
	 * @return array
	 * @throws PrestaShopDatabaseException
	 */
	public static function getOptionGridData($id_option, $add_col_keys = false)
	{
		$grid_data = array();
		$grid_data_row = array();

		$sql = 'SELECT
					* FROM '._DB_PREFIX_.'ppat_product_price_table
				WHERE id_option = '.(int)$id_option.'
				ORDER BY CAST(`row` AS DECIMAL(10,2)), CAST(`col` AS DECIMAL(10,2)) ASC
				';
		$result = DB::getInstance()->executeS($sql);
		$rows = array();

		if ($result) {
			foreach ($result as $row) {
				if ($add_col_keys) {
                    $grid_data[$row['row']][$row['col']] = $row['price'];
                } else {
                    $grid_data[$row['row']][] = $row['price'];
                }
			}

			if (!$add_col_keys) {
                foreach ($grid_data as $key => $row) {
                    $grid_data_row[] = $key;
                    foreach ($row as $key2 => $cell) {
                        if ($add_col_keys) $grid_data_row[$key2] = $cell;
                        else $grid_data_row[] = $cell;
                    }
                    $rows[] = $grid_data_row;
                    unset($grid_data_row);
                }
            }
		}

		// sort rows
        if (!$add_col_keys) {
            if (!empty($rows)) {
                usort($rows, function ($a, $b) {
                    return $a[0] - $b[0];
                });
            }
            return $rows;
        } else {
            return $grid_data;
        }
	}

	/**
	 * Commit the price table
	 * @param $prices_array
	 * @param $id_product
	 */
	public static function saveGrid($prices_array, $id_option)
	{
		if (is_array($prices_array)) {
			DB::getInstance()->delete(
				'ppat_product_price_table',
				'id_option='.(int)$id_option
			);
			foreach ($prices_array as $price_row)
			{
				DB::getInstance()->insert(
					'ppat_product_price_table', array(
						'row' => pSQL($price_row['row']),
						'row_max' => pSQL($price_row['row_max']),
						'col' => pSQL($price_row['col']),
						'col_max' => pSQL($price_row['col_max']),
						'price' => pSQL($price_row['price']),
						'id_option' => (int)$price_row['id_option'],
					)
				);
			}
		}
	}

	/**
	 * Get the lowest price, row and col from the price table by option ID
	 * @param $id_product
	 * @param $id_option
	 * @return bool
	 * @throws PrestaShopDatabaseException
	 */
	public static function getLowestSizeAndPrice($id_product, $id_option)
	{
		$sql = new DbQuery();
		$sql->select('row, col, price');
		$sql->from('ppat_product_price_table');
		$sql->where('id_option = '.(int)$id_option);
		$sql->orderBy('row, col ASC');
		$sql->limit('1');
		$res = Db::getInstance()->executeS($sql);

		if ($res) return $res[0];
		else return false;
	}

	/**
	 * Get the row/col labels for a particular option
	 * @param $name
	 * @param $id_option
	 * @return array|false|mysqli_result|null|PDOStatement|resource
	 * @throws PrestaShopDatabaseException
	 */
	public static function getUnitEntryValues($name, $id_option)
	{
		$sql = 'SELECT
		        DISTINCT(`'.pSQL($name).'`)
				FROM '._DB_PREFIX_.'ppat_product_price_table
				WHERE id_option = '.(int)$id_option.'
				ORDER BY CAST('.pSQL($name).' as DECIMAL(10,5))';
		$results = DB::getInstance()->executeS($sql);
		return $results;
	}

	/*
	 * Fetch price directly corresponding to a specific row and col value
	 */
	public static function getSpecificPrice($id_option, $row, $col)
	{
		$sql = 'SELECT price
				FROM '._DB_PREFIX_.'ppat_product_price_table
				WHERE id_option = '.(int)$id_option.'
				AND `row` = '.(float)$row.'
				AND `col` = '.(float)$col;
		$results = DB::getInstance()->getRow($sql);
		return $results;
	}
	

	/*
	 * lookup price from matrix based on dimension values.  Look up for closes match if necessary
	 * @param $lookup_rounding_mode string
	 */
	public static function getUnitEntryPrice($id_option, $row, $col, $lookup_rounding_mode = 'up')
	{
		if ($lookup_rounding_mode == 'down')
		{
			$sql = 'SELECT
						(SELECT row 
						FROM '._DB_PREFIX_.'ppat_product_price_table
						WHERE '.(float)$row.' >= `row` AND id_option = '.(int)$id_option.' 
						ORDER BY CAST(`row` AS DECIMAL(5,2)) DESC LIMIT 1) AS row,
						(SELECT col 
						FROM '._DB_PREFIX_.'ppat_product_price_table
						WHERE '.(float)$col.' >= `col` AND id_option = '.(int)$id_option.'
						ORDER BY CAST(`col` AS DECIMAL(5,2)) DESC LIMIT 1) AS col					 												
					';
			$result = DB::getInstance()->getRow($sql);
			$row = $result['row'];
			$col = $result['col'];
			return self::getSpecificPrice($id_option, $row, $col);
		}

		//get closest row value (for textbox entries)
		/*$sql = 'SELECT DISTINCT(`row`) 
				FROM '._DB_PREFIX_.'ppat_product_price_table
				WHERE id_option = '.(int)$id_option.'
				AND `row` >= '.(float)$row.' ORDER BY CAST(`row` AS DECIMAL(5)) ASC';
		$result = DB::getInstance()->getRow($sql);
		if ($result) {
		    $row = $result['row'];
        }
		else {
		    $row = -1;
        }

		//$sql = 'SELECT col FROM '._DB_PREFIX_.'ppat_product_price_table WHERE col >= '.(float)$col.' ORDER BY col ASC';
		$sql = 'SELECT DISTINCT(`col`) 
				FROM '._DB_PREFIX_.'ppat_product_price_table pt
				WHERE id_option = '.(int)$id_option.'
				AND `col` >= '.(float)$col.' ORDER BY CAST(`col` AS DECIMAL(5)) ASC';
		$result = DB::getInstance()->getRow($sql);
		if ($result) {
		    $col = $result['col'];
        } else {
		    $col = -1;
        }
		if ($row == -1 || $col == -1) {
		    return false;
        }

		$sql = 'SELECT price
				FROM '._DB_PREFIX_.'ppat_product_price_table
				WHERE id_option = '.(int)$id_option.'
				AND `row` = '.(float)$row.'
				AND `col` = '.(float)$col;

		$results = DB::getInstance()->getRow($sql);*/

        $sql = 'SELECT ppt.price
                    FROM ' . _DB_PREFIX_ . 'ppat_product_price_table ppt
                    WHERE id_option = ' . (int)$id_option . '
                    AND ppt.price <> ""
                    AND ppt.row >= ' . (float)$row . ' AND ppt.col >= ' . (float)$col . '
                    ORDER BY CAST(ppt.row AS DECIMAL(5,2)) ASC, CAST(ppt.col AS DECIMAL(5,2)) ASC';
        $result = DB::getInstance()->executeS($sql);

        if (!empty($result)) {
            return $result[0];
        } else {
            return false;
        }
	}
}
