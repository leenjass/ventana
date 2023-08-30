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

<script type="text/javascript">
    var baseDir = '{$module_dir}/';
    var id_lang = '{$id_lang}';
    var id_employee = '{$id_employee}';
</script>



<div class="panel po_main_content" id="basic_settings">
    <form action="{$request_uri}" method="post">
        <div class="panel_header">
            <div class="panel_title">{l s='Basic Settings' mod='attributewizardpro'}</div>
            <div class="panel_info_text">
                <span class="simple_alert"> </span>
                {l s='You must click on Update for a change to take effect' mod='attributewizardpro'}
            </div>
            <div class="clear"></div>
        </div>
        <div class="two_columns">
            <div class="columns">
                <div class="left_column">
                    {l s='Display Wizard' mod='attributewizardpro'}
                    <a class="info_alert" href="#display_wizard"></a>
                    <div id="display_wizard" class="hideADN info_popup">
                        <div class="panel">
                            <h3>
                                {l s='Display Wizard' mod='attributewizardpro'}
                                <span class="info_icon"> </span>
                            </h3>
                            <div class="upgrade_check_content">
                                {l s='You can display the wizard only for certain products, all other products will load the normal attributes.' mod='attributewizardpro'}
                                <br/><br/>
                                {l s='You can use any of the product fields in the drop down and select a value, any product that will have that value set, will display the wizard.' mod='attributewizardpro'}
                                <br/><br/>
                                <img src='{$path}/views/img/help_dw.png' border=0 />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="right_column">
                    <input type="radio" name="awp_display_wizard" value="1" {if $awp_display_wizard == 1}checked="checked"{/if}/>
                    <span style="">{l s='For all products' mod='attributewizardpro'}</span>
                    <br/><br/>
                    <input type="radio" name="awp_display_wizard" value="0" {if $awp_display_wizard != 1}checked="checked"{/if}/>
                    <span style="">{l s='Only when' mod='attributewizardpro'}</span>

                </div>
            </div>

            <div class="columns">
                <div class="left_column">
                </div>
                <div class="right_column">
                    <select name="awp_display_wizard_field">
                        <option value="Reference" {if $awp_display_wizard_field == 'Reference'}selected{/if}>{l s='Reference' mod='attributewizardpro'}</option>
                        {*<option value="Supplier Reference" {if $awp_display_wizard_field == 'Supplier Reference'}selected{/if}>{l s='Supplier Reference' mod='attributewizardpro'}</option>*}
                        <option value="EAN13" {if $awp_display_wizard_field == 'EAN13'}selected{/if}>{l s='EAN13' mod='attributewizardpro'}</option>
                        <option value="UPC" {if $awp_display_wizard_field == 'UPC'}selected{/if}>{l s='UPC' mod='attributewizardpro'}</option>
                        {*<option value="Location" {if $awp_display_wizard_field == 'Location'}selected{/if}>{l s='Location' mod='attributewizardpro'}</option>*}
                    </select>

                </div>
            </div>

            <div class="columns">
                <div class="left_column">
                </div>
                <div class="right_column">
                    <span class="awp_display_wizard_value_label">{l s='Is set to: ' mod='attributewizardpro'}</span>
                    <input type="text"  name="awp_display_wizard_value" id="awp_display_wizard_value" value="{$awp_display_wizard_value|escape:'htmlall':'UTF-8'}">

                </div>
            </div>

            <div class="columns">
                <div class="left_column">
                    {l s='Wizard Location' mod='attributewizardpro'}
                </div>
                <div class="right_column">
                    <input type="radio" id="awp_in_page" name="awp_popup" value="0" {if $awp_popup != 1}checked{/if}/>
                    <span style="">{l s='In Page:' mod='attributewizardpro'}</span>
                    <br/><br/>
                    <input type="radio" id="awp_in_popup" name="awp_popup" value="1" {if $awp_popup == 1}checked{/if}/>
                    <span style="">{l s='In Popup:' mod='attributewizardpro'}</span>

                </div>
            </div>

            <div class="columns popup_config">
                <div class="left_column">
                    {l s='Fade Background' mod='attributewizardpro'}
                </div>
                <div class="right_column">
                    <input type="checkbox" name="awp_fade" id="awp_fade" value="1" {if $awp_fade == 1}checked{/if}/>
                </div>
            </div>
            <div class="columns popup_config">
                <div class="left_column">
                    {l s='Opacity' mod='attributewizardpro'}
                </div>
                <div class="right_column">
                    <input type="text" name="awp_opacity" id="awp_opacity" value="{$awp_opacity|floatval}" />
                    0-100
                </div>
            </div>
            <div  class="columns popup_config">
                <div class="left_column">
                    {l s='Include Product Image' mod='attributewizardpro'}
                </div>
                <div class="right_column">
                    <input type="checkbox" name="awp_popup_image" id="awp_popup_image" value="1" {if $awp_popup_image == 1}checked{/if} />

                </div>
            </div>

            <div  class="columns popup_config">
                <div class="left_column">
                    {l s='Image Type' mod='attributewizardpro'}
                </div>
                <div class="right_column">
                    <select name="awp_popup_image_type">
                        {$image_formats_options} {* HTML SENT - CANNOT ESCAPE *}
                    </select>
                </div>
            </div>

            <div style="display:none" class="columns">
                <div class="left_column">
                    {l s='Width' mod='attributewizardpro'}
                </div>
                <div class="right_column">
                    <input type="text" name="awp_popup_width" id="awp_popup_width" value="{$awp_popup_width|intval}" />
                </div>
            </div>

            <div style="display:none" class="columns">
                <div class="left_column">
                    {l s='Top position' mod='attributewizardpro'}
                </div>
                <div class="right_column">
                    <input type="text" name="awp_popup_top"  id="awp_popup_top" value="{$awp_popup_top|intval}" />
                </div>
            </div>
            <div style="display:none" class="columns">
                <div class="left_column">
                    {l s='Left position' mod='attributewizardpro'}
                </div>
                <div class="right_column">
                    <input type="text" name="awp_popup_left" id="awp_popup_left" value="{$awp_popup_left|intval}" />
                    <br/>
                    {l s='The default is center, to change enter a value like 100 or -100' mod='attributewizardpro'}
                </div>
            </div>


            <div class="columns">
                <div class="left_column">
                    {l s='Group Image' mod='attributewizardpro'}
                    <a class="info_alert" href="#group_image"></a>
                    <div id="group_image" class="hideADN info_popup">
                        <div class="panel">
                            <h3>
                                {l s='Group Image' mod='attributewizardpro'}
                                <span class="info_icon"> </span>
                            </h3>
                            <div class="upgrade_check_content">
                                {l s='Group images will be displayed to the left of the attribute selection box, you can have them automatically resized, and assign a link to each image.' mod='attributewizardpro'}
                                <br/><br/>
                                <img src='{$path}/views/img/help_gi1.png'  border=0 />
                                <br/><br/>
                                {l s='Group images can be uploaded for each group below.' mod='attributewizardpro'}
                                <br/><br/>
                                <img src='{$path}/views/img/help_gi2.png'  border=0 />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="right_column">
                    <input onclick="update_image_resize()" type="checkbox" name="awp_image_resize" id="awp_image_resize" value="1" {if $awp_image_resize == 1}checked{/if}/>
                    {l s='Resize on upload, max width' mod='attributewizardpro'}
                    <br/>
                    <input onblur="update_image_resize()" type="text" name="awp_image_resize_width" id="awp_image_resize_width" value="{if $awp_width}{$awp_width|intval}{else}100{/if}" />

                </div>
            </div>
            <div class="columns">
                <div class="left_column">
                    {l s='Layered Images' mod='attributewizardpro'}
                    <a class="info_alert" href="#layered_image"></a>
                    <div id="layered_image" class="hideADN info_popup">
                        <div class="panel">
                            <h3>
                                {l s='Layered Images' mod='attributewizardpro'}
                                <span class="info_icon"> </span>
                            </h3>
                            <div class="upgrade_check_content">
                                {l s='When using layered images, product zoom will not be available.' mod='attributewizardpro'}
                                <br/><br/>
                                {l s='Layered images are assigned per attribute, when enabled, an option to upload an image is added below.' mod='attributewizardpro'}
                                <br/><br/>
                                <img src='{$path}/views/img/help_li1.png'   border=0 />
                                <br/><br/>
                                {l s='The size of each image must be exactly the same as the product image, and must be a transparent PNG.' mod='attributewizardpro'}
                                <br/><br/>
                                <img src='{$path}/views/img/help_li2.png'   border=0 />
                                {l s='You can assign different layer images for each attribute in each group, one image from each group will be on top of the product image (and each other).' mod='attributewizardpro'}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="right_column">
                    <select name="awp_layered_image" id="awp_layered_image_sel">
                        <option value="0" {if $awp_layered_image != '1'}selected{/if}>{l s='Disable' mod='attributewizardpro'}</option>
                        <option value="1" {if $awp_layered_image == '1'}selected{/if}>{l s='Enable' mod='attributewizardpro'}</option>
                    </select>
                    <br/>
                    {l s='You must click Update for a change to take affect.' mod='attributewizardpro'}

                </div>
            </div>

            <div class="columns">
                <div class="left_column">
                    {l s='File Upload Setting' mod='attributewizardpro'}
                    <a class="info_alert" href="#fileupload_setting"></a>
                    <div id="fileupload_setting" class="hideADN info_popup">
                        <div class="panel">
                            <h3>
                                {l s='File Upload Setting' mod='attributewizardpro'}
                                <span class="info_icon"> </span>
                            </h3>
                            <div class="upgrade_check_content">
                                {l s='When displaying attributes as File Upload, you can set a maximum file size the customer will be able to upload as well as thumbnail dimensions (if an image is uploaded, the thumbnail will display in the cart and order history)' mod='attributewizardpro'}
                                <br/><br/>
                                {l s='Each File Upload attribute has its own settings as well (like acceptable file extensions)' mod='attributewizardpro'}
                                <br/><br/>
                                <img src='{$path}/views/img/help_fus.png' border=0 />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="right_column">
                    {l s='Thumbnail Width/Height' mod='attributewizardpro'}
                    <input type="text" name="awp_thumbnail_size" id="awp_thumbnail_size" value="{if $awp_thumbnail_size}{$awp_thumbnail_size|intval}{else}60{/if}" />
                    <br/><br/>
                    {l s='Max Upload Size' mod='attributewizardpro'}
                    <input type="text" name="awp_upload_size" id="awp_upload_size" value="{if $awp_upload_size}{$awp_upload_size|intval}{else}2000{/if}" /> KB ({l s='Server limit = ' mod='attributewizardpro'} {$ini_max_upload_filesize|intval} KB)
                </div>
            </div>


            <div class="columns">
                <div class="left_column">
                    {l s='Add to Cart Display' mod='attributewizardpro'}
                </div>
                <div class="right_column">
                    <input type="radio" name="awp_add_to_cart" value="" {if !$awp_add_to_cart}checked{/if}/>
                    {l s='No Change ' mod='attributewizardpro'}
                    <br/>
                    <input type="radio" name="awp_add_to_cart" value="bottom" {if $awp_add_to_cart == "bottom"}checked{/if}/>
                    {l s='Add to Bottom ' mod='attributewizardpro'}
                    <br/>
                    <input type="radio"  name="awp_add_to_cart" value="scroll" {if $awp_add_to_cart == "scroll"}checked{/if}/>
                    {l s='Scroll Existing' mod='attributewizardpro'}
                    <br/>
                    <input type="radio" name="awp_add_to_cart" value="both" {if $awp_add_to_cart == "both"}checked{/if}/>
                    {l s='Both' mod='attributewizardpro'}
                </div>
            </div>
            <div class="columns">
                <div class="left_column">
                    {l s='Add to Cart button' mod='attributewizardpro'}
                </div>
                <div class="right_column">
                    {l s='Display additional button when more than' mod='attributewizardpro'}
                    <input type="text" name="awp_second_add"  id="awp_second_add" value="{$awp_second_add|intval}" />
                    {l s='attribute groups are used' mod='attributewizardpro'}
                    <br /><br />
                    <input type="checkbox"  name="awp_no_customize" value="1" {if $awp_no_customize == 1}checked{/if}/>
                    {l s='Do not replace with Customize (In page)' mod='attributewizardpro'}
                </div>
            </div>
            <div class="columns">
                <div class="left_column">
                    {l s='Unavailable / Out of Stock' mod='attributewizardpro'}
                </div>
                <div class="right_column">
                    <input type="radio" name="awp_out_of_stock" value="" {if !$awp_out_of_stock}checked{/if}/>
                    {l s='No Change ' mod='attributewizardpro'}
                    <br/>
                    <input type="radio" name="awp_out_of_stock" value="disable" {if $awp_out_of_stock == 'disable'}checked{/if}/>
                    {l s='Disable ' mod='attributewizardpro'}
                    <br/>
                    <input type="radio" name="awp_out_of_stock" value="hide" {if $awp_out_of_stock == 'hide'}checked{/if}/>
                    {l s='Hide' mod='attributewizardpro'}
                </div>
            </div>

            <div class="columns">
                <div class="left_column">
                    {l s='Price Impact Display' mod='attributewizardpro'}
                </div>
                <div class="right_column">
                    <input type="radio" name="awp_pi_display" value="" {if !$awp_pi_display}checked{/if}/>
                    {l s='None' mod='attributewizardpro'}
                    <br/>
                    <input type="radio" name="awp_pi_display" value="diff" {if $awp_pi_display == 'diff'}checked{/if}/>
                    {l s='Difference' mod='attributewizardpro'}
                    <br/>
                    <input type="radio" name="awp_pi_display" value="total" {if $awp_pi_display == 'total'}checked{/if}/>
                    {l s='Total' mod='attributewizardpro'}
                </div>
            </div>

            {*
            <div class="columns">
                <div class="left_column">
                    {l s='Not in Product Page' mod='attributewizardpro'}
                    <a class="info_alert" href="#notinproductpage_setting"></a>
                    <div id="notinproductpage_setting" class="hideADN info_popup">
                        <div class="panel">
                            <h3>
                                {l s='Not in Product Page Setting' mod='attributewizardpro'}
                                <span class="info_icon"> </span>
                            </h3>
                            <div class="upgrade_check_content">
                                {l s='You can disable or hide the "Add to Cart" button in product list pages.' mod='attributewizardpro'}
                                <br/><br/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="right_column">
                    <select name="awp_disable_hide">
                        <option value="0" {if $awp_disable_hide != 1}selected{/if}>{l s='Disable' mod='attributewizardpro'}</option>
                        <option value="1" {if $awp_disable_hide == 1}selected{/if}>{l s='Hide' mod='attributewizardpro'}</option>
                    </select>
                    
                    <br/>
                    <input type="radio" name="awp_disable_all" value="" {if $awp_disable_all == 0}checked{/if}/>
                    {l s='Products with a required field' mod='attributewizardpro'}
                    <br/>
                    <input type="radio" name="awp_disable_all" value="1" {if $awp_disable_all == 1}checked{/if}/>
                    {l s='All Products with attributes' mod='attributewizardpro'}
                </div>
            </div>
            *}
            <div class="columns">
                <div class="left_column">
                    {l s='No Attribute Selection' mod='attributewizardpro'}
                    <a class="info_alert" href="#notattributeselection_setting"></a>
                    <div id="notattributeselection_setting" class="hideADN info_popup">
                        <div class="panel">
                            <h3>
                                {l s='No Attribute Selection Setting' mod='attributewizardpro'}
                                <span class="info_icon"> </span>
                            </h3>
                            <div class="upgrade_check_content">
                                {l s='When using checkboxes, allow to add the product to the cart without any attributes selected, for example when offering accessories to a product. If Disabled is selected, at least one box would need to be ticked.' mod='attributewizardpro'}
                                <br/><br/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="right_column">
                    <input type="radio" name="awp_adc_no_attribute" value="" {if $awp_adc_no_attribute == 0}checked{/if}/>
                    {l s='Enabled' mod='attributewizardpro'}
                    <br/>
                    <input type="radio" name="awp_adc_no_attribute" value="1" {if $awp_adc_no_attribute == 1}checked{/if}/>
                    {l s='Disabled' mod='attributewizardpro'}
                </div>
            </div>


            <div class="columns">
                <div class="left_column">
                    {l s='Group Description Display' mod='attributewizardpro'}
                </div>
                <div class="right_column">
                    <input type="radio" name="awp_gd_popup" value="1" {if $awp_gd_popup == 1}checked{/if}/>
                    {l s='In Popup' mod='attributewizardpro'}
                    <br/>
                    <input type="radio" name="awp_gd_popup" value="0" {if $awp_gd_popup != 1}checked{/if}/>
                    {l s='Under Group Name' mod='attributewizardpro'}
                </div>
            </div>

            <div class="columns">
                <div class="left_column">
                    {l s='Expand / Collapse AWP blocks' mod='attributewizardpro'}
                </div>
                <div class="right_column">
                    <input type="radio" name="awp_collapse_block" value="1" {if $awp_collapse_block == 1}checked{/if}/>
                    {l s='Enable' mod='attributewizardpro'}
                    <br/>
                    <input type="radio" name="awp_collapse_block" value="0" {if $awp_collapse_block != 1}checked{/if}/>
                    {l s='Disable' mod='attributewizardpro'}
                </div>
            </div>
                
            <div class="columns">
                <div class="left_column">
                    {l s='Enable / Disable AWP URL hash' mod='attributewizardpro'}
                </div>
                <div class="right_column">
                    <input type="radio" name="awp_disable_url_hash" value="1" {if $awp_disable_url_hash == 1}checked{/if}/>
                    {l s='Enable' mod='attributewizardpro'}
                    <br/>
                    <input type="radio" name="awp_disable_url_hash" value="0" {if $awp_disable_url_hash != 1}checked{/if}/>
                    {l s='Disable' mod='attributewizardpro'}
                </div>
            </div>

            <div class="columns">
                <div class="left_column">
                    {l s='Enable / Disable AWP Attributes Sorting Alphabetically' mod='attributewizardpro'}
                    <a class="info_alert" href="#awpsortaplhab_setting"></a>
                    <div id="awpsortaplhab_setting" class="hideADN info_popup">
                        <div class="panel">
                            <h3>
                                {l s='Enable / Disable AWP Attributes Sorting Alphabetically' mod='attributewizardpro'}
                                <span class="info_icon"> </span>
                            </h3>
                            <div class="upgrade_check_content">
                                {l s='If this is enabled it will override drag-and-drop sorting in the Advanced Settings' mod='attributewizardpro'}
                                <br/><br/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="right_column">
                    <input type="radio" name="awp_sort_attributes_alphab" value="1" {if $awp_sort_attributes_alphab == 1}checked{/if}/>
                    {l s='Enable' mod='attributewizardpro'}
                    <br/>
                    <input type="radio" name="awp_sort_attributes_alphab" value="0" {if $awp_sort_attributes_alphab != 1}checked{/if}/>
                    {l s='Disable' mod='attributewizardpro'}
                </div>
            </div>

            <div class="columns">
                <div class="left_column">
                    <input type="submit" value="{l s='Update' mod='attributewizardpro'}" name="submitChanges" class="submit_button" />
                </div>
                <div class="right_column">
                    <input type="submit" onclick="return confirm(awp_confirm_reset)" value="{l s='Reset' mod='attributewizardpro'}" name="resetData" class="submit_button" />
                    <a class="info_alert" href="#resetdetails_btn"></a>
                    <div id="resetdetails_btn" class="hideADN info_popup">
                        <div class="panel">
                            <h3>
                                {l s='Reset Attribute Wizard Pro configuration' mod='attributewizardpro'}
                                <span class="info_icon"> </span>
                            </h3>
                            <div class="upgrade_check_content">
                                {l s='Will reset all the attribute selections without an option to undo.' mod='attributewizardpro'}
                                <br/><br/>
                                {l s='If you have added new attributes and you do not see them in the list below, you should click \'Reset\'.' mod='attributewizardpro'}
                                <br/><br/>
                                {l s='Deleting them will remove any products that are currently in customers\' carts, so do it during off peak hours.' mod='attributewizardpro'}
                                <br/><br/>
                            </div>
                        </div>
                    </div>

                    <br/><br/>
                    <input type="submit" value="{l s='Delete Temporary (awp_details) Attributes' mod='attributewizardpro'}" name="deleteAttributes" class="submit_button" />
                    <a class="info_alert" href="#awp_details_btn"></a>
                    <div id="awp_details_btn" class="hideADN info_popup">
                        <div class="panel">
                            <h3>
                                {l s='Delete Temporary (awp_details) Attributes' mod='attributewizardpro'}
                                <span class="info_icon"> </span>
                            </h3>
                            <div class="upgrade_check_content">
                                {l s='Whenever a product with attributes gets added to the cart, a new dynamic (temporary) combination is creted (awp_details)' mod='attributewizardpro'}
                                <br/><br/>
                                {l s='While leaving them there will have no negative affects on the site, you can delete them once in a while to reduce the clutter' mod='attributewizardpro'}
                                <br/><br/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="clear"></div>

    </form>
</div>