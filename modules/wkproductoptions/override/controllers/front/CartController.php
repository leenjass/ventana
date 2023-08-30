<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.txt
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to a newer
 * versions in the future. If you wish to customize this module for your
 * needs please refer to CustomizationPolicy.txt file inside our module for more information.
 *
 * @author Webkul IN
 * @copyright Since 2010 Webkul
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */
class CartController extends CartControllerCore
{
    public function init()
    {
        parent::init();
        if (Tools::getValue('add')) {
            require_once _PS_MODULE_DIR_ . 'wkproductoptions/wkproductoptions.php';
            $objProductConfiguration = new WkProductWiseConfiguration();
            $productConfig = $objProductConfiguration->getProductWiseConfiguration(
                $this->id_product,
                $this->context->shop->id
            );
            if (!empty($productConfig) && $productConfig['is_native_customization']) {
                $objCustomerOption = new WkProductCustomerOptions();
                $idProductAttr = Tools::getValue('wk_id_product_attribute_form');
                $errors = $objCustomerOption->addToCartValidations($this->id_product, $idProductAttr);
                if (!empty($errors['error'])) {
                    foreach ($errors['error'] as $error) {
                        $this->errors[] = $error;
                    }
                } else {
                    if ($errors['selected'] == 1) {
                        $objOption = new WkProductOptionsConfig();
                        $options = $objOption->getProductWiseOptionsActive($this->id_product, $this->context->shop->id);
                        if (!empty($options)) {
                            $this->textRecord(new Product($this->id_product));
                            $customization_datas = $this->context->cart->getProductCustomization($this->id_product, null, true);
                            $this->customization_id = empty($customization_datas) ? null : $customization_datas[0]['id_customization'];
                            $this->context->cookie->wk_id_option_customization = $this->customization_id;
                            $this->context->cookie->write();
                        }
                    }
                }
            }
        }
    }

    /**
     * Custom function to add custonization data dynamically
     *
     * @param object $objProduct
     *
     * @return void
     */
    protected function textRecord($objProduct)
    {
        if (!$fieldIds = $objProduct->getCustomizationFieldIds()) {
            return false;
        }

        $authorizedTextFields = [];
        foreach ($fieldIds as $fieldId) {
            if ($fieldId['type'] == Product::CUSTOMIZE_TEXTFIELD) {
                $authorizedTextFields[(int) $fieldId['id_customization_field']] = 'textField' .
                    (int) $fieldId['id_customization_field'];
            }
        }

        $indexes = array_flip($authorizedTextFields);
        foreach ($authorizedTextFields as $fieldName) {
            $value = 'product option content hidden' . rand(10, 1000);
            if (in_array($fieldName, $authorizedTextFields) && $value != '') {
                $this->context->cart->addTextFieldToProduct(
                    $objProduct->id,
                    $indexes[$fieldName],
                    Product::CUSTOMIZE_TEXTFIELD,
                    $value
                );
            } elseif (in_array($fieldName, $authorizedTextFields) && $value == '') {
                $this->context->cart->deleteCustomizationToProduct((int) $objProduct->id, $indexes[$fieldName]);
            }
        }
    }
}
