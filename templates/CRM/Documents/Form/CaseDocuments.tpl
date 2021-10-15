{crmScope extensionKey='org.civicoop.documents'}
  <table>
    <thead>
    <tr>
      <th class="ui-state-default">{ts}Subject{/ts}</th>
      <th class="ui-state-default">{ts}Contacts{/ts}</th>
      <th class="ui-state-default">{ts}Type{/ts}</th>
      <th class="ui-state-default">{ts}Status{/ts}</th>
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
        <td>{$doc->getFormattedTypeId()}</td>
        <td>{$doc->getFormattedStatusId()}</td>
        <td>{$doc->getFormattedDateUpdated()}</td>
        <td>{$doc->getFormattedUpdatedBy()}</td>
        <td>
            {include file=CRM/Documents/actionlinks.tpl contactId=$clientId}
        </td>
      </tr>
    {/foreach}
    </tbody>
  </table>
{/crmScope}
