<?php

use CRM_Documents_ExtensionUtil as E;


Class CRM_Documents_DAO_DocumentVersion extends CRM_Core_DAO {

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
    return 'civicrm_document_version';
  }

  /**
   * Returns localized title of this entity.
   */
  public static function getEntityTitle() {
    return E::ts('Document Version');
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
          'title' => E::ts('ID'),
          'type' => CRM_Utils_Type::T_INT,
          'required' => true,
        ) ,
        'description' => array(
          'name' => 'description',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => E::ts('Description') ,
          'required' => true,
          'maxlength' => 255,
          'size' => CRM_Utils_Type::HUGE,
        ) ,
        'updated_by' => array(
          'name' => 'updated_by',
          'title' => E::ts('Updated by'),
          'type' => CRM_Utils_Type::T_INT,
          'FKClassName' => 'CRM_Contact_DAO_Contact',
        ) ,
        'date_updated' => array(
          'name' => 'date_updated',
          'type' => CRM_Utils_Type::T_DATE,
          'title' => E::ts('Date Updated') ,
        ) ,
        'document_id' => array(
          'name' => 'document_id',
          'type' => CRM_Utils_Type::T_INT,
          'FKClassName' => 'CRM_Documents_DAO_Document',
          'title' => E::ts('Document ID'),
        ) ,
        'version' => array(
          'name' => 'version',
          'type' => CRM_Utils_Type::T_INT,
          'title' => E::ts('Version'),
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
        'subject' => 'subject',
        'updated_by' => 'updated_by',
        'date_updated' => 'date_updated',
        'document_id' => 'document_id',
        'version' => 'version',
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
