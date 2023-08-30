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
{if isset($product_options_info) && !empty($product_options_info)}
    {if isset($wk_option_page) && $wk_option_page == 'orderconfirmation' || $wk_option_page == 'admincart'}
        <br>
    {/if}
    {if Configuration::get('WK_PRODUCT_OPTION_DISPLAY_POPUP') == 1 || $wk_option_page == 'AdminOrders' || $wk_option_page == 'admincart'}
        <button type="button" class ="btn btn-default wk_custom_product_option_btn_mod {if  $wk_option_page == 'AdminOrders'} wk_custom_btn{/if}" href="#" data-toggle="modal" data-target="#product-options-modal-{$option_model_key}-{$display_mode}">{l s='View saved options' mod='wkproductoptions'}
        </button>
        <div class="modal fade wk-customization-option-modal" id="product-options-modal-{$option_model_key}-{$display_mode}" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        {if $wk_option_page == 'admincart'}
                            <h4 class="modal-title" style="display:contents">{l s='This product contains following options:' mod='wkproductoptions'}</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="{l s='Close' mod='wkproductoptions'}">
                            <span aria-hidden="true">&times;</span>
                            </button>
                        {else}
                        <button type="button" class="close" data-dismiss="modal" aria-label="{l s='Close' mod='wkproductoptions'}">
                        <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">{l s='This product contains following options:' mod='wkproductoptions'}</h4>
                        {/if}
                    </div>
                    <div class="modal-body">
                        <table class="table wk-product-option-info-table">
                        <thead>
                            <tr>
                                <th>{l s='Option name' mod='wkproductoptions'}</th>
                                <th>{l s='Option value' mod='wkproductoptions'}</th>
                                <th>{l s='Price impact' mod='wkproductoptions'}</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach $product_options_info as $option}
                                <tr>
                                    <td><strong>{$option.option_title}</strong></td>
                                    <td>
                                        {if $option.option_type == $WK_PRODUCT_OPTIONS_TEXT}
                                            {if $option.user_input == 1}
                                                <span style="color:{$option.input_color}">{$option.option_value}</span>
                                            {else}
                                                {l s='Yes' mod='wkproductoptions'}
                                            {/if}
                                        {elseif $option.option_type == $WK_PRODUCT_OPTIONS_TEXTAREA}
                                            <span style="color:{$option.input_color}">{$option.option_value}</span>
                                        {elseif $option.option_type == $WK_PRODUCT_OPTIONS_DROPDOWN}
                                            <span>
                                                {if $option.multiselect == 1}
                                                    {foreach $option.option_value as $key => $val}
                                                        {$val}
                                                        {if count($option.option_value) != ($key + 1)}
                                                        ,
                                                        {/if}
                                                    {/foreach}
                                                {else}
                                                    {$option.option_value}
                                                {/if}
                                            </span>
                                        {elseif $option.option_type == $WK_PRODUCT_OPTIONS_CHECKBOX}
                                            <span>
                                                {foreach $option.option_value as $key => $val}
                                                    {$val}
                                                    {if count($option.option_value) != ($key + 1)}
                                                    ,
                                                    {/if}
                                                {/foreach}
                                            </span>
                                        {elseif $option.option_type == $WK_PRODUCT_OPTIONS_RADIO}
                                            <span>
                                                {$option.option_value}
                                            </span>
                                        {elseif $option.option_type == $WK_PRODUCT_OPTIONS_FILE}
                                            <span>
                                                {if $option.option_value == 'not_exists'}
                                                    <span style="color:#8d1212;">{l s='File corrupted or not found!' mod='wkproductoptions'}</span>
                                                {else}
                                                    <a class="option-img-preview" target ="blank" href="{$option.option_value}">
                                                        <img class="img-thumbnail" width="80" height="80" src="{$option.option_value}" alt="{$option.option_title}">
                                                    </a>
                                                {/if}
                                            </span>
                                        {else}
                                            {$option.option_value}
                                        {/if}
                                    </td>
                                    <td> + {$option.price_impact_formated} ({l s='Tax Incl.' mod='wkproductoptions'})</td>
                                </tr>
                            {/foreach}
                        </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    {else}
        <div class="wk-product-option-info-full">
            <table class="table wk-product-option-info-table">
                <thead>
                    <tr>
                        <th>{l s='Option name' mod='wkproductoptions'}</th>
                        <th>{l s='Option value' mod='wkproductoptions'}</th>
                        <th>{l s='Price impact' mod='wkproductoptions'}</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach $product_options_info as $option}
                        <tr>
                            <td><strong>{$option.option_title}</strong></td>
                            <td>
                                {if $option.option_type == $WK_PRODUCT_OPTIONS_TEXT}
                                    {if $option.user_input == 1}
                                        <span style="color:{$option.input_color}">{$option.option_value}</span>
                                    {else}
                                        {l s='Yes' mod='wkproductoptions'}
                                    {/if}
                                {elseif $option.option_type == $WK_PRODUCT_OPTIONS_TEXTAREA}
                                    <span style="color:{$option.input_color}">{$option.option_value}</span>
                                {elseif $option.option_type == $WK_PRODUCT_OPTIONS_DROPDOWN}
                                    <span>
                                        {if $option.multiselect == 1}
                                            {foreach $option.option_value as $key => $val}
                                                {$val}
                                                {if count($option.option_value) != ($key + 1)}
                                                ,
                                                {/if}
                                            {/foreach}
                                        {else}
                                            {$option.option_value}
                                        {/if}
                                    </span>
                                {elseif $option.option_type == $WK_PRODUCT_OPTIONS_CHECKBOX}
                                    <span>
                                        {foreach $option.option_value as $key => $val}
                                            {$val}
                                            {if count($option.option_value) != ($key + 1)}
                                            ,
                                            {/if}
                                        {/foreach}
                                    </span>
                                {elseif $option.option_type == $WK_PRODUCT_OPTIONS_RADIO}
                                    <span>
                                        {$option.option_value}
                                    </span>
                                {elseif $option.option_type == $WK_PRODUCT_OPTIONS_FILE}
                                    <span>
                                        {if $option.option_value == 'not_exists'}
                                            <span style="color:#8d1212;">{l s='File corrupted or not found!' mod='wkproductoptions'}</span>
                                        {else}
                                            <a class="option-img-preview" target ="blank" href="{$option.option_value}">
                                                <img class="img-thumbnail" width="80" height="80" src="{$option.option_value}" alt="{$option.option_title}">
                                            </a>
                                        {/if}
                                    </span>
                                {else}
                                    {$option.option_value}
                                {/if}
                            </td>
                            <td> + {$option.price_impact_formated} ({l s='Tax Incl.' mod='wkproductoptions'})</td>
                        </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>
    {/if}
    {if isset($has_no_view) && $has_no_view == 1}
        <input type="hidden" class='wk_option_hide' value="1" />
    {/if}
{/if}