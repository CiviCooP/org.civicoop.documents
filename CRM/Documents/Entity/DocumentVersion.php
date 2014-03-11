<?php

/* 
 * This class holds information about the different document versions
 * 
 */

class CRM_Documents_Entity_DocumentVersion {
  
  /**
   *
   * @var int 
   */
  protected $id;
  
  /**
   *
   * @var int the version number
   */
  protected $version;
  
  /**
   *
   * @var String description of the new version
   */
  protected $description;
  
  /**
   *
   * @var DateTime date updated 
   */
  protected $dateUpdated;
  
  /**
   *
   * @var int updatedBy 
   */
  protected $updatedBy;
  
  /**
   *
   * @var CRM_Documents_Entity_DocumentFile the attached file 
   */
  protected $attachment;
  
  /**
   *
   * @var CRM_Documents_Entity_Document  
   */
  protected $document;
  
  public function __construct(CRM_Documents_Entity_Document $document) {
    $this->document = $document;
    $this->attachment = new CRM_Documents_Entity_DocumentFile();
    $this->dateUpdated = new DateTime();
  }
  
  public function getDocument() {
    return $this->document;
  }
  
  public function setId($id) {
    $this->id = $id;
  }
  
  public function getId() {
    return $this->id;
  }
  
  public function setDescription($description) {
    $this->description = $description;
  }
  
  public function getDescription() {
    return $this->description;
  }
  
  public function setDateUpdated(DateTime $date) {
    $this->dateUpdated = $date;
  }
  
  public function getDateUpdated() {
    return $this->dateUpdated;
  }
  
  public function getUpdatedBy() {
    return $this->updatedBy;
  }
  
  public function setUpdatedBy($updatedBy) {
    $this->updatedBy = $updatedBy;
  }
  
  public function setVersion($version) {
    $this->version = $version;
  }
  
  public function getVersion() {
    return $this->version;
  }
  
  public function setAttachment(CRM_Documents_Entity_DocumentFile $file) {
    $this->attachment = $file;
  }
  
  public function getAttachment() {
    return $this->attachment;
  }
  
  public function getFormattedUpdatedBy($link=TRUE) {
    $formatter = CRM_Documents_Utils_Formatter::singleton();
    return $formatter->formatContact($this->getUpdatedBy(), $link);
  }
  
  public function getFormattedDateUpdated() {
    $formatter = CRM_Documents_Utils_Formatter::singleton();
    return $formatter->formateDate($this->getDateUpdated());
  }
}