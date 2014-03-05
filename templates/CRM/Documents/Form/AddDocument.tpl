{* HEADER *}

<div class="crm-block crm-form-block crm-document-form-block">

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

    {* FOOTER *}
    <div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="bottom"}
    </div>

</div>