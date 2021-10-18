{crmScope extensionKey='org.civicoop.documents'}

{if !$snippet}
<table class="form-layout">
  <tbody>
  <tr>
    <td>
        {ts}Type{/ts}<br>
      <select id="document_type_id" class="huge crm-select2 crm-form-multiselect" multiple="multiple">
          {foreach from=$document_types item=label key=value}
            <option value="{$value}" {if (in_array($value, $selected_document_types))}selected="selected"{/if}>{$label}</option>
          {/foreach}
      </select>
    </td>
    <td>
        {ts}Status{/ts}<br>
      <select id="document_status_id" class="huge crm-select2 crm-form-multiselect" multiple="multiple">
          {foreach from=$document_statuses item=label key=value}
            <option value="{$value}" {if (in_array($value, $selected_document_status))}selected="selected"{/if}>{$label}</option>
          {/foreach}
      </select>
    </td>
  </tr>
  </tbody>
</table>

{if $permission EQ 'edit'}
  {capture assign=newDocumentURL}{crmURL p="civicrm/documents/document" q="reset=1&action=add&cid=`$contactId`"}{/capture}
  <div class="action-link">
    <a accesskey="N" href="{$newDocumentURL}" class="button">
      <span><i class="crm-i fa-plus-circle"></i> {ts}New document{/ts}</span>
    </a>
  </div>
{/if}
{/if}

<div id="contact-documents-body">
<table>
  <thead>
    <tr>
      <th class="ui-state-default">{ts}Subject{/ts}</th>
      <th class="ui-state-default">{ts}Contacts{/ts}</th>
      <th class="ui-state-default">{ts}Type{/ts}</th>
      <th class="ui-state-default">{ts}Status{/ts}</th>
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
        <td>{$doc->getFormattedTypeId()}</td>
        <td>{$doc->getFormattedStatusId()}</td>
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
</div>

{if !$snippet}
<script type="text/javascript">
{literal}
cj(function() {
  function loadDocuments() {
    var formParams = {
      reset: 1,
      'cid': '{/literal}{$contactId}{literal}',
      'filter': 1
    };
    if (cj('#document_type_id').val()) {
      formParams.type_id = cj('#document_type_id').val().join(',');
    }
    if (cj('#document_status_id').val()) {
      formParams.status_id = cj('#document_status_id').val().join(',');
    }
    var formUrl = CRM.url('civicrm/contact/view/documents', formParams);
    var form = CRM.loadPage(formUrl, {
      "target": '#contact-documents-body',
      dialog: false
    });
  }

  cj('#document_type_id').on('change', loadDocuments);
  cj('#document_status_id').on('change', loadDocuments);
});

{/literal}
</script>
{/if}
{/crmScope}
