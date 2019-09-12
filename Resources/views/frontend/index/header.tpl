{extends file="parent:frontend/index/header.tpl"}

{block name="frontend_index_header_javascript_tracking"}
    {$smarty.block.parent}

    {if $GoogleIncludeInHead && $GoogleTrackingID}
        {include 'frontend/index/google_analytics.tpl'}
    {/if}
{/block}
