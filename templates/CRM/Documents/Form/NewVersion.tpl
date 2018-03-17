{* HEADER *}

{crmScope extensionKey='org.civicoop.documents'}

<div class="crm-block crm-form-block crm-document-form-block">

    <table class="form-layout">
        <tr>
            <td class="label">{ts}Document{/ts}</td>
            <td>{$document->getSubject()}</td>
       </tr>

        {foreach from=$elementNames item=elementName}
          <tr>
            <td class="label">{$form.$elementName.label}</td>
            <td>{$form.$elementName.html}</td>
          </tr>
        {/foreach}

        <tr class="crm-activity-form-block-attachment">
          <td colspan="2">
          {include file="CRM/Documents/Form/attachment.tpl" showDelete=0}
          </td>
        </tr>
    </table>

    {* FOOTER *}
    <div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="bottom"}
    </div>

</div>

{/crmScope}
