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

<div class="panel po_main_content" id="import_settings">

    <div class="panel_header">
        <div class="panel_title">{l s='Attribute and Combination Information' mod='attributewizardpro'}</div>
        <div class="panel_info_text important">
            <span class="important_alert"> </span>
            {l s='*Please read this information carefully, attributes are added differently than in previous versions!* ' mod='attributewizardpro'}
        </div>
        <div class="clear"></div>
    </div>
    <div class="special_instructions single_column">
        <div class="special_instructions_header">
            {l s='There are several ways to structure combinations using Attribute Wizard Pro (AWP). Three distinct approaches are explained below, including when and why you would use each.! ' mod='attributewizardpro'}
        </div>
        <div class="special_instructions_content">
            <ul>
               
            </ul>
        </div>
    </div>
    <div class="extra_line spacing_bottom"></div>


    <div class="special_instructions single_column">
        <div class="special_instructions_header">
            {l s='Structure #1 - Original AWP Combination Structure' mod='attributewizardpro'}
        </div>
        <div class="special_instructions_content">
            <ul>
                <li>
                    <span>{l s='The \'original\' AWP combination structure is the most basic way to use as many attributes per product as needed. This is accomplished by changing the way combinations are structured. Unlike the default PrestaShop structure which uses connected attributes, where each combination contains 1 attribute from each group, in the AWP structure, each combination contains 1 or more attributes from a single group. This structure requires far fewer total combinations. But it also allows for each attribute from each group to be selected on the front end of your site. ' mod='attributewizardpro'}</span>
                </li>
                <li>
                    <span>{l s='Use this structure when you need to offer a large number of attributes per product, and the price is always the same for each attribute selection regardless of what the user selects in other attribute groups. Each attribute can have its own price impact in this structure. ' mod='attributewizardpro'}</span>
                </li>
                <li>
                    <span>{l s='This means you can set a price impact for the attribute value "red" for example, and a price impact for the attribute value "small", but because of the way the module creates combinations, technically each combination contains an attribute from only 1 group, which means you cannot have a specific price for a combination that "red" + "small". ' mod='attributewizardpro'}</span>
                </li>
                <li>
                    <span>{l s='Because each combination has one or more attributes from only 1 group, all attribute values in that group can be in one combination if they all have the same stock and/or price impact. If each attribute has a unique stock or price impact you would add only 1 attribute per combination. ' mod='attributewizardpro'}</span>
                </li>
                <li>
                    <span>{l s='Finally, to define the attributes that will be pre-selected on the front end, you must' mod='attributewizardpro'} <u>{l s='create a new combination' mod='attributewizardpro'}</u> {l s='with one attribute from each group (In addition to those attributes being in their own combination) ' mod='attributewizardpro'}</span>
                </li>
                <li>
                    <span>{l s='Example of' mod='attributewizardpro'}:</span>
                    <a style="color:blue;text-decoration:underline" href="{$path}views/img/structure1.jpg" target="_blank">
                        {l s='Structure #1' mod='attributewizardpro'}
                    </a>
                    <br/>
                    <a style="color:blue;text-decoration:underline" href="http://demo.presto-changeo.com/awp2/home/14-81-macbook.html" target="_blank">
                        {l s='Front End Product' mod='attributewizardpro'}
                    </a>
                </li>

            </ul>
        </div>
    </div>

    <div class="extra_line spacing_bottom"></div>


    <div class="special_instructions single_column">
        <div class="special_instructions_header">
            {l s='Structure #2 - PrestaShop\'s Default Combination Structure. ' mod='attributewizardpro'}
        </div>
        <div class="special_instructions_content">
            <ul>

                <li>
                    <span>{l s='You can use many of AWP\'s popular features without changing your attribute and combination structure. Keep in mind that you are still limited by the number of total combinations that PrestaShop supports by default. ' mod='attributewizardpro'}</span>
                </li>
                <li>
                    <span>{l s='Attribute Inputs: With AWP enabled, you can use textbox, textarea, file upload, radio button, checkbox or quantity textbox for attributes while maintaining the existing combination structure. ' mod='attributewizardpro'}</span>
                </li>
                <li>
                    <span>{l s='Unavailable Combinations: With AWP enabled you can disable / hide unavailable combinations instead of using PrestaShop\'s default functionality, which displays unavailable combinations and gives the user an alert that the product is not available in that combination. Not an ideal user experience. ' mod='attributewizardpro'}</span>
                </li>
                <li>
                    <span>{l s='This means that you can enable AWP before or after creating your combinations with the default combination generator. ' mod='attributewizardpro'}</span>
                </li>
                <li>
                    <span>{l s='After you create all the combinations using the default PrestaShop combination structure, simply delete those that contain attribute combinations which do not exist. ' mod='attributewizardpro'}</span>
                </li>
                <li>
                    <span>{l s='Keep in mind that despite using AWP in this structure, If you need a large number of attributes, you are still limited by the total number of combinations that PrestaShop can handle. ' mod='attributewizardpro'}</span>
                </li>

                <li>
                    <span>{l s='Example of' mod='attributewizardpro'}:</span>
                    <a style="color:blue;text-decoration:underline" href="{$path}views/img/structure2.jpg" target="_blank">
                        {l s='Structure #2' mod='attributewizardpro'}
                    </a>
                    <br/>
                    <a style="color:blue;text-decoration:underline" href="http://demo.presto-changeo.com/awp2/home/9-56-default-ps-combinations.html#/1-size-s/11-color-black" target="_blank">
                        {l s='Front End Product' mod='attributewizardpro'}
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="extra_line spacing_bottom"></div>


    <div class="special_instructions single_column">
        <div class="special_instructions_header">
            {l s='Structure #3 - Connected Attributes with AWP. ' mod='attributewizardpro'}
        </div>
        <div class="special_instructions_content">
            <ul>

                <li>
                    <span>{l s='Use this structure when you need to create connected attributes but you have too many total combinations to use the default PrestaShop combination structure. Using AWP, you can connect a limited number of attributes while managing a large number of total combinations. ' mod='attributewizardpro'}</span>
                </li>
                <li>
                    <span>{l s='With connected attributes, the user\'s attribute selection in one attribute group can impact the options available in another attribute group on the front-end, a feature that can be useful in many situations. See our explanation and examples below. ' mod='attributewizardpro'}</span>
                </li>
                <li>
                    <span>{l s='The instructions below correspond to the Example Structure #3 image ' mod='attributewizardpro'}</span>
                </li>
                <li>
                    <div class="extra_line spacing_bottom"></div>
                    <span>{l s='First, non-Connected Attributes: (optional)' mod='attributewizardpro'}</span>
                    <br/><br/><br/>
                    <div class="extra_line spacing_bottom"></div>
                    <span>{l s='In a product requiring connected attributes, sometimes only some of the attribute groups are dependent on (ie. connected to) one another, meaning other attribute are not connected. When this is true, the attributes that are available in all combinations (ie. not connected to other attributes) can be entered separately as combinations, with all attribute values from each attribute group being added to a single combination (see the "color" & "license plate" attributes in the screenshot below.) ' mod='attributewizardpro'}</span>
                    <br/><br/><br/>
                    <div class="extra_line spacing_bottom"></div>
                </li>
                <li>
                    <div class="extra_line spacing_bottom"></div>
                    <span>{l s='Second, The Connected Attributes: '}</span>
                    <br/><br/><br/>
                    <div class="extra_line spacing_bottom"></div>
                    <span>{l s='Attributes which are connected to other attributes need to be added to combinations one by one using a structure more similar to the default PrestaShop combination structure. To create these combinations, add each attribute value to each connected combinations (See the "make" "model" & "year" attributes in the screenshot below.) ' mod='attributewizardpro'}</span>
                    <br/><br/><br/>
                    <div class="extra_line spacing_bottom"></div>
                </li>

                <li>
                    <span>{l s='You can add one or more attributes from each group to a combination, which reduces the number of combinations you need to have in total. That reduction, together with what you save by adding non-connected attributes to separate combinations as explained above, means AWP can continue to support a large number of total attributes while also supporting connected attributes. ' mod='attributewizardpro'}</span>
                </li>
                <li>
                    <span>{l s='Example of' mod='attributewizardpro'}:</span>
                    <a style="color:blue;text-decoration:underline" href="{$path}views/img/structure3.jpg" target="_blank">
                        {l s='Structure #3' mod='attributewizardpro'}
                    </a>
                    <br/>
                    <a style="color:blue;text-decoration:underline" href="http://demo.presto-changeo.com/awp2/home/11-79-car-selection.html#/11-color-black/29-year-2003/31-make-bmw/37-model-b200" target="_blank">
                        {l s='Front End Product' mod='attributewizardpro'}
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="extra_line spacing_bottom"></div>

    <div class="special_instructions single_column">
        <div class="special_instructions_header">
            {l s='Disable or Hide Unavailable / Out of Stock. ' mod='attributewizardpro'}
            <br/>
            {l s='(When using connected attributes)' mod='attributewizardpro'}
        </div>
        <div class="special_instructions_content">
            <ul>

                <li>
                    <span>{l s='When using the Unavailable / Out of Stock "disable" or "hide" options, you may need to set some attribute groups to "Do Not Hide" ' mod='attributewizardpro'}</span>
                </li>
               
                <li>
                    <span>{l s='Example of' mod='attributewizardpro'}:</span>
                    <a style="color:blue;text-decoration:underline" href="{$path}views/img/donothide.jpg" target="_blank">
                        {l s='Do Not Hide' mod='attributewizardpro'}
                    </a>
                    <br/>
                    <a style="color:blue;text-decoration:underline" href="http://demo.presto-changeo.com/awp2/home/11-79-car-selection.html#/11-color-black/29-year-2003/31-make-bmw/37-model-b200" target="_blank">
                        {l s='Front End Product' mod='attributewizardpro'}
                    </a>
                </li>
            </ul>
        </div>
    </div>

</div>
