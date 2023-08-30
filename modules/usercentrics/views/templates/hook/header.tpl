{**
 * usercentrics
 *
 * @author    silbersaiten <info@silbersaiten.de>
 * @copyright 2022 silbersaiten
 * @license   See joined file licence.txt
 * @category  Module
 * @support   silbersaiten <support@silbersaiten.de>
 * @version   1.0.12
 * @link      http://www.silbersaiten.de
*}

{* test
    <script id="usercentrics-cmp" data-settings-id="{$setting_id|escape:'htmlall':'UTF-8'}" src="//storage.googleapis.com/usercentrics-integration-test-browser-ui/develop/bundle.js" defer></script>
*}
{if $enable_sdp == 1}
<meta data-privacy-proxy-server="//privacy-proxy-server.usercentrics.eu">
<script type="application/javascript" src="//privacy-proxy.usercentrics.eu/latest/uc-block.bundle.js"></script>
{/if}
<script id="usercentrics-cmp" data-settings-id="{$setting_id|escape:'htmlall':'UTF-8'}" src="//app.usercentrics.eu/browser-ui/latest/bundle.js" defer></script>
