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

<div class="panel po_main_content" id="installation_instructions">
    
    <div class="panel_header">
        <div class="panel_title">{l s='Installation Instructions' mod='attributewizardpro'}</div>
        <div class="panel_info_text important">
            <span class="important_alert"> </span>
            {l s='This installation instruction is very important. Please read carefully before continuing to the configuration tab.' mod='attributewizardpro'}
        </div>
        <div class="clear"></div>
    </div>
        
    <div class="general_instructions single_column">
        <div class="instructions_title">{l s='General Instructions' mod='attributewizardpro'}</div>
        <div class="general_instructions_content">
            <ul>
                <li>
                    <span>{l s='A new attribute group named "awp_details" was created,' mod='attributewizardpro'}</span>
                    <span class="important_alert"> </span>
                    <span class="important_instructions important_instructions important"> 
                        {l s='DO NOT DELETE OR RENAME IT!' mod='attributewizardpro'}
                    </span>
                </li>
                <li>
                    <span>
                        {l s='The following changes need to be made to your existing PrestaShop files.' mod='attributewizardpro'}
                    </span>
                </li>
                <li>
                    <span>
                        {l s='Please keep a backup of your existing files before overriding or merging them.' mod='attributewizardpro'}
                    </span>
                </li>
                <li>
                    <span>
                    {l s='There is a copy of all the modified files in /modules/attributewizardpro/modified_' mod='attributewizardpro'}{$awp_ps_version}
                    </span>
                </li>
                <li class="notes">
                    <span class="notes">
                    {l s='If you have not made changes in those files on your server, you can copy the files from /modules/attributewizardpro/modified_' mod='attributewizardpro'}{$awp_ps_version} {l s='to your root directory.' mod='attributewizardpro'}
                    </span>
                </li>
                <li class="notes">
                    <span class="notes">
                    {l s='If you have made changes in those files on your server, copy only the lines listed below from the files in /modules/attributewizardpro/modified_' mod='attributewizardpro'}{$awp_ps_version} {l s=' to the corresponding local files..' mod='attributewizardpro'}
                    </span>
                </li>
                <li>
                    <span>
                    {l s='The filenames below will appear in RED until you make the necessary changes, if the changes were made correctly, they will turn GREEN after you reload the page.' mod='attributewizardpro'}
                </li>
                <li>
                    <span>
                    {l s='The code comparison is done on each line + previous and next, if you made custom changes to those files (I.E remove or add lines), you may not get the line as green, even though the code is correct.' mod='attributewizardpro'}
                    </span>
                </li>
            </ul>
        </div>
    </div>
            
    <div class="override_instructions single_column">
        <div class="instructions_title">
            {l s='Override Files' mod='attributewizardpro'}
            <a href="{$request_uri}&awp_shis={$awp_shis}">
            {if $awp_shis == 'none'}
                <span class="arrow_up"></span>
            {else}
                <span class="arrow_down"></span>
            {/if}
             </a>
        </div>

        <div class="override_content {if $awp_shis == 'none'}hideADN{/if}">

            {if $awp_ps_version === '1.7.7'}
                <div class="override_block">
                    <div class="override_class">
                        <span>Copy <b>/attributewizardpro/modified_{$awp_ps_version}/PrestaShop</b> to <b>/attributewizardpro/views</b></span>
                    </div>
                </div>
            {/if}

            {foreach $checks as $check}
			{if isset($check) && !empty($check)}
            <div class="override_block">
                <div class="override_class">
                    <span  class="{if $check['file_installed']}file_installed{else}file_not_installed{/if}">/attributewizardpro/modified_{$awp_ps_version}{$check['file']}</span>
                </div>
                <div class="override_lines">
                    {if $check['file_not_found']}
                    Lines
                        {foreach $check['lines'] as $line => $valid}
                            <span class="{if $valid}file_installed{else}file_not_installed{/if}">#{$line}</span>{if !$valid@last},{/if}
                        {/foreach}
                    {else}
                        {l s='Copy entire file' mod='attributewizardpro'}
                    {/if}
                </div>
            </div>
			{/if}
            {/foreach}

            

            <div class="extra_instructions">
                <span class="important_alert"> </span>
                <span class="important_instructions important"> 
                    {l s='Make sure to clear the cache in Advanced Parameteres->Performance->Clear Cache.' mod='attributewizardpro'}
                </span>
            </div>
        </div>
    </div>
    
    <div class="extra_line"></div>

    <div class="hook_instructions single_column">
        <div class="panel_header">
            <div class="hook_title panel_title">
                {l s='Install on Quick View' mod='attributewizardpro'}
            </div>
            <div class="panel_info_text">
                <span class="simple_alert"> </span>
                {l s='if you wish to diplay the wizard in a QUICK VIEW from product list page' mod='attributewizardpro'}
            </div>
            <div class="clear"></div>
         </div>
  
        <div class="hook_content">
            <ul>
                <li>
                    <span>{l s='The module can ONLY be hooked in one location, make sure to remove is from productFooter or extraRight if these custom hooks are used in your Quick View.' mod='attributewizardpro'}</span>
                </li>
                <li>
                    <span>{l s='In ' mod='attributewizardpro'}/themes/{$theme_folder}/templates/catalog/_partials/quickview.tpl{l s=' add' mod='attributewizardpro'}</span>  <span class="notes">{literal}{hook h="awpProduct"}{/literal}</span> </span>{l s='where you want to display the wizard, make sure it\'s not inside a <form> tag.      ' mod='attributewizardpro'}</span>
                </li>
            </ul>
        </div>
    </div>
        
    <div class="extra_line"></div>

    <div class="hook_instructions single_column">
        <div class="panel_header">
            <div class="hook_title panel_title">
                {l s='Dedicated Hook (Optional)' mod='attributewizardpro'}
            </div>
            <div class="panel_info_text">
                <span class="simple_alert"> </span>
                {l s='if you wish to diplay the wizard in a different location on the product page ' mod='attributewizardpro'}
            </div>
            <div class="clear"></div>
         </div>
  
        <div class="hook_content">
            <ul>
                <li>
                    <span>{l s='The module can ONLY be hooked in one location, make sure to remove is from productFooter if you used the custom hook.' mod='attributewizardpro'}</span>
                </li>
                <li>
                    <span>{l s='In ' mod='attributewizardpro'}/themes/{$theme_folder}/templates/catalog/product.tpl{l s=' add' mod='attributewizardpro'}</span>  <span class="notes">{literal}{hook h="awpProduct"}{/literal}</span> </span>{l s='where you want to display the wizard, make sure it\'s not inside a <form> tag.      ' mod='attributewizardpro'}</span>
                </li>
            </ul>
        </div>
    </div>
                
                
    <div class="special_instructions single_column">
        <div class="special_instructions_header">
                {l s='Please read this information carefully, adding attributes is done differently than before!' mod='attributewizardpro'}
        </div>
        <div class="special_instructions_content">
            <ul>
                <li>
                    <span>{l s='To add attributes, do not use the regular combinations tab.' mod='attributewizardpro'}</span>
                    <br/>
                    <span class="important_alert"> </span>
                    <span class="important">
                        {l s='Use the new combination generator provided by AWP module included in Modules tab from Edit Product!' mod='attributewizardpro'}
                    </span>
                </li>
                <li>
                    <span>{l s='Unlike the default way PrestaShop handles combinations (each combination contains ONLY 1 attribute from EACH group), with our module, each combination can have 1 or more attributes from ONLY 1 group (which requires far less combinations).' mod='attributewizardpro'}</span>
                </li>
                
            </ul>
        </div>            
    </div>
      {*          
    <div class="special_instructions single_column">
        <div class="special_instructions_header">
                {l s='There are 2 ways to structure combinations, you will see them in ' mod='attributewizardpro'}
                <a href="" target="_blank">Example 1</a> and <a href="" target="_blank">Example 2.</a>
        </div>
        <div class="special_instructions_content">
            <ul>
                <li>
                    <span>{l s='If all the items from a group have the same price, weight and share quantity, they can all be grouped in a signle combination ' mod='attributewizardpro'}</span>
                    <a href="" target="_blank">Example 1 </a>
                    
                    <a class="info_alert" href="#example_1"></a>
                    <div id="example_1" class="hideADN info_popup">
                        <div class="panel">
                            <h3>
                                {l s='Customer Information Manager' mod='attributewizardpro'}
                                <span class="info_icon"> </span>
                            </h3>
                            <div class="upgrade_check_content">
                                {l s='By providing quick access to stored customer information, Customer Information Manager (CIM) is ideal for businesses that:' mod='attributewizardpro'}
                                <br/><br/>
                                
                                <img src="{$path}views/img/help_fus.jpg"> 
                            </div>
                        </div>
                    </div>
                                
                </li>
                <li>
                    <span>{l s='If they need to have a different price, weight or quantity, add them individually ' mod='attributewizardpro'}</span>
                    <a href="" target="_blank">Example 2 </a>
                    <a class="info_alert" href="#example_2"></a>
                    <div id="example_2" class="hideADN info_popup">
                        <div class="panel">
                            <h3>
                                {l s='Customer Information Manager' mod='attributewizardpro'}
                                <span class="info_icon"> </span>
                            </h3>
                            <div class="upgrade_check_content">
                                {l s='By providing quick access to stored customer information, Customer Information Manager (CIM) is ideal for businesses that:' mod='attributewizardpro'}
                                <br/><br/>
                                
                                <img src="{$path}views/img/help_fus.jpg"> 
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <span>{l s='Do this for each attribute group ' mod='attributewizardpro'}</span>
                    <a href="" target="_blank">Example 3 </a>
                    <a class="info_alert" href="#example_3"></a>
                    <div id="example_3" class="hideADN info_popup">
                        <div class="panel">
                            <h3>
                                {l s='Customer Information Manager' mod='attributewizardpro'}
                                <span class="info_icon"> </span>
                            </h3>
                            <div class="upgrade_check_content">
                                {l s='By providing quick access to stored customer information, Customer Information Manager (CIM) is ideal for businesses that:' mod='attributewizardpro'}
                                <br/><br/>
                                
                                <img src="{$path}views/img/help_fus.jpg"> 
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <span>{l s='Finally, to define the defaults for each group, you MUST CREATE A NEW COMBINATION with one item from each group (for groups that will use checkboxes, you can use 0 or multiple items, This will also be the default when a customer clicks "Add to cart" from pages other than the product page) ' mod='attributewizardpro'}</span>
                    <a href="" target="_blank">Example 4 </a>
                    <a class="info_alert" href="#example_4"></a>
                    <div id="example_4" class="hideADN info_popup">
                        <div class="panel">
                            <h3>
                                {l s='Customer Information Manager' mod='attributewizardpro'}
                                <span class="info_icon"> </span>
                            </h3>
                            <div class="upgrade_check_content">
                                {l s='By providing quick access to stored customer information, Customer Information Manager (CIM) is ideal for businesses that:' mod='attributewizardpro'}
                                <br/><br/>
                                
                                <img src="{$path}views/img/help_fus.jpg"> 
                            </div>
                        </div>
                    </div>
                </li>
                <li class="notes">
                    <span class="notes">{l s='When a customer adds a product to the cart, a new temporary group is created (awp_details), you should delete them once in a while using the "Delete Temporary Attributes" button. This will not affect existing order details.' mod='attributewizardpro'}</span>
                </li>
               
            </ul>
        </div>            
    </div>
    *}
</div>

