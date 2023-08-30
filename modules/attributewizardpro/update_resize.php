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

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__) . '/attributewizardpro.php');
$awp = new AttributeWizardPro();

$awp_random = Tools::getValue('awp_random');
if ($awp_random != Configuration::get('AWP_RANDOM')) {
    die('No Access');
}

Configuration::updateValue('AWP_IMAGE_RESIZE', Tools::getValue('resize'));
Configuration::updateValue('AWP_IMAGE_RESIZE_WIDTH', Tools::getValue('width'));
