<?php

/* 
 * Class which retrieves entity which could by linked to a document
 * e.g. a case or a project
 */

class CRM_Documents_Utils_EntityRef {
  
  protected static $_instance;
  
  protected $entity_refs;
  
  protected function __construct() {
    $this->loadEntityRefs();
  }
  
  public static function singleton() {
    if (!self::$_instance) {
      self::$_instance = new CRM_Documents_Utils_EntityRef();
    }
    return self::$_instance;
  }
  
  /**
   * Returns an array of CRM_Documents_Interface_EntityRefSpec
   */
  public function getRefs() {
    return $this->entity_refs;
  }
  
  public function getRefBySystemName($sytem_name) {
    foreach($this->entity_refs as $ref) {
      if ($ref->getSystemName() == $sytem_name) {
        return $ref;
      }
    }
    return false;
  }
  
  public function getRefByTableName($table_name) {
    foreach($this->entity_refs as $ref) {
      if ($ref->getEntityTableName() == $table_name) {
        return $ref;
      }
    }
    return false;
  }
  
  public function getRefByObjectName($objectName) {
    foreach($this->entity_refs as $ref) {
      if ($ref->getObjectName() == $objectName) {
        return $ref;
      }
    }
    return false;
  }
  
  protected function loadEntityRefs() {
    unset($this->entity_refs);
    $this->entity_refs = array();
    $hooks = CRM_Utils_Hook::singleton();
    $return = $hooks->invoke(0, CRM_Utils_Hook::$_nullObject, CRM_Utils_Hook::$_nullObject, CRM_Utils_Hook::$_nullObject, CRM_Utils_Hook::$_nullObject, CRM_Utils_Hook::$_nullObject, 'civicrm_documents_entity_ref_spec');
    if (is_array($return)) {
      foreach($return as $ref) {
        if ($ref instanceof CRM_Documents_Interface_EntityRefSpec) {
          $this->entity_refs[$ref->getSystemName()] = $ref;
        }
      }
    }
  }
}

