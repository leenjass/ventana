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

<div id="ppat-options-list">
	<table id="ppat-options-list-table" class="ui-sortable table">
		<thead>
		<tr class="nodrag nodrop">
			<th><span class="title_box">{l s='Option Value' mod='productpriceareatable'}</span></th>
			<th><span class="title_box">{l s='Action' mod='productpriceareatable'}</span></th>
			<th><span class="title_box">{l s='Position' mod='productpriceareatable'}</span></th>
		</tr>
		</thead>
		<tbody>
			{foreach from=$options item=option}
				<tr class="ppat-table-option" data-id_option="{$option->id_option|intval}">
					<td><a href="">{$option->option_text|escape:'htmlall':'UTF-8'}</a></td>
					<td>
						<a href="#delete" data-id_option="{$option->id_option|intval}" class="ppat-option-delete"><i class="material-icons">delete forever</i></a>
					</td>
					<td>
						<i class="material-icons" style="cursor: pointer">swap_vert</i>
					</td>
				</tr>
			{/foreach}
		</tbody>
	</table>
</div>