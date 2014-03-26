{if $context EQ 'Search'}
    {include file="CRM/common/pager.tpl" location="top"}
{/if}

{strip}
<table class="selector">
  <thead class="sticky">
  <tr>
    <th scope="col" title="Select Rows">{$form.toggleSelect.html}</th>
    {foreach from=$columnHeaders item=header}
        <th scope="col">
        {if $header.sort}
          {assign var='key' value=$header.sort}
          {$sort->_response.$key.link}
        {else}
          {$header.name}
        {/if}
        </th>
    {/foreach}
    <th></th>
  </tr>
  </thead>

  {counter start=0 skip=1 print=false}
  {foreach from=$rows item=row}
    <tr id="rowid{$row.document_id}" class="{cycle values="odd-row,even-row"} crm-documents_{$row.doc_id}">
        {assign var=cbName value=$row.checkbox}
        <td>{$form.$cbName.html}</td>
        {foreach from=$columnHeaders item=header}
            {assign var=fName value=$header.field}
            <td>{$row.$fName}</td>
        {/foreach}
        <td>
            {include file=CRM/Documents/actionlinks.tpl contactId=$row.contact_id doc=$row.doc}
        </td>
    </tr>
  {/foreach}
</table>
{/strip}

{if $context EQ 'Search'}
 <script type="text/javascript">
 {* this function is called to change the color of selected row(s) *}
    var fname = "{$form.formName}";
    on_load_init_checkboxes(fname);
 </script>
{/if}

{if $context EQ 'Search'}
    {include file="CRM/common/pager.tpl" location="bottom"}
{/if}
