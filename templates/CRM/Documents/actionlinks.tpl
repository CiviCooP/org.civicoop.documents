{*
 *
 * This template file contains the links to download, upload en delete a document
 *
 * Variables to pass to this template are
 * - $contactId: the id of the contact (current user, or the current viewed contact)
 * - $entity: optional 
 * - $entity_id: optional
 * - $doc: the Document
 *}

{if (!isset($entity))}
    {assign var=entity value=''}
{/if}
{if (!isset($entity_id))}
    {assign var=entity_id value=''}
{/if}

{assign var=document_id value=$doc->getId()}
{assign var=version value=$doc->getCurrentVersion()}
{capture assign=newVersionUrl}{crmURL p="civicrm/documents/newversion" q="reset=1&action=add&cid=`$contactId`&id=`$document_id`"}{/capture}
{capture assign=editDocumentURL}{crmURL p="civicrm/documents/document" q="reset=1&action=add&cid=`$contactId`&id=`$document_id`&entity=`$entity`&entity_id=`$entity_id`"}{/capture}
{capture assign=viewVersionsURL}{crmURL p="civicrm/documents/versions" q="reset=1&cid=`$contactId`&id=`$document_id`"}{/capture}
{capture assign=delDocumentURL}{crmURL p="civicrm/documents/document" q="reset=1&action=delete&cid=`$contactId`&id=`$document_id`&entity=`$entity`&entity_id=`$entity_id`"}{/capture}
{assign var=attachment value=$version->getAttachment()}

{assign var=first value="action-item-first"}
{if $attachment && $attachment->url}
    <span><a href="{$attachment->url}" title="{$attachment->cleanname}" class="action-item {$first}">{ts}Download{/ts}</a></span>
    {assign var=first value=""}
{/if}
<span class="btn-slide">More
<ul class="panel">
<li><a href="{$newVersionUrl}" class="action-item">{ts}Upload new version{/ts}</a></li>
<li><a href="{$editDocumentURL}" class="action-item">{ts}Edit{/ts}</a></li>
<li><a href="{$viewVersionsURL}" class="action-item">{ts}View versions{/ts}</a></li>
<li><a href="{$delDocumentURL}" class="action-item">{ts}Delete{/ts}</a></li>
</ul>
</span>