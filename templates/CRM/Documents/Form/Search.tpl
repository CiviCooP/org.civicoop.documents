{crmScope extensionKey='org.civicoop.documents'}

{* Search form and results for Documents *}
{assign var="showBlock" value="'searchForm'"}
{assign var="hideBlock" value="'searchForm_show'"}
<div class="crm-block crm-form-block crm-documents-search-form-block">
  <div class="crm-accordion-wrapper crm-documents_search_form-accordion {if $rows}collapsed{/if}">
      <div class="crm-accordion-header crm-master-accordion-header">
          {ts}Edit Search Criteria{/ts}
       </div><!-- /.crm-accordion-header -->
      <div class="crm-accordion-body">
        {strip}
          <table class="form-layout">
            <tr>
              <td class="font-size12pt" colspan="2">                    {$form.sort_name.label}&nbsp;&nbsp;{$form.sort_name.html|crmAddClass:'twenty'}&nbsp;&nbsp;&nbsp;{$form.buttons.html}
              </td>
            </tr>
            <tr>
              {if $form.contact_tags}
                <td><label>{ts}Document Tag(s){/ts}</label>
                    {$form.contact_tags.html}
                    {literal}
                    <script type="text/javascript">

                    cj("select#contact_tags").crmasmSelect({
                        addItemTarget: 'bottom',
                        animate: false,
                        highlight: true,
                        sortable: true,
                        respectParents: true
                    });
                    </script>
                    {/literal}
                </td>
              {else}
                <td>&nbsp;</td>
              {/if}

                <td>&nbsp;</td>
            </tr>
            <tr><td><label>{ts}Document Dates{/ts}</label></td></tr>
            <tr>
            {include file="CRM/Core/DateRange.tpl" fieldName="document_date" from='_low' to='_high'}
            </tr>
            <tr>
              <td>
                {$form.subject.label} <br />
                {$form.subject.html|crmAddClass:twenty}
              </td>
              <td>
                  &nbsp;
              </td>
            </tr>
            <tr>
              <td>
                  {$form.type_id.label} <br />
                  {$form.type_id.html}
              </td>
              <td>
                  {$form.status_id.label} <br />
                  {$form.status_id.html}
              </td>
            </tr>
            <tr>
               <td colspan="2">{$form.buttons.html}</td>
            </tr>
            </table>
        {/strip}
      </div><!-- /.crm-accordion-body -->
    </div><!-- /.crm-accordion-wrapper -->
</div><!-- /.crm-form-block -->
{if $rowsEmpty || $rows}
<div class="crm-content-block">
{if $rowsEmpty}
<div class="crm-results-block crm-results-block-empty">
    {include file="CRM/Documents/Form/Search/EmptyResults.tpl"}
</div>
{/if}

{if $rows}
    <div class="crm-results-block">
    {* Search request has returned 1 or more matching rows. *}
        {* This section handles form elements for action task select and submit *}
        <div class="crm-search-tasks crm-event-search-tasks">
            {include file="CRM/common/searchResultTasks.tpl" context="Documents"}
        </div>

        {* This section displays the rows along and includes the paging controls *}
  <div id="documentsSearch" class="crm-search-results">
        {include file="CRM/Documents/Form/Selector.tpl" context="Search"}
  </div>
    {* END Actions/Results section *}
    </div>
{/if}

</div>
{/if}
{literal}
<script type="text/javascript">
cj(function() {
   cj().crmAccordions();
});
</script>
{/literal}

{/crmScope}
