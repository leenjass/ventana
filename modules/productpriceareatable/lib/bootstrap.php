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

/* Library */
include_once(_PS_MODULE_DIR_."/productpriceareatable/lib/classes/PPATControllerCore.php");

/* Models */
include_once(_PS_MODULE_DIR_ . "/productpriceareatable/models/PPATInstall.php");
include_once(_PS_MODULE_DIR_ . "/productpriceareatable/models/PPATUnitModel.php");
include_once(_PS_MODULE_DIR_ . "/productpriceareatable/models/PPATProductModel.php");
include_once(_PS_MODULE_DIR_ . "/productpriceareatable/models/PPATProductOptionLabelLangModel.php");
include_once(_PS_MODULE_DIR_ . "/productpriceareatable/models/PPATProductTableOptionModel.php");
include_once(_PS_MODULE_DIR_ . "/productpriceareatable/models/PPATProductTableOptionsLangModel.php");
include_once(_PS_MODULE_DIR_ . "/productpriceareatable/models/PPATProductPriceTableModel.php");

/* Helpers */
include_once(_PS_MODULE_DIR_ . "/productpriceareatable/helpers/PPATProductHelper.php");
include_once(_PS_MODULE_DIR_ . "/productpriceareatable/helpers/PPATCartHelper.php");
include_once(_PS_MODULE_DIR_ . "/productpriceareatable/helpers/PPATMassAssignHelper.php");
include_once(_PS_MODULE_DIR_ . "/productpriceareatable/helpers/PPATProductPriceTableHelper.php");
include_once(_PS_MODULE_DIR_ . "/productpriceareatable/helpers/PPATProductTableOptionHelper.php");
include_once(_PS_MODULE_DIR_ . "/productpriceareatable/helpers/PPATToolsHelper.php");
include_once(_PS_MODULE_DIR_ . "/productpriceareatable/helpers/PPATTranslationHelper.php");

/* Controllers - Admin */
include_once(_PS_MODULE_DIR_."/productpriceareatable/controllers/admin/config/PPATAdminConfigMainController.php");
include_once(_PS_MODULE_DIR_."/productpriceareatable/controllers/admin/config/PPATAdminConfigGeneralController.php");

include_once(_PS_MODULE_DIR_."/productpriceareatable/controllers/admin/producttab/PPATAdminProductTabController.php");
include_once(_PS_MODULE_DIR_."/productpriceareatable/controllers/admin/producttab/PPATAdminProductTabGeneralController.php");
include_once(_PS_MODULE_DIR_."/productpriceareatable/controllers/admin/producttab/PPATAdminProductTabPricesController.php");

/* Controllers - Front */
include_once(_PS_MODULE_DIR_."/productpriceareatable/controllers/front/PPATFrontProductController.php");
include_once(_PS_MODULE_DIR_."/productpriceareatable/controllers/front/PPATFrontCartController.php");

