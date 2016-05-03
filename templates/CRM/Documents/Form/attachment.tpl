{*

*}
{if $form.attachFile_1 OR $currentAttachmentInfo}

    {capture assign=attachTitle}{ts}Attachment(s){/ts}{/capture}

    <div id="attachments">
      <table class="form-layout-compressed">
      {if $form.attachFile_1}
        <tr>
          <td class="label">{$form.attachFile_1.label}</td>
          <td>{$form.attachFile_1.html}&nbsp;<span class="crm-clear-link">(<a href="#" onclick="clearAttachment( '#attachFile_1', '#attachDesc_1' ); return false;">{ts}clear{/ts}</a>)</span><br />
            <span class="description">{ts}Browse to the <strong>file</strong> you want to upload.{/ts}{if $maxAttachments GT 1} {ts 1=$maxAttachments}You can have a maximum of %1 attachment(s).{/ts}{/if} Each file must be less than {$config->maxFileSize}M in size. You can also add a short description.</span>
          </td>
        </tr>
        {section name=attachLoop start=2 loop=$numAttachments+1}
          {assign var=index value=$smarty.section.attachLoop.index}
          {assign var=attachName value="attachFile_"|cat:$index}
          {assign var=attachDesc value="attachDesc_"|cat:$index}
            <tr class="attachment-fieldset"><td colspan="2"></td></tr>
            <tr>
                <td class="label">{$form.attachFile_1.label}</td>
                <td>{$form.$attachName.html}&nbsp;<span class="crm-clear-link">(<a href="#" onclick="clearAttachment( '#{$attachName}' ); return false;">{ts}clear{/ts}</a>)</span></td>
            </tr>
        {/section}
      {/if}
      {if $currentAttachmentInfo}
        
        <tr>
            <td class="label">{ts}Current Attachment{/ts}</td>
            <td class="view-value">
          {foreach from=$currentAttachmentInfo key=attKey item=attVal}
                <div id="attachStatusMesg" class="status hiddenElement"></div>
                <div id="attachFileRecord_{$attVal.fileID}">
                  <strong><a href="{$attVal.url}">{$attVal.cleanName}</a></strong>
                  {if $attVal.description}&nbsp;-&nbsp;{$attVal.description}{/if}
                  {if $attVal.deleteURLArgs && $showDelete}
                   <a href="#" onclick="showDeleteAttachment('{$attVal.cleanName}', '{$attVal.deleteURLArgs}', {$attVal.fileID}, '#attachStatusMesg', '#attachFileRecord_{$attVal.fileID}'); return false;" title="{ts}Delete this attachment{/ts}"><span class="icon red-icon delete-icon" style="margin:0px 0px -5px 20px" title="{ts}Delete this attachment{/ts}"></span></a>
                  {/if}
                </div>
          {/foreach}
            </td>
        </tr>
        {if $showDelete}
        <tr>
            <td class="label">&nbsp;</td>
            <td>{$form.is_delete_attachment.html}&nbsp;{$form.is_delete_attachment.label}<br />
                <span class="description">{ts}Click the red trash-can next to a file name to delete a specific attachment. If you want to delete ALL attachments, check the box above and click Save.{/ts}</span>
            </td>
        </tr>
        {/if}
      {/if}
      </table>
    </div>
  </div><!-- /.crm-accordion-body -->
  </div><!-- /.crm-accordion-wrapper -->
    {literal}
    <script type="text/javascript">
      function clearAttachment( element, desc ) {
        cj(element).val('');
        cj(desc).val('');
      }
    </script>
    {/literal}

{if $currentAttachmentInfo}
{include file="CRM/Form/attachmentjs.tpl"}
{/if}

{/if} {* top level if *}

