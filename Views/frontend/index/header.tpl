{extends file="parent:frontend/index/header.tpl"}

{block name="frontend_index_header_javascript_tracking"}
    {$smarty.block.parent}
    {if $GoogleIncludeInHead && $GoogleTrackingID}
        {if $GoogleTrackingLibrary == 'ga'}
            {include file="SwagGoogle/analytics.tpl"}
        {else}
            {include file="SwagGoogle/ua.tpl"}
        {/if}
    {/if}
{/block}