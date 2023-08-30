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

{if !empty($product_options)}
    <div class="wk_product_opt_container wk-product-variants">
        {foreach $product_options as $option}
            {if $option.option_type == $WK_PRODUCT_OPTIONS_TEXT}
                <div class="wk-product-variants-item">
                <span class="control-label">
                        {if $option.is_required == 1}
                            <span style="color:brown">*&nbsp;</span>
                        {/if}
                    <input type="checkbox" id="wk_option_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}" class="wk_option_checkbox_parent" value="{$option.id_wk_product_options_config|escape:'html':'UTF-8'}" name="wk_option_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}" {if $option.pre_selected == 1} checked{/if}> <span class="wk_option_title">{$option.display_name|escape:'html':'UTF-8'}</span> <span class="wk_price_impact">({l s='Price Impact' mod='wkproductoptions'} : +{$option.price_impact_formated|escape:'html':'UTF-8'})</span>
                       <a href="javascript:void(0);" data-toggle="tooltip" class="wk_option_tooltip" title="{$option.description|escape:'html':'UTF-8'}"><i class="material-icons wk_option_info_icon" data-id_info="{$option.id_wk_product_options_config|escape:'html':'UTF-8'}">info</i></a>
                    </span>
                    {if $option.user_input}
                        <div style="position:relative">
                    <input type="text" class="form-control wk_custom_text" placeholder="{if $option.placeholder != ''}{$option.placeholder}{else}{l s='Your message here' mod='wkproductoptions'}{/if}" {if $option.text_limit > 0} maxlength="{$option.text_limit}" {/if} id="wk_option_text_area_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}" name="wk_option_text_area_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}" />
                    {if $option.text_limit > 0}<span class="text-muted label_bottom">{l s='Only %max_limit% characters are allowed.' sprintf=['%max_limit%' => $option.text_limit] mod='wkproductoptions'}</span>{/if}
                            {if Configuration::get('WK_PRODUCT_OPTION_DISPLAY_COLOR_PICKER') == 1}
                                <input type="color" class="wk_color_input" name="input_color_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}" data-id_parent="wk_option_text_area_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}" title="{l s='You can select text color from here.' mod='wkproductoptions'}"/>
                            {/if}
                        </div>
                    {/if}
                </div>
            {elseif $option.option_type == $WK_PRODUCT_OPTIONS_TEXTAREA}
                <div class="wk-product-variants-item">
                <span class="control-label">
                        {if $option.is_required == 1}
                            <span style="color:brown">*&nbsp;</span>
                        {/if}
                    <input type="checkbox" id="wk_option_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}" class="wk_option_checkbox_parent" value="{$option.id_wk_product_options_config|escape:'html':'UTF-8'}" name="wk_option_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}" {if $option.pre_selected == 1} checked{/if}> <span class="wk_option_title">{$option.display_name|escape:'html':'UTF-8'}</span> <span class="wk_price_impact">({l s='Price Impact' mod='wkproductoptions'} : +{$option.price_impact_formated|escape:'html':'UTF-8'})</span>
                        <a href="javascript:void(0);" data-toggle="tooltip" class="wk_option_tooltip" title="{$option.description|escape:'html':'UTF-8'}"><i class="material-icons wk_option_info_icon" data-id_info="{$option.id_wk_product_options_config|escape:'html':'UTF-8'}">info</i></a>
                    </span>
                    <div style="position:relative">
                        <textarea placeholder="{if $option.placeholder != ''}{$option.placeholder}{else}{l s='Your message here' mod='wkproductoptions'}{/if}" class="form-control wk_option_text_area" {if $option.text_limit > 0} maxlength="{$option.text_limit}" {/if} id="wk_option_text_area_new_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}" name="wk_option_text_area_new_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}"></textarea>
                        {if $option.text_limit > 0}<span class="text-muted label_bottom">{l s='Only %max_limit% characters are allowed.' sprintf=['%max_limit%' => $option.text_limit] mod='wkproductoptions'}</span>{/if}
                        {if Configuration::get('WK_PRODUCT_OPTION_DISPLAY_COLOR_PICKER') == 1}
                            <input type="color" class="wk_color_input" name="input_color_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}" data-id_parent="wk_option_text_area_new_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}" title="{l s='You can select text color from here.' mod='wkproductoptions'}"/>
                        {/if}
                    </div>
                </div>
            {elseif $option.option_type == $WK_PRODUCT_OPTIONS_DROPDOWN}
                <div class="wk-product-variants-item">
                    <span class="control-label">
                        {if $option.is_required == 1}
                            <span style="color:brown">*&nbsp;</span>
                        {/if}
                        <input type="checkbox" id="wk_option_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}" class="wk_option_checkbox_parent" value="{$option.id_wk_product_options_config|escape:'html':'UTF-8'}" name="wk_option_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}" {if $option.pre_selected == 1} checked{/if}> <span class="wk_option_title">{$option.display_name|escape:'html':'UTF-8'}</span>
                       <a href="javascript:void(0);" data-toggle="tooltip" class="wk_option_tooltip" title="{$option.description|escape:'html':'UTF-8'}"><i class="material-icons wk_option_info_icon" data-id_info="{$option.id_wk_product_options_config|escape:'html':'UTF-8'}">info</i></a>
                    </span>
                    {if $option.multiselect == 1}
                        <input type="hidden" id="wk_option_dropdown_value_id_multi_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}" name="wk_option_dropdown_value_id_multi_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}" value="0">
                        <select
                        class="form-control wk_select_dropdown_multiple"
                        id="wk_option_dropdown_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}"
                        name="wk_option_dropdown_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}[]" multiple="multiple" data-id_field="{$option.id_wk_product_options_config|escape:'html':'UTF-8'}">
                            {foreach $option.options_value_arr as $val}
                                <option value="{$val.id_wk_product_options_value}"  data-id-option-val="{$val.id_wk_product_options_value}">{$val.option_value} ({l s='Price impact' mod='wkproductoptions'} : {$val.price_impact_formated})</option>
                            {/foreach}
                        </select>
                    {else}
                        <input type="hidden" id="wk_option_dropdown_value_id_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}" name="wk_option_dropdown_value_id_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}" value="0">
                        <select
                        class="form-control form-control-select wk_select_dropdown_single"
                        id="wk_option_dropdown_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}"
                        name="wk_option_dropdown_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}" data-id_field="{$option.id_wk_product_options_config|escape:'html':'UTF-8'}">
                            <option value="0">{l s='--select--' mod='wkproductoptions'}</option>
                            {foreach $option.options_value_arr as $val}
                                <option value="{$val.option_value}" data-id-option-val="{$val.id_wk_product_options_value}">{$val.option_value} ({l s='Price impact' mod='wkproductoptions'} : {$val.price_impact_formated})</option>
                            {/foreach}
                        </select>
                    {/if}
                </div>
            {elseif $option.option_type == $WK_PRODUCT_OPTIONS_CHECKBOX}
                <input type="hidden" id="wk_option_checkbox_value_id_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}" name="wk_option_checkbox_value_id_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}" value="0">
                <div class="wk-product-variants-item">
                    <span class="control-label">
                        {if $option.is_required == 1}
                            <span style="color:brown">*&nbsp;</span>
                        {/if}
                        <input type="checkbox" id="wk_option_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}" class="wk_option_checkbox_parent" value="{$option.id_wk_product_options_config|escape:'html':'UTF-8'}" name="wk_option_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}" {if $option.pre_selected == 1} checked{/if}> <span class="wk_option_title">{$option.display_name|escape:'html':'UTF-8'}</span>
                       <a href="javascript:void(0);" data-toggle="tooltip" class="wk_option_tooltip" title="{$option.description|escape:'html':'UTF-8'}"><i class="material-icons wk_option_info_icon" data-id_info="{$option.id_wk_product_options_config|escape:'html':'UTF-8'}">info</i></a>
                    </span>
                    {foreach $option.options_value_arr as $val}
                        <div class="wk_opt_val">
                            <input type="checkbox" id="wk_option_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}_{$val.id_wk_product_options_value}" class="wk_option_checkbox wk_option_checkbox_option wk_checkbox_selected" name="wk_option_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}_{$val.id_wk_product_options_value}" value="{$val.option_value|escape:'html':'UTF-8'}" data-id-check-val="{$val.id_wk_product_options_value}" data-id_field="{$option.id_wk_product_options_config|escape:'html':'UTF-8'}"> {$val.option_value} ({l s='Price impact' mod='wkproductoptions'} : {$val.price_impact_formated})
                        </div>
                    {/foreach}
                </div>
            {elseif $option.option_type == $WK_PRODUCT_OPTIONS_RADIO}
                <input type="hidden" id="wk_option_radio_value_id_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}" name="wk_option_radio_value_id_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}" value="{$option['options_value_arr']['0']['id_wk_product_options_value']}">
                <div class="wk-product-variants-item">
                    <span class="control-label">
                        {if $option.is_required == 1}
                            <span style="color:brown">*&nbsp;</span>
                        {/if}
                        <input type="checkbox" id="wk_option_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}" class="wk_option_checkbox_parent" value="{$option.id_wk_product_options_config|escape:'html':'UTF-8'}" name="wk_option_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}" {if $option.pre_selected == 1} checked{/if}> <span class="wk_option_title">{$option.display_name|escape:'html':'UTF-8'}</span> <a href="javascript:void(0);" data-toggle="tooltip" class="wk_option_tooltip" title="{$option.description|escape:'html':'UTF-8'}"><i class="material-icons wk_option_info_icon" data-id_info="{$option.id_wk_product_options_config|escape:'html':'UTF-8'}">info</i></a>
                    </span>
                    {foreach $option.options_value_arr as $key => $val}
                        <div class="wk_opt_val">
                    <input type="radio" id="wk_option_radio_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}_{$val.id_wk_product_options_value|escape:'html':'UTF-8'}" class="wk_option_checkbox wk_option_checkbox_option wk_radio_selected" name="wk_option_radio_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}" value="{$val.option_value|escape:'html':'UTF-8'}" data-id-radio-val="{$val.id_wk_product_options_value}" data-id_field="{$option.id_wk_product_options_config|escape:'html':'UTF-8'}" {if $key == 0}checked{/if}> {$val.option_value} ({l s='Price impact' mod='wkproductoptions'} : {$val.price_impact_formated})
                        </div>
                    {/foreach}
                </div>
            {elseif $option.option_type == $WK_PRODUCT_OPTIONS_FILE}
                <div class="wk-product-variants-item">
                    <span class="control-label">
                        {if $option.is_required == 1}
                            <span style="color:brown">*&nbsp;</span>
                        {/if}
                        <input type="checkbox" class="wk_option_checkbox_parent" value="{$option.id_wk_product_options_config|escape:'html':'UTF-8'}" name="wk_option_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}" {if $option.pre_selected == 1} checked{/if} /> <span class="wk_option_title">{$option.display_name|escape:'html':'UTF-8'}</span> <span class="wk_price_impact">({l s='Price Impact' mod='wkproductoptions'} : +{$option.price_impact_formated|escape:'html':'UTF-8'})</span>
                       <a href="javascript:void(0);" data-toggle="tooltip" class="wk_option_tooltip" title="{$option.description|escape:'html':'UTF-8'}"><i class="material-icons wk_option_info_icon" data-id_info="{$option.id_wk_product_options_config|escape:'html':'UTF-8'}">info</i></a>
                    </span>
                    <span class="wk-custom-file">
                        <span class="js-file-name" id="wk_option_file{$option.id_wk_product_options_config}_name">{l s='No selected file' mod='wkproductoptions'}</span>
                        <input class="file-input js-file-input" type="file" name="wk_option_file{$option.id_wk_product_options_config}" id="wk_option_file{$option.id_wk_product_options_config}"><button class="btn btn-primary wk_image_upload" data-id_image = "wk_option_file{$option.id_wk_product_options_config}">{l s='Choose file' mod='wkproductoptions'}</button>
                        <input type="hidden" name="wk_option_file_contain_{$option.id_wk_product_options_config}" class="wk_option_file_contain" id="wk_option_file{$option.id_wk_product_options_config}_contain" value="0">
                    </span>
                    <small class="float-xs-right">{l s='Select only .png .jpg .gif file' mod='wkproductoptions'}</small>
                </div>
            {elseif $option.option_type == $WK_PRODUCT_OPTIONS_DATE}
                <div class="wk-product-variants-item">
                <span class="control-label">
                        {if $option.is_required == 1}
                            <span style="color:brown">*&nbsp;</span>
                        {/if}
                    <input type="checkbox" id="wk_option_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}" class="wk_option_checkbox_parent" value="{$option.id_wk_product_options_config|escape:'html':'UTF-8'}" name="wk_option_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}" {if $option.pre_selected == 1} checked{/if}> <span class="wk_option_title">{$option.display_name|escape:'html':'UTF-8'}</span> <span class="wk_price_impact">({l s='Price Impact' mod='wkproductoptions'} : +{$option.price_impact_formated|escape:'html':'UTF-8'})</span>
                        <a href="javascript:void(0);" data-toggle="tooltip" class="wk_option_tooltip" title="{$option.description|escape:'html':'UTF-8'}"><i class="material-icons wk_option_info_icon" data-id_info="{$option.id_wk_product_options_config|escape:'html':'UTF-8'}">info</i></a>
                    </span>
                    <div class="input-group">
                        <input type="text" class="form-control wk_product_option_date" placeholder="YYYY-MM-DD" id="wk_option_date_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}" name="wk_option_date_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}" />
                        <span class="input-group-addon wk_custom_addon">
                            <i class="material-icons">today</i>
                        </span>
                    </div>
                </div>
            {elseif $option.option_type == $WK_PRODUCT_OPTIONS_TIME}
                <div class="wk-product-variants-item">
                <span class="control-label">
                        {if $option.is_required == 1}
                            <span style="color:brown">*&nbsp;</span>
                        {/if}
                    <input type="checkbox" id="wk_option_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}" class="wk_option_checkbox_parent" value="{$option.id_wk_product_options_config|escape:'html':'UTF-8'}" name="wk_option_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}" {if $option.pre_selected == 1} checked{/if}> <span class="wk_option_title">{$option.display_name|escape:'html':'UTF-8'}</span> <span class="wk_price_impact">({l s='Price Impact' mod='wkproductoptions'} : +{$option.price_impact_formated|escape:'html':'UTF-8'})</span>
                        <a href="javascript:void(0);" data-toggle="tooltip" class="wk_option_tooltip" title="{$option.description|escape:'html':'UTF-8'}"><i class="material-icons wk_option_info_icon" data-id_info="{$option.id_wk_product_options_config|escape:'html':'UTF-8'}">info</i></a>
                    </span>
                    <div class="input-group">
                        <input type="text" class="form-control wk_product_option_time" placeholder="HH:MM::SS" id="wk_option_time_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}" name="wk_option_time_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}" />
                        <span class="input-group-addon wk_custom_addon">
                        <i class="material-icons">today</i>
                        </span>
                    </div>
                </div>
            {elseif $option.option_type == $WK_PRODUCT_OPTIONS_DATETIME}
                <div class="wk-product-variants-item">
                <span class="control-label">
                        {if $option.is_required == 1}
                            <span style="color:brown">*&nbsp;</span>
                        {/if}
                    <input type="checkbox" id="wk_option_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}" class="wk_option_checkbox_parent" value="{$option.id_wk_product_options_config|escape:'html':'UTF-8'}" name="wk_option_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}" {if $option.pre_selected == 1} checked{/if}> <span class="wk_option_title">{$option.display_name|escape:'html':'UTF-8'}</span> <span class="wk_price_impact">({l s='Price Impact' mod='wkproductoptions'} : +{$option.price_impact_formated|escape:'html':'UTF-8'})</span>
                        <a href="javascript:void(0);" data-toggle="tooltip" class="wk_option_tooltip" title="{$option.description|escape:'html':'UTF-8'}"><i class="material-icons wk_option_info_icon" data-id_info="{$option.id_wk_product_options_config|escape:'html':'UTF-8'}">info</i></a>
                    </span>
                    <div class="input-group">
                        <input type="text" class="form-control wk_product_option_datetime" placeholder="YYYY-MM-DD HH:MM::SS" id="wk_option_datetime_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}" name="wk_option_datetime_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}" />
                        <span class="input-group-addon wk_custom_addon">
                            <i class="material-icons">today</i>
                        </span>
                    </div>
                </div>
            {/if}
        {/foreach}
    </div>
{/if}
<input type="hidden" name = "wk_id_product_attribute_form" id="wk_id_product_attribute_form" value="0">