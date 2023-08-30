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
{if $product.show_price}
    <div class="product-prices js-product-prices">
      {block name='product_discount'}
        {if $product.has_discount}
          <div class="product-discount">
            {hook h='displayProductPriceBlock' product=$product type="old_price"}
            <span class="regular-price">{$product.regular_price}</span>
          </div>
        {/if}
      {/block}
  
      {block name='product_price'}
        <div
          class="product-price h5 {if $product.has_discount}has-discount{/if}">
  
          <div class="current-price">
            <span class='current-price-value' content="{$product.price}">
                {$product.price}
            </span>
  
            {if $product.has_discount}
              {if $product.discount_type === 'percentage'}
                <span class="discount discount-percentage">{l s='Save %percentage%' mod='wkproductoptions' sprintf=['%percentage%' => $product.discount_percentage_absolute]}</span>
              {else}
                <span class="discount discount-amount">
                    {l s='Save %amount%' mod='wkproductoptions' sprintf=['%amount%' => $product.discount_to_display]}
                </span>
              {/if}
            {/if}
          </div>
        </div>
      {/block}
      {hook h='displayProductPriceBlock' product=$product type="weight" hook_origin='product_sheet'}
    </div>
{/if}