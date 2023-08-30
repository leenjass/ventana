<?php
/**
 * 2007-2022 ETS-Soft
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 wesite only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please contact us for extra customization service at an affordable price
 *
 * @author ETS-Soft <etssoft.jsc@gmail.com>
 * @copyright  2007-2022 ETS-Soft
 * @license    Valid for 1 website (or project) for each purchase of license
 *  International Registered Trademark & Property of ETS-Soft
 */

if (!defined('_PS_VERSION_'))  
    exit;
require_once(dirname(__FILE__) . '/classes/ets_delete_order_defines.php');
class Ets_delete_order extends Module
{
    public $_errors = array();
    public $is17 = false;
    public $_hooks = array(
        'displayBackOfficeHeader',
        'actionOrderGridDefinitionModifier',
        'actionOrderGridQueryBuilderModifier',
        'actionDispatcherBefore'
    );
    public $bulk_orders =array();
    public function __construct()
    {
        $this->name = 'ets_delete_order';
        $this->tab = 'front_office_features';
        $this->version = '1.0.4';
        $this->author = 'ETS-Soft';
        $this->need_instance = 0;
        $this->secure_key = Tools::encrypt($this->name);
        $this->bootstrap = true;
        parent::__construct();
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->module_dir = $this->_path;
        $this->displayName = $this->l('Delete Order');
        $this->description = $this->l('Allow you to delete orders permanently or restore orders from Trash back to the order listing page');
$this->refs = 'https://prestahero.com/';
        if(version_compare(_PS_VERSION_, '1.7', '>='))
            $this->is17 = true;
		$this->module_key = 'bde0ab61507ce6941600f358ed7ea0de';
        if(version_compare(_PS_VERSION_, '1.7.7.0', '>=') && isset($this->context->employee) && isset($this->context->employee->id) && $this->context->employee->id)
        {
            if(Tools::isSubmit('delete_all_order') || Tools::isSubmit('restore_all_order'))
            {
                $order_orders_bulk = Tools::getValue('order_orders_bulk');
                $this->bulk_orders['order_orders_bulk'] = $order_orders_bulk && self::validateArray($order_orders_bulk) ? $order_orders_bulk : array();
                if(Tools::isSubmit('delete_all_order'))
                    $this->bulk_orders['delete_all_order'] =1;
                if(Tools::isSubmit('restore_all_order'))
                    $this->bulk_orders['restore_all_order']=1;
                if(Tools::isSubmit('viewtrash'))
                    $this->bulk_orders['viewtrash'] =1;
                $this->context->cookie->ets_odm_bulk_order = json_encode($this->bulk_orders);
                $this->context->cookie->write();
            }
            else
            {
                if($this->context->cookie->ets_odm_bulk_order)
                {
                    $this->bulk_orders  = json_decode($this->context->cookie->ets_odm_bulk_order,true);
                    $this->context->cookie->ets_odm_bulk_order ='';
                    $this->context->cookie->write();
                }
            }
        }
    }
    public function install()
    {
        if(Module::isInstalled('ets_ordermanager')){
            throw new PrestaShopException($this->l("The module Order Manager has been installed"));
        }
        return parent::install() && Ets_delete_order_defines::installDb() && $this->installHooks()&& $this->_installTabs()&& $this->_installOverried();
    }
    public function unInstall()
    {
        return parent::unInstall()&& $this->unInstallHooks() && $this->_uninstallTabs();
    }
    public function installHooks()
    {
        foreach($this->_hooks as $hook)
            $this->registerHook($hook);
        return true;
    }
    public function unInstallHooks()
    {
        foreach($this->_hooks as $hook)
            $this->unregisterHook($hook);
        return true;
    }
    public function _installTabs()
    {
        if ($parentId = Tab::getIdFromClassName('AdminParentOrders')) {
            $languages = Language::getLanguages(false);
            $tab = new Tab();
            $tab->id_parent = (int)$parentId;
            $tab->class_name = 'AdminOrderDeleteManager';
            $tab->icon = 'icon-AdminPriceRule';
            $tab->module = $this->name;
            $tab->active=0;
            foreach ($languages as $l) {
                $tab->name[$l['id_lang']] = $this->l('Delete orders');
            }
            if (!Tab::getIdFromClassName($tab->class_name))
                return $tab->add();
        }
        return true;
    }
    public function _uninstallTabs()
    {
        if ($id = Tab::getIdFromClassName('AdminOrderDeleteManager')) {
            $tab = new Tab((int)$id);
            if ($tab->delete()) {
                return true;
            }
        }
        return true;
    }
    public function _installOverried()
    {
        if(version_compare(_PS_VERSION_, '1.7.7.0', '<'))
            $this->copy_directory(dirname(__FILE__) . '/views/templates/admin/_configure/templates', _PS_OVERRIDE_DIR_ . 'controllers/admin/templates');
        return true;
    }
    public function _unInstallOverried()
    {
        if(version_compare(_PS_VERSION_, '1.7.7.0', '<'))
        {
            $this->delete_directory(_PS_OVERRIDE_DIR_ . 'controllers/admin/templates');
            if (Module::isInstalled('ets_payment_with_fee'))
                $this->copy_directory(_PS_MODULE_DIR_ . 'ets_payment_with_fee/views/templates/admin/templates', _PS_OVERRIDE_DIR_ . 'controllers/admin/templates');
        }
        return true;
    }
    public function delete_directory($directory)
    {
        $dir = opendir($directory);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($directory . '/' . $file)) {
                    $this->delete_directory($directory . '/' . $file);
                } else {
                    if (file_exists($directory . '/' . $file) && $file != 'index.php' && ($content = Tools::file_get_contents($directory . '/' . $file)) && Tools::strpos($content, 'overried by chung_ets') !== false) {
                        @unlink($directory . '/' . $file);
                        if (file_exists($directory . '/backup_' . $file))
                            copy($directory . '/backup_' . $file, $directory . '/' . $file);
                    }

                }
            }
        }
        closedir($dir);
    }
    public function copy_directory($src, $dst)
    {
        $dir = opendir($src);
        if(!file_exists($dst))
            @mkdir($dst);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->copy_directory($src . '/' . $file, $dst . '/' . $file);
                } elseif(!file_exists($dst . '/' . $file)) {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }
    public function hookDisplayBackOfficeHeader()
    {
        $controller = Tools::getValue('controller');
        if ($controller == 'AdminOrders' || $controller == 'adminorders') {
            $this->context->smarty->assign('ets_odm_can_delete_order',$this->checkAccess('delete'));
            $this->context->controller->addCSS($this->_path . 'views/css/order.css');
            if(version_compare(_PS_VERSION_, '1.7.7.0', '>='))
            {
                $_conf= array();
                $_conf['1111'] = $this->l('Order is moved to Trash successfully, you can restore the order from Trash or delete it forever.') . $this->displayText($this->l('View Trash'),'a',array('href'=>$this->context->link->getAdminLink('AdminOrders').'&viewtrash=1','title'=>$this->l('View Trash')));
                $_conf['111'] = $this->l('Deleted order successfully');
                $_conf['2222'] = $this->l('Order restored successfully.') . ' <a href="' . $this->context->link->getAdminLink('AdminOrders') . '" title="' . $this->l('View Order list') . '">' . $this->l('View Order list') . '</a>';;
                $_conf['3333'] = $this->l('Emptied trash successfully');
                $_conf['333'] = $this->l('Deleted order status successfully');
                $_conf['3'] = $this->l('Duplicated order successfully');
                if(($conf = (int)Tools::getValue('conf')) && isset($_conf[$conf]))
                {
                    $this->context->controller->confirmations = $_conf[$conf];
                }
                $this->addTwigVar('ets_odm_link_order_delete',$this->context->link->getAdminLink('AdminOrderDeleteManager').(Tools::isSubmit('viewtrash') ?'&deleteOrder':'&deleteordertrash'));
                $this->addTwigVar('ets_odm_link_order_restoreorder',$this->context->link->getAdminLink('AdminOrderDeleteManager').'&restoreorder');
                $this->addTwigVar('ets_odm_link_order_viewtrash',$this->context->link->getAdminLink('AdminOrders').'&viewtrash');
                $this->addTwigVar('ets_omd_is_viewtrash',Tools::isSubmit('viewtrash'));
                $this->addTwigVar('Trash_orders_text',$this->l('Trash orders'));
                $this->addTwigVar('Delete_selected_orders_text',$this->l('Delete selected orders'));
                $this->addTwigVar('Restore_selected_orders_text', $this->l('Restore selected orders'));
                $this->addTwigVar('ets_odm_can_delete_order',$this->checkAccess('delete'));
                $this->addTwigVar('ets_odm_link_list_orders',$this->context->link->getAdminLink('AdminOrders'));
            }
        }
    }
    public function getSfContainer()
    {
        if(!class_exists('\PrestaShop\PrestaShop\Adapter\SymfonyContainer'))
        {
            $kernel = null;
            try{
                $kernel = new AppKernel('prod', false);
                $kernel->boot();
                return $kernel->getContainer();
            }
            catch (Exception $ex){
                return null;
            }
        }
        $sfContainer = call_user_func(array('\PrestaShop\PrestaShop\Adapter\SymfonyContainer', 'getInstance'));
        return $sfContainer;
    }
    public function addTwigVar($key, $value)
    {
        if($sfContainer = $this->getSfContainer())
        {
            $sfContainer->get('twig')->addGlobal($key, $value);
        }
    }
    public function hookActionOrderGridQueryBuilderModifier($params)
    {
        if(isset($params['search_query_builder']) && $params['search_query_builder'] && isset($params['count_query_builder']) && $params['count_query_builder'])
        {
            
            $searchQueryBuilder = &$params['search_query_builder'];
            $countQueryBuilder = &$params['count_query_builder'];
            if(Tools::isSubmit('viewtrash'))
            {
                $countQueryBuilder->andWhere('o.deleted=1');
                $searchQueryBuilder->andWhere('o.deleted=1');
            }
            else
            {
                $countQueryBuilder->andWhere('o.deleted!=1');
                $searchQueryBuilder->andWhere('o.deleted!=1');
            }
        }
    }
    public function hookActionOrderGridDefinitionModifier($params)
    {
        $defination = &$params['definition'];
        $gridActions = $defination->getGridActions();
        $columns = $defination->getColumns();
        $columns->add((new PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn('actions'))
            ->setName($this->l('Actions'))
            ->setOptions([
                'actions' => $this->getRowActions(),
            ])
        );
        
        if(!Tools::isSubmit('viewtrash'))
        {
            $gridActions->add(
                    (new PrestaShop\PrestaShop\Core\Grid\Action\Type\LinkGridAction('viewtrash'))
                        ->setName($this->l('Trash orders'))
                        ->setIcon('delete')
                        ->setOptions([
                            'route' => 'admin_orders_viewtrash',
                        ])
            );
            
        }
        $defination->setGridActions($gridActions);
        $bulkActions = $defination->getBulkActions();
        $bulkActions->add((new PrestaShop\PrestaShop\Core\Grid\Action\Bulk\Type\SubmitBulkAction('delete_all_selected'))
            ->setName($this->l('Delete selected orders'))
            ->setOptions([
                'submit_route' => 'admin_order_bulk_delete',
            ]) 
        );
        $defination->setBulkActions($bulkActions);
    }
    private function getRowActions()
    {
        $rowActions =  (new PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection())
        ->add(
            (new PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction('print_invoice'))
                ->setName($this->l('View invoice'))
                ->setIcon('receipt')
                ->setOptions([
                    'accessibility_checker' => new PrestaShop\PrestaShop\Core\Grid\Action\Row\AccessibilityChecker\PrintInvoiceAccessibilityChecker(),
                    'route' => 'admin_orders_generate_invoice_pdf',
                    'route_param_name' => 'orderId',
                    'route_param_field' => 'id_order',
                    'use_inline_display' => true,
                ])
        )
        ->add(
            (new PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction('print_delivery_slip'))
                ->setName($this->l('View delivery slip'))
                ->setIcon('local_shipping')
                ->setOptions([
                    'accessibility_checker' => new PrestaShop\PrestaShop\Core\Grid\Action\Row\AccessibilityChecker\PrintDeliverySlipAccessibilityChecker(),
                    'route' => 'admin_orders_generate_delivery_slip_pdf',
                    'route_param_name' => 'orderId',
                    'route_param_field' => 'id_order',
                    'use_inline_display' => true,
                ])
        )
        ->add(
            (new PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction('view'))
                ->setName($this->l('View'))
                ->setIcon('zoom_in')
                ->setOptions([
                    'route' => 'admin_orders_view',
                    'route_param_name' => 'orderId',
                    'route_param_field' => 'id_order',
                    'use_inline_display' => false,
                    'clickable_row' => true,
                ])
        );
        if(Tools::isSubmit('viewtrash'))
        {
            if ($this->checkAccess('delete'))
                $rowActions->add((new PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction('delete'))
                    ->setName($this->l('Delete order'))
                    ->setIcon('delete')
                    ->setOptions([
                        'route' => 'admin_orders_delete',
                        'route_param_name' => 'orderId',
                        'route_param_field' =>'id_order',
                        'confirm_message' => Tools::isSubmit('viewtrash') ? $this->l('Do you want to delete this order?'): $this->l('Order will be removed from list and moved to Trash, do you want to remove it?'),
                    ])
                );
            if ($this->checkAccess('update'))
                $rowActions->add((new PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction('restore'))
                    ->setName($this->l('Restore'))
                    ->setIcon('refresh')
                    ->setOptions([
                        'route' => 'admin_orders_restore',
                        'route_param_name' => 'orderId',
                        'route_param_field' =>'id_order',
                    ])
                );
        }
        else
        {
            if ($this->checkAccess('update'))
            {
                $rowActions->add(
                    (new PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction('edit'))
                    ->setName('Edit')
                    ->setIcon('edit')
                    ->setOptions([
                        'route' => 'admin_orders_edit',
                        'route_param_name' => 'orderId',
                        'route_param_field' => 'id_order',
                    ])
                );
            }
            if($this->checkAccess('delete'))
            {
                $rowActions->add((new PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction('delete'))
                    ->setName($this->l('Delete order'))
                    ->setIcon('delete')
                    ->setOptions([
                        'route' => 'admin_orders_delete',
                        'route_param_name' => 'orderId',
                        'route_param_field' =>'id_order',
                        'confirm_message' => Tools::isSubmit('viewtrash') ? $this->l('Do you want to delete this order?'): $this->l('Order will be removed from list and moved to Trash, do you want to remove it?'),
                    ])
                );
            }
        } 
        return $rowActions;
    }
    public function checkAccess($action)
    {
        if ($this->context->employee->id_profile==1)
            return true;
        else {
            if($this->is17)
            {
                $slug = 'ROLE_MOD_TAB_ADMINORDERS_';
                $id_authorization_role = Db::getInstance()->getValue('SELECT id_authorization_role FROM `' . _DB_PREFIX_ . 'authorization_role` WHERE slug ="' . pSQL($slug . Tools::strtoupper($action)) . '"');
                return Db::getInstance()->getValue('SELECT id_profile FROM `' . _DB_PREFIX_ . 'access` WHERE id_profile ="' . (int)$this->context->employee->id_profile . '" AND id_authorization_role=' . (int)$id_authorization_role) ? true : false;
            }
            else
            {
                $permistions = array('read'=>'view', 'create'=>'add', 'update'=>'edit', 'delete'=>'delete');
                $tabId = Tab::getIdFromClassName('AdminOrders');
                return Db::getInstance()->getValue('SELECT `'.pSQL($permistions[$action]).'` FROM `'._DB_PREFIX_.'access` WHERE id_profile='.(int)$this->context->employee->id_profile.' AND id_tab='.(int)$tabId);
            }
        }
    }
    public function _postOrder()
    {
        $this->context->controller->_conf['1111'] = $this->l('Order is moved to Trash successfully, you can restore the order from Trash or delete it forever.') .$this->displayText($this->l('View Trash'),'a',array('href'=>$this->context->link->getAdminLink('AdminOrders').'&viewtrash=1','title'=>$this->l('View Trash')));
        $this->context->controller->_conf['2222'] = $this->l('Order restored successfully.') . $this->displayText($this->l('View Order list'),'a',array('href'=>$this->context->link->getAdminLink('AdminOrders'),'title'=>$this->l('View Order list')));
        $this->context->controller->_conf['3333'] = $this->l('Emptied trash successfully');
        $this->context->controller->_conf['333'] = $this->l('Deleted order status successfully');
        if (version_compare(_PS_VERSION_, '1.7', '<') && Tools::isSubmit('vieworder'))
            $this->context->controller->override_folder = '../../../../../override/controllers/admin/templates/orders/';
        if (Tools::isSubmit('viewtrash')) {
            if ($this->checkAccess('delete'))
                $this->context->controller->addRowAction('delete');
            if ($this->checkAccess('update'))
               $this->context->controller->addRowAction('restore');
            $this->context->controller->page_header_toolbar_title = $this->l('Trash');
        } else {
            if ($this->checkAccess('update'))
                $this->context->controller->addRowAction('edit');
            if ($this->checkAccess('delete'))
                $this->context->controller->addRowAction('delete');
        }
        if (Tools::isSubmit('deleteordertrash') && ($id_order = (int)Tools::getValue('id_order'))) {
            if ($this->checkAccess('delete')) {
                Ets_delete_order_defines::deleteordertrash($id_order);
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminOrders') . '&conf=1111');
            } else
                $this->_errors[] = $this->l('You do not have permission to delete this.');
        }
        if(version_compare(_PS_VERSION_, '1.7.7.0', '>='))
        {
            if (Tools::isSubmit('deleteOrder') && ($id_order = (int)Tools::getValue('id_order'))) {
                if ($this->checkAccess('delete')) {
                    $order = new Order($id_order);
                    if($order->delete())
                        Tools::redirectAdmin($this->context->link->getAdminLink('AdminOrders') . '&viewtrash=1&conf=111');
                } else
                    $this->_errors[] = $this->l('You do not have permission to delete this.');
            }
        }
        if (Tools::isSubmit('restoreorder') && ($id_order = (int)Tools::getValue('id_order'))) {
            Ets_delete_order_defines::restoreorder($id_order);
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminOrders') . '&viewtrash=1&conf=2222');
        } 
        if (Tools::isSubmit('delete_all_order') || isset($this->bulk_orders['delete_all_order'])) {
            if($this->checkAccess('delete'))
            {
                if (($orders = Tools::getValue('orderBox',isset($this->bulk_orders['order_orders_bulk']) ? $this->bulk_orders['order_orders_bulk'] : false)) && self::validateArray($orders,'isInt') ) {
                    $conf = 1;
                    if(!Tools::isSubmit('viewtrash'))
                    {
                        Ets_delete_order_defines::deleteordertrash($orders);
                        $conf = 1111;
                    }
                    else
                    {
                        foreach ($orders as $order) {
                            $order_class = new Order($order);
                                $order_class->delete();
                                $conf = 1;
                        }
                    }
                    Tools::redirectAdmin($this->context->link->getAdminLink('AdminOrders') . '&conf=' . $conf . (Tools::isSubmit('viewtrash') ? '&viewtrash=1' : ''));
                }
                $this->_errors[] = $this->l('No order selected');
            }
            else
                $this->_errors[] = $this->l('You do not have permission to delete this order');
        }
        if (Tools::isSubmit('restore_all_order') || isset($this->bulk_orders['restore_all_order'])) {
            if (($orders = Tools::getValue('orderBox',isset($this->bulk_orders['order_orders_bulk']) ? $this->bulk_orders['order_orders_bulk'] : false)) && self::validateArray($orders,'isInt') ) {
                Ets_delete_order_defines::restoreorder($orders);
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminOrders') . '&viewtrash=1&conf=2222');
            } else
                $this->_errors[] = $this->l('No order selected');
        }     
        if($this->_errors)
            $this->context->controller->errors = $this->_errors;
    }
    public function displayText($content,$tag,$attr_datas= array())
    {
        $this->smarty->assign(array(
            'content' => $content,
            'tag' => $tag,
            'attr_datas'=> $attr_datas,
        ));
        return $this->display(__FILE__, 'html.tpl');
    }
    public static function validateArray($array,$validate='isCleanHtml')
    {
        if(!is_array($array))
            return true;
        if(method_exists('Validate',$validate))
        {
            if($array && is_array($array))
            {
                $ok= true;
                foreach($array as $val)
                {
                    if(!is_array($val))
                    {
                        if($val && !Validate::$validate($val))
                        {
                            $ok= false;
                            break;
                        }
                    }
                    else
                        $ok = self::validateArray($val,$validate);
                }
                return $ok;
            }
        }
        return true;
    }
    public function hookActionDispatcherBefore()
    {
        if(version_compare(_PS_VERSION_, '1.7.7.0', '>='))
        {
            if(isset($this->bulk_orders['restore_all_order']) || isset($this->bulk_orders['delete_all_order']))
                $this->_postOrder();
        }
    }
    public function displayRestoreLink($currentIndex,$identifier,$id,$table,$token)
    {
        $href = $currentIndex . '&' . $identifier . '=' . $id . '&restore' . $table . '&token=' .$token;
        $this->context->smarty->assign(
            array(
                $identifier => $id,
                'href' => $href,
                'action'=> $this->l('Restore'),
            )
        );
        return $this->display(__FILE__,'list_action_restore.tpl');
    }
}