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
// Prevent unauthorized access.
if ($awp->awp_random != Tools::getValue('awp_random')) {
    print 'No Permissions';
    exit;
}

$awp_img_dir = dirname(__FILE__) . '/views/img/';
$trusted_extensions = ['jpg','jpeg','gif','png'];
if ((Tools::getValue('action') == 'delete_layered_image' || Tools::getValue('action') == 'delete_image')) {

    if (Tools::getValue('action') == 'delete_layered_image') {
        $filename = $awp->getLayeredImage((int)Tools::getValue('id_attribute', false), true, (int)Tools::getValue('id_group_pos'));

    } elseif (Tools::getValue('action') == 'delete_image') {
        $filename = $awp->getGroupImage((int)Tools::getValue('id_group'), true);
    }
    $filename = $awp_img_dir . $filename;
    $extension = strtolower(pathinfo($filename)['extension']);
    $result = false;

    if(file_exists($filename) && in_array($extension, $trusted_extensions)) {
        $result = unlink($filename);
    }

    die($result ? 'success' : 'error');
}

$result = Db::getInstance()->ExecuteS('SELECT * FROM `' . _DB_PREFIX_ . 'awp_attribute_wizard_pro`');
$result = $result[0]['awp_attributes'];

$attributes = unserialize($result);
$id_group = (int) Tools::getValue('id_group');
$id_attribute = (int) Tools::getValue('id_attribute');

if ($id_group) {
    $order = $awp->isInGroup($id_group, $attributes);
    if (isset($attributes[$order]['image_upload'])) {
        $attributes[$order]['image_upload']++;
    } else {
        $attributes[$order]['image_upload'] = 1;
    }
} elseif ($id_attribute) {
    $pos = (int)Tools::getValue('pos');
    $order = $awp->isInAttribute($id_attribute, $attributes[$pos]['attributes']);
    if (isset($attributes[$pos]['attributes'][$order]['image_upload_attr'])) {
        $attributes[$pos]['attributes'][$order]['image_upload_attr']++;
    } else {
        $attributes[$pos]['attributes'][$order]['image_upload_attr'] = 1;
    }
} else {
    print "Missing Information";
    exit;
}
Db::getInstance()->Execute('UPDATE `' . _DB_PREFIX_ . 'awp_attribute_wizard_pro` SET awp_attributes = "' . pSQL(serialize($attributes)) . '"');

$uploaddir = $awp_img_dir;
if ($id_group) {
    $uploadfile = $uploaddir . Tools::strtolower("id_group_" . $id_group .Tools::substr(basename($_FILES['userfile']['name']), strrpos(basename($_FILES['userfile']['name']), ".")));
} else {
    $uploadfile = $uploaddir . Tools::strtolower("id_attribute_" . $id_attribute .Tools::substr(basename($_FILES['userfile']['name']), strrpos(basename($_FILES['userfile']['name']), ".")));
}
$info = getimagesize($_FILES['userfile']['tmp_name']);
if ($info === FALSE) {
    die("Unable to determine image type of uploaded file");
}
if (($info[2] !== IMAGETYPE_GIF) && ($info[2] !== IMAGETYPE_JPEG) && ($info[2] !== IMAGETYPE_PNG)) {
    die("Not a gif/jpeg/png");
}

$move = move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile);
if ($move) {
    if ($id_group && Configuration::get('AWP_IMAGE_RESIZE') == 1) {
        $newWidth = Configuration::get('AWP_IMAGE_RESIZE_WIDTH');
        $path_info = pathinfo($uploadfile);
        $extension = Tools::strtolower($path_info['extension']);
        if ($extension == 'jpg' || $extension == 'jpeg') {
            $src = imagecreatefromjpeg($uploadfile);
        } else if ($extension == 'png') {
            $src = imagecreatefrompng($uploadfile);
        } else {
            $src = imagecreatefromgif($uploadfile);
        }
        list($width, $height) = getimagesize($uploadfile);
        $newHeight = ($height / $width) * $newWidth;
        $tmp = imagecreatetruecolor($newWidth, $newHeight);
        $no_extention = Tools::substr($uploadfile, 0, Tools::strlen($uploadfile) - Tools::strlen($extension) - 1);
        imagecopyresampled($tmp, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        if (file_exists($no_extention . '.gif')) {
            unlink($no_extention . '.gif');
        }
        if (file_exists($no_extention . '.jpeg')) {
            unlink($no_extention . '.jpeg');
        }
        if (file_exists($no_extention . '.jpg')) {
            unlink($no_extention . '.jpg');
        }
        if (file_exists($no_extention . '.png')) {
            unlink($no_extention . '.png');
        }
        imagejpeg($tmp, $uploadfile, 85);
    }
    echo 'success';
} else {
    // WARNING! DO NOT USE "FALSE" STRING AS A RESPONSE!
    // Otherwise onSubmit event will not be fired
    print "error: could not write " . $uploadfile . ", check your file permission";
}
