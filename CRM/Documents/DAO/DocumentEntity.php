<?php

/**
 * @package CRM
 * @copyright CiviCRM LLC https://civicrm.org/licensing
 *
 * Generated from org.civicoop.documents/xml/schema/CRM/Documents/DocumentEntity.xml
 * DO NOT EDIT.  Generated by CRM_Core_CodeGen
 * (GenCodeChecksum:83a1388ea01f8da2b92d3a405fbd5727)
 */
use CRM_Documents_ExtensionUtil as E;

/**
 * Database access object for the DocumentEntity entity.
 */
class CRM_Documents_DAO_DocumentEntity extends CRM_Core_DAO {
  const EXT = E::LONG_NAME;
  const TABLE_ADDED = '';

  /**
   * Static instance to hold the table name.
   *
   * @var string
   */
  public static $_tableName = 'civicrm_document_entity';

  /**
   * Should CiviCRM log any modifications to this table in the civicrm_log table.
   *
   * @var bool
   */
  public static $_log = TRUE;

  /**
   * Unique DocumentEntity ID
   *
   * @var int
   */
  public $id;

  /**
   * FK to Document
   *
   * @var int
   */
  public $document_id;

  /**
   * physical tablename for entity being joined to file, e.g. civicrm_contact
   *
   * @var string
   */
  public $entity_table;

  /**
   * FK to entity table specified in entity_table column.
   *
   * @var int
   */
  public $entity_id;

  /**
   * Class constructor.
   */
  public function __construct() {
    $this->__table = 'civicrm_document_entity';
    parent::__construct();
  }

  /**
   * Returns localized title of this entity.
   *
   * @param bool $plural
   *   Whether to return the plural version of the title.
   */
  public static function getEntityTitle($plural = FALSE) {
    return $plural ? E::ts('Document Entities') : E::ts('Document Entity');
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
      Civi::$statics[__CLASS__]['links'][] = new CRM_Core_Reference_Basic(self::getTableName(), 'document_id', 'civicrm_document', 'id');
      Civi::$statics[__CLASS__]['links'][] = new CRM_Core_Reference_Dynamic(self::getTableName(), 'entity_id', NULL, 'id', 'entity_table');
      CRM_Core_DAO_AllCoreTables::invoke(__CLASS__, 'links_callback', Civi::$statics[__CLASS__]['links']);
    }
    return Civi::$statics[__CLASS__]['links'];
  }

  /**
   * Returns all the column names of this table
   *
   * @return array
   */
  public static function &fields() {
    if (!isset(Civi::$statics[__CLASS__]['fields'])) {
      Civi::$statics[__CLASS__]['fields'] = [
        'id' => [
          'name' => 'id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => E::ts('ID'),
          'description' => E::ts('Unique DocumentEntity ID'),
          'required' => TRUE,
          'where' => 'civicrm_document_entity.id',
          'table_name' => 'civicrm_document_entity',
          'entity' => 'DocumentEntity',
          'bao' => 'CRM_Documents_DAO_DocumentEntity',
          'localizable' => 0,
          'html' => [
            'type' => 'Number',
          ],
          'readonly' => TRUE,
          'add' => NULL,
        ],
        'document_id' => [
          'name' => 'document_id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => E::ts('Document'),
          'description' => E::ts('FK to Document'),
          'required' => TRUE,
          'where' => 'civicrm_document_entity.document_id',
          'table_name' => 'civicrm_document_entity',
          'entity' => 'DocumentEntity',
          'bao' => 'CRM_Documents_DAO_DocumentEntity',
          'localizable' => 0,
          'FKClassName' => 'CRM_Documents_DAO_Document',
          'html' => [
            'type' => 'EntityRef',
          ],
          'add' => NULL,
        ],
        'entity_table' => [
          'name' => 'entity_table',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => E::ts('Entity Table'),
          'description' => E::ts('physical tablename for entity being joined to file, e.g. civicrm_contact'),
          'maxlength' => 64,
          'size' => CRM_Utils_Type::BIG,
          'where' => 'civicrm_document_entity.entity_table',
          'table_name' => 'civicrm_document_entity',
          'entity' => 'DocumentEntity',
          'bao' => 'CRM_Documents_DAO_DocumentEntity',
          'localizable' => 0,
          'add' => NULL,
        ],
        'entity_id' => [
          'name' => 'entity_id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => E::ts('Entity ID'),
          'description' => E::ts('FK to entity table specified in entity_table column.'),
          'required' => TRUE,
          'where' => 'civicrm_document_entity.entity_id',
          'table_name' => 'civicrm_document_entity',
          'entity' => 'DocumentEntity',
          'bao' => 'CRM_Documents_DAO_DocumentEntity',
          'localizable' => 0,
          'add' => NULL,
        ],
      ];
      CRM_Core_DAO_AllCoreTables::invoke(__CLASS__, 'fields_callback', Civi::$statics[__CLASS__]['fields']);
    }
    return Civi::$statics[__CLASS__]['fields'];
  }

  /**
   * Return a mapping from field-name to the corresponding key (as used in fields()).
   *
   * @return array
   *   Array(string $name => string $uniqueName).
   */
  public static function &fieldKeys() {
    if (!isset(Civi::$statics[__CLASS__]['fieldKeys'])) {
      Civi::$statics[__CLASS__]['fieldKeys'] = array_flip(CRM_Utils_Array::collect('name', self::fields()));
    }
    return Civi::$statics[__CLASS__]['fieldKeys'];
  }

  /**
   * Returns the names of this table
   *
   * @return string
   */
  public static function getTableName() {
    return self::$_tableName;
  }

  /**
   * Returns if this table needs to be logged
   *
   * @return bool
   */
  public function getLog() {
    return self::$_log;
  }

  /**
   * Returns the list of fields that can be imported
   *
   * @param bool $prefix
   *
   * @return array
   */
  public static function &import($prefix = FALSE) {
    $r = CRM_Core_DAO_AllCoreTables::getImports(__CLASS__, 'document_entity', $prefix, []);
    return $r;
  }

  /**
   * Returns the list of fields that can be exported
   *
   * @param bool $prefix
   *
   * @return array
   */
  public static function &export($prefix = FALSE) {
    $r = CRM_Core_DAO_AllCoreTables::getExports(__CLASS__, 'document_entity', $prefix, []);
    return $r;
  }

  /**
   * Returns the list of indices
   *
   * @param bool $localize
   *
   * @return array
   */
  public static function indices($localize = TRUE) {
    $indices = [];
    return ($localize && !empty($indices)) ? CRM_Core_DAO_AllCoreTables::multilingualize(__CLASS__, $indices) : $indices;
  }

}
