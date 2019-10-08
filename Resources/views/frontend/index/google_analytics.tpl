{block name="google_analytics_data"}
    <script>
        window.basketData = {
            {if $sBasket.content && $sOrderNumber}
                hasData:true,
                data: [
                    {foreach from=$sBasket.content item=sBasketItem}
                        {if !$sBasketItem.modus}
                            {
                                id: '{$sOrderNumber}',
                                name: '{$sBasketItem.articlename|escape:'javascript'}',
                                sku: '{$sBasketItem.ordernumber}',
                                price: '{$sBasketItem.priceNumeric|round:2}',
                                quantity: '{$sBasketItem.quantity}'
                            },
                        {/if}
                    {/foreach}
                ],
            {else}
                hasData: false
            {/if}
        };
    </script>
{/block}

{block name="google_analytics_amount"}
    {if $sAmountWithTax}
        {assign var="sRealAmount" value=$sAmountWithTax|replace:",":"."}
    {else}
        {assign var="sRealAmount" value=$sAmount|replace:",":"."}
    {/if}
{/block}

{block name="google_analytics_opt_out"}
    {if $GoogleOptOutCookie}
        {include 'frontend/swag_google/optout.tpl'}
    {/if}
{/block}

{block name="google_analytics_container"}
    <div data-googleAnalytics="true"
        {block name="google_analytics_data-attributes"}
            data-googleConversionID="{$GoogleConversionID}"
            data-googleConversionLabel="{$GoogleConversionLabel}"
            data-googleConversionLanguage="{$GoogleConversionLanguage}"
            data-googleTrackingID="{$GoogleTrackingID}"
            data-googleAnonymizeIp="{$GoogleAnonymizeIp}"
            data-googleOptOutCookie="{$GoogleOptOutCookie}"
            data-googleTrackingLibrary="{$GoogleTrackingLibrary}"
            data-realAmount="{$sRealAmount}"
            data-showCookieNote="{config name="show_cookie_note"}"
            data-cookieNoteMode="{config name="cookie_note_mode"}"
            {if $sBasket.content && $sOrderNumber}
                {if $sAmountNet}
                    {assign var="sAmountNumeric" value=$sAmountNet|replace:",":"."}
                {else}
                    {assign var="sAmountNumeric" value=$sAmount|replace:",":"."}
                {/if}
                data-createEcommerceTransaction="1"
                data-orderNumber="{$sOrderNumber}"
                data-affiliation="{$sShopname|escape:'javascript'}"
                data-revenue="{$sAmountNumeric|round:2}"
                data-tax="{$sAmountTax|round:2}"
                data-shipping="{$sShippingcosts|replace:',':'.'|round:2}"
                data-currency="{$sBasket.sCurrencyName}"
                data-city="{$sUserData.billingaddress.city|escape}"
                data-country="{$sUserData.additional.country.countryen|escape}"
            {/if}
        {/block}
    >
    </div>
{/block}


