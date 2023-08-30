{**
 * Rolige PrestaShop Cleaner Extra
 *
 * @author    Rolige <www.rolige.com>
 * @copyright 2011-2019 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 *}

<div id="configure_content" class="clearfix">
    <div class="col-lg-2 configure-menu">
        {foreach from=$rg_pscleanerextra.menu.items item=group}
            <div class="list-group">
                {foreach from=$group item=item key=key}
                    <a
                        href="{$rg_pscleanerextra.menu.link|escape:'htmlall':'UTF-8'}&menu_active={$key|escape:'htmlall':'UTF-8'}"
                        class="list-group-item{if $key == $rg_pscleanerextra.menu.active} active{/if}"
                    >
                        <i class="{$item.icon|escape:'htmlall':'UTF-8'}"></i>
                        <span class="title">{$item.title|escape:'htmlall':'UTF-8'}</span>
                        {if $key == 'dashboard' && $rg_pscleanerextra.new_version}
                            <span class="badge badge-warning badge-pill">{l s='update' mod='rg_pscleanerextra'}</span>
                        {/if}
                    </a>
                {/foreach}
            </div>
        {/foreach}
        <div class="list-group">
            <span class="list-group-item">
                <i class="icon-info"></i>
                <span class="title">{l s='Version' mod='rg_pscleanerextra'} {$rg_pscleanerextra.version|escape:'htmlall':'UTF-8'}</span>
            </span>
        </div>
    </div>

    <div class="col-lg-10 configure-form">
        {$rg_pscleanerextra.form nofilter}
    </div>
</div>
