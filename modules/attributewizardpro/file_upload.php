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
require_once(dirname(__FILE__) . '/../../images.inc.php');
include(dirname(__FILE__) . '/attributewizardpro.php');

$awp = new AttributeWizardPro();
$ps_version = (float) (Tools::substr(_PS_VERSION_, 0, 3));

$id_attribute = Tools::getValue('id_attribute');
$id_product = Tools::getValue('id_product');

$uploaddir = dirname(__FILE__) . '/file_uploads/';
$uploadfile = Tools::strtolower(md5($id_product . "_" . $id_attribute . "_" . mt_rand()) . Tools::substr($_FILES['userfile']['name'], strrpos($_FILES['userfile']['name'], ".")));

if ($_FILES['userfile']['size'] > Configuration::get('AWP_UPLOAD_SIZE') * 1024) {
    print $awp->l('File size is too big, max size = ') . Configuration::get('AWP_UPLOAD_SIZE') . 'KB';
    exit;
}


// Make sure the uploaded file is one of the allowed extensions.
$move = move_uploaded_file($_FILES['userfile']['tmp_name'], $uploaddir . $uploadfile);
$path_info = pathinfo($uploaddir . $uploadfile);
$extension = Tools::strtolower($path_info['extension']);
if ($extension == 'php' || $extension == 'phtml' || $extension == 'php3' || $extension == 'php4' || $extension == 'php5' || $extension == 'php7' || $extension == 'phps') {
    echo $awp->l('Error: Unauthorized file extension.');
    unlink($uploaddir . $uploadfile);
    exit;
}

$allowed = false;

// If file is not one of the allowed extension, or if someone tries to submit directly to the file, reject the file.
foreach ($awp->awp_attributes as $atts) {
    foreach ($atts['attributes'] as $att) {
        if ($att['id_attribute'] == $id_attribute) {
            $arr = explode("|", $atts['group_file_ext']);
            if (!in_array($extension, $arr) || !isset($atts['group_file_ext']) || !$atts['group_file_ext']) {
                echo $awp->l('Error: Unauthorized file extension.');
                unlink($uploaddir . $uploadfile);
                exit;
            } else {
                $allowed = true;
                break;
            }
        }
    }
}
if (!$allowed) {
    echo $awp->l('Error: Unauthorized file extension.');
    unlink($uploaddir . $uploadfile);
    exit;
}

if ($move) {
    $newSize = Configuration::get('AWP_THUMBNAIL_SIZE');
    $no_extention = Tools::substr($uploaddir . $uploadfile, 0, Tools::strlen($uploaddir . $uploadfile) - Tools::strlen($extension) - 1);
    if ($_FILES['userfile']['size'] < 2000 * 1024 && ($extension == "jpg" || $extension == "jpeg" || $extension == "png" || $extension == "gif")) {
        if ($extension == "jpg" || $extension == "jpeg") {
            $src = imagecreatefromjpeg($uploaddir . $uploadfile);
        } else if ($extension == "png") {
            $src = imagecreatefrompng($uploaddir . $uploadfile);
        } else {
            $src = imagecreatefromgif($uploaddir . $uploadfile);
        }
        list($width, $height) = getimagesize($uploaddir . $uploadfile);
        //$newHeight = ($height / $width) * $newWidth;
        $tmp = imagecreatetruecolor($newSize, $newSize);
        imagecopyresampled($tmp, $src, 0, 0, 0, 0, $newSize, $newSize, $width, $height);
        imagejpeg($tmp, $no_extention . "_small.jpg", 85);
        echo $uploadfile . "|||" . $_FILES['userfile']['name'];
    } else {
        echo $uploadfile . "||||" . $_FILES['userfile']['name'];
    }
} else {
    // WARNING! DO NOT USE "FALSE" STRING AS A RESPONSE!
    // Otherwise onSubmit event will not be fired
    echo $awp->l('Error: Could not copy file, please check there is writing permissions to ') . " $uploaddir";
}
