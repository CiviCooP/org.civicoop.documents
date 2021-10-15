{if $customDataType}
  <div id="customData_{$customDataType}"></div>
  {*include custom data js file*}
  {include file="CRM/Expenses/common/customData.tpl"}
  {assign var='cid' value=$cid|default:'false'}
  {literal}
  <script type="text/javascript">
    CRM.$(function($) {
      function updateCustomData{/literal}{$customDataType}{literal}() {
        var subType = '{/literal}{$expense_type_id}{literal}';
        if ($('#type_id').length) {
          subType = $('#type_id').val();
        }
        CRM.buildCustomData('{/literal}{$customDataType}{literal}', subType, false, false, false, false, false, {/literal}{$cid}{literal});
      }
      if ($('#type_id').length) {
        $('#type_id').on('change', updateCustomData{/literal}{$customDataType}{literal});
      }
      updateCustomData{/literal}{$customDataType}{literal}();
    });
  </script>
  {/literal}
{/if}
