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
  
  public function __construct() {
    $this->setDefaults();
  }
  
  public function setFromArray($data) {
    if (isset($data['id'])) {
      $this->id = $data['id'];
    }
    
    if (isset($data['contact_ids'])) {
      $this->contactIds = explode(",", $data['contact_ids']);
    }
    
    if (isset($data['date_added'])) {
      $this->dateAdded = new DateTime($data['date_added']);
    }
    
    if (isset($data['added_by'])) {
      $this->addedBy = $data['added_by'];
    }
    
    if (isset($data['date_updated'])) {
      $this->dateUpdated = new DateTime($data['date_updated']);
    }
    
    if (isset($data['updated_by'])) {
      $this->addedBy = $data['updated_by'];
    }
    
    if (isset($data['subject'])) {
      $this->subject = $data['subject'];
    }
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
  
  public function getContactIds() {
    return $this->contactIds;
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
  
  
  
  public function getFormattedDateAdded() {
    return $this->formateDate($this->getDateAdded());
  }
  
  public function getFormattedDateUpdated() {
    return $this->formateDate($this->getDateUpdated());
  }
  
  public function getFormattedAddedBy($link=TRUE) {
    return $this->formatContact($this->getAddedBy(), $link);
  }
  
  public function getFormattedUpdatedBy($link=TRUE) {
    return $this->formatContact($this->getUpdatedBy(), $link);
  }
  
  private function formatContact($cid, $link=TRUE) {
    $return = '';
    if ($cid) {
      $display_name = CRM_Contact_BAO_Contact::displayName($cid);
      if ($link) {
        $return = '<a class="" href="' . CRM_Utils_System::url('civicrm/contact/view', 'reset=1&cid=' . $cid) . '" >'.$display_name.'</a>';
      } else {
        $return = $display_name;
      }
    }
    return $return;
  }
  
  private function formateDate($date) {
    $return = '';
    if ($date) {
      $config = CRM_Core_Config::singleton();
      $return = CRM_Utils_Date::customFormat($date->format('Y-m-d H:i:s'));
    }
    return $return;
  }
}
