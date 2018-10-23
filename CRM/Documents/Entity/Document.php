<?php

/* 
 * This class holds all information for a document
 * 
 */

class CRM_Documents_Entity_Document {
  
  protected $id;
  
  /**
   *
   * @var array 
   */
  protected $contactIds = array();
  
  /**
   *
   * @var DateTime 
   */
  protected $dateAdded;
  
  /**
   *
   * @var int ContactId of the contact who added this document 
   */
  protected $addedBy;
  
  /**
   *
   * @var DateTime 
   */
  protected $dateUpdated;
  
  /**
   *
   * @var int ContactId of the contact who updated this document 
   */
  protected $updatedBy;
  
  /**
   *
   * @var String 
   */
  protected $subject;
  
  /**
   *
   * @var int 
   */
  protected $caseIds;
  
  /**
   *
   * 
   * @var array of CRM_Documents_Entity_DocumentEntity 
   */
  protected $entities;
  
  /**
   *
   * 
   * @var array of CRM_Documents_Entity_DocumentEntity 
   */
  protected $removedEntities;
  
  
  /**
   *
   * @var array of CRM_Documents_Entity_DocumentVersion 
   */
  protected $versions;
  
  public function __construct() {
    $this->setDefaults();
  }
  
  /**
   * Set default values for object
   */
  protected function setDefaults() {
    $session = CRM_Core_Session::singleton();
    unset($this->id);
    $this->contactIds = array();
    $this->dateAdded = new DateTime();
    $this->addedBy = $session->get('userID');
    unset($this->dateUpdated);
    unset($this->updatedBy);
    $this->subject = '';
    $this->caseIds = array();
    $this->versions = array();
    $this->entities = array();
    $this->removedEntities = array();
  }
  
  public function getId() {
    if (!empty($this->id)) {
      return $this->id;
    } else {
      return NULL;
    }
  }
  
  public function setId($id) {
    $this->id = (int) $id;
  }
  
  public function setContactIds($contact_ids) {
    if (is_array($contact_ids)) {
      $this->contactIds = $contact_ids;
    } else {
      $this->contactIds = explode(",".$contact_ids);
    }
  }
  
  public function addContactId($contact_id) {
    if (!in_array($contact_id, $this->contactIds)) {
      $this->contactIds[] = $contact_id;
    }
  }
  
  public function removeContactId($contact_id) {
    if(($key = array_search($contact_id, $this->contactIds)) !== false) {
      unset($this->contactIds[$key]);
    }
  }
  
  public function getContactIds() {
    return $this->contactIds;
  }
  
  public function getCaseIds() {
    return $this->caseIds;
  }
  
  public function setCaseIds($case_ids) {
    if (is_array($case_ids)) {
      $this->case_ids = $case_ids;
    } else {
      $this->case_ids = explode(",".$case_ids);
    }
  }
  
  public function addCaseId($caseId) {
    if (!in_array($caseId, $this->caseIds)) {
     $this->caseIds[] = $caseId; 
    }
  }
  
  public function removeCaseId($caseId) {
    if(($key = array_search($caseId, $this->caseIds)) !== false) {
      unset($this->caseIds[$key]);
    }
  }
  
  public function getCaseIdsFormatted() {
    $formatter = CRM_Documents_Utils_Formatter::singleton();
    $return = '';
    foreach($this->caseIds as $caseId) {
      if (strlen($return)) {
        $return .= ', ';
      }
      $return .= $formatter->formatCaseId($caseId);
    }
    return $return;
  }
  
  
  public function setAddedBy($addedBy) {
    $this->addedBy = $addedBy;
  }
  
  public function getAddedBy() {
    return $this->addedBy;
  }
  
  public function setDateAdded(DateTime $date) {
    $this->dateAdded = $date;
  }
  
  public function getDateAdded() {
    return $this->dateAdded;
  }
  
  public function getUpdatedBy() {
    if (isset($this->updatedBy)) {
      return $this->updatedBy;
    } else {
      return NULL;
    }
  }
  
  public function setUpdatedBy($updatedBy) {
    $this->updatedBy = $updatedBy;
  }
  
  public function getDateUpdated() {
    if (isset($this->dateUpdated)) {
      return $this->dateUpdated;
    } else {
      return NULL;
    }
  }
  
  public function setDateUpdated(DateTime $date) {
    $this->dateUpdated = $date;
  }
  
  public function getSubject() {
    return $this->subject;
  }
  
  public function setSubject($subject) {
    $this->subject = $subject;
  }
  
  public function getFormattedContacts() {
    $formatter = CRM_Documents_Utils_Formatter::singleton();
    $contacts = '';
    foreach($this->contactIds as $cid) {
      if (strlen($contacts)) {
        $contacts .= ', ';
      }
      $contacts .= $formatter->formatContact($cid);
    }
    return $contacts;
  }
  
  public function getFormattedDateAdded() {
    $formatter = CRM_Documents_Utils_Formatter::singleton();
    return $formatter->formateDate($this->getDateAdded());
  }
  
  public function getFormattedDateUpdated() {
    $formatter = CRM_Documents_Utils_Formatter::singleton();
    return $formatter->formateDate($this->getDateUpdated());
  }
  
  public function getFormattedAddedBy($link=TRUE) {
    $formatter = CRM_Documents_Utils_Formatter::singleton();
    return $formatter->formatContact($this->getAddedBy(), $link);
  }
  
  public function getFormattedUpdatedBy($link=TRUE) {
    $formatter = CRM_Documents_Utils_Formatter::singleton();
    return $formatter->formatContact($this->getUpdatedBy(), $link);
  }
  
  public function getEntities() {
    return $this->entities;
  }
  
  public function addNewEntity($entity_table, $entity_id) {
    $entity = new CRM_Documents_Entity_DocumentEntity($this);
    $entity->setEntityId($entity_id);
    $entity->setEntityTable($entity_table);
    $this->addEntity($entity); 
  }
  
  public function addEntity(CRM_Documents_Entity_DocumentEntity $entity) {
    foreach($this->entities as $e) {
      if ($e->getEntityTable() == $entity->getEntityTable() && $e->getEntityId() == $entity->getEntityId()) {
        return;
      }
    }
    foreach($this->removedEntities as $e) {
      if ($e->getEntityTable() == $entity->getEntityTable() && $e->getEntityId() == $entity->getEntityId()) {
        $this->entities[] = $e;
        return;
      }
    }
    $this->entities[] = $entity;
  }
  
  public function removeEntity(CRM_Documents_Entity_DocumentEntity $entity) {
    foreach($this->entities as $key => $e) {
      if ($e->getEntityTable() == $entity->getEntityTable() && $e->getEntityId() == $entity->getEntityId()) {
        $this->removedEntities[] = $e;
        unset($this->entities[$key]);
      }
    }
  }
  
  public function getRemovedEntities() {
    return $this->removedEntities;
  }
  
  public function getVersions() {
    ksort($this->versions);
    return $this->versions;
  }

  public function getVersionByVersionId($versionId) {
    foreach($this->versions as $version) {
      if ($version->getId() == $versionId) {
        return $version;
      }
    }
    return false;
  }
  
  public function addVersion(CRM_Documents_Entity_DocumentVersion $version) {
    $vid = $version->getVersion();
    if ((!$vid) || isset($this->versions[$vid])) {
      Throw new Exception("Invalid version");
    }
    $this->versions[$vid] = $version;
  }
  
  public function getCurrentVersion() {
    $lastVersion = false;
    if (ksort($this->versions)) {
      $lastVersion = end($this->versions);
    }

    if ($lastVersion === false) {
     $lastVersion = $this->addNewVersion(); 
    }
    return $lastVersion;
  }
  
  public function addNewVersion() {
    $version = new CRM_Documents_Entity_DocumentVersion($this);
    $vid = 1;
    if (ksort($this->versions)) {
      $lastVersion = end($this->versions);
      if ($lastVersion) {
        $vid = $lastVersion->getVersion();
        $vid ++;
      }
    }
    $version->setVersion($vid);
    $this->versions[$vid] = $version;
    return $version; 
  }

  public function getIcon() {
    return CRM_Utils_File::getIconFromMimeType($this->getCurrentVersion()->getAttachment()->mime_type);
  }
  
}
