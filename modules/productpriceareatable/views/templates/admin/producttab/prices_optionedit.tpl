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

<div id="ppat-option-edit-form-wrapper" style="padding: 20px;">

	<div class="form-group row">
		<h2>{l s='Edit Option' mod='productpriceareatable'} : {$ppat_product_option->option_text[$id_lang_default]|escape:'htmlall':'UTF-8'}</h2>
	</div>

	<div class="form-group row">
		<input type="hidden" name="id_option" id="id_option" value="{$id_option|escape:'htmlall':'UTF-8'}" />
		<input type="hidden" name="id_product" id="id_product" value="{$id_product|escape:'htmlall':'UTF-8'}"/>
		<label class="control-label col-lg-4">
			{l s='Enabled' mod='productpriceareatable'}
		</label>
		<div class="col-lg-8">
			<input data-toggle="switch" class="" id="enabled" name="enabled" data-inverse="true" type="checkbox" value="1" {if $ppat_product_option->enabled}checked="checked"{/if} />
		</div>
	</div>

	<div class="form-group row">
		<label class="control-label col-lg-4">
			{l s='Option Value' mod='productpriceareatable'}
		</label>
		<div class="col-lg-8">
			{foreach from=$languages item=language}
					<div class="translatable-field lang-{$language.id_lang|escape:'htmlall':'UTF-8'}" style="{if $language.id_lang eq $id_lang_default}display: block;{else}display:none;{/if}">
						<div class="col-lg-7" style="display: inline-block">
							<input name="option_text_{$language.id_lang|escape:'htmlall':'UTF-8'}"
								   id="option_text_{$language.id_lang|escape:'htmlall':'UTF-8'}" class="form-control"
								   value="{$ppat_product_option->option_text[$language.id_lang]|escape:'htmlall':'UTF-8'}" style="width: 100%"/>
						</div>

						<div class="col-lg-2" style="display: inline-block">
							<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" tabindex="-1">
								{$language.iso_code|escape:'htmlall':'UTF-8'}
							</button>
							<ul class="dropdown-menu">
								{foreach from=$languages item=language_dropdown}
									<li>
										<a href="javascript:hideOtherLanguage({$language_dropdown.id_lang|escape:'htmlall':'UTF-8'});">{$language_dropdown.name|escape:'htmlall':'UTF-8'}</a>
									</li>
								{/foreach}
							</ul>
						</div>
					</div>
			{/foreach}
		</div>
	</div>

	<div class="form-group row">
		<label class="control-label col-lg-4">
			{l s='Rounding mode for matching closest price in table' mod='productpriceareatable'}
		</label>
		<div class="col-lg-8">
			<select id="lookup_rounding_mode" name="lookup_rounding_mode" class="form-control">
				<option value="up" {if $ppat_product_option->lookup_rounding_mode eq "up"}selected="selected"{/if}>{l s='Up' mod='productpriceareatable'}</option>
				<option value="down" {if $ppat_product_option->lookup_rounding_mode eq "down"}selected="selected"{/if}>{l s='Down' mod='productpriceareatable'}</option>
			</select>
		</div>
	</div>

	<div class="form-group row">
		<label class="control-label col-lg-4">
			{l s='Default Values' mod='productpriceareatable'}
		</label>
		<div class="col-lg-8">
			<label style="display: inline-block">Height</label>
			<input id="default_row" name="default_row" type="text" class="form-control" value="{$ppat_product_option->default_row|escape:'htmlall':'UTF-8'}" style="width: 100px; display: inline-block">

			<label style="display: inline-block; margin-left: 20px;">Width</label>
			<input id="default_col" name="default_col" type="text" class="form-control" value="{$ppat_product_option->default_col|escape:'htmlall':'UTF-8'}" style="width: 100px; display: inline-block">
		</div>
	</div>

	<div class="form-group row">
		<button id="ppat-button-option-edit-save" class="btn btn-primary">{l s='Save' mod='productpriceareatable'}</button>
	</div>
</div>

<script>
	form.switchLanguage($('#form_switch_language').val());
</script>