{extends file="parent:frontend/index/index.tpl"}

{block name="frontend_index_header_javascript_jquery"}
    {$smarty.block.parent}
    {block name="frontend_index_header_google_analytics"}
        {if !$GoogleIncludeInHead && $GoogleTrackingID}
            {if $GoogleOptOutCookie}
                {include file="SwagGoogle/optout.tpl"}
            {/if}
            {if $GoogleTrackingLibrary == 'ga'}
                {include file="SwagGoogle/analytics.tpl"}
            {else}
                {include file="SwagGoogle/ua.tpl"}
            {/if}
        {/if}
    {/block}
{/block}