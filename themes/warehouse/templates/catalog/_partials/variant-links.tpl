<div class="variant-links">  
    {foreach from=$variants item=variant}
        {if $variant.html_color_code} 
            {if $variant.html_color_code !== ""}
                <a href="{$variant.id_attribute_group}"
                    class="{$variant.type}"
                    title="{$variant.name}"
                    aria-label="{$variant.name}"
                    style="background-color: {$variant.html_color_code}"
                ></a>
            {/if}
        {/if}
    {/foreach}
    <span class="js-count count"></span>
</div>