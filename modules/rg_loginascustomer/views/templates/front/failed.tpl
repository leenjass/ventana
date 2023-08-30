{**
 * Login as Customer
 *
 *  @author    Rolige <www.rolige.com>
 *  @copyright 2011-2022 Rolige - All Rights Reserved
 *  @license   Proprietary and confidential
 *}

 {extends file='page.tpl'}

{block name='page_content'}
  <div class="alert alert-info">
    <p><strong>{l s='Login attempt failed.' mod='rg_loginascustomer'}</strong></p>
    <p>{l s='Please retry by using your admin panel.' mod='rg_loginascustomer'}</p>
  </div>
{/block}
