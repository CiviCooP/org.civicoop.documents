<?php
use CRM_Documents_ExtensionUtil as E;

class CRM_Documents_BAO_DocumentContact extends CRM_Documents_DAO_DocumentContact {

  /**
   * Create a new DocumentContact based on array-data
   *
   * @param array $params key-value pairs
   * @return CRM_Documents_DAO_DocumentContact|NULL
   *
  public static function create($params) {
    $className = 'CRM_Documents_DAO_DocumentContact';
    $entityName = 'DocumentContact';
    $hook = empty($params['id']) ? 'create' : 'edit';

    CRM_Utils_Hook::pre($hook, $entityName, CRM_Utils_Array::value('id', $params), $params);
    $instance = new $className();
    $instance->copyValues($params);
    $instance->save();
    CRM_Utils_Hook::post($hook, $entityName, $instance->id, $instance);

    return $instance;
  } */

}
