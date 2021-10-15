<?php

/*
 * This class holds formatting functions for formatting contacts and dates etc.
 *
 */

class CRM_Documents_Utils_Formatter {

  /**
   * We only need one instance of this object. So we use the singleton
   * pattern and cache the instance in this variable
   *
   * @var object
   * @access private
   * @static
   */
  static private $_singleton = NULL;

  protected function __construct() {

  }

  /**
   * Constructor and getter for the singleton instance
   *
   * @return instance of $config->userHookClass
   */
  static function singleton($fresh = FALSE) {
    if (self::$_singleton == NULL || $fresh) {
      self::$_singleton = new CRM_Documents_Utils_Formatter();
    }
    return self::$_singleton;
  }

  /**
   * Format a contact ID to a displayanem and eventually a link
   *
   * @param int contactId
   * @param bool $link
   * @return String
   */
  public function formatContact($contactId, $link=TRUE) {
    $return = '';
    if ($contactId) {
      $display_name = CRM_Contact_BAO_Contact::displayName($contactId);
      if ($link) {
        $return = '<a class="" href="' . CRM_Utils_System::url('civicrm/contact/view', 'reset=1&cid=' . $contactId) . '" >'.$display_name.'</a>';
      } else {
        $return = $display_name;
      }
    }
    return $return;
  }

  /**
   * Formats a caseId to a text (subject of the case)
   *
   * @param type $caseId
   */
  public function formatCaseId($caseId) {
    return CRM_Core_DAO::getFieldValue('CRM_Case_BAO_Case', $caseId, 'subject');
  }

  /**
   * Format a date
   *
   * @param DateTime $date
   * @return String
   */
  public function formateDate(DateTime $date=null) {
    $return = '';
    if ($date) {
      $config = CRM_Core_Config::singleton();
      $return = CRM_Utils_Date::customFormat($date->format('Y-m-d'));
    }
    return $return;
  }

  public function formatType($type_id) {
    static $types = [];
    if (!isset($types[$type_id])) {
      $types[$type_id] = civicrm_api3('OptionValue', 'getvalue', ['value' => $type_id, 'option_group_id' => 'document_type', 'return' => 'label']);
    }
    return $types[$type_id];
  }

  public function formatStatus($status_id) {
    static $statuses = [];
    if (!isset($statuses[$status_id])) {
      $statuses[$status_id] = civicrm_api3('OptionValue', 'getvalue', ['value' => $status_id, 'option_group_id' => 'document_status', 'return' => 'label']);
    }
    return $statuses[$status_id];
  }

}

