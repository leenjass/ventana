<?php
/**
 * 2014-2019 Prestashoppe
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://doc.prestashop.com for more information.
 *
 *  @author    Prestashoppe
 *  @copyright 2014-2019 Prestashoppe
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

class PreCmsAccordions extends ObjectModel
{

    public $id_precmsaccordion;
    public $title;
    public $description;
    public $date_add;
    public $date_upd;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'precmsaccordion',
        'primary' => 'id_precmsaccordion',
        'multilang' => true,
        'multilang_shop' => true,
        'fields' => array(
            'title' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCatalogName', 'size' => 255),
            'description' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'),
            'date_add' => array('type' => self::TYPE_DATE, 'shop' => true, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'shop' => true, 'validate' => 'isDate'),
        ),
    );
	
	public function __construct($id = null, $idLang = null, $idShop = null)
    {
        parent::__construct($id, $idLang, $idShop);
    }

    public static function getCmsAccordionById($id_cmsaccordion)
    {
        $query = new DbQuery();
        $query->select('*');
        $query->from('cmsaccordion', 'cms');
        $query->where('cms.id_cmsaccordion = ' . (int) $id_cmsaccordion);
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
        return $result;
    }
}
