<?php
/**
 * Rolige PrestaShop Cleaner Extra
 *
 * @author    Rolige <www.rolige.com>
 * @copyright 2011-2019 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 */

class RgPSCEModuleFormDatabase extends RgPSCEModuleForm
{
    public function __construct()
    {
        parent::__construct();

        $this->menu_active = 'database';
        $this->submit_action = 'submitCheckAndFix';
        $this->p = 'RGPSCE_DB_';
    }

    public function renderForm()
    {
        $rename_conf = '';
        if (Tools::getValue('rename_conf')) {
            $conf = $this->l('The DB prefix was renamed successfully').'. '.$this->l('Current DB prefix is').': <strong>'._DB_PREFIX_.'</strong>';
            $rename_conf = $this->module->displayConfirmation($conf);
        }
        
        $fields_value = $this->getFormValues();

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->module = $this->module;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = (int)Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG');
        $helper->submit_action = $this->submit_action;
        $helper->currentIndex = $this->currentIndex.'&menu_active='.$this->menu_active;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $fields_value,
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $rename_conf.$helper->generateForm(array(
            array(
                'form' => array(
                    'legend' => array(
                        'title' => $this->l('Functional integrity constraints'),
                        'icon' => 'icon-database'
                    ),
                    'description' => $this->l('This function will check all tables to delete obsolete and unnecessary data from your database and also convert all tables to InnoDB engine.'),
                    'submit' => array(
                        'title' => $this->l('Check & Fix'),
                        'name' => 'submitCheckAndFix',
                    )
                )
            ),
            array(
                'form' => array(
                    'legend' => array(
                        'title' => $this->l('Database cleaning'),
                        'icon' => 'icon-database'
                    ),
                    'description' => $this->l('Delete obsolete data will make your database lighter and faster. You can also set a task at Cron Jobs tab for periodically cleaning.'),
                    'input' => array(
                        array(
                            'type' => 'text',
                            'label' => $this->l('Obsolete connections statistics'),
                            'prefix' => '>=',
                            'suffix' => $this->l('days old'),
                            'col' => 3,
                            'name' => $this->p.'connections'
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Abandoned cart clean range'),
                            'prefix' => '>=',
                            'suffix' => $this->l('days old'),
                            'col' => 3,
                            'name' => $this->p.'cart_range'
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Old cart rules clean range'),
                            'prefix' => '>=',
                            'suffix' => $this->l('days old'),
                            'col' => 3,
                            'name' => $this->p.'cart_rule_range'
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Obsolete search statistics'),
                            'prefix' => '>=',
                            'suffix' => $this->l('days old'),
                            'col' => 3,
                            'name' => $this->p.'stats_search'
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Obsolete error logs'),
                            'prefix' => '>=',
                            'suffix' => $this->l('days old'),
                            'col' => 3,
                            'name' => $this->p.'ps_log'
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Old emails clean range'),
                            'prefix' => '>=',
                            'suffix' => $this->l('days old'),
                            'col' => 3,
                            'name' => $this->p.'mails'
                        )
                    ),
                    'submit' => array(
                        'title' => $this->l('Clean & Optimize'),
                        'name' => 'submitCleanAndOptimize',
                    )
                )
            ),
            array(
                'form' => array(
                    'legend' => array(
                        'title' => $this->l('Database old languages'),
                        'icon' => 'icon-database'
                    ),
                    'description' => $this->l('After delete a language some tables could keep registered the deleted language ID. This could cause that some information block in your shop does not appear or even your categories tree selector in BackOffice does not load properly.'),
                    'input' => array(
                        array(
                            'type' => 'text',
                            'label' => $this->l('Old language ID (deleted)'),
                            'col' => 2,
                            'name' => $this->p.'old_lang_id'
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('New language ID (replacement)'),
                            'col' => 2,
                            'name' => $this->p.'new_lang_id'
                        )
                    ),
                    'buttons' => array(
                        array(
                            'type' => 'submit',
                            'title' => $this->l('Check old languages'),
                            'class' => 'btn btn-default pull-right',
                            'name' => 'submitCheckLangID',
                            'icon' => 'process-icon-ok'
                        ),
                        array(
                            'type' => 'submit',
                            'title' => $this->l('Check & replace old language by new'),
                            'class' => 'btn btn-default pull-right',
                            'name' => 'submitCheckReplaceLangID',
                            'icon' => 'process-icon-save'
                        ),
                    )
                )
            ),
            array(
                'form' => array(
                    'legend' => array(
                        'title' => $this->l('Database prefix'),
                        'icon' => 'icon-database'
                    ),
                    'description' => $this->l('This process can take a while. Please, be patient.'),
                    'input' => array(
                        array(
                            'type' => 'text',
                            'label' => $this->l('New DB tables prefix'),
                            'desc' => $this->l('Your current DB prefix is').': <strong>'._DB_PREFIX_.'</strong>',
                            'name' => $this->p.'new_db_prefix',
                            'col' => 4
                        )
                    ),
                    'submit' => array(
                        'title' => $this->l('Rename tables'),
                        'name' => 'submitRenameDBPrefix',
                    )
                )
            )
        ));
    }

    public function getFormValues($for_save = false)
    {
        $fields_value = array(
            ($name = $this->p.'cart_range') => (int)Tools::getValue($name, 30),
            ($name = $this->p.'cart_rule_range') => (int)Tools::getValue($name, 60),
            ($name = $this->p.'connections') => (int)Tools::getValue($name, 90),
            ($name = $this->p.'stats_search') => (int)Tools::getValue($name, 60),
            ($name = $this->p.'ps_log') => (int)Tools::getValue($name, 180),
            ($name = $this->p.'mails') => (int)Tools::getValue($name, 180),
            ($name = $this->p.'new_db_prefix') => trim(Tools::getValue($name)),
            ($name = $this->p.'old_lang_id') => (int)Tools::getValue($name),
            ($name = $this->p.'new_lang_id') => (int)Tools::getValue($name),
        );

        return $fields_value;
    }

    public function isSubmitForm()
    {
        $actions = array('RenameDBPrefix', 'CheckReplaceLangID', 'CheckLangID', 'CleanAndOptimize', 'CheckAndFix');

        foreach ($actions as $action) {
            if (Tools::isSubmit('submit'.$action)) {
                $this->submit_action = 'submit'.$action;
                return true;
            }
        }

        return false;
    }

    public function validateForm()
    {
        $val = $this->getFormValues();

        $language_ids = Language::getLanguages(false, false, true);

        if ($this->submit_action == 'submitCleanAndOptimize') {
            $panel = $this->l('Database cleaning').' > ';
            if (!Validate::isUnsignedId($val[$this->p.'cart_range'])) {
                return $panel.$this->l('Abandoned cart clean range').': '.$this->l('Is invalid.');
            }
            if (!Validate::isUnsignedId($val[$this->p.'cart_rule_range'])) {
                return $panel.$this->l('Old cart rules clean range').': '.$this->l('Is invalid.');
            }
            if (!Validate::isUnsignedId($val[$this->p.'connections'])) {
                return $panel.$this->l('Obsolete connections statistics').': '.$this->l('Is invalid.');
            }
            if (!Validate::isUnsignedId($val[$this->p.'stats_search'])) {
                return $panel.$this->l('Obsolete search statistics').': '.$this->l('Is invalid.');
            }
            if (!Validate::isUnsignedId($val[$this->p.'ps_log'])) {
                return $panel.$this->l('Obsolete error logs').': '.$this->l('Is invalid.');
            }
            if (!Validate::isUnsignedId($val[$this->p.'mails'])) {
                return $panel.$this->l('Old emails clean range').': '.$this->l('Is invalid.');
            }
        }

        if ($this->submit_action == 'submitCheckReplaceLangID') {
            $panel = $this->l('Database old languages').' > ';
            if (!Validate::isUnsignedId($val[$this->p.'old_lang_id'])) {
                return $panel.$this->l('Old language ID (deleted)').': '.$this->l('Is invalid.');
            }
            if (!Validate::isUnsignedId($val[$this->p.'new_lang_id'])) {
                return $panel.$this->l('New language ID (replacement)').': '.$this->l('Is invalid.');
            }
            if (in_array($val[$this->p.'old_lang_id'], $language_ids)) {
                return $this->l('Old language ID (deleted)').': '.$this->l('Cannot be one of the current languages IDs of the shop at this moment.');
            }
            if (!in_array($val[$this->p.'new_lang_id'], $language_ids)) {
                return $this->l('New language ID (replacement)').': '.$this->l('Must be one of the current languages IDs of the shop at this moment.');
            }
        }

        if ($this->submit_action == 'submitRenameDBPrefix') {
            $panel = $this->l('Database prefix').' > ';
            if (!$val[$this->p.'new_db_prefix'] || !preg_match('/^[a-zA-Z_0-9]+$/', $val[$this->p.'new_db_prefix'])) {
                return $panel.$this->l('New DB tables prefix').': '.$this->l('Is invalid.').' '.$this->l('Just letters, numbers and underscores allowed.');
            }
            if ($val[$this->p.'new_db_prefix'] == _DB_PREFIX_) {
                return $panel.$this->l('New DB tables prefix').': '.$this->l('Is invalid.').' '.$this->l('Cannot be empty.');
            }
        }

        return false;
    }

    public function processForm()
    {
        $val = $this->getFormValues();

        $conf = $this->l('No action selected.');

        switch ($this->submit_action) {
            case 'submitCheckReplaceLangID':
                $logs = RgPSCETools::checkOldLanguages($val[$this->p.'old_lang_id'], $val[$this->p.'new_lang_id'], true);
                if (count($logs)) {
                    $conf = $this->l('The following old (deleted) languages ID where replaces in this tables:').'<br /><ul>';
                    foreach ($logs as $query => $entries) {
                        $conf .= '<li>'.Tools::htmlentitiesUTF8($query).'<br />'.implode('<br />', $entries).'</li>';
                    }
                    $conf .= '</ul>';
                } else {
                    $conf = $this->l('No old languages ID found');
                }
                break;
            case 'submitCheckLangID':
                $logs = RgPSCETools::checkOldLanguages($val[$this->p.'old_lang_id'], $val[$this->p.'new_lang_id']);
                if (count($logs)) {
                    $conf = $this->l('The following tables contains old (deleted) languages ID:').'<br /><ul>';
                    foreach ($logs as $query => $entries) {
                        $conf .= '<li>'.Tools::htmlentitiesUTF8($query).'<br />'.implode('<br />', $entries).'</li>';
                    }
                    $conf .= '</ul>';
                } else {
                    $conf = $this->l('No old languages ID found');
                }
                break;
            case 'submitCheckAndFix':
                $logs = RgPSCETools::checkAndFix();
                if (count($logs)) {
                    $conf = $this->l('The following queries successfully fixed broken data:').'<br /><ul>';
                    foreach ($logs as $query => $entries) {
                        $conf .= '<li>'.Tools::htmlentitiesUTF8($query).'<br />'.sprintf($this->l('%d line(s)'), $entries).'</li>';
                    }
                    $conf .= '</ul>';
                } else {
                    $conf = $this->l('Nothing that need to be fixed');
                }
                break;
            case 'submitRenameDBPrefix':
                if (RgPSCETools::renameDbPrefix($val[$this->p.'new_db_prefix'])) {
                    sleep(2);
                    Tools::redirectAdmin($this->currentIndex.'&menu_active='.$this->menu_active.'&rename_conf=1');
                } else {
                    $conf = $this->l('The DB prefix could not be renamed');
                }
                break;
            case 'submitDeleteOldImages':
                $deleted_images = RgPSCETools::deleteOldImages();
                $conf = sprintf($this->l('%d old product images were deleted'), $deleted_images);
                break;
            case 'submitCleanAndOptimize':
                $logs = RgPSCETools::cleanAndOptimize($val[$this->p.'cart_range'], $val[$this->p.'cart_rule_range'], $val[$this->p.'connections'], $val[$this->p.'stats_search'], $val[$this->p.'ps_log'], $val[$this->p.'mails']);
                if (count($logs)) {
                    $conf = $this->l('The following queries successfully cleaned your database:').'<br /><ul>';
                    foreach ($logs as $query => $entries) {
                        $conf .= '<li>'.Tools::htmlentitiesUTF8($query).'<br />'.sprintf($this->l('%d line(s)'), $entries).'</li>';
                    }
                    $conf .= '</ul>';
                } else {
                    $conf = $this->l('Nothing that need to be cleaned');
                }
                break;
        }

        return $conf;
    }
}
