{*
 * 2008 - 2017 Presto-Changeo
 *
 * MODULE Attribute Wizard
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
    
    var total_groups = '{$ordered_groups|count|intval}';
    var awp_layered_image = '{$awp_layered_image|intval}';
    var module_uri = '{$module_uri nofilter}';
</script>
<div class="panel po_main_content" id="advanced_settings">
    <form action="{$request_uri nofilter}#advanced_settings" method="post" name="wizard_form" id="wizard_form">
        <input type="hidden" name="awp_id_lang" id="awp_id_lang" value="{$id_lang|intval}">
        <div class="panel_header">
            <div class="panel_title">{l s='Advanced Settings' mod='attributewizardpro'}</div>
            <div class="panel_info_text">
                
                <div class="switch_block">
                    <span class="switch_label">{l s='Turn on TinyMCE Editor for all' mod='attributewizardpro'}</span>
                    <span class="switch presto-switch presto-fixed-width-lg">
                        <input type="radio" name="tiny_mce_all" id="tiny_mce_all_on" class="tiny_mce_all_on" value="1" {if $tiny_mce_all} checked="checked"{/if}>
                        <label for="tiny_mce_all_on" class="radioCheck"></label>
  <input type="radio" name="tiny_mce_all" id="tiny_mce_all_off" class="tiny_mce_all_off" value="0" {if !$tiny_mce_all} checked="checked"{/if}>
                        <label for="tiny_mce_all_off" class="radioCheck"></label>
                        <a class="slide-button btn"></a>
                    </span>
                </div>
                  
                <div class="expand_all" >
                    <span class="expand" onClick="presto_toggle_all(0)">{l s='Collapse all' mod='attributewizardpro'}</span>
                    <span class="arrow_up" onClick="presto_toggle_all(0)">
                        
                    </span>
                    <span class="collapse hideADN" onClick="presto_toggle_all(1)">{l s='Expand all' mod='attributewizardpro'}</span>
                    <span class="arrow_down hideADN" onClick="presto_toggle_all(1)">
                        
                    </span>
                </div>
            </div>
            <div class="clear"></div>
        </div>
        <ul id="awp_first-languages">
        {foreach from=$languages key=myId item=language name=languages_list}
            <li id="awp_lang_{$language.id_lang|intval}" {if $language.id_lang == $id_lang}class="selected_language"{/if}>
                <input type="hidden" name="awp_li_lang_{$smarty.foreach.languages_list.index|intval}" id="awp_li_lang_{$smarty.foreach.languages_list.index|intval}" value="{$language.id_lang|intval}" />
                <img onclick="awp_update_lang(true);awp_select_lang({$language.id_lang|intval})" src="{$theme_lang_dir|escape:'htmlall':'UTF-8'}{$language.id_lang|intval}.jpg" alt="{$language.name|escape:'htmlall':'UTF-8'}" />
            </li>
        {/foreach}
        </ul>
        <div class="clear"></div>
            
        <div id="sortable" class="attribute_groups_sort table_format">
            <div class="groups-pagination">
                <label>{l s='Groups count to display' mod='attributewizardpro'}</label>
                <select name="groups_count">
                    <option value="">{l s='All' mod='attributewizardpro'}</option>
                    {if $n != 10 && $n != 25 && $n != 50}
                        <option value="{$n}" selected="selected">{$n}</option>
                    {/if}
                    <option value="10" {if $n == 10}selected="selected"{/if}>10</option>
                    <option value="25" {if $n == 25}selected="selected"{/if}>25</option>
                    <option value="50" {if $n == 50}selected="selected"{/if}>50</option>
                </select>
            </div>
            <div class="row_format row_header">
                <div class="column_format column_1 header_column">
                    
                </div>
                <div class="column_format column_2 header_column">
                    {l s='Group Name' mod='attributewizardpro'}
                </div>
                <div class="column_format column_3 header_column">
                    {l s='Group Image' mod='attributewizardpro'}
                </div>
                <div class="column_format column_4 header_column">
                    {l s='Attribute Type' mod='attributewizardpro'}
                </div>
                <div class="column_format column_5 header_column">
                    {l s='Attribute Order' mod='attributewizardpro'}
                </div>
                <div class="column_format column_6 header_column">
                </div>
                <div class="clear"></div>
            </div>
                

            {foreach from=$ordered_groups key=myId item=group name=ordered_group}
                <div class="row_format" id="row_{$group.id_attribute_group|intval}">
                    <div class="column_format column_1">
                        <img src="{$path|escape:'htmlall':'UTF-8'}views/img/sort_icon.png" />
                    </div>
                    <div class="column_format column_2">
                        <div class="fixed_height" onclick="presto_toggle({$group.id_attribute_group|intval})">
                            {$group.group_name|escape:'htmlall':'UTF-8'}
                        </div>
                        <div data-id="{$group.id_attribute_group|intval}" data-order="{$smarty.foreach.ordered_group.index|intval}" class="fixed_empty_height"></div>
                        <div data-id="{$group.id_attribute_group|intval}" data-order="{$smarty.foreach.ordered_group.index|intval}" id="description_{$group.id_attribute_group|intval}_text">
                            {l s='Add Description' mod='attributewizardpro'}
                        </div>
                        <div data-id="{$group.id_attribute_group|intval}" data-order="{$smarty.foreach.ordered_group.index|intval}" id="description_container_{$group.id_attribute_group|intval}" class="awp_tinymce" >
                            <textarea class="autoload_rte" onchange="awp_update_lang(false)" id="description_{$group.id_attribute_group|intval}" name="description_{$group.id_attribute_group|intval}" ></textarea>

                            {foreach $languages as $language}
                                {assign var='tmpDescr' value="group_description_`$language.id_lang`"}
                                {assign var='tmpHeader' value="group_header_`$language.id_lang`"}
                               
                                <input type="hidden" id="description_{$group.id_attribute_group|intval}_{$language.id_lang|intval}" name="description_{$group.id_attribute_group|intval}_{$language.id_lang|intval}" value="{if isset($group.$tmpDescr)}{$group.$tmpDescr nofilter}{/if}">
                                <input type="hidden" class="full_width_input" name="group_header_{$group.id_attribute_group|intval}_{$language.id_lang|intval}" id="group_header_{$group.id_attribute_group|intval}_{$language.id_lang|intval}" value="{if isset($group.$tmpHeader)}{$group.$tmpHeader nofilter}{/if}">
                            {/foreach}
                        </div>
                        {if $awp_enable_parent_group}
                            <div class="parent-group-name-container hide">
                                <div data-id="{$group.id_attribute_group|intval}" data-order="{$smarty.foreach.ordered_group.index|intval}" class="fixed_empty_height"></div>
                                <div data-id="{$group.id_attribute_group|intval}" data-order="{$smarty.foreach.ordered_group.index|intval}" id="parent_group_name_{$group.id_attribute_group|intval}_text">
                                    {l s='Parent Group Name' mod='attributewizardpro'}
                                </div>
                                <div data-id="{$group.id_attribute_group|intval}" data-order="{$smarty.foreach.ordered_group.index|intval}" id="parent_group_name_container_{$group.id_attribute_group|intval}" >
                                    <input type="text" onchange="awp_update_lang(false)" id="parent_group_name_{$group.id_attribute_group|intval}" name="parent_group_name_{$group.id_attribute_group|intval}" class="pgn">
                                    {foreach $languages as $language}
                                        {assign var='tmpParentGroupName' value="parent_group_name_`$language.id_lang`"}
                                        <input type="hidden" id="parent_group_name_{$group.id_attribute_group|intval}_{$language.id_lang|intval}" name="parent_group_name_{$group.id_attribute_group|intval}_{$language.id_lang|intval}" value="{if isset($group.$tmpParentGroupName)}{$group.$tmpParentGroupName nofilter}{/if}">
                                    {/foreach}
                                </div>
                            </div>

                            <script type="text/javascript">
                                {literal}
                                $(function(){
                                    $('#parent_group_name_{/literal}{$group.id_attribute_group|intval}{literal}').tokenfield({
                                      autocomplete: {
                                        source: function (request, response) {
                                            $.get(baseDirModule + "pgn.php", {
                                                query: request.term,
                                                awp_random: awp_random,
                                                action: 'get_parent_group_names',
                                                id_lang: $("#awp_id_lang").val()
                                            }, function (data) {
                                                var d = new Array();
                                                var el;
                                                data = $.parseJSON(data);
                                                for(var i = 0; i < data.pgn.length; i++) {
                                                    if(data.pgn[i]){
                                                        el = data.pgn[i].split(',');
                                                        d = d.concat(el);
                                                    }
                                                }

                                                $('.pgn').each(function(){
                                                    var data = $(this).val();
                                                    if(data){
                                                        data = data.split(',');
                                                    }

                                                    d = d.concat(data);
                                                });

                                                d = d.map(function (el) {
                                                  return el.trim();
                                                });

                                                var d = d.filter(function (el) {
                                                  return el != '';
                                                });

                                                d = $.unique(d.sort());
                                                response(d);
                                            });
                                        },
                                        minLength: 3
                                      },
                                      showAutocompleteOnFocus: true
                                    })
                                });
                                {/literal}
                            </script>
                        {/if}
                        
                        <div data-id="{$group.id_attribute_group|intval}" data-order="{$smarty.foreach.ordered_group.index|intval}" class="switch_block">
                            <span class="switch_label">{l s='TinyMCE Editor' mod='attributewizardpro'}</span>
                            <span class="switch presto-switch presto-fixed-width-lg">
                                <input class="tiny_mce_on" data-id="{$group.id_attribute_group|intval}" type="radio" name="tiny_mce_{$group.id_attribute_group|intval}" id="tiny_mce_{$group.id_attribute_group|intval}_on" value="1" {if !empty($group.group_tinymce) || $tiny_mce_all} checked="checked"{/if}>
                                <label for="tiny_mce_{$group.id_attribute_group|intval}_on" class="radioCheck"></label>
                                <input class="tiny_mce_off" data-id="{$group.id_attribute_group|intval}" type="radio" name="tiny_mce_{$group.id_attribute_group|intval}" id="tiny_mce_{$group.id_attribute_group|intval}_off" value="0"{if empty($group.group_tinymce) && !$tiny_mce_all} checked="checked"{/if}>
                                <label for="tiny_mce_{$group.id_attribute_group|intval}_off" class="radioCheck"></label>
                                <a class="slide-button btn"></a>
                            </span>
                        </div>
                    </div>
                    <div class="column_format column_3">
                        <input type="hidden" id="id_group_{$smarty.foreach.ordered_group.index|intval}" name="id_group_{$smarty.foreach.ordered_group.index|intval}" value="{$group.id_attribute_group|intval}" />

                        <div id="upload_container_{$smarty.foreach.ordered_group.index|intval}">
                            
                            <div data-order="{$smarty.foreach.ordered_group.index|intval}" data-id="{$group.id_attribute_group|intval}"  class="fixed_height" id="image_container_{$smarty.foreach.ordered_group.index|intval}">
                                {if $group.filename}
                                    <img src="{$group.filename nofilter}" />
                                    
                                {/if}
                            </div>
                            
                            <div data-order="{$smarty.foreach.ordered_group.index|intval}" data-id="{$group.id_attribute_group|intval}" class="fixed_empty_height"></div>
                            {if $group.filename}
                            <div id="edit_new_block_{$smarty.foreach.ordered_group.index|intval}" data-order="{$smarty.foreach.ordered_group.index|intval}" data-id="{$group.id_attribute_group|intval}">   
                            {/if}
                                <input id="upload_button_{$smarty.foreach.ordered_group.index|intval}" class="button {if $group.filename}edit_btn{else}upload_btn{/if}" value="{if $group.filename}{l s='Edit' mod='attributewizardpro'}{else}{l s='Upload Image' mod='attributewizardpro'}{/if}" type="button">

                                
                                <input id="delete_button_{$smarty.foreach.ordered_group.index|intval}" class="button delete_btn {if !$group.filename}hideADN{/if}" value="{l s='Delete' mod='attributewizardpro'}" type="button">
                                
                            {if $group.filename}
                            </div>
                            {/if}    
                            {if $group.filename}
                               
                                
                               
                                <div data-order="{$smarty.foreach.ordered_group.index|intval}" data-id="{$group.id_attribute_group|intval}" id="image_url_{$smarty.foreach.ordered_group.index|intval}">
                                    {l s='Image URL:' mod='attributewizardpro'} 
                                    <input type="text" name="group_url_{$group.id_attribute_group|intval}" value="{if isset($group.group_url)}{$group.group_url nofilter}{else}{/if}">
                                    <br />
                                </div>
                            {else}
                                
                                <div data-order="{$smarty.foreach.ordered_group.index|intval}" data-id="{$group.id_attribute_group|intval}" id="image_url_{$smarty.foreach.ordered_group.index|intval}">
                                    {l s='Image URL:' mod='attributewizardpro'} 
                                    <input type="text" name="group_url_{$group.id_attribute_group|intval}" value="{if isset($group.group_url)}{$group.group_url nofilter}{/if}">
                                    <br />
                                </div>   
                            
                                
                            {/if}
                        </div>

                    </div>
                    <div class="column_format column_4">
                                               
                        <select attr-color="{if $group.group_color}{$group.group_color|intval}{else}0{/if}" class="fixed_height attribute_type group-type" name="group_type_{$group.id_attribute_group|intval}" onchange="presto_toggle({$group.id_attribute_group|intval}, true);awp_change_type(this, {$group.id_attribute_group|intval}, {if $group.group_color}{$group.group_color|intval}{else}0{/if})">
                                <option value="image" {if $group.group_type == 'image'}selected{/if}>{l s='Image (Single-Select)' mod='attributewizardpro'}</option>
                                <!--<option value="images" '.($group['group_type'] == "images"?"selected":"").'>'.$this->l('Image (Multi-Select)').'</option>-->
                                <option value="radio" {if $group.group_type == 'radio'}selected{/if}>{l s='Radio Button' mod='attributewizardpro'}</option>
                                <option value="checkbox" {if $group.group_type == 'checkbox'}selected{/if}>{l s='Checkbox' mod='attributewizardpro'}</option>
                                <option value="dropdown" {if $group.group_type == 'dropdown'}selected{/if}>{l s='Dropdown' mod='attributewizardpro'}</option>
                                <option value="textbox" {if $group.group_type == 'textbox'}selected{/if}>{l s='Textbox' mod='attributewizardpro'}</option>
                                <option value="textarea" {if $group.group_type == 'textarea'}selected{/if}>{l s='Textarea' mod='attributewizardpro'}</option>
                                <option value="file" {if $group.group_type == 'file'}selected{/if}>{l s='File Upload' mod='attributewizardpro'}</option>
                                <option value="quantity" {if $group.group_type == 'quantity'}selected{/if}>{l s='Quantity' mod='attributewizardpro'}</option>
                                <option value="hidden" {if $group.group_type == 'hidden'}selected{/if}>{l s='Hidden' mod='attributewizardpro'}</option>
                                <!--<option value="calculation" '.($group['group_type'] == "calculation"?"selected":"").'>'.$this->l('Calculation').'</option>-->
                        </select>
                        <div data-id="{$group.id_attribute_group|intval}" data-order="{$smarty.foreach.ordered_group.index|intval}" class="fixed_empty_height"></div>
                        <div class="" data-id="{$group.id_attribute_group|intval}" data-order="{$smarty.foreach.ordered_group.index|intval}" id="ipr_container_{$group.id_attribute_group|intval}">
                            <div class="two_columns checkboxView {$ipr_arr_class} {if !in_array($group.group_type, $ipr_arr)}hideADN{/if}">
                                <div class="columns">
                                    <div class="left_column">
                                        {l s='Per Row' mod='attributewizardpro'}
                                    </div>
                                    <div class="right_column">
                                        <input type="text" name="group_per_row_{$group.id_attribute_group|intval}" id="group_per_row_{$group.id_attribute_group|intval}" style="width:50px;display:inline;" value="{if $group['group_per_row']}{$group['group_per_row']|intval}{else}1{/if}" />
                                    </div>
                                </div>

                            </div>
                            <div class="clear"></div>
                        </div>
                        <div class="" data-id="{$group.id_attribute_group|intval}" data-order="{$smarty.foreach.ordered_group.index|intval}" id="il_container_{$group.id_attribute_group|intval}">
                            <div class="two_columns radioView {$ale_arr_class} {if !in_array($group.group_type, $ale_arr)}hideADN{/if}">
                                <div class="columns">
                                    <div class="left_column">
                                        {l s='Attribute Layout' mod='attributewizardpro'}
                                    </div>
                                    <div class="right_column">
                                        <small>
                                            <input type="radio" style="border:none;padding:0;margin:0" name="group_layout_{$group.id_attribute_group|intval}" {*id="group_layout_{$group.id_attribute_group|intval}"*} value="0" {if !isset($group.group_layout) || ($group.group_layout == 0)}checked{/if}/>
                                            {l s='Horizontal' mod='attributewizardpro'}<br />
                                            <input type="radio" style="border:none;padding:0;margin:0" name="group_layout_{$group.id_attribute_group|intval}" {*id="group_layout_{$group.id_attribute_group|intval}"*} value="1" {if isset($group.group_layout) && ($group.group_layout == 1)}checked{/if} />
                                            {l s='Vertical' mod='attributewizardpro'}
                                        </small>
                                    </div>
                                </div>

                            </div>
                            <div class="clear"></div>
                        </div>
                        
                            <div data-id="{$group.id_attribute_group|intval}" data-order="{$smarty.foreach.ordered_group.index|intval}" id="size_container_{$group.id_attribute_group|intval}">
                                <div class="two_columns {$ale_arr_class} {if !in_array($group.group_type, $ale_arr)}hideADN{/if}">
                                    <div class="columns">
                                        <div class="left_column">
                                            {l s='Cell Size' mod='attributewizardpro'}
                                        </div>
                                        <div class="right_column">

                                            <input type="text" name="group_width_{$group.id_attribute_group|intval}" id="group_width_{$group.id_attribute_group|intval}" value="{if isset($group.group_width)}{$group.group_width|intval}{/if}" />
                                            {l s='W' mod='attributewizardpro'}
                                            <br/>
                                            <br/>
                                            <input type="text" name="group_height_{$group.id_attribute_group|intval}" id="group_height_{$group.id_attribute_group|intval}" value="{if isset($group.group_height)}{$group.group_height|intval}{/if}" />
                                            {l s='H' mod='attributewizardpro'}
                                        </div>
                                    </div>

                                </div>
                                <div class="clear"></div>
                            </div>            
                           
                        {if $group.group_color == 1}
                           <div class="" data-id="{$group.id_attribute_group|intval}" data-order="{$smarty.foreach.ordered_group.index|intval}" id="resize_container_{$group.id_attribute_group|intval}">
                                <div class="two_columns ">
                                    <div class="columns">
                                        <div class="left_column">
                                            {l s='Resize Textures' mod='attributewizardpro'}
                                        </div>
                                        <div class="right_column">
                                           <input type="checkbox" name="group_resize_{$group.id_attribute_group|intval}" id="group_resize_{$group.id_attribute_group|intval}" value="1" {if isset($group.group_resize) && $group.group_resize == 1}checked{/if}/>
 
                                        </div>
                                    </div>

                                </div>
                               <div class="clear"></div>
                            </div> 
                        {/if}
                       <div data-id="{$group.id_attribute_group|intval}" data-order="{$smarty.foreach.ordered_group.index|intval}" id="ext_container_{$group.id_attribute_group|intval}">
                            <div class="two_columns fileOpt {if $group.group_type != 'file'}hideADN{/if} ">
                                <div class="columns">
                                    <div class="left_column">
                                        {l s='Allowed Extentions' mod='attributewizardpro'}
                                    </div>
                                    <div class="right_column">
                                        <input type="text" style="min-width:60px;display:inline;" name="group_file_ext_{$group.id_attribute_group|intval}" id="group_file_ext_{$group.id_attribute_group|intval}" value="{if isset($group.group_file_ext)}{$group.group_file_ext}{else}jpg|png|jpeg|gif{/if}" />
                                        <br />
                                        {l s='To include more use |extention (I.E |pdf|bmp)' mod='attributewizardpro'}
                                    </div>
                                </div>

                            </div>
                            <div class="clear"></div>
                        </div>            
                        <div data-id="{$group.id_attribute_group|intval}" data-order="{$smarty.foreach.ordered_group.index|intval}" id="hin_container_{$group.id_attribute_group|intval}">
                            <div class="two_columns {$hin_arr_class} {if !in_array($group.group_type, $hin_arr)}hideADN{/if}">
                                <div class="columns">
                                    <div class="left_column">
                                        {l s='Hide Item Name' mod='attributewizardpro'}
                                    </div>
                                    <div class="right_column">

                                       <input type="checkbox" name="group_hide_name_{$group.id_attribute_group|intval}" id="group_hide_name_{$group.id_attribute_group|intval}" value="1" {if isset($group.group_hide_name) && $group.group_hide_name == 1}checked{/if}/>

                                    </div>
                                </div>

                            </div>
                           <div class="clear"></div>
                        </div>   
                                       
                       <div class="" data-id="{$group.id_attribute_group|intval}" data-order="{$smarty.foreach.ordered_group.index|intval}" id="min_limit_container_{$group.id_attribute_group|intval}">
                            <div class="two_columns {$ml_arr_class} {if !in_array($group.group_type, $ml_arr)}hideADN{/if}">
                                <div class="columns">
                                    <div class="left_column">
                                        {l s='Min Limit' mod='attributewizardpro'}
                                    </div>
                                    <div class="right_column">
                                       <input type="text" name="group_min_limit_{$group.id_attribute_group|intval}" id="group_min_limit_{$group.id_attribute_group|intval}" value="{if isset($group.group_min_limit)}{$group.group_min_limit|intval}{else}0{/if}" style="width:60px;display:inline;"/>
                                    </div>
                                </div>
                            </div>
                           <div class="clear"></div>
                        </div> 
                        <div class="" data-id="{$group.id_attribute_group|intval}" data-order="{$smarty.foreach.ordered_group.index|intval}" id="allow_price_char_container_{$group.id_attribute_group|intval}">
                            <div class="two_columns {$ml_arr_class} {if !in_array($group.group_type, $ml_arr)}hideADN{/if}">
                                <div class="columns">
                                    <div class="left_column">
                                        {l s='Price Impact per Character' mod='attributewizardpro'}
                                    </div>
                                    <div class="right_column">
                                       <input type="checkbox" class="price_impact_per_char" data-attr="{$group.id_attribute_group|intval}" name="price_impact_per_char_{$group.id_attribute_group|intval}" id="price_impact_per_char_{$group.id_attribute_group|intval}" value="1" {if isset($group.price_impact_per_char) && $group.price_impact_per_char == 1}checked="checked"{/if} style="width:60px;display:inline;"/>
                                    </div>
                                </div>
                            </div>
                           <div class="clear"></div>
                        </div>
                        <div class="" data-id="{$group.id_attribute_group|intval}" data-order="{$smarty.foreach.ordered_group.index|intval}" id="exceptions_container_{$group.id_attribute_group|intval}">
                            <div class="two_columns {$ml_arr_class} {if !in_array($group.group_type, $ml_arr)}hideADN{/if}">
                                <div class="columns">
                                    <div class="left_column">
                                        {l s='Exceptions' mod='attributewizardpro'}
                                    </div>
                                    <div class="right_column">
                                       <input type="text" name="exceptions_{$group.id_attribute_group|intval}" id="exceptions_{$group.id_attribute_group|intval}" value="{if isset($group.exceptions)}{$group.exceptions}{else}{/if}" style="width:60px;display:inline;"/>
                                    </div>
                                </div>
                            </div>
                           <div class="clear"></div>
                        </div> 
                                    
                        <div class="" data-id="{$group.id_attribute_group|intval}" data-order="{$smarty.foreach.ordered_group.index|intval}" id="max_limit_container_{$group.id_attribute_group|intval}">
                            <div class="two_columns {$ml_arr_class} {if !in_array($group.group_type, $ml_arr)}hideADN{/if}">
                                <div class="columns">
                                    <div class="left_column">
                                        {l s='Max Limit' mod='attributewizardpro'}
                                    </div>
                                    <div class="right_column">

                                       <input type="text" name="group_max_limit_{$group.id_attribute_group|intval}" id="group_max_limit_{$group.id_attribute_group|intval}" value="{if isset($group.group_max_limit)}{$group.group_max_limit|intval}{else}0{/if}" style="width:60px;display:inline;"/>

                                    </div>
                                </div>

                            </div>
                           <div class="clear"></div>
                        </div>         

                                                                                
                        <div data-id="{$group.id_attribute_group|intval}" data-order="{$smarty.foreach.ordered_group.index|intval}" id="required_container_{$group.id_attribute_group|intval}">
                            <div class="two_columns {$req_arr_class} {if !in_array($group.group_type, $req_arr)}hideADN{/if}">
                                <div class="columns">
                                    <div class="left_column">
                                        {l s='Required' mod='attributewizardpro'}
                                    </div>
                                    <div class="right_column">

                                       <input type="checkbox" alt="{if isset($group.group_required) && $group.group_required == 1}{$group.group_required}{else}0{/if}" name="group_required_{$group.id_attribute_group|intval}" id="group_required_{$group.id_attribute_group|intval}" value="1" {if isset($group.group_required) && $group.group_required == 1}checked{/if}/>
                                    
                                    </div>
                                </div>

                            </div>
                           <div class="clear"></div>
                        </div> 

                                       
                        <div data-id="{$group.id_attribute_group|intval}" data-order="{$smarty.foreach.ordered_group.index|intval}" id="qty_zero_container_{$group.id_attribute_group|intval}">
                            <div class="two_columns quantityOpt {if $group.group_type != 'quantity'}hideADN{/if}">
                                <div class="columns">
                                    <div class="left_column">
                                        {l s='Default Qty = 0' mod='attributewizardpro'}
                                    </div>
                                    <div class="right_column">

                                       <input type="checkbox" name="group_quantity_zero_{$group.id_attribute_group|intval}" id="group_quantity_zero_{$group.id_attribute_group|intval}" value="1" {if isset($group.group_quantity_zero) && $group.group_quantity_zero == 1}checked{/if}/>
                                    
                                    </div>
                                </div>

                            </div>
                           <div class="clear"></div>
                        </div> 
                                       
                        <div data-id="{$group.id_attribute_group|intval}" data-order="{$smarty.foreach.ordered_group.index|intval}" id="chk_limit_container_{$group.id_attribute_group|intval}">
                            <div class="two_columns checkboxOpt {if $group.group_type != 'checkbox'}hideADN{/if}">
                                <div class="columns">
                                    <div class="left_column">
                                        {l s='Min Select' mod='attributewizardpro'}
                                    </div>
                                    <div class="right_column">

                                       <input style="width:60px;display:inline;" type="text" name="chk_limit_min_{$group.id_attribute_group|intval}" id="chk_limit_min_{$group.id_attribute_group|intval}" value="{if (isset($group.chk_limit_min))}{$group.chk_limit_min|intval}{else}0{/if}"/>
                                    
                                    </div>
                                </div>

                            </div>
                           <div class="clear"></div>
                        </div> 
                        <div data-id="{$group.id_attribute_group|intval}" data-order="{$smarty.foreach.ordered_group.index|intval}" id="chk_limit_container_x_{$group.id_attribute_group|intval}">
                            <div class="two_columns checkboxOpt {if $group.group_type != 'checkbox'}hideADN{/if}">
                                <div class="columns">
                                    <div class="left_column">
                                        {l s='Max Select' mod='attributewizardpro'}
                                    </div>
                                    <div class="right_column">
                                       <input style="width:60px;display:inline;" type="text" name="chk_limit_max_{$group.id_attribute_group|intval}" id="chk_limit_max_{$group.id_attribute_group|intval}" value="{if (isset($group.chk_limit_max))}{$group.chk_limit_max|intval}{else}0{/if}"/>
                                    </div>
                                </div>
                            </div>
                           <div class="clear"></div>
                        </div> 
                        <div data-id="{$group.id_attribute_group|intval}" data-order="{$smarty.foreach.ordered_group.index|intval}" id="connected_attributes_container_{$group.id_attribute_group|intval}">
                            <div class="two_columns">
                                <div class="columns">
                                    <div class="left_column">
                                        {l s='Do Not Hide' mod='attributewizardpro'}
                                    </div>
                                    <div class="right_column">
                                       <input type="checkbox" name="connected_do_not_hide_{$group.id_attribute_group|intval}" id="connected_do_not_hide_{$group.id_attribute_group|intval}" value="1" {if isset($group.connected_do_not_hide) && $group.connected_do_not_hide == 1}checked{/if}/>
                                    </div>
                                </div>
                            </div>
                           <div class="clear"></div>
                        </div>                
                                       
                    </div>
                    <div class="column_format column_5">
                        <div class="fixed_height">
                        </div>
                        <div data-id="{$group.id_attribute_group|intval}" data-order="{$smarty.foreach.ordered_group.index|intval}" class="fixed_empty_height"></div>
                        <div data-id="{$group.id_attribute_group|intval}" data-order="{$smarty.foreach.ordered_group.index|intval}" class="module_display_{$smarty.foreach.ordered_group.index|intval}" id="display_{$smarty.foreach.ordered_group.index|intval}">
                            <div class="attribute_values_sort attribute_{$group.id_attribute_group|intval} table_format table_compact">
                                
                                
                                {foreach from=$group.attributes key=myId item=attribute name=ordered_attribute}
                                    <div id="{$group.id_attribute_group|intval}_{$attribute['id_attribute']|intval}" class="row_format {if $smarty.foreach.ordered_attribute.index > 9} hideADN {/if}">
                                        <div class="column_format column_1">
                                            <img src="{$path|escape:'htmlall':'UTF-8'}views/img/sort_icon.png">
                                        </div>
                                        <div class="column_format column_2">
                                            {$attribute['attribute_name']|escape:'htmlall':'UTF-8'}
                                            
                                            {if $awp_layered_image}
                                                {if $attribute.layered_filename}
                                                    <div class="liu" group="{$smarty.foreach.ordered_group.index|intval}" id="upload_container_l{$attribute['id_attribute']|intval}">
                                                        <div id="image_container_l{$attribute['id_attribute']|intval}">
                                                            <img src="{$attribute.layered_filename}" width="32" height="32" />

                                                        
                                                        </div>
                                                        <br/>
                                                        <input id="upload_button_l{$attribute['id_attribute']|intval}" class="button edit_btn" style="cursor:pointer" value="{l s='Change Image' mod='attributewizardpro'}" type="button">
                                                        <br/><br/>
                                                        <input id="delete_image_l{$attribute['id_attribute']|intval}" class="button delete_btn" value="{l s='Delete' mod='attributewizardpro'}" type="button">

                                                    </div>
                                                {else}
                                                    <div class="liu" group="{$smarty.foreach.ordered_group.index|intval}" id="upload_container_l{$attribute['id_attribute']|intval}">
                                                        <div id="image_container_l{$attribute['id_attribute']|intval}">
                                                            
                                                            {*
                                                            <input id="delete_image_l{$attribute['id_attribute']|intval}'" class="button delete_btn" value="{l s='Delete' mod='attributewizardpro'}" type="button">
                                                            *}
                                                        </div>
                                                        <br/>
                                                        <input id="upload_button_l{$attribute['id_attribute']|intval}" class="button upload_btn  " style="cursor:pointer" value="{l s='Upload Image' mod='attributewizardpro'}" type="button">
                                                        
                                                    </div>
                                                {/if}
                                            {/if}
                                            
                                            <div class="attr_description">
                                                <div>{l s='Description' mod='attributewizardpro'}</div>
                                                <div>
                                                    {assign var='tmpAttrDescr' value="attr_description_`$id_lang`"}
                                                    {assign var='tmpIdAttr' value="`$attribute.id_attribute`"}
                                                    <input type="hidden" name="id_attribute" value="{$attribute['id_attribute']|intval}"/>
                                                    <textarea class="autoload_rte" onchange="awp_update_lang(false)" id="attr_description_{$attribute['id_attribute']|intval}" name="attr_description_{$attribute['id_attribute']|intval}">{if isset($group.$tmpIdAttr.$tmpAttrDescr)}{$group.$tmpIdAttr.$tmpAttrDescr nofilter}{/if}</textarea>
                                                    {foreach $languages as $language}
                                                        {assign var='tmpAttrDescr' value="attr_description_`$language.id_lang`"}
                                                        <input type="hidden" id="attr_description_{$attribute['id_attribute']|intval}_{$language.id_lang|intval}" name="attr_description_{$attribute['id_attribute']|intval}_{$language.id_lang|intval}" value="{if isset($group.$tmpIdAttr.$tmpAttrDescr)}{$group.$tmpIdAttr.$tmpAttrDescr nofilter}{/if}">
                                                    {/foreach}
                                                </div>
                                                
                                                <div>{l s='More Info Product ID' mod='attributewizardpro'}</div>
                                                <div>
                                                    <input type="text" id="attr_product_{$attribute['id_attribute']|intval}" name="attr_product_{$attribute['id_attribute']|intval}" value="{if $group.$tmpIdAttr.attr_product > 0}{$group.$tmpIdAttr.attr_product|intval}{/if}"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="clear"></div>
                                    </div>
                                {/foreach}
                                
                            </div>
                            {if $group.attributes|@count > 10}
                            <div id="display_more_{$smarty.foreach.ordered_group.index|intval}" class="display_more">
                                {l s='Show All' mod='attributewizardpro'} {$group.attributes|@count|intval} {l s='attributes' mod='attributewizardpro'}
                                <span class="more_arrow_down"></span>
                            </div>   
                             <div id="hide_more_{$smarty.foreach.ordered_group.index|intval}" class="hide_more display_more">
                                {l s='Hide attributes' mod='attributewizardpro'}
                                <span class="more_arrow_up"></span>
                            </div>
                            {/if}
                        </div>
                    </div>    
                    <div class="column_format column_6">
                        <div id="expand_{$group.id_attribute_group|intval}" class="expand_collapse" onClick="presto_toggle({$group.id_attribute_group|intval})">                        
                            <span class="arrow_up hideADN">

                            </span>
                            <span class="arrow_down">

                            </span>
                        </div>
                    </div>  
                    <div class="clear"></div>
                </div>
            {/foreach}
            
            
        </div>
        <br/><br/>
        {if $pages_count > 1}
            <ul class="pagination awp-pagination">
                {for $p_item=1 to $pages_count}
                    <li {if $p == $p_item}class="active current"{/if}>
                        <a href="{$module_uri nofilter}&p={$p_item|intval}&n={$n|intval}#advanced_settings">{$p_item}</a>
                    </li>
                {/for}
            </ul>
        {/if}
        <div class="columns">
            <div class="left_column">
                 <input type="submit" value="{l s='Update' mod='attributewizardpro'}" name="submitAdvancedChanges" class="submit_button" />
            </div>
            <div class="right_column">
                
            </div>
        </div>    
    </form>            
</div>

