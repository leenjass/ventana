<?php
/*
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2015 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

class FrontController extends FrontControllerCore
{

    protected function assignGeneralPurposeVariables() {

        $templateVars = array(
            'currency' => $this->getTemplateVarCurrency(),
            'customer' => $this->getTemplateVarCustomer(),
            'language' => $this->objectPresenter->present($this->context->language),
            'page' => $this->getTemplateVarPage(),
            'shop' => $this->getTemplateVarShop(),
            'urls' => $this->getTemplateVarUrls(),
            'configuration' => $this->getTemplateVarConfiguration(),
            'field_required' => $this->context->customer->validateFieldsRequiredDatabase(),
            'breadcrumb' => $this->getBreadcrumb(),
            'link' => $this->context->link,
            'time' => time(),
            'static_token' => Tools::getToken(false),
            'token' => Tools::getToken(),
        );

        // add cart presenter only for non-cart pages
        if ('cart' !== Dispatcher::getInstance()->getController()) {
            $templateVars[ 'cart'] = $this->cart_presenter->present($this->context->cart);
        }

        $modulesVariables = Hook::exec('actionFrontControllerSetVariables', [], null, true);

        if (is_array($modulesVariables)) {
            foreach ($modulesVariables as $moduleName => $variables) {
                $templateVars['modules'][$moduleName] = $variables;
            }
        }

        $this->context->smarty->assign($templateVars);

        Media::addJsDef(array(
            'prestashop' => $this->buildFrontEndObject($templateVars),
        ));
    }

}
