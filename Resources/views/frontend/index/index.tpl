{extends file="parent:frontend/index/index.tpl"}

{block name="frontend_index_header_javascript_jquery"}
    {$smarty.block.parent}

    {if !$GoogleIncludeInHead && $GoogleTrackingID}
        {include 'frontend/index/google_analytics.tpl'}
    {/if}
{/block}
