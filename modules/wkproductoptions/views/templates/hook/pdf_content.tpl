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
{if isset($product_name)}
    {$product_name}
{/if}
<br>
{if isset($product_options_info) && !empty($product_options_info)}
    <table class="table wk-product-option-info-table">
        <thead>
            <tr>
                <th><strong>{l s='Option name' mod='wkproductoptions'}</strong></th>
                <th><strong>{l s='Option value' mod='wkproductoptions'}</strong></th>
                <th><strong>{l s='Price impact' mod='wkproductoptions'}</strong></th>
            </tr>
        </thead>
        <tbody>
            {foreach $product_options_info as $option}
                <tr>
                    <td>{$option.option_title}</td>
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
                        {/if}
                    </td>
                    <td> + {$option.price_impact_formated} ({l s='Tax Incl.' mod='wkproductoptions'})</td>
                </tr>
            {/foreach}
        </tbody>
    </table>
{/if}