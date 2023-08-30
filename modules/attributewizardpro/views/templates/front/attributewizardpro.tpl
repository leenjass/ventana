<!-- MODULE Attribute Wizard Pro-->
{if isset($groups)}
    
{if $awp_psv >= 1.6}
	<style type="text/css">
		.awp_nila { margin-top: -4px;}
	</style>
{/if}
{if $isQuickView}
    
    <script type="text/javascript">
        var awp_add_to_cart_display = "{$awp_add_to_cart}";
    </script>
{/if}
<script type="text/javascript" src="{$this_wizard_path}views/js/ajaxupload.js"></script>
<script type="text/javascript">
    var awp_isQuickView = {if isset($isQuickView) && $isQuickView}{$isQuickView}{else}false{/if};
{if isset($awp_product_image) && $awp_popup}
	var awp_layered_img_id = 'awp_product_image';
{else} 
	var awp_layered_img_id = 'product-cover';
{/if}
attribute_anchor_separator = '{$attribute_anchor_separator}';
singleAttributeGroup = {if $singleAttributeGroup}true{else}false{/if};
awp_out_of_stock = '{$awp_out_of_stock}';
attributesCombinations = [];
{if isset($attributesCombinations) && $attributesCombinations}
	{function jsaddattributesCombinations keypart=''}
		{$productLineTurn = 1} 
		{foreach $data as $key => $item}
			{if not $item|@is_array}
				{if $keypart eq ''}
					attributesCombinations['{$key}'] = '{$item}';
				 {else}
					attributesCombinations{$keypart nofilter}['{$key}'] = '{$item}';
				{/if}
			{else}
				attributesCombinations{$keypart nofilter}['{$key}'] = [];
				{jsaddattributesCombinations data = $item keypart = "`$keypart`['`$key`']" }
			{/if}			
			{if $productLineTurn % 100 == 0}
				</script>
				<script type="text/javascript">
			{/if}
			{$productLineTurn = $productLineTurn+1} 
		{/foreach}
	{/function}
   {jsaddattributesCombinations data=$attributesCombinations}
{/if}
    
connectedAttributes = [];

{if isset($connectedAttributes) && $connectedAttributes}
	{function jsadd keypart=''}
		{$productLineTurn = 1} 
		{foreach $data as $key => $item}
			{if not $item|@is_array}
				{if $keypart eq ''}
					connectedAttributes['{$key}'] = '{$item}';
				 {else}
					connectedAttributes{$keypart nofilter}['{$key}'] = '{$item}';
				{/if}
			{else}
				connectedAttributes{$keypart nofilter}['{$key}'] = [];
				{jsadd data = $item keypart = "`$keypart`['`$key`']" }
			{/if}			
			{if $productLineTurn % 100 == 0}
				</script>
				<script type="text/javascript">
			{/if}
			{$productLineTurn = $productLineTurn+1} 
		{/foreach}
	{/function}
   {jsadd data=$connectedAttributes}
{/if}
notConnectedGroups = [];
{if isset($notConnectedGroups) && $notConnectedGroups}	
	{function jsaddA keypart=''}
		{$productLineTurnA = 1} 
		{foreach $data as $key => $item}		
			{if not $item|@is_array}
				{if $keypart eq ''}
					notConnectedGroups['{$key}'] = '{$item}';
				 {else}
					notConnectedGroups{$keypart nofilter}['{$key}'] = '{$item}';
				{/if}
			{else}		
				notConnectedGroups{$keypart nofilter}['{$key}'] = [];
				{jsaddA data = $item keypart = "`$keypart`['`$key`']" }
			{/if}			
			{if $productLineTurnA % 100 == 0}
				</script>
				<script type="text/javascript">
			{/if}
			{$productLineTurnA = $productLineTurnA+1} 			
		{/foreach}
	{/function}   
   {jsaddA data=$notConnectedGroups}
{/if}
     
defaultConnectedAttribute = [];
{if isset($defaultConnectedAttribute) && $defaultConnectedAttribute}	
	{function jsaddC keypart=''}
		{$productLineTurnC = 1} 
		{foreach $data as $key => $item}		
			{if not $item|@is_array}
				{if $keypart eq ''}
					defaultConnectedAttribute['{$key}'] = '{$item}';
				 {else}
					defaultConnectedAttribute{$keypart nofilter}['{$key}'] = '{$item}';
				{/if}
			{else}		
				defaultConnectedAttribute{$keypart nofilter}['{$key}'] = [];
				{jsaddC data = $item keypart = "`$keypart`['`$key`']" }
			{/if}			
			{if $productLineTurnC % 100 == 0}
				</script>
				<script type="text/javascript">
			{/if}
			{$productLineTurnC = $productLineTurnC+1} 
		{/foreach}
	{/function}   
   {jsaddC data=$defaultConnectedAttribute}
{/if}

bothConnectedAttributes = [];
{if isset($bothConnectedAttributes) && $bothConnectedAttributes}	
	{function jsaddB keypart=''}
		{$productLineTurnB = 1} 
		{foreach $data as $key => $item}		
			{if not $item|@is_array}
				{if $keypart eq ''}
					bothConnectedAttributes['{$key}'] = '{$item}';
				 {else}
					bothConnectedAttributes{$keypart nofilter}['{$key}'] = '{$item}';
				{/if}
			{else}		
				bothConnectedAttributes{$keypart nofilter}['{$key}'] = [];
				{jsaddB data = $item keypart = "`$keypart`['`$key`']" }
			{/if}
			
			{if $productLineTurnB % 100 == 0}
				</script>
				<script type="text/javascript">
			{/if}
			{$productLineTurnB = $productLineTurnB+1} 
		{/foreach}
	{/function}   
   {jsaddB data=$bothConnectedAttributes}
{/if}

var containsSearchAll = "{$containsSearchAll}";

var awp_not_available = "{l s='---' mod='attributewizardpro' js=1}";


awp_selected_attribute = "";
awp_selected_groups_multiple = new Array();
awp_selected_group = "";
var awp_converted_price = {$awp_converted_price};
var awp_tmp_arr = new Array()
productHasAttributes = false;

var awp_no_tax_impact = {if $awp_no_tax_impact}true{else}false{/if};
var awp_psv = "{$awp_psv}";
var awp_psv3 = "{$awp_psv3}";
var awp_stock = "{$awp_stock}";
var awp_allow_oosp = "{$awp_allow_oosp}";
var awp_reload_page = "{$awp_reload_page}";
var awp_display_qty = {if $awp_display_qty}true{else}false{/if};
var awp_is_edit = {$awp_is_edit};
var awp_qty_edit = {$awp_qty_edit};
var awp_no_customize = "{$awp_no_customize}";
var awp_ajax = {if $awp_ajax}true{else}false{/if};


var awp_customize = "{l s='Customize Product' mod='attributewizardpro' js=1}";
var awp_add_cart = "{l s='Add to cart' mod='attributewizardpro' js=1}"; // changed to using the button original text  // changed back in awaiting of full rewrite
var awp_add = "{l s='Add' mod='attributewizardpro' js=1}";
var awp_edit = "{l s='Edit' mod='attributewizardpro' js=1}";
var awp_sub = "{l s='Subtract' mod='attributewizardpro' js=1}";
var awp_minimal_1 = "{l s='(Min:' mod='attributewizardpro' js=1}";
var awp_minimal_2 = "{l s=')' mod='attributewizardpro' js=1}";
var awp_min_qty_text = "{l s='The minimum quantity for this product is' mod='attributewizardpro' js=1}";
var awp_ext_err = "{l s='Error: invalid file extension, use only ' mod='attributewizardpro' js=1}";
var awp_adc_no_attribute = {if $awp_adc_no_attribute}true{else}false{/if};
var awp_popup = {if $awp_popup}true{else}false{/if};
var awp_pi_display = '{$awp_pi_display}';
var awp_currency = {$awp_currency->id};
var awp_is_required = "{l s='is a required field!' mod='attributewizardpro' js=1}";
var awp_select_attributes = "{l s='You must select at least 1 product option' mod='attributewizardpro' js=1}";
var awp_must_select_least = "{l s='You must select at least' mod='attributewizardpro' js=1}";
var awp_must_select_up = "{l s='You must select up to' mod='attributewizardpro' js=1}";
var awp_must_select = "{l s='You must select' mod='attributewizardpro' js=1}";
var awp_option = "{l s='option' mod='attributewizardpro' js=1}";
var awp_options = "{l s='options' mod='attributewizardpro' js=1}";
var awp_oos_alert = "{l s='This combination is out of stock, please choose another' mod='attributewizardpro' js=1}";

var awp_file_ext = new Array();
var awp_file_list = new Array();
var awp_required_list = new Array();
var awp_required_group = new Array();
var awp_required_list_name = new Array();
var awp_qty_list = new Array();
var awp_attr_to_group = new Array();
var awp_selected_groups = new Array();
var awp_sel_cont_var = new Array();
var awp_cell_cont_text_group = new Array();
var awp_max_text_length = new Array();

var awp_connected_do_not_hide  = new Array();

var awp_group_impact = new Array();
var awp_group_order = new Array();
var awp_group_type = new Array();
var awp_group_name = new Array();
var awp_min_qty = new Array();
var awp_chk_limit = new Array();
var awp_impact_list = new Array();
var awp_impact_only_list = new Array();
var awp_weight_list = new Array();
var awp_is_quantity_group = new Array();
var awp_hin = new Array();
var awp_multiply_list = new Array();
var awp_layered_image_list = new Array();
var awp_selected_attribute_default = false;
var awp_groups = new Array();

var awp_groups_chars = new Array();

var awp_fade = {if $awp_fade}true{else}false{/if};
if (typeof group_reduction == 'undefined')
{
	if (typeof groupReduction  == 'undefined')
		group_reduction = 1;
	else
		group_reduction = groupReduction;
}
var awp_group_reduction = group_reduction;
if (parseFloat(awp_psv) >= 1.6)
	awp_group_reduction = awp_group_reduction == 1?group_reduction:1-group_reduction;

{foreach from=$attributeImpacts key=id_attributeImpact item=attributeImpact}{strip}
	{if $attributeImpact.price != 0}
		awp_impact_only_list[{$attributeImpact.id_attribute}] = '{$attributeImpact.price}';
	{/if}
	awp_min_qty[{$attributeImpact.id_attribute}] = '{$attributeImpact.minimal_quantity}';
	awp_impact_list[{$attributeImpact.id_attribute}] = '{$attributeImpact.price}';
	awp_weight_list[{$attributeImpact.id_attribute}] = '{$attributeImpact.weight}';
{/strip}{/foreach}


attributeImpacts = [];
{if isset($attributeImpacts) && $attributeImpacts}	
	{function jsaddattributeImpacts keypart=''}
		{$productLineTurnB = 1} 
		{foreach $data as $key => $item}		
			{if not $item|@is_array}
				{if $keypart eq ''}
					attributeImpacts['{$key}'] = '{$item}';
				 {else}
					attributeImpacts{$keypart nofilter}['{$key}'] = '{$item}';
				{/if}
			{else}		
				attributeImpacts{$keypart nofilter}['{$key}'] = [];
				{jsaddattributeImpacts data = $item keypart = "`$keypart`['`$key`']" }
			{/if}
			
			{if $productLineTurnB % 100 == 0}
				</script>
				<script type="text/javascript">
			{/if}
			{$productLineTurnB = $productLineTurnB+1} 
		{/foreach}
	{/function}   
   {jsaddattributeImpacts data=$attributeImpacts}
{/if}
    

// var awpAvailableIcon = '<i class="material-icons product-available">&#xE5CA;</i>';
// var awpLastRemainingItemIcon = '<i class="material-icons product-last-items">&#xE002;</i>';
// var awpUnavailableIcon = '<i class="material-icons product-unavailable">&#xE14B;</i>';

var awpUnaivailableTxt = "{l s='Product available with different options' mod='attributewizardpro'}";
</script>

<span class="modal quickview" style="display: none;"></span>

<div id="awp_container" {if $awp_popup}class="awp_popup"{/if}>
	<div class="awp_box">
		<b class="xtop"><b class="xb1"></b><b class="xb2 xbtop"></b><b class="xb3 xbtop"></b><b class="xb4 xbtop"></b></b>
		<div class="awp_header">
				<div style="float:left"><b style="font-size:14px">{l s='Product Options' mod='attributewizardpro'}</b></div>
			{if $awp_popup}
				<div class="close">
					<img src="{$this_wizard_path}views/img/close.png">
				</div>
			{/if}
		</div>
		<div class="awp_content">
			{if isset($awp_product_image) && $awp_popup}
				<div id="awp_product_image" style="width:{$awp_product_image.width}px;height:{$awp_product_image.height}px;margin:auto">
					<img src="{$awp_product_image.src}"	title="{$product.name|escape:'htmlall':'UTF-8'}" alt="{$product.name|escape:'htmlall':'UTF-8'}" id="awp_bigpic" width="{$awp_product_image.width}" height="{$awp_product_image.height}" />
				</div>
			{/if}
			<form name="awp_wizard" id="awp_wizard">
			<input type="hidden" name="awp_p_impact" id="awp_p_impact" value="" />
			<input type="hidden" name="awp_p_weight" id="awp_p_weight" value="" />
			<input type="hidden" name="awp_ins" id="awp_ins" value="{$awp_ins|escape:'html':'UTF-8'}" />
			<input type="hidden" name="awp_ipa" id="awp_ipa" value="{$awp_ipa|intval}" />
			{if ($awp_add_to_cart == "both" || $awp_add_to_cart == "bottom") && $groups|@count >= $awp_second_add}
				<div class="awp_stock_container awp_sct">
					<div class="awp_stock">
							&nbsp;&nbsp;<b class="price our_price_display" id="awp_second_price"></b>
					</div>
					<div class="awp_quantity_additional awp_stock">
						&nbsp;&nbsp;{l s='Quantity' mod='attributewizardpro'}: <input type="text" style="width:30px;padding:0;margin:0" id="awp_q1" onkeyup="$('#quantity_wanted').val(this.value);$('#awp_q2').val(this.value);" value="1" />
						<span class="awp_minimal_text"></span>
					</div>
					{if $awp_is_edit}
						<div class="awp_stock_btn">
							<input type="button" value="{l s='Edit' mod='attributewizardpro'}" class="exclusive awp_edit" onclick="$(this){if $awp_psv3 < 1.6}.attr('disabled', 'disabled');{else}.prop('disabled', false);{/if};awp_add_to_cart(true);$(this){if $awp_psv3 < 1.6}.attr('disabled', 'disabled');{else}.prop('disabled', false);{/if}('disabled', {if $awp_psv3 == '1.4.9' || $awp_psv3 == '1.4.10' || $awp_psv >= '1.5'}false{else}''{/if});" />&nbsp;&nbsp;&nbsp;&nbsp;
						</div>
					{/if}
					<div class="awp_stock_btn box-info-product">
						{if $awp_psv >= 1.6}
								<button type="button" name="Submit" class="exclusive" onclick="$(this).prop('disabled', true);awp_add_to_cart();{if $awp_popup}awp_customize_func();{/if}$(this).prop('disabled', false);">
									<span>{l s='Add to cart' mod='attributewizardpro'}</span>
								</button>
						{else}
							<input type="button" value="{l s='Add to cart' mod='attributewizardpro'}" class="exclusive" onclick="$(this){if $awp_psv3 < 1.6}.attr('disabled', 'disabled');{else}.prop('disabled', false);{/if};awp_add_to_cart();{if $awp_popup}awp_customize_func();{/if}$(this){if $awp_psv3 < 1.6}.attr('disabled', 'disabled');{else}.prop('disabled', false);{/if}('disabled', {if $awp_psv3 == '1.4.9' || $awp_psv3 == '1.4.10' || $awp_psv >= '1.5'}false{else}''{/if});" />
						{/if}
					</div>
					{if $awp_popup}
						<div class="awp_stock_btn">
							<input type="button" value="{l s='Close' mod='attributewizardpro'}" class="button_small" onclick="$('#awp_container').fadeOut(1000);$('#awp_background').fadeOut(1000);awp_customize_func();" />
						</div>
					{/if}
					<div id="awp_in_stock_second"></div>
				</div>
			{/if}
			{foreach from=$groups key=id_attribute_group item=group name=awp_groups}
			{strip}
				<script type="text/javascript">
                                awp_groups.push({$group.id_group});
				awp_selected_groups[{$group.id_group}] = 0;
				awp_group_type[{$group.id_group}] = '{$group.group_type}';
				awp_group_order[{$group.id_group}] = {$smarty.foreach.awp_groups.index};
				awp_group_name[{$group.id_group}] = '{$group.name|escape:'htmlall':'UTF-8'}';
				awp_hin[{$group.id_group}] = '{if isset($group.group_hide_name)}{$group.group_hide_name}{else}0{/if}';
				awp_required_group[{$group.id_group}] = {if isset($group.group_required) && $group.group_required}1{else}0{/if};
				
				awp_connected_do_not_hide[{$group.id_group}] = {if isset($group.connected_do_not_hide) && $group.connected_do_not_hide}1{else}0{/if};
				
                                
                                {if $group.group_type == 'textbox' || $group.group_type == 'textarea'}
                                    {if isset($group.price_impact_per_char) && $group.price_impact_per_char == 1}
                                        awp_groups_chars[{$group.id_group}] = new Array();
                                        awp_groups_chars[{$group.id_group}]['price_impact_per_char'] = {$group.price_impact_per_char|intval};
                                        awp_groups_chars[{$group.id_group}]['group_min_limit'] = {$group.group_min_limit|intval};
                                        awp_groups_chars[{$group.id_group}]['exceptions'] = '{$group.exceptions}';                                        
                                    {/if}
                                {/if}
				
				{if $group.group_type == 'checkbox'}
					awp_chk_limit[{$group.id_group}] = Array({$group.chk_limit_min}, {$group.chk_limit_max});
				{/if}
				{assign var='default_impact' value=''}
				
				{if $default_impact == ''}
					{assign var='default_impact' value=0}
				{/if}
				{foreach from=$group.attributes item=group_attribute}
				{strip}
					{assign var='id_attribute' value=$group_attribute.0}
					{if isset($group.group_required) && $group.group_required}
						awp_required_list[{$id_attribute}] = 1;
					{else}
						awp_required_list[{$id_attribute}] = 0;
					{/if}
					{if $awp_layered_image}
						awp_layered_image_list[{$id_attribute}] = '{getLayeredImageTag id_attribute=$id_attribute v=$group_attribute.3}';
					{/if}
					{if $group.group_type == "file"}
						awp_file_list.push({$id_attribute});
						awp_file_ext.push(/^({$group.group_file_ext})$/);
					{/if}
					awp_multiply_list[{$id_attribute}] = '{if isset($group.group_calc_multiply)}{$group.group_calc_multiply}{/if}';
					awp_qty_list[{$id_attribute}] = '{$group.attributes_quantity.$id_attribute}';
					awp_required_list_name[{$id_attribute}] = '{$group_attribute.1|escape:'htmlall':'UTF-8'}';
					awp_attr_to_group[{$id_attribute}] = '{$group.id_group}';
				{/strip}
				{/foreach}
				</script>
				<div class="awp_group_image_container">
					{if isset($group.group_url) && $group.group_url}<a href="{$group.group_url}" target="_blank" alt="{$group.group_url}">{/if}{if isset($group.image_upload)}{getGroupImageTag id_group=$group.id_group alt=$group.name|escape:'htmlall':'UTF-8' v=$group.image_upload}{if isset($group.group_url) && $group.group_url}</a>{/if}{/if}
				</div>
				{if $group.group_type != "hidden"}
					<div class="awp_box awp_box_inner" data-parent-group-name="{if isset($group.parent_group_name)}{$group.parent_group_name}{/if}">
						<b class="xtop"><b class="xb1"></b><b class="xb2 xbtop"></b><b class="xb3 xbtop"></b><b class="xb4 xbtop"></b></b>
						<div class="awp_header">
							<div class="awp_header_collapible">
								<div class="awo_header_fixed">
									{if isset($group.group_header) && $group.group_header}
										{$group.group_header|escape:'htmlspecialchars':'UTF-8'}
									{else}
										{$group.name|escape:'htmlspecialchars':'UTF-8'}
									{/if}
									{if isset($group.group_required) && $group.group_required}&nbsp;&nbsp;&nbsp;<small class="awp_red">* {l s='Required' mod='attributewizardpro'}</small>{/if}

                                                                        {if $group.group_type == "textbox" || $group.group_type == 'textarea'}
                                                                            {if isset($group.price_impact_per_char) && $group.price_impact_per_char == 1}
                                                                                &nbsp;&nbsp;&nbsp;
                                                                                <small class="awp_red">* 
                                                                                    {if $group.group_min_limit > 0}
                                                                                        {l s='Minimum: ' mod='attributewizardpro'}
                                                                                        <i>{$group.group_min_limit}</i>
                                                                                        {if $group.group_min_limit == 1}
                                                                                            {l s=' character' mod='attributewizardpro'}
                                                                                        {else}
                                                                                            {l s=' characters' mod='attributewizardpro'}
                                                                                        {/if}
                                                                                        
                                                                                    {/if}                                                                                    
                                                                                </small>
                                                                            {/if}
                                                                        {/if}
									{if $group.group_type == "checkbox"}
										{if isset($group.chk_limit_min) && $group.chk_limit_min > 0 && $group.chk_limit_max == $group.chk_limit_max}
											<small>({l s='Select' mod='attributewizardpro'} {$group.chk_limit_min} {if $group.chk_limit_min > 1}{l s='options' mod='attributewizardpro'}{else}{l s='option' mod='attributewizardpro'}{/if})</small>
										{elseif isset($group.chk_limit_min) && $group.chk_limit_min > 0 && $group.chk_limit_max > 0}
											<small>({l s='Select' mod='attributewizardpro'} {$group.chk_limit_min} - {$group.chk_limit_max} {l s='options' mod='attributewizardpro'})</small>
										{elseif isset($group.chk_limit_min) && $group.chk_limit_min > 0 && $group.chk_limit_max <= 0}
											<small>({l s='Select at least' mod='attributewizardpro'} {$group.chk_limit_min} {if $group.chk_limit_min > 1}{l s='options' mod='attributewizardpro'}{else}{l s='option' mod='attributewizardpro'}{/if})</small>
										{elseif isset($group.chk_limit_min) && $group.chk_limit_min <= 0 && $group.chk_limit_max > 0}
											<small>({l s='Select up to' mod='attributewizardpro'} {$group.chk_limit_max} {if $group.chk_limit_max > 1}{l s='options' mod='attributewizardpro'}{else}{l s='option' mod='attributewizardpro'}{/if})</small>
										{/if}
									{/if}
									{if isset($group.group_description) && $group.group_description != ""}
										{if $awp_gd_popup}
										<span class="info_tooltip" title="{$group.group_description|replace:'"':'\''|escape:'htmlspecialchars':'UTF-8'}">
										</span>	
									{/if}
									{/if}
									
								</div>
								{if $awp_collapse_block}
								<div class="open_close awp_arrow_down"><img src="{$this_wizard_path}/views/img/down_arrow1.png"/></div>
								<div class="open_close awp_arrow_up"><img src="{$this_wizard_path}/views/img/up_arrow.png"/></div>
								{/if}
							</div>
							{if isset($group.group_description) && $group.group_description != ""}
								{if !$awp_gd_popup}
								<div class="awp_description">
									{$group.group_description nofilter} {* HTML SENT - needs to use nofilter*}
								</div>
								{/if}
							{/if}
						</div>
						<div class="awp_content">
				{/if}
					{if $group.group_type == "dropdown"}
	               		<div id="awp_cell_cont_{$id_attribute}" class="awp_cell_cont awp_cell_cont_{$group.id_group} awp_clear">
    						{if $group.group_color == 1}
								<div id="awp_select_colors_{$group.id_group}" {if !$group.group_layout}class="awp_left"{/if} {if isset($group.group_width) && $group.group_width}style="width:{$group.group_width}px;{if isset($group.group_height)}height:{$group.group_height}px;{/if}"{/if}>
									{foreach from=$group.attributes item=group_attribute}
									{strip}
										{assign var='id_attribute' value=$group_attribute.0}
										{if file_exists($col_img_dir|cat:$id_attribute|cat:'.jpg')}
											<div id="awp_group_div_{$id_attribute}" class="awp_group_image" style="{if isset($group.group_width) && $group.group_width > 0}width:{$group.group_width}px;{/if}{if isset($group.group_height) && $group.group_height > 0}height:{$group.group_height}px;{/if}display:{if $id_attribute|in_array:$group.default}block{else}none{/if};">
												{if !$awp_popup}<a href="{$img_col_dir}{$id_attribute}.jpg" border="0" class="{if $awp_psv < 1.6}thickbox{else}fancybox shown{/if}">{/if}<img {if isset($group.group_resize) && $group.group_resize && isset($group.group_width) && $group.group_width}style="width:{$group.group_width}px;{if isset($group.group_height)}height:{$group.group_height}px;{/if}"{/if} src="{$img_col_dir}{$id_attribute}.jpg" alt="" title="{$group_attribute.1|escape:'htmlall':'UTF-8'}" />{if !$awp_popup}</a>{/if}
           									</div>
           								{else}
											<div id="awp_group_div_{$id_attribute}" class="awp_group_image" style="{if isset($group.group_width) && $group.group_width > 0}width:{$group.group_width}px;{/if}{if isset($group.group_height) && $group.group_height > 0}height:{$group.group_height}px;{/if}background-color:{$group_attribute.2};display:{if $id_attribute|in_array:$group.default}block{else}none{/if};">
           									</div>
           								{/if}
           							{/strip}
          							{/foreach}
           						</div>
   							{/if}
   							<div id="awp_sel_cont_{$id_attribute}" class="{if !$group.group_layout}awp_sel_conth{else}awp_sel_contv{/if}">
	                   			<select class="awp_attribute_selected" onblur="this.style.position='';" name="awp_group_{$group.id_group}" id="awp_group_{$group.id_group}" onchange="awp_select('{$group.id_group|intval}', this.options[this.selectedIndex].value, {$awp_currency->id},false);this.style.position='';$('#awp_select_colors_{$group.id_group} div').each(function() {ldelim}$(this).css('display','none'){rdelim});$('#awp_group_div_'+this.value).fadeIn(1000);">
								{foreach from=$group.attributes item=group_attribute name=awp_dropdown}
								{strip}
								{assign var='id_attribute' value=$group_attribute.0}
								{if $smarty.foreach.awp_dropdown.first && isset($group.group_required) && $group.group_required}
	                   				<option value="" selected="selected">{l s='Select' mod='attributewizardpro'} {$group.name|escape:'htmlspecialchars':'UTF-8'}</option>
                   				{/if}
	                   				<option value="{$id_attribute}"{if $group.attributes_quantity.$id_attribute == 0 && $awp_allow_oosp != 1}{if $awp_out_of_stock == 'hide'} class="awp_oos"{/if}{if $awp_out_of_stock == 'disable'} disabled="disabled"{/if}{/if} {if $id_attribute|in_array:$group.default && (!isset($group.group_required) || !$group.group_required)}selected="selected"{/if}>{$group_attribute.1|escape:'htmlall':'UTF-8'}
			                    	{foreach from=$attributeImpacts key=id_attributeImpact item=attributeImpact}
	                      				{if $id_attribute == $attributeImpact.id_attribute}
											{math equation="x - y" x=$attributeImpact.price y=$default_impact assign='awp_pi'}
                       						&nbsp;
	                       					
                                                                
                                                                {if !$noTaxForThisProduct}
                                                                                                    {math equation="(x / 100) + 1" x=$taxRate assign=awpTax}
                                                                                                    {math equation="x * y" x=$awpTax y=$awp_pi assign=awp_pi}
                                                                                                
                                                                                                {/if}
                   								{if $awp_pi_display == ""}
	                   								
                								{elseif $awp_pi > 0}
		               								[{l s='Add' mod='attributewizardpro'} {awpConvertPriceWithCurrency price=$awp_pi currency=$awp_currency}]
                								{elseif $awp_pi < 0}
		               								[{l s='Subtract' mod='attributewizardpro'} {awpConvertPriceWithCurrency price=($awp_pi|abs) currency=$awp_currency}]
	                							{/if}	
                                                                                
                                                                                
                       					{/if}
                   					{/foreach}
                   					</option>
                   				{/strip}
                    			{/foreach}
                   				</select>
							</div>
           					{if !$group.group_layout && $group.group_height}
                                                    <script type="text/javascript">
                                                        awp_sel_cont_var[{$id_attribute}] = '{$group.group_height}';
                                                        
                                                    </script>
           					{/if}
						</div>
					{elseif $group.group_type == "radio" || $group.group_type == "image"}
						<input type="hidden" id="awp_group_layout_{$group.id_group}" value="{$group.group_layout}" />
						<input type="hidden" id="awp_group_per_row_{$group.id_group}" value="{$group.group_per_row}" />
						{foreach from=$group.attributes name=awp_loop item=group_attribute}
						{strip}
						{assign var='id_attribute' value=$group_attribute.0}
	                   		<div id="awp_cell_cont_{$id_attribute}" class="awp_cell_cont awp_cell_cont_{$group.id_group}{if $smarty.foreach.awp_loop.iteration % $group.group_per_row == 1 || $group.group_per_row == 1} awp_clear{/if} {if $group.attributes_quantity.$id_attribute == 0 && $awp_out_of_stock == 'hide'} awp_oos{/if}">
								<div id="awp_cell_{$id_attribute}" {if $group.attributes_quantity.$id_attribute == 0 && $awp_allow_oosp != 1 && $awp_out_of_stock == 'hide'}class="awp_oos"{/if} style="{if $group.group_color != 1}{if isset($group.group_width) && $group.group_width > 0}width:{$group.group_width}px;{/if}{/if}" {if $group.attributes_quantity.$id_attribute > 0 || $awp_out_of_stock != 'disable' || $awp_allow_oosp == 1}onclick="$('input[name=\'awp_group_{$group.id_group}\']').removeAttr('checked');$('#awp_radio_group_{$id_attribute}').attr('checked', 'checked');$('#awp_radio_group_{$id_attribute}').prop('checked', true);{if $group.group_type == "image"}awp_toggle_img({$group.id_group|intval},{$group_attribute.0|intval});{/if}awp_select('{$group.id_group|intval}',{$group_attribute.0|intval}, {$awp_currency->id}, false){/if}">
	                   				{if file_exists($col_img_dir|cat:$id_attribute|cat:'.jpg')}
                   						<div id="awp_tc_{$id_attribute}" class="awp_group_image awp_gi_{$group.id_group}{if !$group.group_layout} awp_left{/if}{if $group.group_type == "image"}{if $id_attribute|in_array:$group.default && (!isset($group.group_required) || !$group.group_required)} awp_image_sel{else} awp_image_nosel{/if}{/if}" style="{if isset($group.group_width) && $group.group_width > 0}width:{$group.group_width}px;{/if}{if isset($group.group_height) && $group.group_height > 0}height:{$group.group_height}px;{/if}">
		                    				{if $group.group_type != "image" && !$awp_popup}<a href="{$img_col_dir}{$id_attribute}.jpg" border="0" class="{if $awp_psv < 1.6}thickbox{else}fancybox shown{/if}">{/if}<img {if $group.group_resize}style="{if isset($group.group_width) && $group.group_width > 0}width:{$group.group_width}px;{/if}{if isset($group.group_height) && $group.group_height > 0}height:{$group.group_height}px;{/if}"{/if} src="{$img_col_dir}{$id_attribute}.jpg" alt="{$group_attribute.1|escape:'htmlall':'UTF-8'}" title="{$group_attribute.1|escape:'htmlall':'UTF-8'}" />{if $group.group_type != "image" && !$awp_popup}</a>{/if}
                   						</div>
                   					{elseif $group_attribute.2 != ""}
	                   					<div id="awp_tc_{$id_attribute}" class="awp_group_image awp_gi_{$group.id_group}{if !$group.group_layout} awp_left{/if}{if $group.group_type == "image"}{if $id_attribute|in_array:$group.default && (!isset($group.group_required) || !$group.group_required)} awp_image_sel{else} awp_image_nosel{/if}{/if}" style="{if isset($group.group_width)}width:{$group.group_width}px;{/if}{if isset($group.group_height)}height:{$group.group_height}px;{/if}background-color:{$group_attribute.2};">
                   							&nbsp;
	                   					</div>
                   					{/if}
                                                        <div class="awp_left awp_full_text">
                   					<div id="awp_radio_cell{$id_attribute}" class="{if !$group.group_layout}awp_rrla{else}awp_rrca{/if}{if $group.group_type == "image"} awp_none{/if}">
	                   					<input type="radio" {if $group.attributes_quantity.$id_attribute == 0 && $awp_allow_oosp != 1  && $awp_out_of_stock == 'disable' && $awp_allow_oosp == false}disabled="disabled"{/if} class="awp_attribute_selected awp_clean" id="awp_radio_group_{$id_attribute}" name="awp_group_{$group.id_group}" value="{$group_attribute.0|intval}" {if $id_attribute|in_array:$group.default && (!isset($group.group_required) || !$group.group_required)}checked="checked"{/if} />&nbsp;
                   						{if $smarty.foreach.awp_loop.first}
                 								<input type="hidden" name="pi_default_{$group.id_group}" id="pi_default_{$group.id_group}" value="{$default_impact}" />
               							{/if}
               						</div>
               						<div id="awp_impact_cell{$id_attribute}" class="{if !$group.group_layout}awp_nila{else}awp_nica{/if}">
										{if isset($group.group_hide_name) && !$group.group_hide_name}
											<div class="{if !$group.group_layout}awp_tbla{else}awp_tbca{/if}">{$group_attribute.1|escape:'htmlall':'UTF-8'}</div>
										{/if}
                   						{foreach from=$attributeImpacts key=id_attributeImpact item=attributeImpact}
	                   						{if $id_attribute == $attributeImpact.id_attribute}
												{math equation="x - y" x=$attributeImpact.price y=$default_impact assign='awp_pi'}
           										<div class="{if !$group.group_layout}awp_tbla{else}awp_tbca{/if}" id="price_change_{$id_attribute}">
                   								
                                                                                
                                                                                {if !$noTaxForThisProduct}
                                                                                                    {math equation="(x / 100) + 1" x=$taxRate assign=awpTax}
                                                                                                    {math equation="x * y" x=$awpTax y=$awp_pi assign=awp_pi}
                                                                                                
                                                                                                {/if}
                   								{if $awp_pi_display == ""}
	                   								&nbsp;
                								{elseif $awp_pi > 0}
		               								[{l s='Add' mod='attributewizardpro'} {awpConvertPriceWithCurrency price=$awp_pi currency=$awp_currency}]
                								{elseif $awp_pi < 0}
		               								[{l s='Subtract' mod='attributewizardpro'} {awpConvertPriceWithCurrency price=($awp_pi|abs) currency=$awp_currency}]
	                							{/if}	
                                                                                
                  								</div>
	                   						{/if}
    	              					{/foreach}
                   					</div>
                                                        
                                                        
                                                        {if $group_attribute.4 > 0}
                                                            <div class="clearfix"></div>
                                                            <div class="attribute_extra_description">
                                                                <div>
                                                                    {$group_attribute.5}
                                                                </div>
                                                                <div>
                                                                    <a data-product-id="{$group_attribute.4}" data-product-attribute-id="{$group_attribute.6}" class="awpquickview" href="#" >
                                                                        {l s='More info' mod='attributewizardpro'}
                                                                    </a>
                                                                    
                                                                </div>
                                                            </div>
                                                        {/if}
                                                        </div>
                                                        
                  				</div>
                  			</div>
						{/strip}                    		
						{/foreach}
					{elseif $group.group_type == "textbox"}
						<input type="hidden" id="awp_group_layout_{$group.id_group}" value="{$group.group_layout}" />
						<input type="hidden" id="awp_group_per_row_{$group.id_group}" value="{$group.group_per_row}" />
						{if isset($group.group_hide_name) && !$group.group_hide_name}
							<script type="text/javascript">
								var awp_max_text_length_{$group.id_group} = 0;
							</script>
						{/if}
						{foreach from=$group.attributes name=awp_loop item=group_attribute}
						{strip}
							{assign var='id_attribute' value=$group_attribute.0}
                  			<div id="awp_cell_cont_{$id_attribute}" class="awp_cell_cont awp_cell_cont_{$group.id_group}{if $smarty.foreach.awp_loop.iteration % $group.group_per_row == 1 || $group.group_per_row == 1} awp_clear{/if} {if $group.attributes_quantity.$id_attribute == 0  && $awp_allow_oosp != 1 && $awp_out_of_stock == 'hide'} awp_oos{/if}">
                   				<div id="awp_cell_{$id_attribute}" {if $group.attributes_quantity.$id_attribute == 0 && $awp_allow_oosp != 1  && $awp_out_of_stock == 'hide'}class="awp_oos"{/if}>
	               					{if file_exists($col_img_dir|cat:$id_attribute|cat:'.jpg')}
                   						<div id="awp_tc_{$id_attribute}" class="awp_group_image awp_gi_{$group.id_group}{if !$group.group_layout} awp_left{/if}{if $group.group_type == "image"}{if $id_attribute|in_array:$group.default} awp_image_sel{else} awp_image_nosel{/if}{/if}" style="{if isset($group.group_width) && $group.group_width > 0}width:{$group.group_width}px;{/if}{if isset($group.group_height) && $group.group_height > 0}height:{$group.group_height}px;{/if}">
		                					{if $group.group_type != "image" && !$awp_popup}<a href="{$img_col_dir}{$id_attribute}.jpg" border="0" class="{if $awp_psv < 1.6}thickbox{else}fancybox shown{/if}">{/if}<img {if $group.group_resize}style="{if isset($group.group_width) && $group.group_width > 0}width:{$group.group_width}px;{/if}{if isset($group.group_height) && $group.group_height > 0}height:{$group.group_height}px;{/if}"{/if} src="{$img_col_dir}{$id_attribute}.jpg" alt="{$group_attribute.1|escape:'htmlall':'UTF-8'}" title="{$group_attribute.1|escape:'htmlall':'UTF-8'}" />{if $group.group_type != "image" && !$awp_popup}</a>{/if}
                   						</div>
                   					{elseif $group_attribute.2 != ""}
	               						<div id="awp_tc_{$id_attribute}" class="awp_group_image awp_gi_{$group.id_group}{if !$group.group_layout} awp_left{/if}{if $group.group_type == "image"}{if $id_attribute|in_array:$group.default} awp_image_sel{else} awp_image_nosel{/if}{/if}" style="{if isset($group.group_width) && $group.group_width > 0}width:{$group.group_width}px;{/if}{if isset($group.group_height) && $group.group_height > 0}height:{$group.group_height}px;{/if}background-color:{$group_attribute.2};">
		                   					&nbsp;
                   						</div>
		                   			{/if}
                                                        <div class="awp_left awp_full_text">
                   					{if isset($group.group_hide_name) && !$group.group_hide_name}
                   						<div id="awp_text_length_{$id_attribute}" class="awp_text_length_group awp_text_length_group_{$group.id_group} {if !$group.group_layout}awp_nila{else}awp_nica{/if}" >{$group_attribute.1|escape:'htmlall':'UTF-8'}&nbsp;</div>
                   					{/if}
		                    		<div id="awp_textbox_cell{$id_attribute}" class="{if !$group.group_layout}awp_tbla{else}awp_tbca{/if}">
                						<input type="text" value="{if isset($awp_edit_special_values.$id_attribute)}{$awp_edit_special_values.$id_attribute}{/if}" style="margin:0;padding:0;{if $group.group_width}width:{$group.group_width}px;{/if}" {if $group.attributes_quantity.$id_attribute == 0 && $awp_allow_oosp != 1  && $awp_out_of_stock == 'disable'}disabled="disabled"{/if} class="awp_attribute_selected awp_group_class_{$group.id_group}" id="awp_textbox_group_{$id_attribute}" name="awp_group_{$group.id_group}_{$id_attribute}" onkeyup="{if $group.group_max_limit > 0}awp_max_limit_check('{$group_attribute.0|intval}',{$group.group_max_limit});{/if}awp_select('{$group.id_group|intval}',{$group_attribute.0|intval}, {$awp_currency->id}, false);" onblur="{if $group.group_max_limit > 0}awp_max_limit_check('{$group_attribute.0|intval}',{$group.group_max_limit});{/if}awp_select('{$group.id_group|intval}',{$group_attribute.0|intval}, {$awp_currency->id}, false);" />&nbsp;
                   						{if $smarty.foreach.awp_loop.first}
               								<input type="hidden" name="pi_default_{$group.id_group}" id="pi_default_{$group.id_group}" value="{$default_impact}" />
               							{/if}
               						</div>
               						<div id="awp_impact_cell{$id_attribute}" class="{if !$group.group_layout}awp_nila{else}awp_nica{/if}">
                   						{foreach from=$attributeImpacts key=id_attributeImpact item=attributeImpact}
										{strip}
                   							{if $id_attribute == $attributeImpact.id_attribute}
               									{assign var='awp_pi' value=$attributeImpact.price}
												{if $awp_pi_display  != ""}
			                    					<div class="{if !$group.group_layout}awp_tbla{else}awp_tbca{/if}" id="price_change_{$id_attribute}">
												{else}
				                   					<div class="{if !$group.group_layout}awp_tbla{else}awp_tbca{/if}" id="price_change_{$id_attribute}" style="display:none"></div><div class="{if !$group.group_layout}awp_tbla{else}awp_tbca{/if}">
												{/if}
                                                                                                {if !$noTaxForThisProduct}
                                                                                                    {math equation="(x / 100) + 1" x=$taxRate assign=awpTax}
                                                                                                    {math equation="x * y" x=$awpTax y=$awp_pi assign=awp_pi}
                                                                                                
                                                                                                {/if}
                   								{if $awp_pi_display == ""}
                   									&nbsp;
               									{elseif $awp_pi > 0}
	               									[{l s='Add' mod='attributewizardpro'} {awpConvertPriceWithCurrency price=$awp_pi currency=$awp_currency} {if isset($group.price_impact_per_char) && $group.price_impact_per_char == 1}{l s=' per character' mod='attributewizardpro'}{/if}]
               									{elseif $awp_pi < 0}
	               									[{l s='Subtract' mod='attributewizardpro'} {$awp_pi|abs} {if isset($group.price_impact_per_char) && $group.price_impact_per_char == 1}{l s=' per character' mod='attributewizardpro'}{/if}]
	           									{/if}
           										</div>
												{if isset($group.group_required) && $group.group_required}<div class="{if !$group.group_layout}awp_tbla{else}awp_tbca{/if} awp_red">* {l s='Required' mod='attributewizardpro'}</div>{/if}
												{if $group.group_max_limit > 0}<div class="{if !$group.group_layout}awp_tbla{else}awp_tbca{/if} awp_red">{l s='Characters left:' mod='attributewizardpro'} <span id="awp_max_limit_{$id_attribute}" class="awp_max_limit" awp_limit="{$group.group_max_limit}">{$group.group_max_limit}</span></div>{/if}
                   							{/if}
                   						{/strip}
   	              						{/foreach}
               						</div>
       								
               					</div>
                                                        
                                                        {if $group_attribute.4 > 0}
                                                            <div class="attribute_extra_description">
                                                                <div>
                                                                    {$group_attribute.5}
                                                                </div>
                                                                <div>
                                                                    <a data-product-id="{$group_attribute.4}" data-product-attribute-id="{$group_attribute.6}" class="awpquickview" href="#" >
                                                                        {l s='More info' mod='attributewizardpro'}
                                                                    </a>
                                                                    
                                                                </div>
                                                            </div>
                                                        {/if}
                                                        </div>
               					
               				</div>
                  		{/strip}
                                {if isset($group.group_hide_name) && !$group.group_hide_name}
                                                    <script type="text/javascript">
                                                        awp_cell_cont_text_group[{$id_attribute}] = {$group.id_group};

                                                        awp_sel_cont_var[{$id_attribute}] = '{$group.group_height}';
                                                    </script>
						{/if}
						{/foreach}
						
					{elseif $group.group_type == "textarea"}
						<input type="hidden" id="awp_group_layout_{$group.id_group}" value="{$group.group_layout}" />
						<input type="hidden" id="awp_group_per_row_{$group.id_group}" value="{$group.group_per_row}" />
						{if isset($group.group_hide_name) && !$group.group_hide_name}
							<script type="text/javascript">
								var awp_max_text_length_{$group.id_group} = 0;
							</script>
						{/if}
						{foreach from=$group.attributes name=awp_loop item=group_attribute}
						{strip}
							{assign var='id_attribute' value=$group_attribute.0}
                 			<div id="awp_cell_cont_{$id_attribute}" class="awp_cell_cont awp_cell_cont_{$group.id_group}{if !$group.group_layout} awp_clear{/if} {if $group.attributes_quantity.$id_attribute == 0 && $awp_allow_oosp != 1  && $awp_out_of_stock == 'hide'} awp_oos{/if}">
	                   			<div id="awp_cell_{$id_attribute}" {if $group.attributes_quantity.$id_attribute == 0 && $awp_allow_oosp != 1  && $awp_out_of_stock == 'hide'}class="awp_oos"{/if} style="width:100%">
		               				{if file_exists($col_img_dir|cat:$id_attribute|cat:'.jpg')}
                   						<div id="awp_tc_{$id_attribute}" class="awp_group_image awp_gi_{$group.id_group}{if !$group.group_layout} awp_left{/if}{if $group.group_type == "image"}{if $id_attribute|in_array:$group.default} awp_image_sel{else} awp_image_nosel{/if}{/if}" style="{if isset($group.group_width) && $group.group_width > 0}width:{$group.group_width}px;{/if}{if isset($group.group_height) && $group.group_height > 0}height:{$group.group_height}px;{/if}">
			                				{if $group.group_type != "image" && !awp_popup}<a href="{$img_col_dir}{$id_attribute}.jpg" border="0" class="{if $awp_psv < 1.6}thickbox{else}fancybox shown{/if}">{/if}<img {if $group.group_resize}style="{if isset($group.group_width) && $group.group_width > 0}width:{$group.group_width}px;{/if}{if isset($group.group_height) && $group.group_height > 0}height:{$group.group_height}px;{/if}"{/if} src="{$img_col_dir}{$id_attribute}.jpg" alt="{$group_attribute.1|escape:'htmlall':'UTF-8'}" title="{$group_attribute.1|escape:'htmlall':'UTF-8'}" />{if $group.group_type != "image" && !$awp_popup}</a>{/if}
                   						</div>
                   					{elseif $group_attribute.2 != ""}
		               					<div id="awp_tc_{$id_attribute}" class="awp_group_image awp_gi_{$group.id_group}{if !$group.group_layout} awp_left{/if}{if $group.group_type == "image"}{if $id_attribute|in_array:$group.default} awp_image_sel{else} awp_image_nosel{/if}{/if}" style="{if isset($group.group_width)}width:{$group.group_width}px;{/if}{if isset($group.group_height)}height:{$group.group_height}px;{/if}background-color:{$group_attribute.2};">
                   							&nbsp;
                   						</div>
                   					{/if}
                                                        
                                                        <div class="awp_left awp_full_text">
									{if isset($group.group_hide_name) && !$group.group_hide_name}
	                   					<div id="awp_text_length_{$id_attribute}" class="awp_text_length_group_{$group.id_group} {if !$group.group_layout}awp_nila{else}awp_nica{/if}">{$group_attribute.1|escape:'htmlall':'UTF-8'}&nbsp;</div>
										
									{/if}
                    				<div id="awp_textarea_cell{$id_attribute}" class="{if !$group.group_layout}awp_tbla{else}awp_tbca{/if}">
	                  					<textarea {if $group.attributes_quantity.$id_attribute == 0 && $awp_allow_oosp != 1  && $awp_out_of_stock == 'disable'}disabled="disabled"{/if} style="margin:0;padding:0;{if $group.group_width}width:{$group.group_width}px;{/if}{if $group.group_height}height:{$group.group_height}px;{/if}" class="awp_attribute_selected awp_group_class_{$group.id_group}" id="awp_textarea_group_{$id_attribute}" name="awp_group_{$group.id_group}_{$id_attribute}" onkeyup="{if $group.group_max_limit > 0}awp_max_limit_check('{$group_attribute.0|intval}',{$group.group_max_limit});{/if}awp_select('{$group.id_group|intval}',{$group_attribute.0|intval}, {$awp_currency->id}, false);" onblur="{if $group.group_max_limit > 0}awp_max_limit_check('{$group_attribute.0|intval}',{$group.group_max_limit});{/if}awp_select('{$group.id_group|intval}',{$group_attribute.0|intval}, {$awp_currency->id}, false);">{if isset($awp_edit_special_values.$id_attribute)}{$awp_edit_special_values.$id_attribute}{/if}</textarea>&nbsp;
                   						{if $smarty.foreach.awp_loop.first}
	               							<input type="hidden" name="pi_default_{$group.id_group}" id="pi_default_{$group.id_group}" value="{$default_impact}" />
               							{/if}
               						</div>
               						<div id="awp_impact_cell{$id_attribute}" class="{if !$group.group_layout}awp_nila{else}awp_nica{/if}">
	                   					{foreach from=$attributeImpacts key=id_attributeImpact item=attributeImpact}
                   						{strip}
	                   						{if $id_attribute == $attributeImpact.id_attribute}
               									{assign var='awp_pi' value=$attributeImpact.price}
												{if $awp_pi_display != ""}
				                      				<div id="price_change_{$id_attribute}" class="{if !$group.group_layout}awp_tbla{else}awp_tbca{/if}">
												{else}
					                      			<div id="price_change_{$id_attribute}" class="{if !$group.group_layout}awp_tbla{else}awp_tbca{/if}" style="display:none"></div><div class="{if !$group.group_layout}awp_tbla{else}awp_tbca{/if}">
												{/if}	
                                                                                                
                                                                                                {if !$noTaxForThisProduct}
                                                                                                    {math equation="(x / 100) + 1" x=$taxRate assign=awpTax}
                                                                                                    {math equation="x * y" x=$awpTax y=$awp_pi assign=awp_pi}
                                                                                                
                                                                                                {/if}
                   								{if $awp_pi_display == ""}
                   									&nbsp;
               									{elseif $awp_pi > 0}
	                  								[{l s='Add' mod='attributewizardpro'} {awpConvertPriceWithCurrency price=$awp_pi currency=$awp_currency}]
               									{elseif $awp_pi < 0}
	                   								[{l s='Subtract' mod='attributewizardpro'} {awpConvertPriceWithCurrency price=($awp_pi|abs) currency=$awp_currency}]
               									{/if}
                   								</div>
												{if isset($group.group_required) && $group.group_required}<div class="{if !$group.group_layout}awp_tbla{else}awp_tbca{/if} awp_red">* {l s='Required' mod='attributewizardpro'}</div>{/if}
												{if $group.group_max_limit > 0}<div class="{if !$group.group_layout}awp_tbla{else}awp_tbca{/if} awp_red">{l s='Characters left:' mod='attributewizardpro'} <span id="awp_max_limit_{$id_attribute}" class="awp_max_limit" awp_limit="{$group.group_max_limit}">{$group.group_max_limit}</span></div>{/if}
                       						{/if}
	                       				{/strip}
   		               					{/foreach}
                   					</div>
           							{if !$group.group_layout && $group.group_height}
                                                                    <script type="text/javascript">
                                                                        awp_sel_cont_var[{$id_attribute}] = '{$group.group_height}';
               							</script>
           							{/if}
                   				</div>
                                                
                                                {if $group_attribute.4 > 0}
                                                            <div class="attribute_extra_description">
                                                                <div>
                                                                    {$group_attribute.5}
                                                                </div>
                                                                <div>
                                                                    <a data-product-id="{$group_attribute.4}" data-product-attribute-id="{$group_attribute.6}" class="awpquickview" href="#" >
                                                                        {l s='More info' mod='attributewizardpro'}
                                                                    </a>
                                                                    
                                                                </div>
                                                            </div>
                                                        {/if}
                                                        
                                                        </div>
                   			</div>
                    	{/strip}	
						{/foreach}
						{if isset($group.group_hide_name) && !$group.group_hide_name}
							
						{/if}
					{elseif $group.group_type == "file"}
						<input type="hidden" id="awp_group_layout_{$group.id_group}" value="{$group.group_layout}" />
						<input type="hidden" id="awp_group_per_row_{$group.id_group}" value="{$group.group_per_row}" />
						{foreach from=$group.attributes name=awp_loop item=group_attribute}
						{strip}
							{assign var='id_attribute' value=$group_attribute.0}
							{assign var='id_attribute_file' value=$group_attribute.0|cat:'_file'}
                  			<div id="awp_cell_cont_{$id_attribute}" class="awp_cell_cont awp_cell_cont_{$group.id_group}{if $smarty.foreach.awp_loop.iteration % $group.group_per_row == 1 || $group.group_per_row == 1} awp_clear{/if} {if $group.attributes_quantity.$id_attribute == 0 && $awp_out_of_stock == 'hide'} awp_oos{/if} {if $group.attributes_quantity.$id_attribute == 0 && $awp_out_of_stock == 'hide'} awp_oos{/if}">
	                    		<div id="awp_cell_{$id_attribute}" {if $group.attributes_quantity.$id_attribute == 0 && $awp_allow_oosp != 1  && $awp_out_of_stock == 'hide'}class="awp_oos"{/if} style="width:100%">
	               					{if file_exists($col_img_dir|cat:$id_attribute|cat:'.jpg')}
	                   					<div id="awp_tc_{$id_attribute}" class="awp_group_image awp_gi_{$group.id_group}{if !$group.group_layout} awp_left{/if}{if $group.group_type == "image"}{if $id_attribute|in_array:$group.default} awp_image_sel{else} awp_image_nosel{/if}{/if}" style="{if isset($group.group_width) && $group.group_width > 0}width:{$group.group_width}px;{/if}{if isset($group.group_height) && $group.group_height > 0}height:{$group.group_height}px;{/if}">
		                					{if $group.group_type != "image" && !$awp_popup}<a href="{$img_col_dir}{$id_attribute}.jpg" border="0" class="{if $awp_psv < 1.6}thickbox{else}fancybox shown{/if}">{/if}<img {if $group.group_resize}style="{if isset($group.group_width) && $group.group_width > 0}width:{$group.group_width}px;{/if}{if isset($group.group_height) && $group.group_height > 0}height:{$group.group_height}px;{/if}"{/if} src="{$img_col_dir}{$id_attribute}.jpg" alt="{$group_attribute.1|escape:'htmlall':'UTF-8'}" title="{$group_attribute.1|escape:'htmlall':'UTF-8'}" />{if $group.group_type != "image" && !$awp_popup}</a>{/if}
                   						</div>
                   					{elseif $group_attribute.2 != ""}
		               					<div id="awp_tc_{$id_attribute}" class="awp_group_image awp_gi_{$group.id_group}{if !$group.group_layout} awp_left{/if}{if $group.group_type == "image"}{if $id_attribute|in_array:$group.default} awp_image_sel{else} awp_image_nosel{/if}{/if}" style="{if isset($group.group_width)}width:{$group.group_width}px;{/if}{if isset($group.group_height)}height:{$group.group_height}px;{/if}background-color:{$group_attribute.2};">
                   							&nbsp;
                   						</div>
                   					{/if}
                                                        <div class="awp_left awp_full_text">
               						{if isset($group.group_hide_name) && !$group.group_hide_name}
	               						<div id="awp_text_length_{$id_attribute}" class="{if !$group.group_layout}awp_tbla{else}awp_tbca{/if}">
	               							{$group_attribute.1|escape:'htmlall':'UTF-8'}
               							</div>
               						{/if}
                   					<div id="awp_file_cell{$id_attribute}" class="{if !$group.group_layout}awp_tbla{else}awp_tbca{/if}">
										<input id="upload_button_{$id_attribute}" {if $group.attributes_quantity.$id_attribute == 0 && $awp_allow_oosp != 1  && $awp_out_of_stock == 'disable'}disabled="disabled"{/if} class="{if $awp_psv < 1.6}button{else}btn btn-default button button-small{/if}" style="{if $awp_psv < 1.6}margin:0;padding:0;{else}margin:0;padding:3px 8px;{/if}cursor:pointer" value="{l s='Upload File' mod='attributewizardpro'}" type="button" />
	               						<input type="hidden"  class="awp_attribute_selected awp_group_class_{$group.id_group}" id="awp_file_group_{$id_attribute}" name="awp_group_{$group.id_group}_{$id_attribute}" {if isset($awp_edit_special_values.$id_attribute_file)}value=""{/if} />&nbsp;
		               						{if $smarty.foreach.awp_loop.first}
               								<input type="hidden" name="pi_default_{$group.id_group}" id="pi_default_{$group.id_group}" value="{$default_impact}" />
               							{/if}
               						</div>
	               					<div id="awp_image_cell_{$id_attribute}" class="up_image_clear awp_tbla">
	              						{if isset($awp_edit_special_values.$id_attribute)}{$awp_edit_special_values.$id_attribute nofilter}{/if} {* html is here *}
               						</div>
               						<div id="awp_image_delete_cell_{$id_attribute}" class="up_image_hide awp_tbla" style="display:{if isset($awp_edit_special_values.$id_attribute)}block{else}none{/if}">
	               						<img src="{$this_wizard_path}views/img/delete.gif" style="cursor: pointer" onclick="$('#awp_image_cell_{$id_attribute}').html('');$('#awp_image_delete_cell_{$id_attribute}').css('display','none');$('#awp_file_group_{$id_attribute}').val('');awp_price_update();" /> 
               						</div>
               						<div id="awp_impact_cell{$id_attribute}" class="{if !$group.group_layout}awp_nila{else}awp_nica{/if}">
		               					{foreach from=$attributeImpacts key=id_attributeImpact item=attributeImpact}
	               						{strip}
	                   						{if $id_attribute == $attributeImpact.id_attribute}
               									{assign var='awp_pi' value=$attributeImpact.price}
												{if $awp_pi_display != ""}
				                      				<div class="{if !$group.group_layout}awp_tbla{else}awp_tbca{/if}" id="price_change_{$id_attribute}">
												{else}
					                      			<div class="{if !$group.group_layout}awp_tbla{else}awp_tbca{/if}" id="price_change_{$id_attribute}" style="display:none"></div><div class="{if !$group.group_layout}awp_tbla{else}awp_tbca{/if}">
												{/if}
                                                                                                
                                                                                                {if !$noTaxForThisProduct}
                                                                                                    {math equation="(x / 100) + 1" x=$taxRate assign=awpTax}
                                                                                                    {math equation="x * y" x=$awpTax y=$awp_pi assign=awp_pi}
                                                                                                
                                                                                                {/if}
                   								{if $awp_pi_display == ""}
	                   								&nbsp;
                								{elseif $awp_pi > 0}
		               								[{l s='Add' mod='attributewizardpro'} {awpConvertPriceWithCurrency price=$awp_pi currency=$awp_currency}]
                								{elseif $awp_pi < 0}
		               								[{l s='Subtract' mod='attributewizardpro'} {awpConvertPriceWithCurrency price=($awp_pi|abs) currency=$awp_currency}]
	                							{/if}	
                  								</div>
												{if isset($group.group_required) && $group.group_required}<div class="{if !$group.group_layout}awp_tbla{else}awp_tbca{/if} awp_red">* {l s='Required' mod='attributewizardpro'}</div>{/if}
	                   						{/if}
                       					{/strip}
   		               					{/foreach}
                   					</div>
           							{if !$group.group_layout && $group.group_height}
                                                                    <script type="text/javascript">
                                                                        awp_sel_cont_var[{$id_attribute}] = '{$group.group_height}';
               							
                                                                    </script>
           							{/if}
                    			</div>
                                        
                                                        {if $group_attribute.4 > 0}
                                                            <div class="attribute_extra_description">
                                                                <div>
                                                                    {$group_attribute.5}
                                                                </div>
                                                                <div>
                                                                    <a data-product-id="{$group_attribute.4}" data-product-attribute-id="{$group_attribute.6}" class="awpquickview" href="#" >
                                                                        {l s='More info' mod='attributewizardpro'}
                                                                    </a>
                                                                    
                                                                </div>
                                                            </div>
                                                        {/if}
                                                        
                                                                </div>
                    		</div>
                    	{/strip}
						{/foreach}
					{elseif $group.group_type == "checkbox"}
						<input type="hidden" id="awp_group_layout_{$group.id_group}" value="{$group.group_layout}" />
						<input type="hidden" id="awp_group_per_row_{$group.id_group}" value="{$group.group_per_row}" />
						{foreach from=$group.attributes name=awp_loop item=group_attribute}
						{strip}
							{assign var='id_attribute' value=$group_attribute.0}
                 			<div id="awp_cell_cont_{$id_attribute}" class="awp_cell_cont awp_cell_cont_{$group.id_group}{if $smarty.foreach.awp_loop.iteration % $group.group_per_row == 1 || $group.group_per_row == 1} awp_clear{/if}  {if $group.attributes_quantity.$id_attribute == 0 && $awp_allow_oosp != 1 && $awp_out_of_stock == 'hide'} awp_oos{/if}">
	                   			<div id="awp_cell_{$id_attribute}" {if $group.attributes_quantity.$id_attribute == 0 && $awp_allow_oosp != 1  && $awp_out_of_stock == 'hide'}class="awp_oos"{/if} style="{if $group.group_color != 1}{if isset($group.group_width) && $group.group_width > 0}width:{$group.group_width}px;{/if}{/if}" onclick="{if $group.group_color == 100}updateColorSelect({$id_attribute}){/if};">
									{if file_exists($col_img_dir|cat:$id_attribute|cat:'.jpg')}
										<div id="awp_tc_{$id_attribute}" class="awp_group_image{if !$group.group_layout} awp_left{/if}" style="{if isset($group.group_width) && $group.group_width > 0}width:{$group.group_width}px;{/if}{if isset($group.group_height) && $group.group_height > 0}height:{$group.group_height}px;{/if}">
											{if !$awp_popup}<a href="{$img_col_dir}{$id_attribute}.jpg" border="0" class="{if $awp_psv < 1.6}thickbox{else}fancybox shown{/if}">{/if}
												<img {if $group.group_resize}style="{if isset($group.group_width) && $group.group_width > 0}width:{$group.group_width}px;{/if}{if isset($group.group_height) && $group.group_height > 0}height:{$group.group_height}px;{/if}"{/if} src="{$img_col_dir}{$id_attribute}.jpg" alt="{$group_attribute.1|escape:'htmlall':'UTF-8'}" title="{$group_attribute.1|escape:'htmlall':'UTF-8'}" />
											{if !$awp_popup}</a>{/if}
										</div>
									{elseif $group_attribute.2 != ""}
										<div id="awp_tc_{$id_attribute}" class="awp_group_image{if !$group.group_layout} awp_left{/if}" style="{if isset($group.group_width)}width:{$group.group_width}px;{/if}{if isset($group.group_height)}height:{$group.group_height}px;{/if}background-color:{$group_attribute.2};">
											&nbsp;
										</div>
									{/if}
                                                                        <div class="awp_left awp_full_text">
									<div id="awp_checkbox_cell{$id_attribute}" class="{if !$group.group_layout}awp_tbla{else}awp_tbca awp_checkbox_group{/if}">
		            					<input type="checkbox" {if $group.attributes_quantity.$id_attribute == 0 && $awp_allow_oosp != 1  && $awp_out_of_stock == 'disable'}disabled="disabled"{/if} class="awp_attribute_selected awp_group_class_{$group.id_group} awp_clean" name="awp_group_{$group.id_group}" id="awp_checkbox_group_{$id_attribute}" onclick="awp_select('{$group.id_group|intval}',{$group_attribute.0|intval}, {$awp_currency->id},false);" value="{$group_attribute.0|intval}" {if $group.default|is_array && $id_attribute|in_array:$group.default}checked{/if} />&nbsp;
									</div>
									<div id="awp_impact_cell{$id_attribute}" class="{if !$group.group_layout}awp_nila{else}awp_nica{/if}">
										{if isset($group.group_hide_name) && !$group.group_hide_name}
											<div class="{if !$group.group_layout}awp_tbla{else}awp_tbca{/if}">{$group_attribute.1|escape:'htmlall':'UTF-8'}</div>
										{/if}
										{foreach from=$attributeImpacts key=id_attributeImpact item=attributeImpact}
											{if $id_attribute == $attributeImpact.id_attribute}
												{assign var='awp_pi' value=$attributeImpact.price}
												{if $awp_pi_display != ""}
						                   			<div class="{if !$group.group_layout}awp_tbla{else}awp_tbca{/if}" id="price_change_{$id_attribute}">
												{else}	
					                    			<span class="{if !$group.group_layout}awp_tbla{else}awp_tbca{/if}" id="price_change_{$id_attribute}" style="display:none"></span><div class="{if !$group.group_layout}awp_tbla{else}awp_tbca{/if}">
												{/if}
												{math equation="x * y" x=$awp_pi y=$awp_currency_rate assign=converted}
                                                                                                
                                                                                                {if !$noTaxForThisProduct}
                                                                                                    {math equation="(x / 100) + 1" x=$taxRate assign=awpTax}
                                                                                                    {math equation="x * y" x=$awpTax y=$converted assign=converted}
                                                                                                
                                                                                                {/if}
                   								{if $awp_pi_display == ""}
		                   							&nbsp;
                   								{elseif $awp_pi > 0}
		                   							[{l s='Add' mod='attributewizardpro'} {awpConvertPriceWithCurrency price=$converted currency=$awp_currency}]
                   								{elseif $awp_pi < 0}
		                   							[{l s='Subtract' mod='attributewizardpro'} {awpConvertPriceWithCurrency price=$converted currency=$awp_currency}]
                   								{/if} 
	                     						</div>
											{/if}	
    		               				{/foreach}
                    				</div>
                                                        <script type="text/javascript">
               						{if !$group.group_layout && $group.group_height}
                                                            awp_sel_cont_var[{$id_attribute}] = '{$group.group_height}';
              						{/if}
                                                            awp_cell_cont_text_group[{$id_attribute}] = {$group.id_group};
               						
	               					</script>              						
                    			</div>
                                                            
                                                            {if $group_attribute.4 > 0}
                                                            <div class="attribute_extra_description">
                                                                <div>
                                                                    {$group_attribute.5}
                                                                </div>
                                                                <div>
                                                                    <a data-product-id="{$group_attribute.4}" data-product-attribute-id="{$group_attribute.6}" class="awpquickview" href="#" >
                                                                        {l s='More info' mod='attributewizardpro'}
                                                                    </a>
                                                                    
                                                                </div>
                                                            </div>
                                                        {/if}
                                                                        </div>
                    		</div>
                    	{/strip}
						{/foreach}
					{elseif $group.group_type == "quantity"}
						<script type="text/javascript">
							awp_is_quantity_group.push({$group.id_group});
						</script>
						<input type="hidden" id="awp_group_layout_{$group.id_group}" value="{$group.group_layout}" />
						<input type="hidden" id="awp_group_per_row_{$group.id_group}" value="{$group.group_per_row}" />
						{foreach from=$group.attributes name=awp_loop item=group_attribute}
						{strip}
						{assign var='id_attribute' value=$group_attribute.0}
                                                
                                                
	                   		<div id="awp_cell_cont_{$id_attribute}" class="awp_cell_cont awp_cell_cont_{$group.id_group}{if $smarty.foreach.awp_loop.iteration % $group.group_per_row == 1 || $group.group_per_row == 1} awp_clear{/if}">
                   				<div id="awp_cell_{$id_attribute}" {if $group.attributes_quantity.$id_attribute == 0 && $awp_allow_oosp != 1  && $awp_out_of_stock == 'hide'}class="awp_oos"{/if} style="{if $group.group_color != 1}{if isset($group.group_width) && $group.group_width > 0}width:{$group.group_width}px;{/if}{if isset($group.group_height)}height:{$group.group_height}px;{/if}{/if}" onclick="{if $group.group_color == 100}updateColorSelect({$id_attribute}){/if};">
									{if file_exists($col_img_dir|cat:$id_attribute|cat:'.jpg')}
										<div id="awp_tc_{$id_attribute}" class="awp_group_image{if !$group.group_layout} awp_left{/if}" style="{if isset($group.group_width) && $group.group_width > 0}width:{$group.group_width}px;{/if}{if isset($group.group_height) && $group.group_height > 0}height:{$group.group_height}px;{/if}">
											{if !$awp_popup}<a href="{$img_col_dir}{$id_attribute}.jpg" border="0" class="{if $awp_psv < 1.6}thickbox{else}fancybox shown{/if}">{/if}<img {if $group.group_resize}style="{if isset($group.group_width) && $group.group_width > 0}width:{$group.group_width}px;{/if}{if isset($group.group_height) && $group.group_height > 0}height:{$group.group_height}px;{/if}"{/if} src="{$img_col_dir}{$id_attribute}.jpg" alt="{$group_attribute.1|escape:'htmlall':'UTF-8'}" title="{$group_attribute.1|escape:'htmlall':'UTF-8'}" />{if !$awp_popup}</a>{/if}
										</div>
										{elseif $group_attribute.2 != ""}
										<div id="awp_tc_{$id_attribute}" class="awp_group_image{if !$group.group_layout} awp_left{/if}" style="{if isset($group.group_width)}width:{$group.group_width}px;{/if}{if isset($group.group_height)}height:{$group.group_height}px;{/if}background-color:{$group_attribute.2};">
											&nbsp;
										</div>	
										{/if}
                                                                                
                                                                                <div class="awp_left awp_full_text">
										<div id="awp_quantity_cell{$id_attribute}" class="awp_quantity_cell {if !$group.group_layout}awp_nila{else}awp_nica{/if}">
				            				{l s='Quantity' mod='attributewizardpro'}: <input type="text" {if $group.attributes_quantity.$id_attribute == 0 && $awp_allow_oosp != 1  && $awp_out_of_stock == 'disable'}disabled="disabled"{/if} class="awp_attribute_selected awp_qty_box" onchange="awp_add_to_cart_func()" alt="awp_group_{$group.id_group}" name="awp_group_{$group.id_group}_{$id_attribute}" id="awp_quantity_group_{$id_attribute}"  value="{if $id_attribute|in_array:$group.default && !$group.group_quantity_zero}1{else}0{/if}" />
										</div>
										<div id="awp_impact_cell{$id_attribute}" class="{if !$group.group_layout}awp_nila{else}awp_nica{/if}">
											{if isset($group.group_hide_name) && !$group.group_hide_name}
												<div class="qty_name_{$id_attribute} {if !$group.group_layout}awp_tbla{else}awp_tbca{/if}">{$group_attribute.1|escape:'htmlall':'UTF-8'}</div>
											{/if}
											{foreach from=$attributeImpacts key=id_attributeImpact item=attributeImpact}
											{strip}
												{if $id_attribute == $attributeImpact.id_attribute}
													{assign var='awp_pi' value=$attributeImpact.price}
			                      					<div class="{if !$group.group_layout}awp_tbla{else}awp_tbca{/if}"  id="price_change_{$id_attribute}">
    	                   								{if $awp_pi_display == ""}
	                    	   								&nbsp;
			                   							{elseif $awp_pi > 0}
            			       								[{l s='Add' mod='attributewizardpro'} {awpConvertPriceWithCurrency price=$awp_pi currency=$awp_currency}]
                   										{elseif $awp_pi < 0}
                   											[{l s='Subtract' mod='attributewizardpro'} {awpConvertPriceWithCurrency price=($awp_pi|abs) currency=$awp_currency}]
                   										{/if}
                     								</div>
												{/if}
											{/strip}
    		               					{/foreach}
                    					</div>
                                                        
                                                        {if $group_attribute.4 > 0}
                                                            <div class="clearfix"></div>
                                                            <div class="attribute_extra_description">
                                                                <div>
                                                                    {$group_attribute.5}
                                                                </div>
                                                                <div>
                                                                    <a data-product-id="{$group_attribute.4}" data-product-attribute-id="{$group_attribute.6}" class="awpquickview" href="#" >
                                                                        {l s='More info' mod='attributewizardpro'}
                                                                    </a>
                                                                    
                                                                </div>
                                                            </div>
                                                        {/if}
                                                                                </div>
               							{if !$group.group_layout && $group.group_height}
	                   						<script type="text/javascript">
                                                                            awp_sel_cont_var[{$id_attribute}] = '{$group.group_height}';
                   							</script>
              							{/if}
                    			</div>
                    		</div>
                    	{/strip}
						{/foreach}
					{elseif $group.group_type == "hidden"}
						<input type="hidden" id="awp_group_layout_{$group.id_group}" value="{$group.group_layout}" />
						<input type="hidden" id="awp_group_per_row_{$group.id_group}" value="{$group.group_per_row}" />
						{foreach from=$group.attributes name=awp_loop item=group_attribute}
						{strip}
						{assign var='id_attribute' value=$group_attribute.0}
							<input type="text" style="display:none;" class="awp_attribute_selected" name="awp_group_{$group.id_group}_{$id_attribute}" id="awp_quantity_group_{$id_attribute}"  value="1" />
                    	{/strip}
						{/foreach}
					{*elseif $group.group_type == "calculation"}
					<input type="hidden" id="awp_group_layout_{$group.id_group}" value="{$group.group_layout}" />
					<input type="hidden" id="awp_group_per_row_{$group.id_group}" value="{$group.group_per_row}" />
					<table cellpadding="6" border=0>
               		<tr style="height:20px">
					{foreach from=$group.attributes name=awp_loop item=group_attribute}
					{strip}
					{assign var='id_attribute' value=$group_attribute.0}
                   		<td align="center" {if $group.group_layout}valign="top"{/if}>
                   			<div id="awp_cell_{$id_attribute}" {if $group.attributes_quantity.$id_attribute == 0 && $awp_allow_oosp != 1  && $awp_out_of_stock == 'hide'}class="awp_oos"{/if} style="{if $group.group_color != 1}{if isset($group.group_width)}width:{$group.group_width}px;{/if}{if isset($group.group_height)}height:{$group.group_height}px;{/if}{/if}" onclick="{if $group.group_color == 100}updateColorSelect({$id_attribute}){/if};">
								{if file_exists($col_img_dir|cat:$id_attribute|cat:'.jpg')}
									{if $group.group_per_row > 1}<center>{/if}
									<div id="awp_tc_{$id_attribute}" class="awp_group_image{if !$group.group_layout} awp_left{/if}" style="{if isset($group.group_width)}width:{$group.group_width}px;{/if}{if isset($group.group_height)}height:{$group.group_height}px;{/if}">
										{if !$awp_popup}<a href="{$img_col_dir}{$id_attribute}.jpg" border="0" class="{if $awp_psv < 1.6}thickbox{else}fancybox shown{/if}">{/if}<img {if $group.group_resize}style="{if isset($group.group_width)}width:{$group.group_width}px;{/if}{if isset($group.group_height)}height:{$group.group_height}px;{/if}"{/if} src="{$img_col_dir}{$id_attribute}.jpg" alt="{$group_attribute.1|escape:'htmlall':'UTF-8'}" title="{$group_attribute.1|escape:'htmlall':'UTF-8'}" />{if !$awp_popup}</a>{/if}
									</div>
									{if $group.group_per_row > 1}</center>{/if}
									{elseif $group_attribute.2 != ""}
									{if $group.group_per_row > 1}<center>{/if}
									<div id="awp_tc_{$id_attribute}" class="awp_group_image{if !$group.group_layout} awp_left{/if}" style="{if isset($group.group_width)}width:{$group.group_width}px;{/if}{if isset($group.group_height)}height:{$group.group_height}px;{/if}background-color:{$group_attribute.2};">
										&nbsp;
									</div>
									{if $group.group_per_row > 1}</center>{/if}
									{/if}
									{if $group.group_per_row > 1}<center>{/if}
									<div id="awp_quantity_cell{$id_attribute}" style="{if !$group.group_layout}float:left;{else}width:100%;clear:left;{/if}">
                    					{if $group.group_per_row > 1}<center>{/if}
			            				{l s='Minimum' mod='attributewizardpro'}: {$group.group_calc_min}
			            				<input style="width:60px" type="text" default="{$group.group_calc_min}" {if $group.attributes_quantity.$id_attribute == 0 && $awp_allow_oosp != 1  && $awp_out_of_stock == 'disable'}disabled="disabled"{/if} class="awp_attribute_selected" onblur="if(parseFloat($(this).val()) < parseFloat({$group.group_calc_min})) {ldelim}$(this).val('{$group.group_calc_min}'){rdelim};if(parseFloat($(this).val()) > parseFloat({$group.group_calc_max})) {ldelim}$(this).val('{$group.group_calc_max}'){rdelim};" onkeyup="awp_select('{$group.id_group|intval}',{$group_attribute.0|intval}, {$awp_currency->id}, false);" name="awp_group_{$group.id_group}_{$id_attribute}" id="awp_calc_group_{$id_attribute}" value="{$group.group_calc_min}" />&nbsp;
			            				{l s='Maximum' mod='attributewizardpro'}: {$group.group_calc_max}
										{if $group.group_per_row > 1}</center>{/if}
									</div>
									<div id="awp_impact_cell{$id_attribute}" style="{if !$group.group_layout}float:left;text-align:center;{else}width:100%;clear: left;{/if}">
										{if $group.group_per_row > 1}<center>{/if}
										{if isset($group.group_hide_name) && !$group.group_hide_name}{$group_attribute.1|escape:'htmlall':'UTF-8'}{/if}
										{foreach from=$attributeImpacts key=id_attributeImpact item=attributeImpact}
										{strip}
										{if $id_attribute == $attributeImpact.id_attribute}
											{assign var='awp_pi' value=$attributeImpact.price}
			                      			<span id="price_change_{$id_attribute}" style="display:none">
                       						{if $awp_pi_display == ""}
                       							&nbsp;
                   							{elseif $awp_pi > 0}
                   								{if !$group.group_layout} {elseif $group.group_per_row > 1 && isset($group.group_hide_name) && !$group.group_hide_name}<br />{/if}[{l s='Add' mod='attributewizardpro'} {convertPriceWithCurrency price=$awp_pi currency=$awp_currency}]
                   							{elseif $awp_pi < 0}
                   								{if !$group.group_layout} {elseif $group.group_per_row > 1 && isset($group.group_hide_name) && !$group.group_hide_name}<br />{/if}[{l s='Subtract' mod='attributewizardpro'} {convertPrice price=$awp_pi|abs}]
                   							{/if}
                     						</span>
										{/if}
										{/strip}
    		               				{/foreach}
                    					{if $group.group_per_row > 1}</center>{/if}
                    				</div>
               						{if !$group.group_layout && $group.group_height}
                   						<script type="text/javascript">
                   						$("#awp_quantity_cell{$id_attribute}").css('margin-top',({$group.group_height}/2) - 8);
                   						$("#awp_impact_cell{$id_attribute}").css('margin-top',({$group.group_height}/2) - 8);
                   						</script>
              						{/if}
                    		</div>
                    	</td>
                    	{if $smarty.foreach.awp_loop.iteration < $group.attributes|@count && $smarty.foreach.awp_loop.iteration % $group.group_per_row == 0}
                    	</tr>
                    	<tr style="height:20px;">
                    	{/if}
                    {/strip}
					{/foreach}
           			</tr>
					</table>*}
					{/if}						
				{if $group.group_type != "hidden"}
					</div>
					<b class="xbottom"><b class="xb4 xbbot"></b><b class="xb3 xbbot"></b><b class="xb2 xbbot"></b><b class="xb1"></b></b>
				</div>
				{/if}
			{/strip}
			{/foreach}
			{if $awp_add_to_cart == "both" || $awp_add_to_cart == "bottom"}
				<div class="awp_stock_container awp_sct">
					<div class="awp_stock">
						&nbsp;&nbsp;<b class="price our_price_display" id="awp_price"></b>
					</div>
					<div class="awp_quantity_additional awp_stock">
						&nbsp;&nbsp;{l s='Quantity' mod='attributewizardpro'}: <input type="text" style="width:30px;padding:0;margin:0" id="awp_q2" onkeyup="$('#quantity_wanted').val(this.value);$('#awp_q2').val(this.value);" value="1" />
						<span class="awp_minimal_text"></span>
					</div>
					{if $awp_is_edit}
						<div class="awp_stock_btn">
							<input type="button" value="{l s='Edit' mod='attributewizardpro'}" class="exclusive awp_edit" onclick="{if ($awp_psv3 < 1.6)}$(this).attr('disabled', 'disabled'){else}$(this).prop('disabled', true){/if};awp_add_to_cart(true);$(this){if ($awp_psv3 < 1.6)}.attr{else}.prop{/if}('disabled', {if $awp_psv3 == '1.4.9' || $awp_psv3 == '1.4.10' || $awp_psv >= '1.5'}false{else}''{/if});" />&nbsp;&nbsp;&nbsp;&nbsp;
						</div>
					{/if}	
					<div class="awp_stock_btn box-info-product">
						{if $awp_psv >= 1.6}
								<button type="button" name="Submit" class="exclusive" onclick="$(this).prop('disabled', true);awp_add_to_cart();{if $awp_popup}awp_customize_func();{/if}$(this).prop('disabled', false);">
									<span>{l s='Add to cart' mod='attributewizardpro'}</span>
								</button>
						{else}
							<input type="button" value="{l s='Add to cart' mod='attributewizardpro'}" class="exclusive" onclick="{if ($awp_psv3 < 1.6)}$(this).attr('disabled', 'disabled'){else}$(this).prop('disabled', true){/if};awp_add_to_cart();{if $awp_popup}awp_customize_func();{/if}$(this){if ($awp_psv3 < 1.6)}.attr{else}.prop{/if}('disabled', {if $awp_psv3 == '1.4.9' || $awp_psv3 == '1.4.10' || $awp_psv >= '1.5'}false{else}''{/if});" />
						{/if}
					</div>
					{if $awp_popup}
						<div class="awp_stock_btn">
							<input type="button" value="{l s='Close' mod='attributewizardpro'}" class="button_small" onclick="$('#awp_container').fadeOut(1000);$('#awp_background').fadeOut(1000);awp_customize_func();" />
						</div>
					{/if}
					<div id="awp_in_stock" style="padding-top: 6px;"></div>
				</div>
			{/if}
			</form>
		</div>
		<b class="xbottom"><b class="xb4 xbbot"></b><b class="xb3 xbbot"></b><b class="xb2 xbbot"></b><b class="xb1"></b></b>
	</div>
</div>
<script type="text/javascript">
var awp_disable_url_hash = {$awp_disable_url_hash|intval};
var  awp_collapse_block = {$awp_collapse_block|intval};
var awp_popup_top = {$awp_popup_top|intval};
var awp_popup_width = {$awp_popup_width|intval};
var awp_opacity = {$awp_opacity|intval};
var awp_currency_chars = {$awp_currency_chars nofilter};

/* OLD PS 1.6 JS VARS */
var noTaxForThisProduct = {if $noTaxForThisProduct}{$noTaxForThisProduct|boolval}{else}false{/if};
var taxRate = {$taxRate|floatval};
var productBasePriceTaxIncl = {$productBasePriceTaxIncl|floatval};
var productPriceTaxExcluded = {$productPriceTaxExcluded|floatval};
var currencyRate = {$currencyRate|floatval};
var priceDisplayPrecision = {$priceDisplayPrecision|floatval};
var currencyFormat = '{$currencyFormat|escape:'htmlall':'UTF-8'}';
var currencySign = '{$currencySign|escape:'htmlall':'UTF-8'}';
var currencyBlank = '{$currencyBlank|escape:'htmlall':'UTF-8'}';
var roundMode = {$roundMode|intval};
var displayPrice = {$displayPrice|floatval};
var reduction_percent = {$reduction_percent|floatval};
var allowBuyWhenOutOfStock = {$allowBuyWhenOutOfStock|floatval};
{foreach from=$combinations key=k item=def}
    {if !empty($k) && is_string($k)}
        {if is_bool($def)}
            var {$k} = {$def|var_export:true nofilter};
        {elseif is_int($def)}
            var {$k} = {$def|intval};
        {elseif is_float($def)}
            var {$k} = {$def|floatval|replace:',':'.'};
        {elseif is_string($def)}
            var {$k} = '{$def|strval}';
        {elseif is_array($def) || is_object($def)}
            var {$k} = {$def|json_encode nofilter};
        {elseif is_null($def)}
            var {$k} = null;
        {else}
            var {$k} = '{$def|@addcslashes:'\''}';
        {/if}
    {/if}
{/foreach}
{foreach from=$product_specific_price key=k item=def}
    {if !empty($k) && is_string($k)}
        {if is_bool($def)}
            var {$k} = {$def|var_export:true nofilter};
        {elseif is_int($def)}
            var {$k} = {$def|intval};
        {elseif is_float($def)}
            var {$k} = {$def|floatval|replace:',':'.'};
        {elseif is_string($def)}
            var {$k} = '{$def|strval}';
        {elseif is_array($def) || is_object($def)}
            var {$k} = {$def|json_encode nofilter};
        {elseif is_null($def)}
            var {$k} = null;
        {else}
            var {$k} = '{$def|@addcslashes:'\''}';
        {/if}
    {/if}
{/foreach}
</script>
{if $isQuickView}
    <link rel="stylesheet" href="{$this_wizard_path}views/css/awp.css" type="text/css" media="all">
    <script type="text/javascript" src="{$this_wizard_path}views/js/awp_product.js"></script>
    <link rel="stylesheet" href="{$this_wizard_path}views/css/tooltipster.css" type="text/css" media="all">
    <script type="text/javascript" src="{$this_wizard_path}views/js/jquery.tooltipster.min.js"></script>
	<input type="hidden" name="awpQuickViewProductLink" id="awpQuickViewProductLink" value="{$quickViewProductLink}">
{/if}
<div class="modal fade" id="awpModal" tabindex="-1" role="dialog" aria-labelledby="awpModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        
      </div>
      <div class="modal-footer">
        
      </div>
    </div>
  </div>
</div>

{/if}
<!-- /MODULE AttributeWizardPro -->

