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
<script type="text/javascript" src="{$path}views/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="{$path}views/js/bootstrap-tokenfield.min.js"></script>
<script type="text/javascript" src="{$path}views/js/globalBack.js"></script>
<script type="text/javascript" src="{$path}views/js/specificBack.js"></script>

<script type="text/javascript" src="{$path}views/js/ajaxupload.js"></script>
<script type="text/javascript" src="{$path}views/js/jquery.tablednd_0_5.js"></script>
<script type="text/javascript" src="{$path}views/js/awp.js"></script>

<script type="text/javascript" src="{$base_uri}js/jquery/ui/jquery.ui.sortable.min.js"></script>

<script type="text/javascript">
    var awp_random = '{$awp_random}';
    
    var baseDirModule = '{$module_dir|escape:'htmlall':'UTF-8'}';
    var awp_shops = '{$awp_shops|escape:'htmlall':'UTF-8'}';
    var aw_copy_src = "{l s='You must enter a Source Product ID (to copy from)' mod='attributewizardpro'}";
    var aw_copy_tgt = "{l s='You must enter a Target Product or Category ID (to copy to)' mod='attributewizardpro'}";
    var aw_invalid_src = "{l s='Invalid Source ID' mod='attributewizardpro'}";
    var aw_invalid_tgt = "{l s='Invalid Target ID' mod='attributewizardpro'}";
    var aw_copy_same = "{l s='Source and Target ID must be different' mod='attributewizardpro'}";
    var aw_are_you = "{l s='Are you sure you want to copy the attributes From' mod='attributewizardpro'}";
    var aw_will_delete = "{l s='This will delete all the existing attributes in the Target Product or Category' mod='attributewizardpro'}";
    var aw_to = "{l s='to' mod='attributewizardpro'}";
    var aw_copy = "{l s='Copy' mod='attributewizardpro'}";
    var aw_cancel = "{l s='Cancel' mod='attributewizardpro'}";
    var aw_copied = "{l s='Attributes Copied' mod='attributewizardpro'}";
    var aw_change_image = "{l s='Edit' mod='attributewizardpro'}";
    var awp_id_lang = {$id_lang|intval};
    var awp_delete = "{l s='Delete' mod='attributewizardpro'}";
    var awp_upload_img = "{l s='Upload Image' mod='attributewizardpro'}";
    
    var awp_edit_img = "{l s='Edit Image' mod='attributewizardpro'}";
</script>

<div id="module_top">
    <div id="module_header">
        <div class="module_name_presto">
            {$module_name}
            <span class="module_version">{$mod_version}</span>
            {if $contactUsLinkPrestoChangeo != ''}
                <div class="module_upgrade {if $upgradeCheck}showBlock{else}hideBlock{/if}">
                    {l s='A new version is available.' mod='attributewizardpro'}
                    <a href="{$contactUsLinkPrestoChangeo}#upgrade">{l s='Upgrade now' mod='attributewizardpro'}</a>
                </div>
            {/if}
        </div>
        {if $contactUsLinkPrestoChangeo != ''}   
        <div class="request_upgrade">
            <a href="{$contactUsLinkPrestoChangeo}#upgrade">{l s='Request an Upgrade' mod='attributewizardpro'}</a>
        </div>
        <div class="contact_us">
            <a href="{$contactUsLinkPrestoChangeo}#customerservice">{l s='Contact us' mod='attributewizardpro'}</a>
        </div>

        <div class="presto_logo"><a href="{$contactUsLinkPrestoChangeo}">{$logoPrestoChangeo nofilter}</a></div>
        <div class="clear"></div>
        {/if}
    </div>
    
    
    <!-- Module upgrade popup -->
    {if $displayUpgradeCheck != ''}
    <a id="open_module_upgrade" href="#module_upgrade"></a>
    <div id="module_upgrade">
        {$displayUpgradeCheck nofilter}
    </div>
    {/if}
    <!-- END - Module upgrade popup -->
    <div class="clear"></div>
    <!-- Main menu - each main menu is connected to a submenu with the data-left-menu value -->
    <div id="main_menu">
        <div id="menu_0" class="menu_item" data-left-menu="secondary_0" data-content="basic_settings">{l s='Configuration' mod='attributewizardpro'}</div>
        <div id="menu_1" class="menu_item" data-contact-us="1" data-content="installation_instructions">{l s='Installation Instructions' mod='attributewizardpro'}</div> 
        <div id="menu_2" class="menu_item" data-contact-us="1" data-content="import_settings">{l s='Attribute and Combination Information' mod='attributewizardpro'}</div> 
        <div id="menu_3" class="menu_item" data-contact-us="1" data-content="copy_attributes">{l s='Copy Attributes' mod='attributewizardpro'}</div> 
        
        <div class="clear"></div>
    </div>
    <!-- END Main menu - each main menu is connected to a submenu with the ALT value -->
</div>
<div class="clear"></div>