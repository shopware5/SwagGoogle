{extends file="parent:frontend/checkout/finish.tpl"}

{block name='frontend_index_header_javascript_jquery'}
    {$smarty.block.parent}
    {if $sOrderNumber || $sTransactionumber}
        {include file="SwagGoogle/adwords.tpl"}
    {/if}
{/block}
