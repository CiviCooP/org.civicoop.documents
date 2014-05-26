<?php

/* 
 * class which holds information to linked entities from a document
 */

class CRM_Documents_Entity_DocumentEntity {
  
  /**
   *
   * @var int 
   */
  protected $id;
  
  /**
   *
   * @var CRM_Documents_Entity_Document  
   */
  protected $document;
  
  /**
   *
   * @var string 
   */
  protected $entity_table;
  
  /**
   *
   * @var int 
   */
  protected $entity_id;
  
  public function __construct(CRM_Documents_Entity_Document $document) {
    $this->document = $document;
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
  
  public function setEntityTable($entity_table) {
    $this->entity_table = $entity_table;
  }
  
  public function getEntityTable() {
    return $this->entity_table;
  }
  
  public function getEntityId() {
    return $this->entity_id;
  }
  
  public function setEntityId($entity_id) {
    $this->entity_id = $entity_id;
  }
  
}

