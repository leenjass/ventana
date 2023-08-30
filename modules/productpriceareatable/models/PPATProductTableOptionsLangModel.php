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

if (!defined('_PS_VERSION_'))
	exit;

class PPATProductTableOptionsLangModel extends ObjectModel
{
	/** @var integer Option ID */
	public $id_option;

	/** @var integer Product ID */
	public $id_lang;

	/** @var string  */
	public $option_text;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'ppat_product_table_options_lang',
		'primary' => 'id_option',
		'multilang' => false,
		'fields' => array(
			'id_lang' => array('type' => self::TYPE_INT),
			'option_text' => array('type' => self::TYPE_STRING)
		)
	);

    /**
     * Load options by Option ID
     * @param $id_option
     */
	public function loadByOption($id_option)
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from(self::$definition['table']);
        $sql->where('id_option = ' . (int)$id_option);
        $result = DB::getInstance()->executeS($sql);
        if ($result) {
            return $this->hydrateCollection('PPATProductTableOptionsLangModel', $result);
        } else
            return array();

    }
}
