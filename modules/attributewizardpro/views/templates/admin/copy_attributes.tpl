{*
 * 2008 - 2017 Presto-Changeo
 *
 * MODULE Attribute Wizard Pro
 *
 * @version   2.0.0
 * @author    Presto-Changeo <info@presto-changeo.com>
 * @link      http://www.presto-changeo.com
 * @copyright Copyright (c) permanent, Presto-Changeo
 * @license   Addons PrestaShop license limitation
 *
 * NOTICE OF LICENSE
 *
 * Don't use this module on several shops. The license provided by PrestaShop Addons
 * for all its modules is valid only once for a single shop.
*}
<div class="panel po_main_content" id="copy_attributes">
    
    <div class="panel_header">
        <div class="panel_title">{l s='Copy Attributes' mod='attributewizardpro'}</div>
        <div class="panel_info_text">
            <span class="simple_alert"> </span>
            {l s='This tool allows you to copy All attributes from one product to another (or to all products in a category, manufacturer or supplier)' mod='attributewizardpro'}
        </div>
        <div class="clear"></div>
    </div>
     <div class="two_columns">
        <div class="columns">
            <div class="left_column">
                {l s='Source (Product ID)' mod='attributewizardpro'}
            </div>
            <div class="right_column">
                <input type="text" id="aw_copy_src" name="aw_copy_src" />
            </div>
        </div>
            
        <div class="columns">
            <div class="left_column">
                {l s='Target Type' mod='attributewizardpro'}
            </div>
            <div class="right_column">
                <select name="aw_copy_tgt_type" id="aw_copy_tgt_type">
                    <option value="p">{l s='Product' mod='attributewizardpro'}</option>
                    <option value="c">{l s='Category' mod='attributewizardpro'}</option>
                    <option value="m">{l s='Manufacturer' mod='attributewizardpro'}</option>
                    <option value="s">{l s='Supplier' mod='attributewizardpro'}</option>
                </select>               
               
                
            </div>
        </div>
         <div class="columns">
            <div class="left_column">
                {l s='Target (ID)' mod='attributewizardpro'}
            </div>
            <div class="right_column">
                <input type="text" name="aw_copy_tgt" id="aw_copy_tgt" value="">
            </div>
        </div>
                
        <div class="columns">
            <div class="left_column">               
            </div>
            <div class="right_column">
                <input class="submit_button" type="button" id="aw_copy_validate" value="{l s='Confirm' mod='attributewizardpro'}">
            </div>
        </div>
        <div class="clear"></div>
        <div id="aw_copy_confirmation" class="special_instructions single_column">
        </div>
        <div class="clear"></div>
     </div>
    <div class="clear"></div>
</div>