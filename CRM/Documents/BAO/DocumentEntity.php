<?php
use CRM_Documents_ExtensionUtil as E;

class CRM_Documents_BAO_DocumentEntity extends CRM_Documents_DAO_DocumentEntity {

  /**
   * Create a new DocumentEntity based on array-data
   *
   * @param array $params key-value pairs
   * @return CRM_Documents_DAO_DocumentEntity|NULL
   *
  public static function create($params) {
    $className = 'CRM_Documents_DAO_DocumentEntity';
    $entityName = 'DocumentEntity';
    $hook = empty($params['id']) ? 'create' : 'edit';

    CRM_Utils_Hook::pre($hook, $entityName, CRM_Utils_Array::value('id', $params), $params);
    $instance = new $className();
    $instance->copyValues($params);
    $instance->save();
    CRM_Utils_Hook::post($hook, $entityName, $instance->id, $instance);

    return $instance;
  } */

}
