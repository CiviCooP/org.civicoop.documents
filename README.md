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

## Roadmap

### Beta2

* Link documents to cases

### Future (dreaming)

* Add hooks for linking documents to custum entities (e.g. campaigns)
* Add a connection with ownCloud for interacting with documents
* Add functionality to work together on a document with the webODF functionality





