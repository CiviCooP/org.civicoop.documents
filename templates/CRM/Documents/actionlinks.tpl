{*
 *
 * This template file contains the links to download, upload en delete a document
 *
 * Variables to pass to this template are
 * - $contactId: the id of the contact (current user, or the current viewed contact)
 * - $doc: the Document
 *}

{assign var=document_id value=$doc->getId()}
{capture assign=delDocumentURL}{crmURL p="civicrm/documents/document" q="reset=1&action=delete&cid=`$contactId`&id=`$document_id`"}{/capture}
{assign var=attachment value=$doc->getAttachment()}

<a href="{$attachment->url}" title="{$attachment->cleanname}" class="action-item action-item-first">{ts}Download{/ts}</a>
<a href="#" class="action-item">{ts}Upload new version{/ts}</a>
<a href="{$delDocumentURL}" class="action-item">{ts}Delete{/ts}</a>