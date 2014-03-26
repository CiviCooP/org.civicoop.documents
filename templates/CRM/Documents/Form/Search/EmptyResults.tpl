{* No matches for submitted search request. *}
<div class="messages status no-popup">
    <div class="icon inform-icon"></div>  &nbsp;
        {if $qill}{ts}No matches found for:{/ts}
            {include file="CRM/common/displaySearchCriteria.tpl"}
        {else}
            {ts}No matching documents found.{/ts}
        {/if}
        <br />
        {ts}Suggestions:{/ts}
        <ul>
        <li>{ts}if you are searching by contact name, check your spelling{/ts}</li>
        <li>{ts}try a different spelling or use fewer letters{/ts}</li>
        <li>{ts}if you are searching within a date or amount range, try a wider range of values{/ts}</li>
        </ul>
</div>
