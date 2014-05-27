# Document storage in CiviCRM

## Functionality

* Store documents on the contact cart
* A document can be linked to more than one contact
* Version management with a document
* Custom search to find documents

## Technical background

There is an entity **CRM_Documents_Entity_Document** which contains 
all the information for a document. E.g. the linked contact ID's. 
Every document contains one or more **CRM_Documents_Entity_DocumentVersion** 
for a version of the document. A Document version contains a link to the file
which is a **civicrm_entity_file** item.

## Hooks

See [doc/hooks.ms](available hooks) for the documentation of the hooks in this extension

## Roadmap

### Next beta release

* Search document by case type
* Store the user context upon search (e.g. for going back on a edit form)
* Removing document on a merge of duplicate contacts

### Future (dreaming)

* Add file type icons (such as pdf/doc etc...)
* Add tagging to to a document
* Search with tags
* Add hooks for linking documents to custum entities (e.g. campaigns)
* Add a connection with ownCloud for interacting with documents
* Add functionality to work together on a document with the webODF functionality





