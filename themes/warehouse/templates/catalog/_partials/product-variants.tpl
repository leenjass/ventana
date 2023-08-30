{**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
<div class="product-variants js-product-variants ventanastore_accordian_product_variation">
    {assign var="priceCounter" value=1}
    {foreach from=$groups key=id_attribute_group item=group}
        {if !empty($group.attributes)}
        <a class="modal-trigger" data-toggle="modal" data-target="#modal-hint"
            {if $group.name == 'Kleur'}
                data-title="Kleur"
                data-description="Kleur description" 
            {/if}
            {if $group.name == 'Montagewijze'}
                data-title="Montagewijze"
                data-description="Montagewijze description" 
            {/if}
            {if $group.name == 'Bedieningszijde'}
                data-title="Bedieningszijde"
                data-description="Bedieningszijde description" 
            {/if}
            {if $group.name == 'Ladderband'}
                data-title="Ladderband"
                data-description="Ladderband description" 
            {/if}
            {if $group.name == 'Kleur ladderband'}
                data-title="Kleur ladderband"
                data-description="Kleur ladderband description" 
            {/if}
            {if $group.name == 'Zijgeleiding'}
                data-title="Zijgeleiding"
                data-description="Zijgeleiding description" 
            {/if}
        ><span><i class="fa fa-question-circle custom-icon" aria-hidden="true"></i></span></a>
        <div class="clearfix product-variants-item product-variants-item-{$id_attribute_group} ventanastore_accordion_item ">
            <span class="form-control-label ventanastore_accordion_title" data-position="{$priceCounter}">{$group.name} 
            <i class="fa fa-check-circle" aria-hidden="true"></i>                
            </span>

            {assign var="priceCounter" value=$priceCounter+1}
            {if $group.group_type == 'select'}
                <div class="custom-select2">
                <select
                        id="group_{$id_attribute_group}"
                        aria-label="{$group.name}"
                        data-product-attribute="{$id_attribute_group}"
                        name="group[{$id_attribute_group}]"
                        class="form-control form-control-select">
                    {foreach from=$group.attributes key=id_attribute item=group_attribute}
                        <option value="{$id_attribute}"
                                title="{$group_attribute.name}"{if $group_attribute.selected} selected="selected"{/if} {if $group.attributes_quantity.$id_attribute <= 0} class="attribute-not-in-stock"{/if}>{$group_attribute.name}

                      </option>
                    {/foreach}
                </select>
                </div>
            {elseif $group.group_type == 'color'}
                <div class="ventanastore_accordion_content" >
                    <ul id="group_{$id_attribute_group}">
                        {foreach from=$group.attributes key=id_attribute item=group_attribute}
                            <lable>
                            <li class="float-left input-container {if $group.attributes_quantity.$id_attribute <= 0} attribute-not-in-stock{/if}" data-toggle="tooltip" data-animation="false" data-placement="top"  data-container= ".product-variants" title="{$group_attribute.name}">
                            
                                <input class="input-color" type="radio" data-product-attribute="{$id_attribute_group}"
                                       name="group[{$id_attribute_group}]"
                                       value="{$id_attribute}"{if $group_attribute.selected} checked="checked"{/if}>
                                <span
                                        {if $group_attribute.texture}
                                            class="color texture {if $group_attribute.selected} product_current_attribute {/if}" style="background-image: url({$group_attribute.texture})"
                                        {elseif $group_attribute.html_color_code}
                                            class="color" style="background-color: {$group_attribute.html_color_code}"
                                        {/if}
                                ><span class="attribute-name sr-only">{$group_attribute.name}</span></span>
                                <br>
                                <span class="{if $group_attribute.selected} radio_button_custom_or{/if} radio_button_custom_norm"></span>{$group_attribute.name}
                               
                                {if $group_attribute.selected}
                                <!--display overview data -->
                                <span class="{$group.name}get_data" style="display:none;">{$group.name}: {$group_attribute.name}</span>
                                <!--end of overview data -->
                                {/if}
                
                            </li>
                        {/foreach}
                    </ul>
                </div>

            {elseif $group.group_type == 'radio'}
                <div class="ventanastore_accordion_content" >
                    <ul id="group_{$id_attribute_group}">
                        {foreach from=$group.attributes key=id_attribute item=group_attribute}
                            <li class="input-container float-left {if $group.attributes_quantity.$id_attribute <= 0} attribute-not-in-stock{/if}">
                                
                                <input class="input-radio" type="radio" data-product-attribute="{$id_attribute_group}"
                                       name="group[{$id_attribute_group}]"
                                       title="{$group_attribute.name}"
                                       value="{$id_attribute}"{if $group_attribute.selected} checked="checked"{/if}>
                                <span class="radio-label">{$group_attribute.name}</span>
                                
                                {if $group_attribute.selected}
                                <!--display overview data -->
                                <span class="{$group.name}get_data" style="display:none;">{$group.name}: {$group_attribute.name}</span>
                                <!--end of overview data -->
                                {/if}
                            </li>
                        {/foreach}
                    </ul>
                </div>
            {/if}
        </div>
        {/if}
    {/foreach}
</div>


<script>
    if(typeof $ != typeof undefine) {
        $(document).ready(function(){
            ventanastore_product_varition_accordian(0);
        });
    }
</script>