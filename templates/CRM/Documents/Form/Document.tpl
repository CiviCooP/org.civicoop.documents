{* HEADER *}

<div class="crm-block crm-form-block crm-document-form-block">

{if $action eq 8} {* Delete action. *}
  <table class="form-layout">
  <tr>
    <td colspan="2">
      <div class="status">{ts 1=$document->getSubject()}Are you sure you want to delete '%1'?{/ts}</div>
    </td>
  </tr>
  </table>
{else}

    <table class="form-layout">
        <tr>
            <td class="label">{ts}Contacts{/ts}</td>
            <td>{include file="CRM/Contact/Form/NewContact.tpl" noLabel=true skipBreak=true multiClient=true parent="document" showNewSelect=false}</td>
       </tr>

        {foreach from=$elementNames item=elementName}
          <tr>
            <td class="label">{$form.$elementName.label}</td>
            <td>{$form.$elementName.html}</td>
          </tr>
        {/foreach}

        <tr class="crm-activity-form-block-attachment">
          <td colspan="2">
          {include file="CRM/Documents/Form/attachment.tpl"}
          </td>
        </tr>
    </table>
{/if}

    {* FOOTER *}
    <div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="bottom"}
    </div>

</div>