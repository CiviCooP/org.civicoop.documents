<?php

Class CRM_Documents_DAO_DocumentVersion extends CRM_Core_DAO {
  
  /**
   * static instance to hold the field values
   *
   * @var array
   * @static
   */
  static $_fields = null;
  
  /**
   * empty definition for virtual function
   */
  static function getTableName() {
    return 'civicrm_document_version';
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
          'type' => CRM_Utils_Type::T_INT,
          'required' => true,
        ) ,
        'description' => array(
          'name' => 'description',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('Description') ,
          'required' => true,
          'maxlength' => 255,
          'size' => CRM_Utils_Type::HUGE,
        ) ,
        'updated_by' => array(
          'name' => 'updated_by',
          'type' => CRM_Utils_Type::T_INT,
          'FKClassName' => 'CRM_Contact_DAO_Contact',
        ) ,
        'date_updated' => array(
          'name' => 'date_updated',
          'type' => CRM_Utils_Type::T_DATE,
          'title' => ts('Date Added') ,
        ) ,
        'document_id' => array(
          'name' => 'document_id',
          'type' => CRM_Utils_Type::T_INT,
          'FKClassName' => 'CRM_Documents_DAO_Document',
        ) ,
        'version' => array(
          'name' => 'version',
          'type' => CRM_Utils_Type::T_INT,
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
  
}