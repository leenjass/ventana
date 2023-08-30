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
<div class="wk_option_container">
    <div class="form-group">
        <div class="col-lg-9">
            {foreach from=$languages item=language}
                <input type="text" value=""
                name="display_value_{$id_option}_{$language.id_lang|escape:'html':'UTF-8'}"
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
    {if isset($use_same) && $use_same == 1}
        <div class="form-group">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-md-3" title="{l s='Price' mod='wkproductoptions'}">
                        <div class="input-group">
                            <input type="text" name="option_value_price_{$id_option}" class="option_value_price" value="{$price}">
                            <div class="input-group-addon group_wk_option-currency {if $last_price_type == 1}hide{/if}">{$currency_symbol|escape:'html':'UTF-8'}</div>
                            <div class="input-group-addon group_wk_option-per {if $last_price_type == 2}hide{/if}">%</div>
                        </div>
                        <div class="help-block">{l s='Price impact' mod='wkproductoptions'}</div>
                    </div>
                    <div class="col-md-3">
                        <select name='option_value_price_type_{$id_option}' class="option_value_price_type" title="{l s='Price Type' mod='wkproductoptions'}">
                            <option value='2' {if $last_price_type == 2}selected{/if}>{l s='Amount' mod='wkproductoptions'}</option>
                            <option value='1' {if $last_price_type == 1}selected{/if}>{l s='Percentage' mod='wkproductoptions'}</option>
                        </select>
                        <div class="help-block">{l s='Price impact type' mod='wkproductoptions'}</div>
                    </div>
                    <div class="col-md-3 {if $last_price_type == 1}hide{/if}">
                        <select name ='option_value_tax_type_{$id_option}' class='option_value_tax_type' title="{l s='Tax Type' mod='wkproductoptions'}">
                            <option value='0' {if $last_tax_type == 0}selected{/if}>{l s='Tax excluded' mod='wkproductoptions'}</option>
                            <option value='1' {if $last_tax_type == 1}selected{/if}>{l s='Tax included' mod='wkproductoptions'}</option>
                        </select>
                        <div class="help-block">{l s='Tax type' mod='wkproductoptions'}</div>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-default remove_dropdownvalue" data-id_option_val="0" data-remove-id="{$id_option}" title="{l s='Delete option' mod='wkproductoptions'}"><i class="icon-trash"></i></button>
                    </div>
    
                </div>
            </div>
        </div>
    {else}
        <div class="form-group">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-md-3" title="{l s='Price' mod='wkproductoptions'}">
                        <div class="input-group">
                            <input type="text" name="option_value_price_{$id_option}" class="option_value_price" value="0.00">
                            <div class="input-group-addon group_wk_option-currency">{$currency_symbol|escape:'html':'UTF-8'}</div>
                            <div class="input-group-addon group_wk_option-per hide">%</div>
                        </div>
                        <div class="help-block">{l s='Price impact' mod='wkproductoptions'}</div>
                    </div>
                    <div class="col-md-3">
                        <select name='option_value_price_type_{$id_option}' class="option_value_price_type" title="{l s='Price Type' mod='wkproductoptions'}">
                            <option value='2'>{l s='Amount' mod='wkproductoptions'}</option>
                            <option value='1'>{l s='Percentage' mod='wkproductoptions'}</option>
                        </select>
                        <div class="help-block">{l s='Price impact type' mod='wkproductoptions'}</div>
                    </div>
                    <div class="col-md-3">
                        <select name ='option_value_tax_type_{$id_option}' class='option_value_tax_type' title="{l s='Tax Type' mod='wkproductoptions'}">
                            <option value='0'>{l s='Tax excluded' mod='wkproductoptions'}</option>
                            <option value='1'>{l s='Tax included' mod='wkproductoptions'}</option>
                        </select>
                        <div class="help-block">{l s='Tax type' mod='wkproductoptions'}</div>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-default remove_dropdownvalue" data-id_option_val="0" data-remove-id="{$id_option}" title="{l s='Delete option' mod='wkproductoptions'}"><i class="icon-trash"></i></button>
                    </div>

                </div>
            </div>
        </div>
    {/if}
</div>