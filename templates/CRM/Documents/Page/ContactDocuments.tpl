{crmScope extensionKey='org.civicoop.documents'}

{if $permission EQ 'edit'}
  {capture assign=newDocumentURL}{crmURL p="civicrm/documents/document" q="reset=1&action=add&cid=`$contactId`"}{/capture}
  <div class="action-link">
    <a accesskey="N" href="{$newDocumentURL}" class="button">
      <span><i class="crm-i fa-plus-circle"></i> {ts}New document{/ts}</span>
    </a>
  </div>
{/if}

<table>
  <thead>
    <tr>
      <th class="ui-state-default">{ts}Subject{/ts}</th>
      <th class="ui-state-default">{ts}Contacts{/ts}</th>
      <th class="ui-state-default">{ts}Date added{/ts}</th>
      <th class="ui-state-default">{ts}Date modified{/ts}</th>
      <th class="ui-state-default">{ts}Modified by{/ts}</th>
      <th class="no-sort ui-state-default"></th>
    </tr>
   </thead>
  <tbody>
    {foreach from=$documents item=doc}
      <tr class="{cycle values="odd,even"}">
        <td><i class="crm-i {$doc->getIcon()}"></i> {$doc->getSubject()}</td>
        <td>{$doc->getFormattedContacts()}</td>
        <td>{$doc->getFormattedDateAdded()}</td>
        <td>{$doc->getFormattedDateUpdated()}</td>
        <td>{$doc->getFormattedUpdatedBy()}</td>
        <td>
          {include file=CRM/Documents/actionlinks.tpl}
        </td>
      </tr>
    {/foreach}
  </tbody>
</table>

{/crmScope}
