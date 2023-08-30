{*
* 2007-2017 Musaffar
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
* needs please refer to http://www.prestashop.com for more information.
*
*  @author Musaffar Patel <musaffar.patel@gmail.com>
*  @copyright  2007-2017 Musaffar Patel
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Property of Musaffar Patel
*}

<ul class="nav nav-tabs" id="myTab" role="tablist">
	<li class="nav-item">
		<a class="nav-link active" data-toggle="tab" href="#ppat-general-tab" role="tab">{l s='General Options' mod='productpriceareatable'}</a>
	</li>
	<li class="nav-item">
		<a class="nav-link" data-toggle="tab" href="#ppat-prices-tab" role="tab">{l s='Price Tables' mod='productpriceareatable'}</a>
	</li>
</ul>

<div class="tab-content">
	<div class="tab-pane active" id="ppat-general-tab" role="tabpanel"></div>
	<div class="tab-pane" id="ppat-prices-tab" role="tabpanel"></div>
</div>

<script type="text/javascript" src="{$module_url|escape:'quotes':'UTF-8'}views/js/grid/pqgrid.min.js"></script>

<script>
	$(document).ready(function () {
		module_ajax_url_ppat = '{$module_ajax_url|escape:'quotes':'UTF-8'}';
		id_product = '{$id_product|escape:'quotes':'UTF-8'}';

		ppat_admin_producttab_general_controller = new PPATAdminProductTabGeneralController("#ppat-general-tab");
		ppat_admin_producttab_prices_controller = new PPATAdminProductTabPricesController("#ppat-prices-tab");
	});
</script>