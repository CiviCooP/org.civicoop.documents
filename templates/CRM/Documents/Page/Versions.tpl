{crmScope extensionKey='org.civicoop.documents'}

{assign var=document_id value=$document->getId()}
<div class="action-link">
{if $permission EQ 'edit'}
    {capture assign=newDocumentURL}{crmURL p="civicrm/documents/newversion" q="reset=1&action=add&cid=`$contactId`&id=`$document_id`"}{/capture}
    <a accesskey="N" href="{$newDocumentURL}" class="button">
        <span><i class="crm-i fa-plus-circle"></i> {ts}Upload new version{/ts}</span>
    </a>
{/if}
{if $goBackUrl}
    <a class="button cancel" href="{$goBackUrl}">{ts}Go back{/ts}</a>
{/if}
</div>

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
                        <span><a href="{$attachment->url}" title="{$attachment->cleanname}" class="{$first}">{ts}Download{/ts}</a></span>
                        {assign var=first value=""}
                    {/if}
                </td>
            </tr>
        {/foreach}
    </tbody>
</table>

{/crmScope}
