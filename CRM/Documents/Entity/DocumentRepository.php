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
    $sql = "SELECT DISTINCT `doc`.`id`, `doc`.* FROM `civicrm_document` `doc` INNER JOIN `civicrm_document_contact` `doc_contact` ON `doc`.`id` = `doc_contact`.`document_id` WHERE `doc`.`added_by` = %1 OR `doc`.`updated_by` = %1 OR `doc_contact`.`contact_id` = %1";
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
    
    $this->loadAdditionalDocData($doc);
  }
  
  /**
   * Load additional data for the documents, such as the linked contacts and the linked file
   * 
   * @param CRM_Documents_Entity_Document $doc
   */
  public function loadAdditionalDocData(CRM_Documents_Entity_Document $doc) {
    //load contact ID's
    $sql = "SELECT * FROM `civicrm_document_contact` WHERE `document_id` = %1";
    $contactDao = CRM_Core_DAO::executeQuery($sql, array(
        '1' => array($doc->getId(), 'Integer')
    ));
    
    while($contactDao->fetch()) {
      $doc->addContactId($contactDao->contact_id);
    }
    
    //load only the first attachment because there should be only one attachment available
    $attachments = CRM_Core_BAO_File::getEntityFile('civicrm_document', $doc->getId());
    if (!empty($attachments)) {
      $attachment = reset($attachments);
      $file = new CRM_Documents_Entity_DocumentFile();
      $file->setFromArray($attachment);
      $doc->setAttachment($file);
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
    
    $dao = new CRM_Documents_DAO_Document();
    $sql = "SELECT * FROM `civicrm_document` `doc`  WHERE `doc`.`id` = %1";
    $docsDao = $dao->executeQuery(
        $sql, array(
          '1' => array($id, 'Integer')
        )
    );
    if($docsDao->fetch()) {
      $doc = new CRM_Documents_Entity_Document();
      $this->loadDocByDao($doc, $docsDao);
    } else {
      //document not found
      throw new CRM_Documents_Exception_NotFound("Document with id: ".$id." not found");
    }
    
    return $doc;
  }
  
  public function persist(CRM_Documents_Entity_Document $document) {
    $session = CRM_Core_Session::singleton();
    $dao = new CRM_Documents_DAO_Document();
    
    //set the new updated information, if it is a new document
    if ($document->getId()) {
      $document->setUpdatedBy($session->get('userID'));
      $document->setDateUpdated(new DateTime());
    } else {
      $document->setAddedBy($session->get('userID'));
      $document->setDateAdded(new DateTime());
    }
    
    $dao->id = $document->getId();
    $dao->subject = $document->getSubject();
    if ($document->getDateAdded()) {
      $dao->date_added = $document->getDateAdded()->format('Ymd');
    }
    if ($document->getAddedBy()) {
      $dao->added_by = $document->getAddedBy();
    }
    if ($document->getDateUpdated()) {
      $dao->date_updated = $document->getDateUpdated()->format('Ymd');
    }
    if ($document->getUpdatedBy()) {
      $dao->updated_by = $document->getUpdatedBy();
    }
    
    //prepare for hook
    $op = 'create';
    if ($dao->id) {
      $op = 'edit';
    }
    
    //pre hook: copy values into array
    $params = array();
    CRM_Documents_DAO_Document::storeValues($dao, $params);    
    //call pre hook
    CRM_Utils_Hook::pre($op, 'Document', $dao->id, $params);
    //pre hook: copy array back to dao
    $dao->copyValues($params);
    
    //do the actuall save
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
    
    //post hook, copy values into array and call post hook
    $params = array();
    CRM_Documents_DAO_Document::storeValues($dao, $params);
    CRM_Utils_Hook::post($op, 'Document', $dao->id, $params);
  }
  
  public function remove(CRM_Documents_Entity_Document $document) {
    //remove attachments
    CRM_Core_BAO_File::deleteEntityFile('civicrm_document', $document->getId());

    //remove document
    $sql = "DELETE FROM `civicrm_document_contact` WHERE `document_id` = %1";
    CRM_Core_DAO::executeQuery($sql, array(
        '1' => array($document->getId(), 'Integer')
    ));
    
    
    $dao = new CRM_Documents_DAO_Document();
    $sql = "SELECT * FROM `civicrm_document` WHERE `id` = %1";
    $docsDao = $dao->executeQuery(
        $sql, array(
          '1' => array($document->getId(), 'Integer')
        )
    );
    if ($docsDao->fetch()) {
      //pre hook: copy values into array
      $params = array();
      //CRM_Documents_DAO_Document::storeValues($docsDao, $params);    
      //call pre hook
      CRM_Utils_Hook::pre('delete', 'Document', $docsDao->id, $params);
      //pre hook: copy array back to dao
      //$docsDao->copyValues($params);
      
      $sql = "DELETE FROM `civicrm_document` WHERE `id` = %1";
      CRM_Core_DAO::executeQuery(
        $sql, array(
          '1' => array($document->getId(), 'Integer')
        )
      );
            
      //call post hook
      $params = array();
      //CRM_Documents_DAO_Document::storeValues($docsDao, $params);
      CRM_Utils_Hook::post('delete', 'Document', $docsDao->id, $params);
    }
  }
    
  
}

