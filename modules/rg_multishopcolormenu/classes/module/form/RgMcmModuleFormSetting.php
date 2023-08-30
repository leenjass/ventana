<?php
/**
 * Multishop Color Menu
 *
 * @author    Rolige <www.rolige.com>
 * @copyright 2011-2019 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 */

class RgMcmModuleFormSettings extends RgMcmModuleForm
{

    public function __construct()
    {
        parent::__construct();

        $this->menu_active = 'settings';
        $this->submit_action = 'submit'.Tools::ucfirst($this->menu_active).'Form';
        $this->p .= 'SETTINGS_';
    }

    public function getFormFields()
    {
        return array(
            array(
                'form' => array(
                    'legend' => array(
                        'title' => $this->l('Settings'),
                        'icon' => 'icon-cogs',
                    ),
                    'input' => array(
                        array(
                            'type' => 'color',
                            'label' => $this->l('Single shop context font color'),
                            'name' => $this->p.'SINGLE_COLOR',
                            'class' => 'input fixed-width-xs',
                            'hint' => $this->l('Color of the font of the shop selector menu in single shop context.'),
                        ),
                        array(
                            'type' => 'color',
                            'label' => $this->l('Single shop context background color'),
                            'name' => $this->p.'SINGLE_BACK_COLOR',
                            'class' => 'input fixed-width-xs',
                            'hint' => $this->l('Color of the background of the shop selector menu in single shop context.'),
                        ),
                        array(
                            'type' => 'color',
                            'label' => $this->l('Multi shop context font color'),
                            'name' => $this->p.'MULTI_COLOR',
                            'class' => 'input fixed-width-xs',
                            'hint' => $this->l('Color of the font of the shop selector menu in multi shop context.'),
                        ),
                        array(
                            'type' => 'color',
                            'label' => $this->l('Multi shop context background color'),
                            'name' => $this->p.'MULTI_BACK_COLOR',
                            'class' => 'input fixed-width-xs',
                            'hint' => $this->l('Color of the background of the shop selector menu in multi shop context.'),
                        ),
                        
                    ),
                    'submit' => array(
                        'title' => $this->l('Save'),
                    ),
                ),
            ),
        );
    }

    public function getFormValues($for_save = false)
    {
        $fields_value = array(
            ($name = $this->p.'SINGLE_COLOR') => trim(Tools::getValue($name, Configuration::get($name))),
            ($name = $this->p.'SINGLE_BACK_COLOR') => trim(Tools::getValue($name, Configuration::get($name))),
            ($name = $this->p.'MULTI_COLOR') => trim(Tools::getValue($name, Configuration::get($name))),
            ($name = $this->p.'MULTI_BACK_COLOR') => trim(Tools::getValue($name, Configuration::get($name))),
        );

        if ($for_save) {
            array_walk($fields_value, function (&$value)
            {
                if (Tools::substr($value, 0, 1) != '#') {
                    $value = '#'.$value;
                }
            });
        } else {
            array_walk($fields_value, function (&$value)
            {
                if (Tools::strlen($value) <= 1) {
                    $value = '';
                }
            });
        }

        return $fields_value;
    }

    public function validateForm()
    {
        $val = $this->getFormValues();
        $panel = $this->l('Settings').' > ';

        if ($val[$this->p.'SINGLE_COLOR'] && !Validate::isColor($val[$this->p.'SINGLE_COLOR'])) {
            return $panel.$this->l('Single shop context font color').': '.$this->l('Is not a valid color.');
        }

        if ($val[$this->p.'SINGLE_BACK_COLOR'] && !Validate::isColor($val[$this->p.'SINGLE_BACK_COLOR'])) {
            return $panel.$this->l('Single shop context background color').': '.$this->l('Is not a valid color.');
        }

        if ($val[$this->p.'MULTI_COLOR'] && !Validate::isColor($val[$this->p.'MULTI_COLOR'])) {
            return $panel.$this->l('Multi shop context font color').': '.$this->l('Is not a valid color.');
        }

        if ($val[$this->p.'MULTI_BACK_COLOR'] && !Validate::isColor($val[$this->p.'MULTI_BACK_COLOR'])) {
            return $panel.$this->l('Multi shop context background color').': '.$this->l('Is not a valid color.');
        }

        return false;
    }

    public function processForm()
    {
        $res = parent::processForm();
        
        if (Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP) {
            $prefix = $this->p.'SINGLE_';
        } else {
            $prefix = $this->p.'MULTI_';
        }
        Media::addJsDef(array('multishopcolormenu' => array(
            'color' => RgMcmConfig::get($prefix.'COLOR'),
            'back_color' => RgMcmConfig::get($prefix.'BACK_COLOR'),
        )));

        return $res;
    }
}
