{extends file="parent:frontend/index/index.tpl"}

{block name="frontend_index_header_javascript_jquery"}
    {$smarty.block.parent}
    {if !$GoogleIncludeInHead && $GoogleTrackingID}
        {if $GoogleOptOutCookie}
            {include file="frontend/swag_google/optout.tpl"}
        {/if}
        {if $GoogleTrackingLibrary == 'ga'}
            {include file="frontend/swag_google/analytics.tpl"}
        {else}
            {include file="frontend/swag_google/ua.tpl"}
        {/if}
    {/if}
{/block}
