<?php

/* 
 * This file contains the document repository
 * This class is used for saving and retrieving documents
 * 
 */

class CRM_Documents_Entity_DocumentRepository {
  
  /**
   * We only need one instance of this object. So we use the singleton
   * pattern and cache the instance in this variable
   *
   * @var object
   * @access private
   * @static
   */
  static private $_singleton = NULL;
  
  protected function __construct() {
    
  }
  
  /**
   * Constructor and getter for the singleton instance
   *
   * @return instance of $config->userHookClass
   */
  static function singleton($fresh = FALSE) {
    if (self::$_singleton == NULL || $fresh) {
      self::$_singleton = new CRM_Documents_Entity_DocumentRepository();
    }
    return self::$_singleton;
  }
  
  
  /**
   * Returns a list with CRM_Documents_Entity_Document
   * 
   * When no documents are found an empty array is returns
   * 
   * @param int $contactId
   * @return array
   */
  public function getDocumentsByContactId($contactId) {
    $documents = array();
    
    $dao = new CRM_Documents_DAO_Document();
    $sql = "SELECT DISTINCT `doc`.`id`, `doc`.*, `doc_contact`.* FROM `civicrm_document` `doc` INNER JOIN `civicrm_document_contact` `doc_contact` ON `doc`.`id` = `doc_contact`.`document_id` WHERE `doc`.`added_by` = %1 OR `doc`.`updated_by` = %1 OR `doc_contact`.`contact_id` = %1";
    $docsDao = $dao->executeQuery(
        $sql, array(
          '1' => array($contactId, 'Integer')
        )
    );
    while($docsDao->fetch()) {
      $doc = new CRM_Documents_Entity_Document();
      $this->loadDocByDao($doc, $docsDao);
      $documents[] = $doc;
    }
    
    return $documents;
  }
  
  /**
   * Load a Document entity object from DAO resultset
   * 
   * @param CRM_Documents_Entity_Document $doc
   * @param CRM_Core_DAO $dao
   */
  protected function loadDocByDao(CRM_Documents_Entity_Document $doc, CRM_Core_DAO $dao) {
    $doc->setId($dao->id);
    $doc->setAddedBy($dao->added_by);
    $doc->setSubject($dao->subject);
    $doc->setDateAdded(new DateTime($dao->date_added));
    if ($dao->updated_by) {
      $doc->setUpdatedBy($dao->updated_by);
    }
    if ($dao->date_updated) {
      $doc->setDateUpdated(new DateTime($dao->date_updated));
    }
  }
  
  /**
   * Returns a document
   * 
   * @param type $id
   * @return CRM_Documents_Entity_Document 
   * @throws CRM_Documents_Exception_NotFound when no document is found
   */
  public function getDocumentById($id) {
    $doc = new CRM_Documents_Entity_Document();
    
    //throw new CRM_Documents_Exception_NotFound("Document with id: ".$id." not found");
    return $doc;
  }
  
  public function persist(CRM_Documents_Entity_Document $document) {
    $dao = new CRM_Documents_DAO_Document();
    $dao->id = $document->getId();
    $dao->subject = $document->getSubject();
    if ($document->getDateAdded()) {
      $dao->date_added = $document->getDateAdded()->format('Y-m-d H:i:s');
    }
    if ($document->getAddedBy()) {
      $dao->added_by = $document->getAddedBy();
    }
    if ($document->getDateUpdated()) {
      $dao->date_updated = $document->getDateUpdated()->format('Y-m-d H:i:s');
    }
    if ($document->getUpdatedBy()) {
      $dao->updated_by = $document->getUpdatedBy();
    }
    
    $dao->save();
    $document->setId($dao->id);
    
    //update the document_contact table
    $sql = "DELETE FROM `civicrm_document_contact` WHERE `document_id` = %1";
    CRM_Core_DAO::executeQuery($sql, array(
        '1' => array($document->getId(), 'Integer')
    ));
    
    $sql = "INSERT INTO `civicrm_document_contact` (`document_id`, `contact_id`) VALUES";
    
    foreach($document->getContactIds() as $contactId) {
      $sql .= " ('".$document->getId()."', '".$contactId."')";
    }
    $sql .= ";";
    CRM_Core_DAO::executeQuery($sql);
  }
    
  
}

