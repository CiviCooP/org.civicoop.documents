<?php

/* 
 * This class converts an array to a document and loads additional data
 */

class CRM_Documents_Entity_ArrayToDocumentsConvertor {
  
  /**
   * Converts an array to a document
   * 
   * @param array $data
   * @return CRM_Documents_Entity_Document
   */
  public static function convert($data) {
    $repo = CRM_Documents_Entity_DocumentRepository::singleton();
    $doc = new CRM_Documents_Entity_Document();
    
    if (isset($data['id'])) {
      $doc->setId($data['id']);
    }
    
    if (isset($data['contact_ids'])) {
      $doc->setContactIds($data['contact_ids']);
    }
    
    if (isset($data['date_added'])) {
      $doc->setDateAdded(new DateTime($data['date_added']));
    }
    
    if (isset($data['added_by'])) {
      $doc->setAddedBy($data['added_by']);
    }
    
    if (isset($data['date_updated'])) {
      $doc->setDateUpdated(new DateTime($data['date_updated']));
    }
    
    if (isset($data['updated_by'])) {
      $doc->setUpdatedBy($data['updated_by']);
    }
    
    if (isset($data['subject'])) {
      $doc->setSubject($data['subject']);
    }
    
    $repo->loadAdditionalDocData($doc);
    
    return $doc;
  }
  
}

