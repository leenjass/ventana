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
*  @copyright  2015-2017 Musaffar Patel
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Property of Musaffar Patel
*}

{if $ppat_product->enabled == 1}
	<div id="ppat-widget">
		{if $table_options|@count gt 0}
			<div class="ppat-table-options-wrapper" style="{if $table_options|@count eq 1}display:none;{/if}">
				<label>{$ppat_product_option_label|escape:'htmlall':'UTF-8'}</label>
				<select name='ppat_id_option' class="form-control">
					{foreach from=$table_options item=option}
						<option value="{$option.id_option|escape:'htmlall':'UTF-8'}">{$option.option_text|escape:'htmlall':'UTF-8'}</option>
					{/foreach}
				</select>
			</div>
		{/if}

		<div class="values row">
			{if $units|@count gt 0}
				{foreach from=$units item=unit}
					<div class="ppat-unit-entry-wrapper col-xs-12 col-sm-6">
						{if $unit.type == 'dropdown' || $unit.type == ''}
							<label>{$unit.display_name|escape:'htmlall':'UTF-8'} ({$unit.suffix|escape:'htmlall'})</label>
							<select name="{$unit.name|escape:'htmlall':'UTF-8'}" class="form-control ppat-unit-entry {$unit.name|escape:'htmlall':'UTF-8'}">
							</select>
						{/if}
						{if $unit.type == 'textbox'}
							{if $unit.name eq 'row'}
								{assign var="value" value=$default_row}
							{else}
								{assign var="value" value=$default_col}
							{/if}
							<label>{$unit.display_name|escape:'htmlall':'UTF-8'}</label>
							<input type="text" name="{$unit.name|escape:'htmlall':'UTF-8'}" value="{$value|escape:'htmlall':'UTF-8'}" class="form-control ppat-unit-entry {$unit.name|escape:'htmlall':'UTF-8'}" style="margin-left:0px;">
						{/if}
						<div class="ppat-error">
						</div>
					</div>
				{/foreach}
			{/if}
		</div>
	</div>
{/if}

<script>
    ppat_product = {$ppat_product_json nofilter};
    ppat_price_matrix = {$ppat_price_matrix_json nofilter};
    ppat_table_options = {$table_options_json nofilter};
</script>
