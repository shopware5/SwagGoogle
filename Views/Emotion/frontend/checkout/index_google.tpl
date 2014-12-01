{block name='frontend_index_header_javascript' append}
    {if $GoogleTrackingID}
        {if $GoogleTrackingLibrary == 'ga'}
            {include file="SwagGoogle/analytics.tpl"}
        {else}
            {include file="SwagGoogle/ua.tpl"}
        {/if}
    {/if}
{/block}
