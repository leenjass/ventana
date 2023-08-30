<?php
/**
 * Login as Customer
 *
 *  @author    Rolige <www.rolige.com>
 *  @copyright 2011-2022 Rolige - All Rights Reserved
 *  @license   Proprietary and confidential
 */

namespace LoginAsCustomerNamespace\Controller\Admin;

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;

class LoginAsCustomerController extends FrameworkBundleAdminController
{
    public $ssl = true;

    public function loginAsCustomerAction($customerId)
    {
        $link = \Context::getContext()->link->getModuleLink('rg_loginascustomer', 'login', ['id_customer' => $customerId, 'xtoken' => \Tools::hash($customerId . date('YmdH'))]);
        \Tools::redirect($link);
    }
}
