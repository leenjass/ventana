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
<div id="left_menu">

    <!-- Secondary menu - not all top menus have this option -->
    <div id="secondary_menu">
        <!-- Secondary menu - connected to First top menu item -->
        <div id="secondary_0" class="menu">

            <!-- Submenu with header -->
            <div id="secondary_0_0">
                <!-- Submenu header -->
                <div class="menu_header">
                    <span class="menu_header_text">{l s='Module Settings' mod='attributewizardpro'}</span>
                    <!-- Arrow - will allow to show / hide the submenu items -->
                    <!-- If you need a left menu item always visible just remove the span arrow -->
                    <span id="left_menu_arrow" class="arrow_up"></span>
                </div>
                <!-- END - Submenu header -->
                <!-- Submenu -->
                <div class="secondary_submenu">
                    <!-- Submenu without instructions -->
                    <div id="secondary_menu_item_0_0_1" class="secondary_menu_item" data-instructions="" data-content="basic_settings">
                        {l s='Basic Settings' mod='attributewizardpro'}
                    </div>
                    <!-- END Submenu without instructions -->
                    <div id="secondary_menu_item_0_0_2" class="secondary_menu_item" data-instructions="instructions-advanced-settings" data-content="advanced_settings">
                        {l s='Advanced Settings' mod='attributewizardpro'}
                    </div>                   
                </div>
                <!-- END - Submenu -->
            </div>
            <!-- END Submenu with header -->
            
        </div>
        <!-- END Secondary menu - connected to First top menu item -->
       
    </div>
    <!-- END  Secondary menu - not all top menus have this option -->
    <!-- Instructions Block - connected to left submenu items (only some submenus have this instructions) -->
    <div class="instructions">
        <div class="instructions_block" id="instructions-advanced-settings">
            <div class="instructions_title">
                <span class="icon"> </span>
                {l s='Tips' mod='attributewizardpro'}
            </div>
            <div class="instructions_content">
                <div class="instructions_line">
                    <span class="icon"> </span>
                    {l s='Set each group display type (radio, dropdown, checkbox, etc...), select the number of attributes to display in each row..' mod='attributewizardpro'}
                </div>
                <div class="instructions_line">
                    <span class="icon"> </span>
                    {l s='Select a layout ("Vertical" is better with multiple items per row, or "Horizontal") as well as image related settings.' mod='attributewizardpro'}
                </div>
                <div class="instructions_line">
                    <span class="icon"> </span>
                    {l s='Attribute Colors and Images are assigned from the existing PS interface (Catalog -> Attributes and Groups, make sure the group is set as "Color" and then edit each attribute)' mod='attributewizardpro'}
                </div>
            </div>
        </div>
       
    </div>   
    <!-- END Instructions Block - connected to left submenu items (only some submenus have this instructions) -->

    <!-- Required only for some menu items -->      
    {if $contactUsLinkPrestoChangeo != ''}
    <div class="contact_form_left_menu">
        <div class="contact_form_text">{l s='For any technical questions, or problems with the module, please contact us using our' mod='attributewizardpro'}</div>
        <a class="contact_button" href="{$contactUsLinkPrestoChangeo}">{l s='Contact form' mod='attributewizardpro'}</a>
    </div>
    {/if}

    <!-- END Required only for some menu items -->   
    <!-- Module Recommandations block -->
    <div id="module_recommandations" class="module_recommandations_top">
       {$getModuleRecommendations nofilter}
    </div>
    <!-- END Module Recommandations block -->
</div>