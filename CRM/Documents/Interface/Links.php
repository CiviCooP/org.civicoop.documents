<?php

/* 
 * Interface for linking documents to other entities
 * 
 */

interface CRM_Documents_Interface_EntityTable {
  
  /**
   * Returns the entity table name, e.g. civicrm_case
   * 
   * @return string
   */
  public function getEntityTableName();
  
  /**
   * Returns the name of the BAO/DAO of this entity
   * E.g. CRM_Case_BAO_Case 
   * 
   * Return the dao when the bao doesn't exist
   * 
   * @return string
   */
  public function getBAO();
  
  /**
   * Returns the human name of the entity
   * e.g. 'Case' 
   *
   * @return string
   */
  public function getHumanName();
  
  /**
   * Returns a list with all active entities
   * e.g. the active cases
   * 
   * The return value is an array with the entity id as the key and the name/label of the entity as the value
   * 
   * @return array
   */
  public function getActiveEntities();
  
  /**
   * Returns the label of a given entity
   * E.g. for a case this might be 'Housingsupport: case subject'
   * 
   * @return string
   */
  public function getEntityLabelByEntityId($entity_id);
}

