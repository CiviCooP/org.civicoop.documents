{*
 * Template file to display documents on a case
 *
 *}

{crmScope extensionKey='org.civicoop.documents'}

  <div id="case-documents" class="crm-accordion-wrapper collapsed">
    <div class="crm-accordion-header">{ts}Documents{/ts}</div>
    <div class="crm-accordion-body">
      <table class="form-layout">
        <tbody>
        <tr>
          <td>
              {ts}Type{/ts}<br>
            <select id="document_type_id" class="huge crm-select2 crm-form-multiselect" multiple="multiple">
                {foreach from=$document_types item=label key=value}
                  <option value="{$value}">{$label}</option>
                {/foreach}
            </select>
          </td>
          <td>
              {ts}Status{/ts}<br>
            <select id="document_status_id" class="huge crm-select2 crm-form-multiselect" multiple="multiple">
                {foreach from=$document_statuses item=label key=value}
                  <option value="{$value}">{$label}</option>
                {/foreach}
            </select>
          </td>
        </tr>
        </tbody>
      </table>
      <div id="case-documents-body"></div>

        {if $permission EQ 'edit'}
          {capture assign=newDocumentURL}{crmURL p="civicrm/documents/document" q="reset=1&action=add&cid=`$clientId`&context=case&case_id=`$caseId`"}{/capture}
          <div class="action-link">
            <a accesskey="N" href="{$newDocumentURL}" class="button">
              <span><i class="crm-i fa-plus-circle"></i> {ts}New document{/ts}</span>
            </a>
          </div>
        {/if}
    </div>
  </div>


  <script type="text/javascript">
      {literal}
      cj(function() {

        var caseDocuments = cj('#case-documents').detach();
        cj('#case_custom_edit').after(caseDocuments);

        function loadDocuments() {
          var formParams = {
            reset: 1,
            case_id: {/literal}{$caseId}{literal}
          };
          if (cj('#document_type_id').val()) {
            formParams.type_id = cj('#document_type_id').val().join(',');
          }
          if (cj('#document_status_id').val()) {
            formParams.status_id = cj('#document_status_id').val().join(',');
          }
          var formUrl = CRM.url('civicrm/contact/view/casedocument', formParams);
          var form = CRM.loadPage(formUrl, {
            "target": '#case-documents-body',
            dialog: false
          });
        }

        cj('#document_type_id').on('change', loadDocuments);
        cj('#document_status_id').on('change', loadDocuments);

        loadDocuments();
      });

      {/literal}
  </script>

{/crmScope}
