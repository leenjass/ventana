{**
 * Multishop Color Menu
 *
 * @author    Rolige <www.rolige.com>
 * @copyright 2011-2019 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 *}

<div class="dashboard-form">
    <div class="col-lg-12{if !$rg_multishopcolormenu.new_version} hidden{/if}">
        <div class="panel module-update">
            <p>
                <strong>{l s='A new version %s update is now available!' mod='rg_multishopcolormenu' sprintf=$rg_multishopcolormenu.new_version}</strong>
                <a href="{$rg_multishopcolormenu.module_link|escape:'htmlall':'UTF-8'}" class="btn btn-warning" target="_bank">
                    {l s='Download now' mod='rg_multishopcolormenu'}
                </a>
            </p>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="panel module-info">
            <p class="logo">
                <img src="{$rg_multishopcolormenu._path|escape:'htmlall':'UTF-8'}logo.png" />
            </p>
            <p class="title">
                {$rg_multishopcolormenu.displayName|escape:'htmlall':'UTF-8'}
            </p>
            <p class="description">
                {$rg_multishopcolormenu.description|escape:'htmlall':'UTF-8'}
            </p>
            <p class="reference">
                <a href="{$rg_multishopcolormenu.module_link|escape:'htmlall':'UTF-8'}" target="_bank">{l s='more info' mod='rg_multishopcolormenu'}</a>
            </p>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="panel partner-info">
            <p class="title">
                {l s='We are PrestaShop Partners!' mod='rg_multishopcolormenu'}
            </p>
            <p class="logo">
                <a class="rolige-logo" href="{$rg_multishopcolormenu.author_link|escape:'htmlall':'UTF-8'}" target="_bank">
                    <img class="img-responsive" src="https://www.rolige.com/img/logo.png" />
                </a>
                <a class="partner-logo" href="{$rg_multishopcolormenu.partner_link|escape:'htmlall':'UTF-8'}" target="_bank">
                    <img class="img-responsive" src="https://www.rolige.com/img/badge-preston-partner.png" />
                </a>
            </p>
        </div>
    </div>
    {if count($rg_multishopcolormenu.products_marketing)}
    <div class="col-lg-12">
        <div class="panel products-marketing">
            <div class="title">
                {l s='Other of our excellent and certified modules!' mod='rg_multishopcolormenu'}
            </div>
            <div class="products-marketing-list">
            {foreach $rg_multishopcolormenu.products_marketing as $prod}
                <div>
                    <div class="image">
                        <a href="{$prod.prod_url|escape:'htmlall':'UTF-8'}" target="_blank">
                            <img class="img-responsive" src="{$prod.img_url|escape:'htmlall':'UTF-8'}" alt="{$prod.name|escape:'htmlall':'UTF-8'}" />
                        </a>
                    </div>
                    <div class="info">
                        <div class="name">
                            <a href="{$prod.prod_url|escape:'htmlall':'UTF-8'}" target="_blank">
                                {$prod.name|escape:'htmlall':'UTF-8'}
                            </a>
                        </div>
                        <div class="price">
                            {if $rg_multishopcolormenu.source == 'rolige'}
                                {if $prod.price.base != $prod.special_price.base}
                                    <span class="discount-percentage">
                                        -{(100 - ($prod.special_price.base / $prod.price.base) * 100)|string_format:'%d'}%
                                    </span>
                                    <span class="base-price">{$prod.price.display|escape:'htmlall':'UTF-8'}</span>
                                    <span class="special-price">{$prod.special_price.display|escape:'htmlall':'UTF-8'}</span>
                                {else}
                                    <span class="special-price">{$prod.price.display|escape:'htmlall':'UTF-8'}</span>
                                {/if}
                            {else}
                                <span class="special-price">{$prod.price.display|escape:'htmlall':'UTF-8'}</span>
                            {/if}
                        </div>
                    </div>
                </div>
            {/foreach}
            </div>
        </div>
    </div>
    {/if}
</div>
