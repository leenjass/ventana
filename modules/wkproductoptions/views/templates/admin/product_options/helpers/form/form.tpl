{*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License version 3.0
* that is bundled with this package in the file LICENSE.txt
* It is also available through the world-wide-web at this URL:
* https://opensource.org/licenses/AFL-3.0
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your
* needs please refer to CustomizationPolicy.txt file inside our module for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
*}

<div class="panel">
	<div class="panel-heading">
		{if isset($id_option)}
			{l s='Update product options' mod='wkproductoptions'}
		{else}
			{l s='Add product options' mod='wkproductoptions'}
		{/if}
	</div>
	<form id="{$table|escape:'html':'UTF-8'}_form" class="defaultForm form-horizontal"
		action="{if isset($id_option)}{$current|escape:'html':'UTF-8'}&update{$table|escape:'html':'UTF-8'}&id_wk_product_options_config={$id_option|escape:'html':'UTF-8'}&token={$token|escape:'html':'UTF-8'} {else}
		{$current|escape:'html':'UTF-8'}&add{$table|escape:'html':'UTF-8'}&token={$token|escape:'html':'UTF-8'}{/if}"
		method="post" enctype="multipart/form-data">
		<input type="hidden" name="id_wk_product_options_config" value="{if isset($id_option)}{$id_option|escape:'html':'UTF-8'}{/if}">
		<div class="form-group">
			<div class="row">
				<label class="col-lg-3 control-label">
					<span class="label-tooltip" data-toggle="tooltip" data-html="true" title="{l s='Select type of option' mod='wkproductoptions'}">
						{l s='Option type' mod='wkproductoptions'}
					</span>
				</label>
				<div class="col-lg-6">
					{if isset($option_info)}
						<input type="hidden" id="option_type" name="option_type" value="{if $option_info['option_type'] == $WK_PRODUCT_OPTIONS_TEXT}{$WK_PRODUCT_OPTIONS_TEXT|escape:'html':'UTF-8'}{elseif $option_info['option_type'] == $WK_PRODUCT_OPTIONS_DROPDOWN}{$WK_PRODUCT_OPTIONS_DROPDOWN|escape:'html':'UTF-8'}{elseif $option_info['option_type'] == $WK_PRODUCT_OPTIONS_CHECKBOX}{$WK_PRODUCT_OPTIONS_CHECKBOX|escape:'html':'UTF-8'}{elseif $option_info['option_type'] == $WK_PRODUCT_OPTIONS_RADIO}{$WK_PRODUCT_OPTIONS_RADIO|escape:'html':'UTF-8'}{elseif $option_info['option_type'] == $WK_PRODUCT_OPTIONS_FILE}{$WK_PRODUCT_OPTIONS_FILE|escape:'html':'UTF-8'}{elseif $option_info['option_type'] == $WK_PRODUCT_OPTIONS_TEXTAREA}{$WK_PRODUCT_OPTIONS_TEXTAREA|escape:'html':'UTF-8'}{elseif $option_info['option_type'] == $WK_PRODUCT_OPTIONS_DATE}{$WK_PRODUCT_OPTIONS_DATE|escape:'html':'UTF-8'}{elseif $option_info['option_type'] == $WK_PRODUCT_OPTIONS_TIME}{$WK_PRODUCT_OPTIONS_TIME|escape:'html':'UTF-8'}{elseif $option_info['option_type'] == $WK_PRODUCT_OPTIONS_DATETIME}{$WK_PRODUCT_OPTIONS_DATETIME|escape:'html':'UTF-8'}{/if}">
						<input type="text" value="{if $option_info['option_type'] == $WK_PRODUCT_OPTIONS_TEXT}{l s='Text' mod='wkproductoptions'}{elseif $option_info['option_type'] == $WK_PRODUCT_OPTIONS_DROPDOWN} {l s='Dropdown' mod='wkproductoptions'}{elseif $option_info['option_type'] == $WK_PRODUCT_OPTIONS_CHECKBOX}{l s='Checkbox' mod='wkproductoptions'}{elseif $option_info['option_type'] == $WK_PRODUCT_OPTIONS_RADIO}{l s='Radio' mod='wkproductoptions'}{elseif $option_info['option_type'] == $WK_PRODUCT_OPTIONS_FILE}{l s='Image file' mod='wkproductoptions'}{elseif $option_info['option_type'] == $WK_PRODUCT_OPTIONS_TEXTAREA}{l s='Textarea' mod='wkproductoptions'}{elseif $option_info['option_type'] == $WK_PRODUCT_OPTIONS_DATE}{l s='Date' mod='wkproductoptions'}{elseif $option_info['option_type'] == $WK_PRODUCT_OPTIONS_TIME}{l s='Time' mod='wkproductoptions'}{elseif $option_info['option_type'] == $WK_PRODUCT_OPTIONS_DATETIME}{l s='Datetime' mod='wkproductoptions'}{/if}" disabled="" >
					{else}
						<select id="option_type" name="option_type" class="form-control">
							{if isset($option_types)}
								{foreach $option_types as $option}
									<option {if isset($smarty.post.option_type) && $smarty.post.option_type == {$option.id_option|escape:'html':'UTF-8'}}selected="selected"{/if} value="{$option.id_option|escape:'html':'UTF-8'}">{$option.name|escape:'htmlall':'UTF-8'}</option>
								{/foreach}
							{/if}
						</select>
					{/if}
				</div>
			</div>
		</div>
		<div class="form-group wk_user_input">
			<div class="row">
				<label class="col-lg-3 control-label">
					<span class="title_box">
						<span class="label-tooltip" data-toggle="tooltip" title="" data-original-title="{l s='If yes, then an input field will be displayed on product page to take user input from customer for given field.' mod='wkproductoptions'}">
						{l s='Take user input' mod='wkproductoptions'}
						</span>
					</span>
				</label>
				<div class="col-lg-6">
					{if isset($smarty.post.user_input)}
						{assign var="user_input" value="`$smarty.post.user_input`"}
					{elseif isset($option_info.user_input)}
						{assign var="user_input" value="`$option_info.user_input`"}
					{else}
					{assign var="user_input" value="1"}
					{/if}
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" {if isset($user_input) && ($user_input == '1')}checked="checked" {/if}value="1" id="user_inputOn" name="user_input">
						<label for="user_inputOn">{l s='Yes' mod='wkproductoptions'}</label>
						<input type="radio" value="0" {if isset($user_input) && ($user_input == '0')}checked="checked" {/if} id="user_inputOff" name="user_input">
						<label for="user_inputOff">{l s='No' mod='wkproductoptions'}</label>
						<a class="slide-button btn"></a>
					</span>
				</div>
			</div>
		</div>
        <div class="form-group">
			<div class="row">
				<label class="col-lg-3 control-label required">
					<span class="title_box">
						<span class="label-tooltip" data-toggle="tooltip" title="" data-original-title="{l s='This will be visible to admin during option assignment from product page.' mod='wkproductoptions'}">
						{l s='Option name' mod='wkproductoptions'}
						</span>
					</span>
				</label>
				<div class="row">
                    <div class="col-lg-6">
                        {foreach from=$languages item=language}
							{assign var="option_name_smarty" value="option_name_`$language.id_lang`"}
                            <input type="text"
							id="option_name_{$language.id_lang|escape:'html':'UTF-8'}"
							name="option_name_{$language.id_lang|escape:'html':'UTF-8'}"
                            class="form-control default_value_all"
                            value="{if isset($smarty.post.$option_name_smarty)}{$smarty.post.$option_name_smarty|escape:'htmlall':'UTF-8'}{elseif isset($option_name)}{$option_name[$language.id_lang]|escape:'html':'UTF-8'}{/if}"
                            {if $current_lang.id_lang != $language.id_lang}style="display:none;"{/if}
							autocomplete="off" />
                        {/foreach}
                    </div>
                    {if $total_languages > 1}
                        <div class="col-lg-2">
                            <button type="button" class="btn btn-default dropdown-toggle default_value_lang_btn" data-toggle="dropdown">
         						{$current_lang.iso_code|escape:'html':'UTF-8'}
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                {foreach from=$languages item=language}
                                    <li>
                                        <a href="javascript:void(0)" onclick="showExtraLangField('{$language.iso_code|escape:'html':'UTF-8'}', {$language.id_lang|escape:'html':'UTF-8'});">{$language.name|escape:'html':'UTF-8'}</a>
                                    </li>
                                {/foreach}
                            </ul>
                        </div>
                    {/if}
                </div>
			</div>
		</div>
		<div class="form-group">
			<div class="row">
				<label class="col-lg-3 control-label required">
					<span class="title_box">
						<span class="label-tooltip" data-toggle="tooltip" title="" data-original-title="{l s='This will be visible to customer during option selection from product page.' mod='wkproductoptions'}">
						{l s='Display name' mod='wkproductoptions'}
						</span>
					</span>
				</label>
				<div class="row">
                    <div class="col-lg-6">
                        {foreach from=$languages item=language}
							{assign var="display_name_smarty" value="display_name_`$language.id_lang`"}
                            <input type="text"
							id="display_name_{$language.id_lang|escape:'html':'UTF-8'}"
							name="display_name_{$language.id_lang|escape:'html':'UTF-8'}"
                            class="form-control default_value_all"
                            value="{if isset($smarty.post.$display_name_smarty)}{$smarty.post.$display_name_smarty|escape:'htmlall':'UTF-8'}{elseif isset($display_name)}{$display_name[$language.id_lang]|escape:'html':'UTF-8'}{/if}"
                            {if $current_lang.id_lang != $language.id_lang}style="display:none;"{/if}
							autocomplete="off" />
                        {/foreach}
                    </div>
                    {if $total_languages > 1}
                        <div class="col-lg-2">
                            <button type="button" class="btn btn-default dropdown-toggle default_value_lang_btn" data-toggle="dropdown">
         						{$current_lang.iso_code|escape:'html':'UTF-8'}
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                {foreach from=$languages item=language}
                                    <li>
                                        <a href="javascript:void(0)" onclick="showExtraLangField('{$language.iso_code|escape:'html':'UTF-8'}', {$language.id_lang|escape:'html':'UTF-8'});">{$language.name|escape:'html':'UTF-8'}</a>
                                    </li>
                                {/foreach}
                            </ul>
                        </div>
                    {/if}
                </div>
			</div>
		</div>
		<div class="form-group" id="wk_option_placeholder">
			<div class="row">
				<label class="col-lg-3 control-label">
					<span class="title_box">
						<span class="label-tooltip" data-toggle="tooltip" title="" data-original-title="{l s='This will be visible to customer as a placeholder.' mod='wkproductoptions'}">
						{l s='Placeholder' mod='wkproductoptions'}
						</span>
					</span>
				</label>
				<div class="row">
                    <div class="col-lg-6">
                        {foreach from=$languages item=language}
							{assign var="placeholder_smarty" value="placeholder_`$language.id_lang`"}
                            <input type="text"
							id="placeholder_{$language.id_lang|escape:'html':'UTF-8'}"
							name="placeholder_{$language.id_lang|escape:'html':'UTF-8'}"
                            class="form-control default_value_all"
                            value="{if isset($smarty.post.$placeholder_smarty)}{$smarty.post.$placeholder_smarty|escape:'htmlall':'UTF-8'}{elseif isset($placeholder)}{$placeholder[$language.id_lang]|escape:'html':'UTF-8'}{/if}"
                            {if $current_lang.id_lang != $language.id_lang}style="display:none;"{/if}
							autocomplete="off" />
                        {/foreach}
                    </div>
                    {if $total_languages > 1}
                        <div class="col-lg-2">
                            <button type="button" class="btn btn-default dropdown-toggle default_value_lang_btn" data-toggle="dropdown">
         						{$current_lang.iso_code|escape:'html':'UTF-8'}
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                {foreach from=$languages item=language}
                                    <li>
                                        <a href="javascript:void(0)" onclick="showExtraLangField('{$language.iso_code|escape:'html':'UTF-8'}', {$language.id_lang|escape:'html':'UTF-8'});">{$language.name|escape:'html':'UTF-8'}</a>
                                    </li>
                                {/foreach}
                            </ul>
                        </div>
                    {/if}
                </div>
			</div>
		</div>
		<div class="form-group" id="wk_option_char_limit">
			<div class="row">
				<label class="col-lg-3 control-label required">
					<span class="title_box">
						<span class="label-tooltip" data-toggle="tooltip" title="" data-original-title="{l s='Maximum character limit for this option.' mod='wkproductoptions'}">
						{l s='Character limit' mod='wkproductoptions'}
						</span>
					</span>
				</label>
				<div class="row">
					<div class="col-lg-6">
						<div class="input-group">
							<input type="text"
								id="max_character_limit"
								name="max_character_limit"
								class="form-control"
								value="{if isset($smarty.post.max_character_limit)}{$smarty.post.max_character_limit|escape:'htmlall':'UTF-8'}{elseif isset($option_info)}{$option_info.text_limit|escape:'html':'UTF-8'}{/if}"
								autocomplete="off" />
							<div class="input-group-addon">{l s='Characters' mod='wkproductoptions'}</div>
						</div>
						<p class="help-block">{l s='Fill 0 for unlimited character limit.' mod='wkproductoptions'}<p>
					</div>
				</div>
			</div>
		</div>
		<div class="form-group wk_drop_option">
			<div class="row">
				<label class="col-lg-3 control-label required">
					<span class="title_box">
						<span class="label-tooltip" data-toggle="tooltip" title="" data-original-title="{l s='Enter value option values for dropdown/checkbox/radio.' mod='wkproductoptions'}">
						{l s='Options values' mod='wkproductoptions'}
						</span>
					</span>
				</label>
				{if isset($custom_option_drop) && ($custom_option_drop == 0)}
					<div class="col-lg-9">
						<div class="form-group wk_drop_option_custom">
							<div class="col-lg-12">
								<input type="hidden" name="max_options" id="max_options" value="1">
								<input type="hidden" name="count_options" id="count_options" value="1">
								<div id="dropdown_label_info">
									<div class="wk_option_container"></div>
									<div class="wk_option_container">
										<div class="form-group">
											<div class="col-lg-9">
												{foreach from=$languages item=language}
													<input type="text" value=""
													name="display_value_1_{$language.id_lang|escape:'html':'UTF-8'}"
													class="form-control dropdown_value_all dropdown_value_{$language.id_lang|escape:'html':'UTF-8'}"
													placeholder="{l s='display value' mod='wkproductoptions'}"
													{if $current_lang.id_lang != $language.id_lang}style="display:none;"{/if} />
												{/foreach}
											</div>
											{if $total_languages > 1}
												<div class="col-lg-2">
													<button type="button" class="btn btn-default dropdown-toggle dropdown_value_lang_btn" data-toggle="dropdown">
													{$current_lang.iso_code|escape:'html':'UTF-8'}
													<span class="caret"></span>
													</button>
													<ul class="dropdown-menu">
													{foreach from=$languages item=language}
														<li>
														<a href="javascript:void(0)" onclick="showExtraLangField('{$language.iso_code|escape:'html':'UTF-8'}', {$language.id_lang|escape:'html':'UTF-8'});">{$language.name|escape:'html':'UTF-8'}</a>
														</li>
													{/foreach}
													</ul>
												</div>
											{/if}
										</div>
										<div class="form-group">
											<div class="col-lg-12">
												<div class="row">
													<div class="col-md-3" title="{l s='Price' mod='wkproductoptions'}">
														<div class="input-group">
															<input type="text" name="option_value_price_1" class="option_value_price" value="0.00">
															<div class="input-group-addon group_wk_option-currency">{$currency_symbol|escape:'html':'UTF-8'}</div>
															<div class="input-group-addon group_wk_option-per hide">%</div>
														</div>
														<div class="help-block">{l s='Price impact' mod='wkproductoptions'}</div>
													</div>
													<div class="col-md-3">
														<select name='option_value_price_type_1' class="option_value_price_type" title="{l s='Price Type' mod='wkproductoptions'}">
															<option value='2'>{l s='Amount' mod='wkproductoptions'}</option>
															<option value='1'>{l s='Percentage' mod='wkproductoptions'}</option>
														</select>
														<div class="help-block">{l s='Price impact type' mod='wkproductoptions'}</div>
													</div>
													<div class="col-md-3">
														<select name ='option_value_tax_type_1' class='option_value_tax_type' title="{l s='Tax Type' mod='wkproductoptions'}">
															<option value='0'>{l s='Tax excluded' mod='wkproductoptions'}</option>
															<option value='1'>{l s='Tax included' mod='wkproductoptions'}</option>
														</select>
														<div class="help-block">{l s='Tax type' mod='wkproductoptions'}</div>
													</div>
													<div class="col-md-3">
														<button type="button" class="btn btn-default remove_dropdownvalue" data-id_option_val="0" data-remove-id="1" title="{l s='Delete option' mod='wkproductoptions'}"><i class="icon-trash"></i></button>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-lg-12">
										<a href="" id="add_another_dropdownvalue" class="btn btn-default"><i class="icon-plus-circle"></i> {l s='Add new option' mod='wkproductoptions'}</a>
										&nbsp;&nbsp;&nbsp;<input type="checkbox" id="use_same_as_above" name="use_same_as_above" value="1"> {l s='Use same configuration as previous option.' mod='wkproductoptions'}
									</div>
								</div>
							</div>
						</div>
					</div>
				{else}
					<div class="col-lg-9">
						<div class="form-group wk_drop_option_custom">
							<div class="col-lg-12">
								<input type="hidden" name="max_options" id="max_options" value="{if isset($max_option)}{$max_option}{else}1{/if}">
								<input type="hidden" name="count_options" id="count_options" value="{$all_saved_options|@count|escape:'htmlall':'UTF-8'}">
								<div id="dropdown_label_info">
									<div class="wk_option_container"></div>
									{foreach $all_saved_options as $opt_index=>$opt_value}
										<div class="wk_option_container">
											<div class="form-group">
												<div class="col-lg-9">
													{foreach from=$languages item=language}
														<input type="text"
														name="display_value_{$opt_index+1}_{$language.id_lang|escape:'html':'UTF-8'}"
														class="form-control dropdown_value_all dropdown_value_{$language.id_lang|escape:'html':'UTF-8'}"
														placeholder="{l s='display value' mod='wkproductoptions'}"
														value="{$opt_value['display_option_value'][$language.id_lang]['option_value']}"
														{if $current_lang.id_lang != $language.id_lang}style="display:none;"{/if} />
													{/foreach}
												</div>
												{if $total_languages > 1}
													<div class="col-lg-2">
														<button type="button" class="btn btn-default dropdown-toggle dropdown_value_lang_btn" data-toggle="dropdown">
														{$current_lang.iso_code|escape:'html':'UTF-8'}
														<span class="caret"></span>
														</button>
														<ul class="dropdown-menu">
														{foreach from=$languages item=language}
															<li>
															<a href="javascript:void(0)" onclick="showExtraLangField('{$language.iso_code|escape:'html':'UTF-8'}', {$language.id_lang|escape:'html':'UTF-8'});">{$language.name|escape:'html':'UTF-8'}</a>
															</li>
														{/foreach}
														</ul>
													</div>
												{/if}
											</div>
											<div class="form-group">
												<div class="col-lg-12">
													<div class="row">
														<div class="col-md-3" title="{l s='Price' mod='wkproductoptions'}">
															<div class="input-group">
																<input type="text" name="option_value_price_{$opt_index+1}" class="option_value_price" value="{$opt_value.price}">
																<div class="input-group-addon group_wk_option-currency {if $opt_value.price_type == 1} hide {/if}">{$currency_symbol|escape:'html':'UTF-8'}</div>
																<div class="input-group-addon group_wk_option-per {if $opt_value.price_type == 2} hide {/if}">%</div>
															</div>
															<div class="help-block">{l s='Price impact' mod='wkproductoptions'}</div>
														</div>
														<div class="col-md-3">
															<select name='option_value_price_type_{$opt_index+1}' class="option_value_price_type" title="{l s='Price Type' mod='wkproductoptions'}">
																<option value='2' {if $opt_value.price_type == 2} selected {/if}>{l s='Amount' mod='wkproductoptions'}</option>
																<option value='1' {if $opt_value.price_type == 1} selected {/if}>{l s='Percentage' mod='wkproductoptions'}</option>
															</select>
															<div class="help-block">{l s='Price impact type' mod='wkproductoptions'}</div>
														</div>
														<div class="col-md-3 {if $opt_value.price_type == 1} hide {/if}">
															<select name ='option_value_tax_type_{$opt_index+1}' class='option_value_tax_type' title="{l s='Tax Type' mod='wkproductoptions'}">
																<option value='0'  {if $opt_value.tax_type == 0} selected {/if}>{l s='Tax excluded' mod='wkproductoptions'}</option>
																<option value='1' {if $opt_value.tax_type == 1} selected {/if}>{l s='Tax included' mod='wkproductoptions'}</option>
															</select>
															<div class="help-block">{l s='Tax type' mod='wkproductoptions'}</div>
														</div>
														<div class="col-md-3">
															<button type="button" class="btn btn-default remove_dropdownvalue" data-id_option_val="{$opt_value.id_wk_product_options_value}" data-remove-id="{$opt_index+1}" title="{l s='Delete option' mod='wkproductoptions'}"><i class="icon-trash"></i></button>
														</div>
													</div>
												</div>
											</div>
										</div>
									{/foreach}
								</div>
								<div class="row">
									<div class="col-lg-12">
										<a href="" id="add_another_dropdownvalue" class="btn btn-default"><i class="icon-plus-circle"></i> {l s='Add new option' mod='wkproductoptions'}</a>
										&nbsp;&nbsp;&nbsp;<input type="checkbox" id="use_same_as_above" name="use_same_as_above" value="1"> {l s='Use same configuration as previous option.' mod='wkproductoptions'}
									</div>
								</div>
							</div>
						</div>
					</div>
				{/if}
			</div>
		</div>
		<div class="form-group wk_option_multi_select">
			<div class="row">
				<label class="col-lg-3 control-label">
					<span class="title_box">
						<span class="label-tooltip" data-toggle="tooltip" title="" data-original-title="{l s='If yes, then customer can select multiple options.' mod='wkproductoptions'}">
						{l s='Multiselect' mod='wkproductoptions'}
						</span>
					</span>
				</label>
				<div class="col-lg-6">
					{if isset($smarty.post.multiselect)}
						{assign var="multiselect" value="`$smarty.post.multiselect`"}
					{elseif isset($option_info.multiselect)}
						{assign var="multiselect" value="`$option_info.multiselect`"}
					{else}
					{assign var="multiselect" value="1"}
					{/if}
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" {if isset($multiselect) && ($multiselect == '1')}checked="checked" {/if}value="1" id="multiselectOn" name="multiselect">
						<label for="multiselectOn">{l s='Yes' mod='wkproductoptions'}</label>
						<input type="radio" value="0" {if isset($multiselect) && ($multiselect == '0')}checked="checked" {/if} id="multiselectOff" name="multiselect">
						<label for="multiselectOff">{l s='No' mod='wkproductoptions'}</label>
						<a class="slide-button btn"></a>
					</span>
				</div>
			</div>
		</div>
        <div class="form-group">
			<div class="row">
				<label class="col-lg-3 control-label required">
					<span class="title_box">
						<span class="label-tooltip" data-toggle="tooltip" title="" data-original-title="{l s='It will be displayed as hint of the option for the customer.' mod='wkproductoptions'}">
						{l s='Option description' mod='wkproductoptions'}
						</span>
					</span>
				</label>
				<div class="row">
                    <div class="col-lg-7">
                        {foreach from=$languages item=language}
							{assign var="option_description_smarty" value="option_description_`$language.id_lang`"}
							<div class="default_value_all" {if $current_lang.id_lang != $language.id_lang}style="display:none;"{/if} id="option_description_{$language.id_lang|escape:'html':'UTF-8'}">
								<textarea type="text"
								name="option_description_{$language.id_lang|escape:'html':'UTF-8'}"
								class="form-control wk_tinymce"
								autocomplete="off">{if isset($smarty.post.$option_description_smarty)}{$smarty.post.$option_description_smarty|escape:'htmlall':'UTF-8'}{elseif isset($option_description)}{$option_description[$language.id_lang]|escape:'html':'UTF-8'}{/if}</textarea>
							</div>
                        {/foreach}
                    </div>
                    {if $total_languages > 1}
                        <div class="col-lg-1">
                            <button type="button" class="btn btn-default dropdown-toggle default_value_lang_btn" data-toggle="dropdown">
         						{$current_lang.iso_code|escape:'html':'UTF-8'}
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                {foreach from=$languages item=language}
                                    <li>
                                        <a href="javascript:void(0)" onclick="showExtraLangField('{$language.iso_code|escape:'html':'UTF-8'}', {$language.id_lang|escape:'html':'UTF-8'});">{$language.name|escape:'html':'UTF-8'}</a>
                                    </li>
                                {/foreach}
                            </ul>
                        </div>
                    {/if}
                </div>
			</div>
		</div>
		<div class="form-group clearfix">
			<label class="control-label col-lg-3 wklabel">
				<span class="title_box">
					<span class="label-tooltip" data-toggle="tooltip" title="" data-original-title="{l s='Select country and currency for which option will be available.' mod='wkproductoptions'}">
						{l s='Country and currency' mod='wkproductoptions'}
					</span>
				</span>
			</label>
			<div class="col-lg-9">
				<div class="row">
					<div class="col-lg-3">
						{if isset($smarty.post.option_country) && $smarty.post.option_country}
							{assign var="opt_country" value="`$smarty.post.option_country`"}
						{elseif isset($option_product)
							&& $option_product.id_country}
							{assign var="opt_country" value="`$option_product.id_country`"}
						{/if}
						<select name="option_country" id="option_country">
							<option value="0">{l s='All countries' mod='wkproductoptions'}</option>
							{foreach from=$countries item=country}
							<option value="{$country.id_country}" {if isset($opt_country) && ($country.id_country== $opt_country)}selected{/if}>
								{$country.name|escape:'html':'UTF-8'}
							</option>
							{/foreach}
						</select>
					</div>
					<div class="col-lg-3">
						{if isset($smarty.post.option_currency) && $smarty.post.option_currency}
							{assign var="opt_crr" value="`$smarty.post.option_currency`"}
						{elseif isset($option_product)
							&& $option_product.id_currency}
							{assign var="opt_crr" value="`$option_product.id_currency`"}
						{/if}
						<select name="option_currency" id="option_currency">
							<option value="0">{l s='All currencies' mod='wkproductoptions'}</option>
							{foreach from=$currencies item=curr}
								<option value="{$curr.id_currency}" {if isset($opt_crr) && ($curr.id_currency == $opt_crr)}selected{/if}>
									{$curr.name|escape:'html':'UTF-8'}
								</option>
							{/foreach}
						</select>
					</div>
				</div>
			</div>
		</div>
		<div class="form-group clearfix">
			<label class="control-label col-lg-3" for="customer">
				<span class="title_box">
					<span class="label-tooltip" data-toggle="tooltip" title="" data-original-title="{l s='Select customer for which option will be available.' mod='wkproductoptions'}">
						{l s='Customer' mod='wkproductoptions'}
					</span>
				</span>
			</label>
			<div class="col-lg-4">
				{if isset($option_product)}
					<input type="hidden" name="option_customer" id="id_customer" value="{if isset($option_product.id_customer)}{$option_product.id_customer|escape:'html':'UTF-8'}{/if}" />
				{else}
					<input type="hidden" name="option_customer" id="id_customer" value="0" />
				{/if}
				<div class="input-group">
					<input type="text" name="customer" value="{if isset($customer_name)}{$customer_name|escape:'html':'UTF-8'}{/if}" id="wkoptioncustomer" autocomplete="off"
						class="form-control wk_text_field" placeholder="{l s='Search customer' mod='wkproductoptions'}" />
					<span class="input-group-addon"><i id="customerLoader" class="icon-refresh icon-spin"
							style="display: none;"></i><i class="icon-search"></i></span>
				</div>
			</div>
		</div>
		<div class="form-group clearfix">
			<div class="col-lg-10 col-lg-offset-3">
				<div id="customers"></div>
			</div>
		</div>
		<div class="form-group clearfix">
			<label class="control-label col-lg-3 wklabel">
				<span class="title_box">
					<span class="label-tooltip" data-toggle="tooltip" title="" data-original-title="{l s='Select date range for option availability.' mod='wkproductoptions'}">
						{l s='Availability date' mod='wkproductoptions'}
					</span>
				</span>
			</label>
			<div class="col-lg-9">
				<div class="row">
					<div class="col-lg-6 wkoptiondate">
						<div class="input-group">
							<span class="input-group-addon">{l s='From' mod='wkproductoptions'}</span>
							<input type="text" name="option_from" class="form-control wk_text_field" value="{if isset($smarty.post.option_from) && $smarty.post.option_from}{$smarty.post.option_from|escape:'html':'UTF-8'}{elseif isset($option_product) && ($option_product.from != '0000-00-00 00:00:00')}{$option_product.from|escape:'html':'UTF-8'}{/if}"
								style="text-align: center" id="option_from" />
							<span class="input-group-addon wk-calender-from"><i class="icon-calendar-empty"></i></span>
						</div>
					</div>
					<div class="col-lg-6 wkoptiondate">
						<div class="input-group">
							<span class="input-group-addon">{l s='To' mod='wkproductoptions'}</span>
							<input type="text" name="option_to" class="form-control wk_text_field" value="{if isset($smarty.post.option_to) && $smarty.post.option_to}{$smarty.post.option_to|escape:'html':'UTF-8'}{elseif isset($option_product) && ($option_product.to != '0000-00-00 00:00:00')}{$option_product.to|escape:'html':'UTF-8'}{/if}"
								style="text-align: center" id="option_to" />
							<span class="input-group-addon wk-calender-to"><i class="icon-calendar-empty"></i></span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="wk_price_impact_row">
			<div class="form-group">
				<label class="control-label col-lg-3 required">
				<span class="title_box">
					<span class="label-tooltip" data-toggle="tooltip" title="" data-original-title="{l s='Set fixed price or variable price based on product price percentage.' mod='wkproductoptions'}">
					{l s='Price impact ' mod='wkproductoptions'}
					</span>
				</span>
				</label>
				<div class="col-md-7">
					<div class="row">
						<div class="col-md-3" title="{l s='Price' mod='wkproductoptions'}">
							<div class="input-group">
								<input type="text" name="option_price" value = "{if isset($smarty.post.option_price) && $smarty.post.option_price}{$smarty.post.option_price|escape:'html':'UTF-8'}{elseif isset($option_info)}{$option_info.price|escape:'html':'UTF-8'}{else}0.00{/if}" id="option_price">
								<div class="input-group-addon group_wk_option-currency hide">{$currency_symbol|escape:'html':'UTF-8'}</div>
								<div class="input-group-addon group_wk_option-per">%</div>
							</div>
						</div>
						<div class="col-md-3">
							{if isset($smarty.post.option_price_type) && $smarty.post.option_price_type}
								{assign var="price_type" value="`$smarty.post.option_price_type`"}
							{elseif isset($option_info)
								&& $option_info.price_type}
								{assign var="price_type" value="`$option_info.price_type`"}
							{/if}
							<select name='option_price_type' id='option_price_type' title="{l s='Price Type' mod='wkproductoptions'}">
								<option value='2' {if isset($price_type) && $price_type == 2}selected{/if}>{l s='Amount' mod='wkproductoptions'}</option>
								<option value='1' {if isset($price_type) && $price_type == 1}selected{/if}>{l s='Percentage' mod='wkproductoptions'}</option>
							</select>
						</div>
						<div class="col-md-4" {if (isset($price_type) && $price_type == 1)} style="display:none" {elseif isset($option_info) && $option_info.tax_type == 1}style="display:none"{/if}>
							{if isset($smarty.post.option_tax_type) && $smarty.post.option_tax_type}
								{assign var="tax_type" value="`$smarty.post.option_tax_type`"}
							{elseif isset($option_info)
								&& $option_info.tax_type}
								{assign var="tax_type" value="`$option_info.tax_type`"}
							{/if}
							<select name ='option_tax_type' id='option_tax_type' title="{l s='Tax Type' mod='wkproductoptions'}">
								<option value='0' {if isset($tax_type) && $tax_type == 0}selected{/if}>{l s='Tax excluded' mod='wkproductoptions'}</option>
								<option value='1' {if isset($tax_type) && $tax_type == 1}selected{/if}>{l s='Tax included' mod='wkproductoptions'}</option>
							</select>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="row">
				<label class="col-lg-3 control-label">{l s='Active' mod='wkproductoptions'}</label>
				<div class="col-lg-6">
					{if isset($smarty.post.option_status)}
						{assign var="option_status" value="`$smarty.post.option_status`"}
					{elseif isset($option_info.active)}
						{assign var="option_status" value="`$option_info.active`"}
					{else}
						{assign var="option_status" value="1"}
					{/if}
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" {if isset($option_status) && ($option_status == '1')}checked="checked" {/if}value="1" id="option_statusOn" name="option_status">
						<label for="option_statusOn">{l s='Yes' mod='wkproductoptions'}</label>
						<input type="radio" value="0" {if isset($option_status) && ($option_status == '0')}checked="checked" {/if} id="option_statusOff" name="option_status">
						<label for="option_statusOff">{l s='No' mod='wkproductoptions'}</label>
						<a class="slide-button btn"></a>
					</span>
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="row">
				<label class="col-lg-3 control-label">{l s='Pre-selected' mod='wkproductoptions'}</label>
				<div class="col-lg-6">
					{if isset($smarty.post.pre_selected)}
						{assign var="pre_selected" value="`$smarty.post.pre_selected`"}
					{elseif isset($option_info.active)}
						{assign var="pre_selected" value="`$option_info.pre_selected`"}
					{else}
						{assign var="pre_selected" value="1"}
					{/if}
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" {if isset($pre_selected) && ($pre_selected == '1')}checked="checked" {/if}value="1" id="pre_selectedOn" name="pre_selected">
							<label for="pre_selectedOn">{l s='Yes' mod='wkproductoptions'}</label>
							<input type="radio" value="0" {if isset($pre_selected) && ($pre_selected == '0')}checked="checked" {/if} id="pre_selectedOff" name="pre_selected">
							<label for="pre_selectedOff">{l s='No' mod='wkproductoptions'}</label>
							<a class="slide-button btn"></a>
					</span>
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="row">
				<label class="col-lg-3 control-label">{l s='Required' mod='wkproductoptions'}</label>
				<div class="col-lg-6">
					{if isset($smarty.post.is_required)}
						{assign var="is_required" value="`$smarty.post.is_required`"}
					{elseif isset($option_info.active)}
						{assign var="is_required" value="`$option_info.is_required`"}
					{else}
						{assign var="is_required" value="1"}
					{/if}
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" {if isset($is_required) && ($is_required == '1')}checked="checked" {/if}value="1" id="is_requiredOn" name="is_required">
							<label for="is_requiredOn">{l s='Yes' mod='wkproductoptions'}</label>
							<input type="radio" value="0" {if isset($is_required) && ($is_required == '0')}checked="checked" {/if} id="is_requiredOff" name="is_required">
							<label for="is_requiredOff">{l s='No' mod='wkproductoptions'}</label>
							<a class="slide-button btn"></a>
					</span>
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="row">
				<label class="col-lg-3 control-label">
					<span class="title_box">
					<span class="label-tooltip" data-toggle="tooltip" title="" data-original-title=
					"{l s='Created product option will be available only for selected category. Leave empty if don\'t want to apply.' mod='wkproductoptions'}">
						{l s='Allowed categories' mod='wkproductoptions'}
					</span>
				</span>
				</label>
				<div class="col-lg-7">
					{$wk_category_tree}
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="row">
				<label class="col-lg-3 control-label">
					<span class="title_box">
					<span class="label-tooltip" data-toggle="tooltip" title="" data-original-title=
					"{l s='It will apply this option in above selected categories products.' mod='wkproductoptions'}">
						{l s='Apply option on selected category products' mod='wkproductoptions'}
					</span>
				</span>
				</label>
				<div class="col-lg-6">
					{if isset($smarty.post.is_bulk_enabled)}
						{assign var="is_bulk_enabled" value="`$smarty.post.is_bulk_enabled`"}
					{elseif isset($option_info.active)}
						{assign var="is_bulk_enabled" value="`$option_info.is_bulk_enabled`"}
					{else}
						{assign var="is_bulk_enabled" value="1"}
					{/if}
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" {if isset($is_bulk_enabled) && ($is_bulk_enabled == '1')}checked="checked" {/if}value="1" id="is_bulk_enabledOn" name="is_bulk_enabled">
							<label for="is_bulk_enabledOn">{l s='Yes' mod='wkproductoptions'}</label>
							<input type="radio" value="0" {if isset($is_bulk_enabled) && ($is_bulk_enabled == '0')}checked="checked" {/if} id="is_bulk_enabledOff" name="is_bulk_enabled">
							<label for="is_bulk_enabledOff">{l s='No' mod='wkproductoptions'}</label>
							<a class="slide-button btn"></a>
					</span>
				</div>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-3">
			<span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='Created product option will be available only for selected groups. Leave empty if don\'t want to apply.' mod='wkproductoptions'}
			">
				{l s='Allowed group' mod='wkproductoptions'}
			</span>
			</label>
			<div class="col-lg-7">
				<div class="row">
					<div class="col-lg-6">
						<table class="table table-bordered">
							<thead>
								<tr>
									<th class="fixed-width-xs">
										<span class="title_box">
										<input type="checkbox" name="checkme" id="checkme" onclick="checkDelBoxes(this.form, 'groupBox[]', this.checked)">
										</span>
									</th>
									<th class="fixed-width-xs"><span class="title_box">{l s='ID' mod='wkproductoptions'}</span></th>
									<th>
										<span class="title_box">
										{l s='Group name' mod='wkproductoptions'}
										</span>
									</th>
								</tr>
							</thead>
							<tbody>
								{if !empty($all_groups)}
									{foreach $all_groups as $groupBox}
										<tr>
											<td>
											<input type="checkbox" name="groupBox[]" class="groupBox" id="groupBox_{$groupBox.id_group}" value="{$groupBox.id_group|escape:'html':'UTF-8'}" {if isset($option_groups) && in_array($groupBox.id_group, $option_groups)}checked{/if}>
											</td>
											<td>{$groupBox.id_group|escape:'html':'UTF-8'}</td>
											<td>
											<label for="groupBox_1">{$groupBox.name|escape:'html':'UTF-8'}</label>
											</td>
										</tr>
									{/foreach}
								{else}
									<div class="alert alert-warning">
										{l s='No group exists' mod='wkproductoptions'}
									</div>
								{/if}
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class ="row">
				<label for="wk_slider_product_selection" class="col-lg-3 control-label">
					<span class="title_box">
					<span class="label-tooltip" data-toggle="tooltip" title="" data-original-title="{l s='Exclude product option on specific product(s).' mod='wkproductoptions'}">
					{l s='Exclude products' mod='wkproductoptions'}
					</span>
					</span>
				</label>
				<div class="col-lg-6">
				<div class="input-group full-width-lg">
					<input type="text" id="wk_option_product_selection" placeholder="{l s='Type product name here' mod='wkproductoptions'}" name="product_selection">
					<span class="input-group-addon btn btn-primary product_selection_search_icon">
						<i class="icon-search"></i>
					</span>
					</div>
					<div class="dropdown">
						<ul class="wk_option_product_selection_value wk-dropdown-ul" id="wk_option_product_selection_value" style="display: none;"></ul>
					</div>
				</div>
			</div>
		</div>
		<div class="form-group" id="option_selected_product">
			<div class="row">
				<div class="col-lg-12">
					<div class="table-responsive-sm">
						<table class="table table-bordered" id="productImageTobeAdd" {if !empty($products)}style="display:table;"{/if}>
							<thead>
								<tr>
									<th>{l s='Product image' mod='wkproductoptions'}</th>
									<th>{l s='Product name' mod='wkproductoptions'}</th>
									<th>{l s='Action' mod='wkproductoptions'}</th>
								</tr>
							</thead>
							<tbody id="productImageTobeAppend">
								{if !empty($products)}
									{foreach $products as $product}
										<tr class='productImageInfo'>
											<td><img class='img-responsive img-thumbnail wk-custom-image-size' src='{$product.img_path|escape:'html':'UTF-8'}'></td>
											<td><a href="{$product.product_link|escape:'html':'UTF-8'}" target="blank">{$product.name|escape:'html':'UTF-8'}</a></td>
											<td><span class='btn btn-sm btn-default deleteRow'><i class='material-icons'>delete_forever</i></span>
											<input type='hidden' name='idProducts[]' class="wk_product_append" value='{$product.id_product|escape:'html':'UTF-8'}'>
											</td>
										</tr>
									{/foreach}
								{/if}
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="panel-footer">
			<a href="{$link->getAdminLink('AdminProductOptions')|escape:'html':'UTF-8'}" class="btn btn-default">
				<i class="process-icon-cancel"></i>{l s='Cancel' mod='wkproductoptions'}
			</a>
			<button type="submit" name="submitAdd{$table|escape:'html':'UTF-8'}" id="submitAdd{$table|escape:'html':'UTF-8'}" class="btn btn-default pull-right" >
				<i class="process-icon-save"></i>{l s='Save' mod='wkproductoptions'}
			</button>
			<button type="submit" name="submitAdd{$table|escape:'html':'UTF-8'}AndStay" id="submitAdd{$table|escape:'html':'UTF-8'}AndStay" class="btn btn-default pull-right">
				<i class="process-icon-save"></i> {l s='Save and stay' mod='wkproductoptions'}
			</button>
		</div>
	</form>
</div>
