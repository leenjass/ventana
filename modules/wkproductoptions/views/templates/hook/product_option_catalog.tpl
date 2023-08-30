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
<div class="row">
    <div class="col-md-12">
        <div class="form-group row">
            <label class="form-control-label">
                {l s='Allow product options' mod='wkproductoptions'}
                <span class="help-box" data-toggle="popover" data-content="{l s='If enabled, you will not be able to use native customization feature. Only product options will be visible to the product page if this configuration is enabled.' mod='wkproductoptions'}" data-original-title="" title="">
                </span>
            </label>
            <div class="col-sm">
                <div class="input-group">
                    <span class="ps-switch">
                        <input id="wk_disable_native_customization_0" class="ps-switch" name="wk_disable_native_customization" value="0" type="radio" {if isset($wk_product_config.is_native_customization) && $wk_product_config.is_native_customization == 0} checked{/if} {if !isset($wk_product_config.is_native_customization)} checked{/if}>
                        <label for="wk_disable_native_customization_0">{l s='No' mod='wkproductoptions'}</label>
                        <input id="wk_disable_native_customization_1" class="ps-switch" name="wk_disable_native_customization" value="1" type="radio" {if isset($wk_product_config.is_native_customization) && $wk_product_config.is_native_customization == 1} checked{/if}>
                        <label for="wk_disable_native_customization_1">{l s='Yes' mod='wkproductoptions'}</label>
                        <span class="slide-button"></span>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
{if !empty($all_options)}
    <div class="wk_product_option_container">
        <hr>
        <div class="alert alert-info">
            <span>{l s='Enable product options from the list below.' mod='wkproductoptions'}</span>
        </div>
        {if $has_combination == 1}
            <div id="wk_option_combination_wise">
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            <span style="font-weight:bold;font-size:16px;">
                                {l s='Combinations' mod='wkproductoptions'}
                            </span>
                        </div>
                        <div class="col-md-6">
                            <div style="font-weight:bold;text-align:right;font-size:16px;">
                                {l s='Action' mod='wkproductoptions'}
                            </div>
                        </div>
                    </div>
                </div>
                {foreach $attribute_combination as $attribute}
                    <div class="form-group" style="border:1px solid #ccc;padding:8px;">
                        <div class="row">
                            <div class="col-md-6">
                                <div style="margin-top:7px;font-weight:bold;">
                                {$attribute.name|escape:'html':'UTF-8'}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <span class="btn btn-success collapsed" data-toggle="collapse" data-target="#att_{$attribute.id_product_attribute|escape:'html':'UTF-8'}" aria-expanded="false" style="float:right;">
                                {l s='Edit' mod='wkproductoptions'} <i class="material-icons float-right" style="margin-top:0px;">keyboard_arrow_down</i></span>
                            </div>
                        </div>
                    </div>
                    <div class="collapse" id="att_{$attribute.id_product_attribute|escape:'html':'UTF-8'}">
                        <div class="row">
                            {foreach $all_options as $option}
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="form-control-label">
                                            {$option.name|escape:'html':'UTF-8'}
                                        </label>
                                        <div class="col-sm">
                                            <div class="input-group">
                                                <span class="ps-switch">
                                                    <input id="active_option_{$attribute.id_product_attribute}_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}_0" class="ps-switch" name="active_option_{$attribute.id_product_attribute}_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}" value="0" type="radio" {if !in_array($option.id_wk_product_options_config, $attribute.option_values)} checked {/if}>
                                                        <label for="active_option_{$attribute.id_product_attribute}_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}_0">{l s='No' mod='wkproductoptions' }</label>
                                                    <input id="active_option_{$attribute.id_product_attribute}_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}_1" class="ps-switch" name="active_option_{$attribute.id_product_attribute}_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}" value="1" type="radio" {if in_array($option.id_wk_product_options_config, $attribute.option_values)} checked {/if}>
                                                        <label for="active_option_{$attribute.id_product_attribute}_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}_1">{l s='Yes' mod='wkproductoptions' }</label>
                                                    <span class="slide-button"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            {/foreach}
                        </div>
                    </div>
                {/foreach}
            </div>
            <p class="form-control bulk-action collapsed" data-toggle="collapse" href="#bulk-option-container" aria-expanded="false" aria-controls="bulk-combinations-container" style="padding:8px;background:#ccc;">
                <strong> {l s='Bulk actions' mod='wkproductoptions'}</strong>
                <i class="material-icons float-right">keyboard_arrow_down</i>
            </p>
            <div class="js-collapse collapse" id="bulk-option-container">
                <div class="border p-3">
                    <div class="row">
                        <div class="col-md-4">
                            <p><strong> {l s='Select combinations' mod='wkproductoptions'}</strong></p>
                            <div class="form-group" style="padding:8px 0px;">
                                <div class="checkbox">                       
                                    <label class="form-check-label"><input type="checkbox" id="wk_select_option_all" name="wk_select_option_all" value="1">
                                        <strong>{l s='Select all' mod='wkproductoptions'}</strong>
                                    </label> 
                                </div>
                            </div>
                            {foreach $attribute_combination as $attribute}
                                <div class="form-group" style="padding:8px 0px;">
                                    <div class="checkbox">                       
                                        <label class="form-check-label"><input type="checkbox" class="wk_select_option" name="wk_select_option[]" value="{$attribute.id_product_attribute|escape:'html':'UTF-8'}">
                                            {$attribute.name|escape:'html':'UTF-8'}
                                        </label>
                                    </div>
                                </div>
                            {/foreach}
                        </div>
                        <div class="col-md-8">
                            <p><strong> {l s='Select options' mod='wkproductoptions'}</strong></p>
                            <div class="bulk_option_container">
                                {foreach $all_options as $option}
                                    <div class="form-group row">
                                        <label class="form-control-label" style="text-align: left;">
                                            {$option.name|escape:'html':'UTF-8'}
                                        </label>
                                        <div class="col-sm">
                                            <div class="input-group">
                                                <span class="ps-switch">
                                                    <input id="active_option_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}_0" class="ps-switch" name="active_option_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}" value="0" type="radio">
                                                        <label for="active_option_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}_0">{l s='No' mod='wkproductoptions' }</label>
                                                    <input id="active_option_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}_1" class="ps-switch" name="active_option_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}" value="1" checked type="radio">
                                                        <label for="active_option_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}_1">{l s='Yes' mod='wkproductoptions' }</label>
                                                    <span class="slide-button"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                {/foreach}
                            </div>
                        </div>
                    </div>
                    <div class="form-group" style="text-align:right;">
                        <input type="hidden" name="id_ps_product" id="id_ps_product" value="{$id_ps_product}" />
                        <button class="btn btn-success wk_option_bulk_apply">
                            {l s='Apply' mod='wkproductoptions'}
                        </button>
                    </div>
                </div>
            </div>
        {else}
            <div class="row">
                {foreach $all_options as $option}
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label class="form-control-label">
                                {$option.name|escape:'html':'UTF-8'}
                            </label>
                            <div class="col-sm">
                                <div class="input-group">
                                    <span class="ps-switch">
                                        <input id="active_option_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}_0" class="ps-switch" name="active_option_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}" value="0" type="radio" {if $option.selected_value == 0}checked{/if}>
                                            <label for="active_option_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}_0">{l s='No' mod='wkproductoptions' }</label>
                                        <input id="active_option_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}_1" class="ps-switch" name="active_option_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}" value="1" type="radio" {if $option.selected_value == 1}checked{/if}>
                                            <label for="active_option_{$option.id_wk_product_options_config|escape:'html':'UTF-8'}_1">{l s='Yes' mod='wkproductoptions' }</label>
                                        <span class="slide-button"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                {/foreach}
            </div>
        {/if}
    </div>
{else}
<div class="alert alert-warning">
    <span>{l s='No product options created. First create product options from ' mod='wkproductoptions'}<a href="{$wk_option_controller_link|escape:'html':'UTF-8'}&addwk_product_options_config" target="blank">{l s='here' mod='wkproductoptions'}</a></span>
</div>
{/if}
