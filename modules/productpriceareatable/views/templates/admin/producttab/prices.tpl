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

<div id="ppat-prices-wrapper" class="row">

	<!-- Start: Options -->
	<div id="ppat-options-wrapper" class="col-sm-6">
		<div class="form-group row" style="margin:20px 0;">
			<label class="control-label col-lg-2">
				{l s='Option Label' mod='productpriceareatable'}
			</label>
			<div id="form-ppat-options-label" class="col-lg-10">
				<input type="hidden" name="id_product" value="{$id_product|escape:'htmlall':'UTF-8'}"/>

				{foreach from=$languages item=language}
					<div class="translations tabbable">
						<div class="translationsFields tab-content ">
							<div class="tab-pane translation-field translation-label-{$language.iso_code|escape:'htmlall':'UTF-8'}">
								<input name="text_{$language.id_lang|escape:'htmlall':'UTF-8'}" id="text_{$language.id_lang|escape:'htmlall':'UTF-8'}" class="form-control"
									   value="{$option_label.text[$language.id_lang]|escape:'htmlall':'UTF-8'}" style="float: left; display: inline-block; width: auto;" />
							</div>
						</div>
					</div>
				{/foreach}
				<button type="button" id="ppat-btn-option-label-save" class="btn btn-primary-outline">{l s='save' mod='productpriceareatable'}</button>
			</div>
		</div>

		<!-- Options -->
		<div id="ppat-option-values">
			<h3>Option Based Price Tables</h3>

			<!-- Add option -->
			<div id="form-ppat-option-value" class="ppat-add-option-value">
				<input type="hidden" name="id_product" value="{$id_product|escape:'htmlall':'UTF-8'}"/>
				<div class="form-group row">
					<label class="control-label col-lg-3">
						{l s='Option Value' mod='productpriceareatable'}
					</label>

					{foreach from=$languages item=language}
						<div class="translations tabbable">
							<div class="translationsFields tab-content ">
								<div class="tab-pane translation-field translation-label-{$language.iso_code|escape:'htmlall':'UTF-8'}">
									<input name="option_text_{$language.id_lang|escape:'htmlall':'UTF-8'}" id="text_{$language.id_lang|escape:'htmlall':'UTF-8'}" class="form-control"
								   	value="" style="float: left; display: inline-block; width: auto;" />
								</div>
							</div>
						</div>
					{/foreach}

				</div>

				<button type="button" id="ppat-btn-option-value-add" class="btn btn-primary-outline">{l s='Add' mod='productpriceareatable'}</button>
			</div>
			<!-- / Add option -->

			<div id="ppat-options-list-wrapper">
			</div>

		</div>
		<!-- /Options -->

	</div>
	<!-- End: Options -->



	<!-- Start: Single Option -->

	<div id="ppat-single-option-wrapper" class="col-sm-6">
		<div id="ppat-table-wrapper">

		</div>
	</div>
	<!-- End: Single Option -->

</div>

<script>
	form.switchLanguage($('#form_switch_language').val());
</script>
