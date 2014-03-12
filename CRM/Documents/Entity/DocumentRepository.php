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
   * Returns a list with CRM_Documents_Entity_Document
   * 
   * When no documents are found an empty array is returns
   * 
   * @param int $caseId
   * @return array
   */
  public function getDocumentsByCaseId($caseId) {
    $documents = array();
    
    $dao = new CRM_Documents_DAO_Document();
    $sql = "SELECT DISTINCT `doc`.`id`, `doc`.* FROM `civicrm_document` `doc` INNER JOIN `civicrm_document_case` `doc_case` ON `doc`.`id` = `doc_case`.`document_id` WHERE `doc_case`.`case_id` = %1";
    $docsDao = $dao->executeQuery(
        $sql, array(
          '1' => array($caseId, 'Integer')
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
    
    //load versions
    $sql = "SELECT * FROM `civicrm_document_version` WHERE `document_id` = %1 ORDER BY `version` ASC";
    $versionDao = CRM_Core_DAO::executeQuery($sql, array(
        '1' => array($doc->getId(), 'Integer')
    ));
    
    while($versionDao->fetch()) {
      $version = new CRM_Documents_Entity_DocumentVersion($doc);
      $version->setId($versionDao->id);
      $version->setDescription($versionDao->description);
      $version->setDateUpdated(new DateTime($versionDao->date_updated));
      $version->setUpdatedBy($versionDao->updated_by);
      $version->setVersion($versionDao->version);
      
      //load only the first attachment because there should be only one attachment available
      $attachments = CRM_Core_BAO_File::getEntityFile('civicrm_document_version', $version->getId());
      if (!empty($attachments)) {
        $attachment = reset($attachments);
        $file = new CRM_Documents_Entity_DocumentFile();
        $file->setFromArray($attachment);
        $version->setAttachment($file);
      }
      
      $doc->addVersion($version);
    }
    
    $this->loadCases($doc);
  }
  
  /**
   * Loads case information for this document
   * 
   * @param CRM_Documents_Entity_Document $doc
   */
  protected function loadCases(CRM_Documents_Entity_Document $doc) {
    $sql = "SELECT * FROM `civicrm_document_case` WHERE `document_id` = %1";
    $caseDao = CRM_Core_DAO::executeQuery($sql, array(
        '1' => array($doc->getId(), 'Integer')
    ));
    
    while($caseDao->fetch()) {
      $doc->addCaseId($caseDao->case_id);
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
    
    //copy document into dao
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
    
    $this->persistContacts($document);
    
    //only persist the current version into the database 
    //because other versions are already persisted
    $this->persistCurrentVersion($document);
    
    //persist the linked cases
    $this->persistCases($document);

    //post hook, copy values into array and call post hook
    $params = array();
    CRM_Documents_DAO_Document::storeValues($dao, $params);
    CRM_Utils_Hook::post($op, 'Document', $dao->id, $params);
  }
  
  public function remove(CRM_Documents_Entity_Document $document) {    
    if ($document->getId()) {
      
      //pre hook
      $params = array();
      CRM_Utils_Hook::pre('delete', 'Document', $document->getId(), $params);
      
      //remove version
      $this->removeVersions($document);
    
      //remove document contacts
      $this->removeContacts($document);
      
      //remove cases
      $this->removeCases($document);
      
      $sql = "DELETE FROM `civicrm_document` WHERE `id` = %1";
      CRM_Core_DAO::executeQuery(
        $sql, array(
          '1' => array($document->getId(), 'Integer')
        )
      );
            
      //call post hook
      $params = array();
      CRM_Utils_Hook::post('delete', 'Document', $document->getId(), $params);
    }
  }
  
  protected function removeContacts(CRM_Documents_Entity_Document $document) {
    $sql = "DELETE FROM `civicrm_document_contact` WHERE `document_id` = %1";
    CRM_Core_DAO::executeQuery($sql, array(
        '1' => array($document->getId(), 'Integer')
    ));
  }
  
  protected function removeCases(CRM_Documents_Entity_Document $document) {
    $sql = "DELETE FROM `civicrm_document_case` WHERE `document_id` = %1";
    CRM_Core_DAO::executeQuery($sql, array(
        '1' => array($document->getId(), 'Integer')
    ));
  }
  
  protected function removeVersions(CRM_Documents_Entity_Document $document) {
    foreach($document->getVersions() as $version) {
      if ($version->getId()) {
        CRM_Core_BAO_File::deleteEntityFile('civicrm_document_version', $version->getId());
      }
    }
    
    if ($document->getId()) {
      $sql = "DELETE FROM `civicrm_document_version` WHERE `document_id` = %1";
      CRM_Core_DAO::executeQuery($sql, array(
        '1' => array($document->getId(), 'Integer')
      ));
    }
  }
  
  protected function persistCases(CRM_Documents_Entity_Document $document) {
    //update the document_contact table
    $sql = "DELETE FROM `civicrm_document_case` WHERE `document_id` = %1";
    CRM_Core_DAO::executeQuery($sql, array(
        '1' => array($document->getId(), 'Integer')
    ));
    
    $sql = "INSERT INTO `civicrm_document_case` (`document_id`, `case_id`) VALUES";
    
    foreach($document->getCaseIds() as $caseId) {
      $sql .= " ('".$document->getId()."', '".$caseId."')";
    }
    $sql .= ";";
    CRM_Core_DAO::executeQuery($sql);
  }
  
  protected function persistContacts(CRM_Documents_Entity_Document $document) {
    //update the document_contact table
    $sql = "DELETE FROM `civicrm_document_contact` WHERE `document_id` = %1";
    CRM_Core_DAO::executeQuery($sql, array(
        '1' => array($document->getId(), 'Integer')
    ));
    
    $sql = "INSERT INTO `civicrm_document_contact` (`document_id`, `contact_id`) VALUES";
    
    $values = "";
    foreach($document->getContactIds() as $contactId) {
      if (strlen($values)) {
        $values .= ", ";
      }
      $values .= " ('".$document->getId()."', '".$contactId."')";
    }
    $sql .= $values.";";
    CRM_Core_DAO::executeQuery($sql);
  }
  
  protected function persistCurrentVersion(CRM_Documents_Entity_Document $document) {
    $session = CRM_Core_Session::singleton();
    $version = $document->getCurrentVersion();
    $version->setUpdatedBy($session->get('userID'));
    $version->setDateUpdated(new DateTime());
    $vdao = new CRM_Documents_DAO_DocumentVersion();
    $vdao->id = $version->getId();
    $vdao->description = $version->getDescription();
    $vdao->version = $version->getVersion();
    $vdao->document_id = $document->getId();
    if ($version->getDateUpdated()) {
      $vdao->date_updated = $version->getDateUpdated()->format('Ymd');
    }
    if ($version->getUpdatedBy()) {
      $vdao->updated_by = $version->getUpdatedBy();
    }
    
    $vdao->save();
    
    $version->setId($vdao->id);
  }
  
}

