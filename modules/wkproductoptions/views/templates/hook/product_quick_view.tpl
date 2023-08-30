{*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License version 3.0
* that is bundled with this package in the file LICENSE.txt
* It is also available through the world-wide-web at this URL:
* https://opensource.org/licenses/AFL-3.0
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your
* needs please refer to CustomizationPolicy.txt file inside our module for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
*}

{extends file=$extendFilePath}

{if isset($allow_customization) && $allow_customization == 0}
  {block name='product_variants'}
      {include file='catalog/_partials/product-variants.tpl'}
      {*Start- custom hook for product options*}
      <div class="wk_custom_variant">
      {hook h='displayAfterProductVariant' product=$product}
      </div>
      {*End- custom hook for product options*}
  {/block}
  {block name='product_prices'}
    <div class="wk_price_option">
      {include file='catalog/_partials/product-prices.tpl'}
    </div>
  {/block}
  {block name='product_customization'}
    {* {include file="catalog/_partials/product-customization.tpl" customizations=$product.customizations} *}
  {/block}
  {if $extendFilePath == 'catalog/_partials/quickview.tpl'}
    {block name='product_add_to_cart'}
        {$smarty.block.parent}
      <div class="wk_ajax-error" style="display:none;">
        <div class="alert alert-danger"></div>
      </div>
    {/block}
  {/if}
{/if}