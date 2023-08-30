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
	<li class="nav-item active">
		<a class="nav-link" data-toggle="tab" href="#ppat-general-tab" role="tab">{l s='General' mod='productpriceareatable'}</a>
	</li>
</ul>

<div class="tab-content">
	<div class="ppat-breadcrumb"></div>
	<div class="tab-pane active" id="ppat-general-tab" role="tabpanel"></div>
</div>

<script>
	$(document).ready(function () {
		breadcrumb = new Breadcrumb(".ppat-breadcrumb", "#ppat-general-tab");
		module_config_url = '{$module_config_url|escape:'quotes':'UTF-8'}';
		ppat_general_controller = new PPATAdminConfigGeneralController('#ppat-general-tab');
	});
</script>