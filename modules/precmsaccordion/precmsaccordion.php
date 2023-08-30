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
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'precmsaccordion/classes/PreCmsAccordions.php';

class PreCmsAccordion extends Module
{

    protected $configform = false;
    private $output = '';

    public function __construct()
    {
        $this->name = 'precmsaccordion';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Prestashoppe';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
        $this->module_key = '';
        $this->bootstrap = true;
        parent::__construct();
        $this->languages = Language::getLanguages(false);
        $this->displayName = $this->l('Cms Accordion');
        $this->description = $this->l('Accordion content display on prestashop content page.');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall Cms Accordion module?');
    }

    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }
        include(_PS_MODULE_DIR_ . 'precmsaccordion/sql/install.php');
        return parent::install() &&
            $this->registerHook('displayHeader') &&
			$this->registerHook('filterProductContent') &&
			$this->registerHook('displayProductExtraContent') &&
            $this->registerHook('filterCmsContent') &&
            $this->registerHook('filterCmsCategoryContent') &&
            
            $this->installPreCmsAccordionSettings() &&
            $this->createPrestashoppeTab() &&
            $this->createPreCmsAccordionTab();
    }

    protected function installPreCmsAccordionSettings()
    {
        Configuration::updateValue('PRE_CMS_ACC_COLLAPSE_CONTENT', 1);
        Configuration::updateValue('PRE_CMS_ACC_AUTO_HEIGHT_CONTENT', 1);
        return true;
    }

    protected function createPrestashoppeTab()
    {
        if (!(int) Tab::getIdFromClassName('AdminPrestashoppe')) {

            $parentTab = new Tab();
            $parentTab->active = 1;
            $parentTab->name = array();
            $parentTab->class_name = "AdminPrestashoppe";
            foreach ($this->languages as $language) {
                $parentTab->name[$language['id_lang']] = 'PRESTASHOPPE';
            }
            $parentTab->id_parent = 0;
            $parentTab->module = '';
            $parentTab->add();
        }
        return true;
    }

    protected function createPreCmsAccordionTab()
    {
        if (!(int) Tab::getIdFromClassName('AdminPreCmsAccordion')) {
            $parentTabID = Tab::getIdFromClassName('AdminPrestashoppe');
            $parentTab = new Tab($parentTabID);

            $tab = new Tab();
            $tab->active = 1;
            $tab->class_name = "AdminPreCmsAccordion";
            $tab->icon = "question_answer";
            $tab->name = array();
            foreach ($this->languages as $language) {
                $tab->name[$language['id_lang']] = $this->l('Cms Accordion');
            }
            $tab->id_parent = $parentTab->id;
            $tab->module = $this->name;
            $tab->add();
        }
        return true;
    }

    public function uninstall()
    {
        //include(_PS_MODULE_DIR_ . 'precmsaccordion/sql/uninstall.php');
        return parent::uninstall() &&
            $this->unregisterHook('displayHeader') &&
            $this->unregisterHook('filterCmsContent') &&
            $this->unregisterHook('filterCmsCategoryContent') &&
            $this->uninstallPreCmsAccordionSettings() &&
            $this->deletePreCmsAccordionTab() &&
            $this->deletePrestashoppeTab();
    }
    
    protected function uninstallPreCmsAccordionSettings()
    {
        Configuration::deleteByName('PRE_CMS_ACC_COLLAPSE_CONTENT');
        Configuration::deleteByName('PRE_CMS_ACC_AUTO_HEIGHT_CONTENT');
        return true;
    }

    protected function deletePreCmsAccordionTab()
    {
        if ((int) Tab::getIdFromClassName('AdminPreCmsAccordion')) {
            $id_tab = Tab::getIdFromClassName('AdminPreCmsAccordion');
            $tab = new Tab($id_tab);
            $tab->delete();
        }
        return true;
    }

    protected function deletePrestashoppeTab()
    {
        if ((int) Tab::getIdFromClassName('AdminPrestashoppe')) {
            $parentTabID = Tab::getIdFromClassName('AdminPrestashoppe');
            $tabCount = Tab::getNbTabs($parentTabID);
            if ($tabCount == 0) {
                $parentTab = new Tab($parentTabID);
                $parentTab->delete();
            }
        }
        return true;
    }
    
     public function getContent()
     {
         if (((bool) Tools::isSubmit('submitPreCmsAccordionForm')) == true) {
             $this->prePreCmsAccordionFormPostProcess();
         }
         $this->output .= $this->context->smarty->fetch($this->local_path . 'views/templates/admin/configure.tpl');
         return $this->output . $this->preCmsAccordionForm();
     }
     
     protected function preCmsAccordionForm()
    {
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');
        $helper->module = $this;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitPreCmsAccordionForm';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'uri' => $this->getPathUri(),
            'fields_value' => $this->getPreCmsAccordionFormValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );
        return $helper->generateForm(array($this->getPreCmsAccordionForm()));
    }
    
    protected function getPreCmsAccordionForm()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('General Settings'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Collapse content'),
                        'name' => 'PRE_CMS_ACC_COLLAPSE_CONTENT',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'PRE_CMS_ACC_COLLAPSE_CONTENT_ON',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'PRE_CMS_ACC_COLLAPSE_CONTENT_OFF',
                                'value' => 0,
                                'label' => $this->l('No')
                            ),
                        ),
                        'desc' => $this->l('If you choose YES then allow for all sections to be be collapsible. By default accordions always keep one section open.'),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Auto height content'),
                        'name' => 'PRE_CMS_ACC_AUTO_HEIGHT_CONTENT',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'PRE_CMS_ACC_AUTO_HEIGHT_CONTENT_ON',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'PRE_CMS_ACC_AUTO_HEIGHT_CONTENT_OFF',
                                'value' => 0,
                                'label' => $this->l('No')
                            ),
                        ),
                        'desc' => $this->l('If you choose YES then allows the accordion panels to keep their native height.'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                )
            ),
        );
        return $fields_form;
    }
    
    protected function getPreCmsAccordionFormValues()
    {
        return array(
            'PRE_CMS_ACC_COLLAPSE_CONTENT' => Tools::getValue('PRE_CMS_ACC_COLLAPSE_CONTENT', Configuration::get('PRE_CMS_ACC_COLLAPSE_CONTENT')),
            'PRE_CMS_ACC_AUTO_HEIGHT_CONTENT' => Tools::getValue('PRE_CMS_ACC_AUTO_HEIGHT_CONTENT', Configuration::get('PRE_CMS_ACC_AUTO_HEIGHT_CONTENT')),
        );
    }
    
    protected function prePreCmsAccordionFormPostProcess()
    {
        Configuration::updateValue('PRE_CMS_ACC_COLLAPSE_CONTENT', Tools::getValue('PRE_CMS_ACC_COLLAPSE_CONTENT'));
        Configuration::updateValue('PRE_CMS_ACC_AUTO_HEIGHT_CONTENT', Tools::getValue('PRE_CMS_ACC_AUTO_HEIGHT_CONTENT'));
        $this->output .= $this->displayConfirmation($this->l('General settings have been updated'));
    }

    public function hookdisplayHeader()
    {
        if (!method_exists($this->context->controller, 'addJquery')) {
            return false;
        }
        if ('cms' === $this->context->controller->php_self || 'product' === $this->context->controller->php_self) {
            $this->context->controller->addJqueryUI('ui.accordion');
            $this->context->controller->registerStylesheet('precmsaccordion', 'modules/precmsaccordion/views/css/precmsaccordion.css');
            $this->context->controller->registerJavascript('precmsaccordion', 'modules/precmsaccordion/views/js/precmsaccordion.js');
            
            $this->context->smarty->assign(array(
                'precmsacccollapsecontent' => Configuration::get('PRE_CMS_ACC_COLLAPSE_CONTENT'),
                'precmsautoheightcontent' => Configuration::get('PRE_CMS_ACC_AUTO_HEIGHT_CONTENT') ? 'content' : 'auto',
            ));
            return $this->fetch('module:precmsaccordion/views/templates/hook/hookdisplayHeader.tpl');
        }
    }
	
	public function hookdisplayProductExtraContent($params) {
		//var_dump($params['product']->description); 
		//die;
	}
	
	public function hookfilterProductContent($params) {
		preg_match_all('/\{pre_cms_accordion\:[(0-9\,)]+\}/i', $params['object']['description'], $matches);
        foreach ($matches[0] as $index => $match) {
            $explode = explode(":", $match);
            $params['object']['description'] = str_replace($match, $this->returnPreCmsAccordionContent(str_replace("}", "", $explode[1])), $params['object']['description']);
        }

        preg_match_all('/\{pre_cms_accordion \: [(0-9\,)]+\}/i', $params['object']['description'], $matches);
        foreach ($matches[0] as $index => $match) {
            $explode = explode(":", $match);
            $params['object']['description'] = str_replace($match, $this->returnPreCmsAccordionContent(str_replace("}", "", $explode[1])), $params['object']['description']);
        }
        return $params;
	}
	

    public function hookfilterCmsContent($params)
    {
        preg_match_all('/\{pre_cms_accordion\:[(0-9\,)]+\}/i', $params['object']['content'], $matches);
        foreach ($matches[0] as $index => $match) {
            $explode = explode(":", $match);
            $params['object']['content'] = str_replace($match, $this->returnPreCmsAccordionContent(str_replace("}", "", $explode[1])), $params['object']['content']);
        }

        preg_match_all('/\{pre_cms_accordion \: [(0-9\,)]+\}/i', $params['object']['content'], $matches);
        foreach ($matches[0] as $index => $match) {
            $explode = explode(":", $match);
            $params['object']['content'] = str_replace($match, $this->returnPreCmsAccordionContent(str_replace("}", "", $explode[1])), $params['object']['content']);
        }
        return $params;
    }
    
    public function hookFilterCmsCategoryContent($params)
    {
        preg_match_all('/\{pre_cms_accordion\:[(0-9\,)]+\}/i', $params['object']['cms_category']['description'], $matches);
        foreach ($matches[0] as $index => $match) {
            $explode = explode(":", $match);
            $params['object']['cms_category']['description'] = str_replace($match, $this->returnPreCmsAccordionContent(str_replace("}", "", $explode[1])), $params['object']['cms_category']['description']);
        }

        preg_match_all('/\{pre_cms_accordion \: [(0-9\,)]+\}/i', $params['object']['cms_category']['description'], $matches);
        foreach ($matches[0] as $index => $match) {
            $explode = explode(":", $match);
            $params['object']['cms_category']['description'] = str_replace($match, $this->returnPreCmsAccordionContent(str_replace("}", "", $explode[1])), $params['object']['cms_category']['description']);
        }

        return $params;
    }

    protected function returnPreCmsAccordionContent($id_precmsaccordions)
    {
        $explodeprecmsaccordions = explode(",", $id_precmsaccordions);
        $precmsaccordions = array();
		
		foreach ($explodeprecmsaccordions as $ida) {
			$explode[] = $ida;
			
			foreach ($explode as $id_precmsaccordion) {
				if ($id_precmsaccordion != '') {
					$preCmsAccordionObj = new PreCmsAccordions($id_precmsaccordion, $this->context->language->id);
					if (!empty($preCmsAccordionObj) && isset($preCmsAccordionObj->id)) {
						if(!empty($preCmsAccordionObj->title)) {
							$precmsaccordions[$id_precmsaccordion]['title'] = $preCmsAccordionObj->title;
                        	$precmsaccordions[$id_precmsaccordion]['description'] = $preCmsAccordionObj->description;
						}
					}
				}
			}
		}
        $this->context->smarty->assign('precmsaccordions', $precmsaccordions);
        $content = $this->fetch('module:precmsaccordion/views/templates/hook/hookFilterCmsContent.tpl');
        return $content;
    }
}
