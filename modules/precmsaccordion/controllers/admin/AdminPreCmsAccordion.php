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

require_once _PS_MODULE_DIR_ . 'precmsaccordion/precmsaccordion.php';

class AdminPreCmsAccordionController extends ModuleAdminController
{

    public function __construct()
    {
        $this->context = Context::getContext();
        $this->default_form_language = $this->context->language->id;
        $this->table = 'precmsaccordion';
        $this->className = 'PreCmsAccordions';
        $this->module = new PreCmsAccordion;
        $this->lang = true;
        $this->bootstrap = true;
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->context->getTranslator()->trans('Delete selected'),
                'confirm' => $this->context->getTranslator()->trans('Delete selected items?'),
                'icon' => 'icon-trash'
            )
        );

        $this->fields_list = array(
            'id_precmsaccordion' => array(
                'title' => $this->module->l('ID'),
                'width' => 100,
                'type' => 'text',
            ),
            'title' => array(
                'title' => $this->module->l('Title'),
                'type' => 'text',
            ),
            'date_add' => array(
                'title' => $this->module->l('Date'),
                'type' => 'datetime',
                'havingFilter' => false,
                'tmpTableFilter' => false,
            )
        );
        parent::__construct();
    }

    public function renderForm()
    {
        if (!($obj = $this->loadObject(true))) {
            return;
        }

        $this->fields_form = array(
            'tinymce' => true,
            'legend' => array(
                'title' => $this->module->l('Accordion'),
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Title'),
                    'name' => 'title',
                    'lang' => true,
                    'required' => true,
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->module->l('Description'),
                    'name' => 'description',
                    'rows' => 10,
                    'cols' => 60,
                    'lang' => true,
                    'class' => 'rte',
                    'autoload_rte' => true,
                ),
            ),
            'submit' => array(
                'title' => $this->module->l('Save')
            ),
        );

        return parent::renderForm();
    }

    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        return parent::renderList();
    }

    public function initToolbar()
    {
        parent::initToolbar();
    }
}
