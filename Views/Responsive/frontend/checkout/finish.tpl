{extends file="parent:frontend/checkout/finish.tpl"}

{block name='frontend_index_header_javascript_tracking' append}
    {if $sOrderNumber || $sTransactionumber}
        {include file="SwagGoogle/adwords.tpl"}
    {/if}
{/block}
