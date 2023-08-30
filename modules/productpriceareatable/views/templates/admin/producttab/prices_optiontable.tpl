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

<div id="ppat-option-edit-wrapper"></div>

<div id="ppat-grid-wrapper">
	<input type="hidden" name="id_option" id="id_option" value="{$id_option|escape:'htmlall':'UTF-8'}" />
	<div id="grid_array"></div>
</div>

<div id="ppat-import" style="display: none;">
	<input type="file" id="ppat-csv" name="file"/>
</div>

<button id="ppat-btn-grid-save" class="btn btn-primary ">{l s='Save' mod='productpriceareatable'}</button>

<div id="form-add-col">
    <div class="form-add-new">
        Column Title: <input type="text" name="col" value="" class="form-control"><br>
    </div>
</div>

<script>
	$(document).ready(function () {
		data_columns = {$data_columns nofilter};
		data_grid = {$data_grid nofilter};
		row_title = '{$row_title|escape:'htmlall':'UTF-8'}';
		col_title = '{$col_title|escape:'htmlall':'UTF-8'}';
		id_option = "{$id_option|escape:'htmlall':'UTF-8'}";

		ppat_grid_controller = new PPATGridController();
	});
</script>