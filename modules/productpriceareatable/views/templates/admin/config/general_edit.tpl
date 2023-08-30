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

<div id="ppat-unit-edit">

	<div id="form-ppat-unit-edit" class="form-wrapper ppat-form-wrapper" style="padding-left: 15px;">
		<h4>{l s='Edit Unit' mod='productpriceareatable'}</h4>

		<input name="id_ppat_unit" value="{if !empty($unit->id_ppat_unit)}{$unit->id_ppat_unit|escape:'html':'UTF-8'}{/if}" type="hidden" />

		<div class="form-group row">
			<div class="col-sm-12">
				<label>{l s='Display Name' mod='productpriceareatable'}</label>

				{foreach from=$languages item=language}
					<div class="translatable-field lang-{$language.id_lang|escape:'htmlall':'UTF-8'}" style="{if $language.id_lang eq $id_lang_default}display: block;{else}display:none;{/if}">
						<div class="col-lg-7">
							<input name="display_name_{$language.id_lang|escape:'htmlall':'UTF-8'}" id="display_name_{$language.id_lang|escape:'htmlall':'UTF-8'}" class="form-control"
								   value="{if !empty($unit->display_name[$language.id_lang])}{$unit->display_name[$language.id_lang]|escape:'html':'UTF-8'}{/if}" />
						</div>

						<div class="col-lg-2">
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
			<div class="col-sm-12">
				<label>{l s='Suffix' mod='productpriceareatable'}</label>

				{foreach from=$languages item=language}
					<div class="translatable-field lang-{$language.id_lang|escape:'htmlall':'UTF-8'}" style="{if $language.id_lang eq $id_lang_default}display: block;{else}display:none;{/if}">
						<div class="col-lg-7">
							<input name="suffix_{$language.id_lang|escape:'htmlall':'UTF-8'}" id="suffix_{$language.id_lang|escape:'htmlall':'UTF-8'}" class="form-control"
								   value="{if !empty($unit->suffix[$language.id_lang])}{$unit->suffix[$language.id_lang]|escape:'html':'UTF-8'}{/if}" />
						</div>

						<div class="col-lg-2">
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
			<div class="col-sm-12">
				<label>{l s='Display as' mod='productpriceareatable'}</label>
				<select name="type" id="type" class="form-control">
					<option value="dropdown">Dropdown</option>
					<option value="textbox">Textbox</option>
				</select>
			</div>
		</div>

		<button type="button" id="btn-ppat-edit-save" class="btn btn-primary">{l s='Save' mod='productpriceareatable'}</button>
		<button type="button" id="btn-ppat-edit-cancel" class="btn btn-primary-outline">{l s='Cancel' mod='productpriceareatable'}</button>


	</div>

</div>