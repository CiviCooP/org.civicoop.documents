<?php

use CRM_Documents_ExtensionUtil as E;


Class CRM_Documents_DAO_Document extends CRM_Core_DAO {

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
    return 'civicrm_document';
  }

  /**
   * Returns localized title of this entity.
   */
  public static function getEntityTitle() {
    return E::ts('Document');
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
        'subject' => array(
          'name' => 'subject',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => E::ts('Subject') ,
          'required' => true,
          'maxlength' => 255,
          'size' => CRM_Utils_Type::HUGE,
        ) ,
        'added_by' => array(
          'name' => 'added_by',
          'type' => CRM_Utils_Type::T_INT,
          'title' => E::ts('Added by'),
          'FKClassName' => 'CRM_Contact_DAO_Contact',
        ) ,
        'date_added' => array(
          'name' => 'date_added',
          'type' => CRM_Utils_Type::T_DATE,
          'title' => E::ts('Date Added') ,
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
        'added_by' => 'added_by',
        'date_added' => 'date_added',
        'updated_by' => 'updated_by',
        'date_updated' => 'date_updated',
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
