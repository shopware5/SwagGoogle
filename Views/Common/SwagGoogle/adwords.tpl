{if $GoogleConversionID}
    {$sRealAmount=$sAmount|replace:",":"."}
    {if $sAmountWithTax}
        {assign var="sRealAmount" value=$sAmountWithTax|replace:",":"."}
    {else}
        {assign var="sRealAmount" value=$sAmount|replace:",":"."}
    {/if}
    <script type="text/javascript">
        var google_conversion_id = {$GoogleConversionID};
            google_conversion_language = "{$GoogleConversionLanguage}";
            google_conversion_format = "1";
            google_conversion_color = "FFFFFF";
            google_conversion_value = "{$sRealAmount}";
            google_conversion_label = "{$GoogleConversionLabel}";
            google_conversion_currency = "EUR";
            google_remarketing_only = false;
    </script>
    <script type="text/javascript" src="https://www.googleadservices.com/pagead/conversion.js">
    </script>
    <noscript>
        <img height=1 width=1 border=0 src="https://www.googleadservices.com/pagead/conversion/{$GoogleConversionID}/?value={$sRealAmount}&currency_code=EUR&label={$GoogleConversionLabel}&guid=ON&script=0">
    </noscript>
{/if}
