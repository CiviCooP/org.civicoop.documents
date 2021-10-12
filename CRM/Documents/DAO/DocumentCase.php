<?php

use CRM_Documents_ExtensionUtil as E;


Class CRM_Documents_DAO_DocumentCase extends CRM_Core_DAO {

  /**
   * static instance to hold the field values
   *
   * @var array
   * @static
   */
  static $_fields = null;

  /**
   * static value to see if we should log any modifications to
   * this table in the civicrm_log table
   *
   * @var boolean
   * @static
   */
  static $_log = false;

  /**
   * empty definition for virtual function
   */
  static function getTableName() {
    return 'civicrm_document_case';
  }

  /**
   * Returns localized title of this entity.
   */
  public static function getEntityTitle() {
    return E::ts('Document Case');
  }

  /**
   * Returns foreign keys and entity references.
   *
   * @return array
   *   [CRM_Core_Reference_Interface]
   */
  public static function getReferenceColumns() {
    if (!isset(Civi::$statics[__CLASS__]['links'])) {
      Civi::$statics[__CLASS__]['links'] = static::createReferenceColumns(__CLASS__);
      Civi::$statics[__CLASS__]['links'][] = new CRM_Core_Reference_Basic(self::getTableName(), 'case_id', 'civicrm_case', 'id');
      Civi::$statics[__CLASS__]['links'][] = new CRM_Core_Reference_Basic(self::getTableName(), 'document_id', 'civicrm_document', 'id');
      CRM_Core_DAO_AllCoreTables::invoke(__CLASS__, 'links_callback', Civi::$statics[__CLASS__]['links']);
    }
    return Civi::$statics[__CLASS__]['links'];
  }

  /**
   * returns all the column names of this table
   *
   * @access public
   * @return array
   */
  static function &fields()
  {
    if (!(self::$_fields)) {
      self::$_fields = array(
        'id' => array(
          'name' => 'id',
          'title' => E::ts('ID') ,
          'type' => CRM_Utils_Type::T_INT,
          'required' => true,
        ) ,
        'document_id' => array(
          'name' => 'document_id',
          'title' => E::ts('Document ID'),
          'type' => CRM_Utils_Type::T_INT,
          'FKClassName' => 'CRM_Documents_DAO_Document',
        ) ,
        'case_id' => array(
          'name' => 'case_id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => E::ts('Case ID') ,
          'required' => true,
          'FKClassName' => 'CRM_Case_DAO_Case',
        ) ,

      );
    }
    return self::$_fields;
  }
  /**
   * Returns an array containing, for each field, the arary key used for that
   * field in self::$_fields.
   *
   * @access public
   * @return array
   */
  static function &fieldKeys()
  {
    if (!(self::$_fieldKeys)) {
      self::$_fieldKeys = array(
        'id' => 'id',
        'document_id' => 'document_id',
        'case_id' => 'case_id',
      );
    }
    return self::$_fieldKeys;
  }

  /**
   * returns if this table needs to be logged
   *
   * @access public
   * @return boolean
   */
  function getLog()
  {
    return self::$_log;
  }
}
