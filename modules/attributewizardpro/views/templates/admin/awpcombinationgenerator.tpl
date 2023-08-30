<script type="text/javascript" src="{$awp_path}../../js/jquery/plugins/fancybox/jquery.fancybox.js"></script>
<script type="text/javascript" src="{$awp_path}views/js/globalBack.js"></script>
<script type="text/javascript" src="{$awp_path}views/js/specificBack.js"></script>

<div class="instructions_awp">
    <h2>{l s='AWP Combination Generator' mod='attributewizardpro'}</h2>

    <div class="small no-padding">
        {l s='This AWP combination generator will allow you to create all combinations at once for this product which uses the AWP module.' mod='attributewizardpro'}
        <a class="info_alert" href="#bulk_prewview"></a>
        <div id="bulk_prewview" class="hideADN info_popup">
            <div class="panel">
                <h3>
                    {l s='How to use AWP Combination Generator' mod='attributewizardpro'}
                    <span class="info_icon"> </span>
                </h3>
                <div class="upgrade_check_content">
                    <img src='{$awp_path}views/img/bulk_combinations_preview.png'/>
                    </br></br>

                </div>
            </div>
        </div>
        </br></br>
        {l s='After you select the attribute values, click on' mod='attributewizardpro'}
        <b><i>{l s='PREVIEW COMBINATIONS' mod='attributewizardpro'}</i></b> {l s='button to see the exact combinations which will be created. Then just click on' mod='attributewizardpro'}
        <b><i>{l s='SAVE' mod='attributewizardpro'}</i></b> {l s='button to create the combiantions.' mod='attributewizardpro'}
        </br></br>
        {l s='The combination generator will CREATE / UPDATE the combinations generated. If a combination already exists the module will update the price , weight and stock.' mod='attributewizardpro'}
        </br></br>
        {l s='To create the combinations just select which attribute values you need. Select the attribute group type: Connected or Separeted and check the Shared option if the attributes should be shared.' mod='attributewizardpro'}
        </br></br>
        <b>{l s='Connected Combinations' mod='attributewizardpro'}</b> {l s='- You need to select at least 2 attribute groups to be connected in order to use connected combinations.' mod='attributewizardpro'}
        </br>
        <b>{l s='Connected & Shared' mod='attributewizardpro'}</b> {l s='- A connected combination can be shared, meaning that the combination will contain all attribute values in a single attribute group and it will SHARE the price impact, weight impact and stock.' mod='attributewizardpro'}
        </br></br>
        <b>{l s='Separated Combinations' mod='attributewizardpro'}</b> {l s='- will contain one attribute value from a single attribute group.' mod='attributewizardpro'}
        </br>
        <b>{l s='Separated & Shared' mod='attributewizardpro'}</b> {l s='- will contain all attribute values in a single combinations and it will SHARE price impact, weight impact and stock.' mod='attributewizardpro'}
        </br></br>
    </div>
</div>
<div class="row">
    <div class="col-md-7 awp_attribute_list" id="awp_attribute_list">
        <div class="row">
            <div class="col-lg-12 mb-3 delete_awp_combinations">
                <input type="button" id="awp_delete_all" class="btn btn-action " name="awp[awp_delete_all]"
                       value="{l s='Delete ALL Combinations' mod='attributewizardpro'}"/>
                <a class="info_alert" href="#delete_all"></a>
                <div id="delete_all" class="hideADN info_popup">
                    <div class="panel">
                        <h3>
                            {l s='AWP Delete all Combinations' mod='attributewizardpro'}
                            <span class="info_icon"> </span>
                        </h3>
                        <div class="upgrade_check_content">
                            {l s='Delete all combinations will clean / delete all attribute combinations created previousely for this product.' mod='attributewizardpro'}

                            </br></br>

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 awp_preview_btn">
                <input type="button" class="btn btn-action " name="awp_preview_combinations"
                       id="awp_preview_combinations" value="{l s='Preview Combinations' mod='attributewizardpro'}"/>
            </div>
            <div class="col-lg-3">
                <button class="btn btn-outline-primary" id="awp-create-combinations" type="button">
                    Generate
                </button>
            </div>
        </div>
        </br></br>
        {foreach $attribute_groups as $attribute_group}
            {if $attribute_group.id_attribute_group != $awpDetailsIdGroup}
                <div class="attribute-group">
                    <a class="attribute-group-name  collapsed " data-toggle="collapse"
                       href="#awp-attribute-group-{$attribute_group.id_attribute_group}" aria-expanded="false">
                        {$attribute_group.public_name}
                    </a>
                    <select class="awp_group_opt awp_input" id="awp_group_type_{$attribute_group.id_attribute_group}"
                            name="awp[awp_group_type][{$attribute_group.id_attribute_group}]">
                        <option value="connected">{l s='Connected' mod='attributewizardpro'}</option>
                        <option value="separated">{l s='Separated' mod='attributewizardpro'}</option>
                    </select>
                    <a class="info_alert" href="#connected_separated"></a>
                    <div id="connected_separated" class="hideADN info_popup">
                        <div class="panel">
                            <h3>
                                {l s='Connected / Separated attribute groups' mod='attributewizardpro'}
                                <span class="info_icon"> </span>
                            </h3>
                            <div class="upgrade_check_content">
                                <b>{l s='Connected Attributes:' mod='attributewizardpro'}</b> {l s='Combinations with Connected Attributes are combinations which contains one or more attribute value from all attribute groups which are set to \'Connected\'. Usually this is because the combination of connected attributes has a specific price, weight and/or quantity impact. To use Connected Attributes with Attribute Wizard Pro, you must set at least two (2) attributes groups as \'Connected\' as shown below.' mod='attributewizardpro'}
                                <img src='{$awp_path}views/img/connected_preview.png'/>
                                </br></br>
                                <b>{l s='Separated Attributes:' mod='attributewizardpro'}</b> {l s='Set this dropdown to \'Separated\' when none of the attribute values in the attribute group are connected to the attribute values in another attribute group. The resulting attribute combinations will contain only one attribute value from the attribute group.' mod='attributewizardpro'}
                                <img src='{$awp_path}views/img/separated_preview.png'/>

                            </div>
                        </div>
                    </div>
                    <input class="awp_input" style="margin-left: 25px;" type="checkbox"
                           id="awp_group_shared_{$attribute_group.id_attribute_group}"
                           name="awp[awp_group_shared][{$attribute_group.id_attribute_group}]" value="1"/>
                    <label class="awp_shared_label"
                           for="awp_group_shared_{$attribute_group.id_attribute_group}">{l s='Shared' mod='attributewizardpro'}</label>
                    <a class="info_alert" href="#shared"></a>
                    <div id="shared" class="hideADN info_popup">
                        <div class="panel">
                            <h3>
                                {l s='Shared attribute groups' mod='attributewizardpro'}
                                <span class="info_icon"> </span>
                            </h3>
                            <div class="upgrade_check_content">
                                <b>{l s='Connected & Shared: ' mod='attributewizardpro'}</b> {l s='An attribute group that is connected to another attribute group can be set to \'Shared,\' meaning that the resulting combinations will each contain all attribute values in that attribute group and will share the price, weight & stock impact, as shown below. This is a great way to reduce the total number of attribute combinations even when Connected Attributes are used.' mod='attributewizardpro'}
                                <img src='{$awp_path}views/img/connected_shared_preview.png'/>
                                </br></br>
                                <b>{l s='Separated & Shared:' mod='attributewizardpro'}</b> {l s='An attribute group that is set to \'Separated\' can be set to \'Shared,\' resulting in a single combinations containing all attribute values from that attribute group with one shared price, weight and stock impact. This is a great way to reduce the total number of combinations. ' mod='attributewizardpro'}
                                <img src='{$awp_path}views/img/separated_shared_preview.png'/>
                                </br></br>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>

                    <div class="collapse  in  attributes "
                         id="awp-attribute-group-{$attribute_group.id_attribute_group}">
                        <div class="attributes-overflow ">
                            <div class="awp_attribute">
                                <div class="awp_cols awp_attribute_in">
                                    <input type="checkbox" name="awp['select_all']"
                                           data-group-id="{$attribute_group.id_attribute_group}"
                                           class="awp_select_all awp_input"/>
                                    <a class="info_alert" href="#awp_select_all"></a>
                                    <div id="awp_select_all" class="hideADN info_popup">
                                        <div class="panel">
                                            <h3>
                                                {l s='Select All Attribute Values' mod='attributewizardpro'}
                                                <span class="info_icon"> </span>
                                            </h3>
                                            <div class="upgrade_check_content">
                                                <b>{l s='Click to Select / Deselect all attribute values' mod='attributewizardpro'}</b>
                                                </br></br>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="awp_cols awp_default_attr">
                                    <label class="attribute-label">
                                        {l s='Default' mod='attributewizardpro'}
                                    </label>
                                </div>
                                <div class="awp_cols price_impact">
                                    <label class="attribute-label">
                                        {l s='Price impact' mod='attributewizardpro'}

                                    </label>
                                </div>
                                <div class="awp_cols weight_impact">
                                    <label class="attribute-label">
                                        {l s='Weight impact' mod='attributewizardpro'}

                                    </label>
                                </div>
                                <div class="awp_cols price_impact">
                                    <label class="attribute-label">
                                        {l s='Quantity' mod='attributewizardpro'}

                                    </label>
                                </div>
                                <div class="clear"></div>
                            </div>
                            <div class="clear"></div>
                            {foreach from=$attribute_group.attributes  item="attribute" name='attributes'}
                                <div class="awp_attribute">
                                    <div class="awp_cols awp_attribute_in">
                                        <input class="js-attribute-checkbox awp_input"
                                               name="awp[awp_attribute][{$attribute_group.id_attribute_group}][{$attribute.id_attribute}]"
                                               id="awp-attribute-{$attribute.id_attribute}"
                                               data-value="{$attribute.id_attribute}"
                                               data-group-id="{$attribute.id_attribute_group}" type="checkbox">
                                        <label class="attribute-label" for="attribute-{$attribute.id_attribute}">
                                        <span class="pretty-checkbox  not-color ">
                                        </span>
                                            <span class="awp-attribute-name-{$attribute.id_attribute}">
                                            {$attribute.name}
                                        </span>
                                        </label>
                                    </div>
                                    <div class="awp_cols awp_default_attr">
                                        <span>&nbsp;</span>
                                        <input name="awp[awp_attribute_default][{$attribute_group.id_attribute_group}][{$attribute.id_attribute}]"
                                               type="checkbox" data-group-id="{$attribute_group.id_attribute_group}"
                                               class="awp-attribute-default-{$attribute.id_attribute} awp_attribute_default   {if $smarty.foreach.attributes.first}firstAttr firstAttrGroup{$attribute_group.id_attribute_group}{/if} awp_input"
                                               value="1"/>
                                    </div>
                                    <div class="awp_cols price_impact">

                                        <input placeholder="Price Impact"
                                               name="awp[awp_attribute_price][{$attribute_group.id_attribute_group}][{$attribute.id_attribute}]"
                                               type="text"
                                               class="awp-attribute-price-{$attribute.id_attribute} form-control text-xs-right awp_input"
                                               value="0"/>
                                    </div>
                                    <div class="awp_cols weight_impact">

                                        <input placeholder="Weight Impact"
                                               name="awp[awp_attribute_weight][{$attribute_group.id_attribute_group}][{$attribute.id_attribute}]"
                                               type="text"
                                               class="awp-attribute-weight-{$attribute.id_attribute} form-control text-xs-right awp_input"
                                               value="0"/>
                                    </div>
                                    <div class="awp_cols awp_qty">

                                        <input placeholder="Quantity"
                                               name="awp[awp_attribute_qty][{$attribute_group.id_attribute_group}][{$attribute.id_attribute}]"
                                               type="text"
                                               class="awp-attribute-qty-{$attribute.id_attribute} form-control text-xs-right awp_input"
                                               value="0"/>
                                    </div>
                                    <div class="clear"></div>
                                </div>
                            {/foreach}

                        </div>
                    </div>
                </div>
            {/if}
        {/foreach}


    </div>
    <div class="col-md-5 awp_preview_attribute_list">
        <div class=" awp_preview_attribute_list" id="awp_preview_attribute_list">

        </div>
    </div>
</div>
<div class="clear"></div>
