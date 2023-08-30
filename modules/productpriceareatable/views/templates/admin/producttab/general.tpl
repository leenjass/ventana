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

<div id="form-ppat-generaL-edit" class="form-wrapper PPAT-form-wrapper" style="padding: 20px;">

	<input type="hidden" name="id_product" value="{$id_product|escape:'htmlall':'UTF-8'}">

	<div class="alert alert-danger mp-errors" style="display: none"></div>

	<div class="form-group row">
		<label class="control-label col-lg-2">
			{l s='Enabled for this product' mod='productpriceareatable'}
		</label>
		<div class="col-lg-10">
			<input data-toggle="switch" class="" id="enabled" name="enabled" data-inverse="true" type="checkbox" value="1" {if $ppat_product_model->enabled}checked{/if} />
		</div>
	</div>

	<div class="form-group row">
		<label class="control-label col-lg-2">
			{l s='Min' mod='productpriceareatable'} {$row_name|escape:'htmlall':'UTF-8'}
		</label>
		<div class="col-lg-2">
			<input class="form-control" id="min_row" name="min_row" type="textbox" value="{$ppat_product_model->min_row|escape:'htmlall':'UTF-8'}" />
		</div>
	</div>

	<div class="form-group row">
		<label class="control-label col-lg-2">
			{l s='Max' mod='productpriceareatable'} {$row_name|escape:'htmlall':'UTF-8'}
		</label>
		<div class="col-lg-2">
			<input class="form-control" id="max_row" name="max_row" type="textbox" value="{$ppat_product_model->max_row|escape:'htmlall':'UTF-8'}" />
		</div>
	</div>

	<div class="form-group row">
		<label class="control-label col-lg-2">
			{l s='Min' mod='productpriceareatable'} {$col_name|escape:'htmlall':'UTF-8'}
		</label>
		<div class="col-lg-2">
			<input class="form-control" id="min_col" name="min_col" type="textbox" value="{$ppat_product_model->min_col|escape:'htmlall':'UTF-8'}" />
		</div>
	</div>

	<div class="form-group row">
		<label class="control-label col-lg-2">
			{l s='Max' mod='productpriceareatable'} {$col_name|escape:'htmlall':'UTF-8'}
		</label>
		<div class="col-lg-2">
			<input class="form-control" id="max_col" name="max_col" type="textbox" value="{$ppat_product_model->max_col|escape:'htmlall':'UTF-8'}" />
		</div>
	</div>


</div>

<button type="button" id="ppat-btn-general-save" class="btn btn-primary">{l s='Save' mod='productpriceareatable'}</button>

<script>
	$(document).ready(function () {
		prestaShopUiKit.init();
	});
</script>