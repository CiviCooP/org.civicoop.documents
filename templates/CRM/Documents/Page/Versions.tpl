{assign var=document_id value=$document->getId()}
{if $permission EQ 'edit'}
    {capture assign=newDocumentURL}{crmURL p="civicrm/documents/newversion" q="reset=1&action=add&cid=`$contactId`&id=`$document_id`"}{/capture}
    <div class="action-link">
        <a accesskey="N" href="{$newDocumentURL}" class="button">
            <span><div class="icon add-icon"></div>{ts}Upload new version{/ts}</span>
        </a>
    </div>

{/if}

<table>
    <thead>
        <tr>
            <th class="ui-state-default">{ts}Number{/ts}</th>
            <th class="ui-state-default">{ts}Description{/ts}</th>
            <th class="ui-state-default">{ts}Date added{/ts}</th>
            <th class="ui-state-default">{ts}Added by{/ts}</th>
            <th class="no-sort ui-state-default"></th>
        </tr>
     </thead>
     <tbody>
        
        {foreach from=$versions item=version}
            {assign var=attachment value=$version->getAttachment()}
            <tr class="{cycle values="odd,even"}">
                <td>{$version->getVersion()}</td>
                <td>{$version->getDescription()}</td>
                <td>{$version->getFormattedDateUpdated()}</td>
                <td>{$version->getFormattedUpdatedBy()}</td>
                <td>
                    {assign var=first value="action-item-first"}
                    {if $attachment && $attachment->url}
                        <span><a href="{$attachment->url}" title="{$attachment->cleanname}" class="action-item {$first}">{ts}Download{/ts}</a></span>
                        {assign var=first value=""}
                    {/if}
                </td>
            </tr>
        {/foreach}
    </tbody>
</table>