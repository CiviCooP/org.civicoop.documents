{*
 *
 * This template file contains the links to download, upload en delete a document
 *
 * Variables to pass to this template are
 * - $contactId: the id of the contact (current user, or the current viewed contact)
 * - $doc: the Document
 *}


{assign var=document_id value=$doc->getId()}
{assign var=version value=$doc->getCurrentVersion()}
{capture assign=editDocumentURL}{crmURL p="civicrm/documents/document" q="reset=1&action=add&cid=`$contactId`&id=`$document_id`"}{/capture}
{capture assign=delDocumentURL}{crmURL p="civicrm/documents/document" q="reset=1&action=delete&cid=`$contactId`&id=`$document_id`"}{/capture}
{assign var=attachment value=$version->getAttachment()}

{assign var=first value="action-item-first"}
{if $attachment && $attachment->url}
    <a href="{$attachment->url}" title="{$attachment->cleanname}" class="action-item {$first}">{ts}Download{/ts}</a>
    {assign var=first value=""}
{/if}
<a href="{$editDocumentURL}" class="action-item {$first}">{ts}Edit{/ts}</a>
<a href="{$delDocumentURL}" class="action-item">{ts}Delete{/ts}</a>