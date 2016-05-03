<?php
/**
 * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 */
class CRM_Documents_Utils_HookInvoker {

  private static $singleton;

  private function __construct() {

  }

  /**
   * @return \CRM_Documents_Utils_HookInvoker
   */
  public static function singleton() {
    if (!self::$singleton) {
      self::$singleton = new CRM_Documents_Utils_HookInvoker();
    }
    return self::$singleton;
  }

  /**
   * Returns an array with instances of classes which implements the interface CRM_Documents_Interface_EntityRefSpec
   *
   * @return array
   */
  public function hook_civicrm_documents_entity_ref_spec() {
    return $this->invoke('civicrm_documents_entity_ref_spec', 0, CRM_Utils_Hook::$_nullObject, CRM_Utils_Hook::$_nullObject, CRM_Utils_Hook::$_nullObject, CRM_Utils_Hook::$_nullObject, CRM_Utils_Hook::$_nullObject);
  }

  /**
   * This hook is called the retrieve the status of a document.
   *
   * A status indicates if a document is (un)used and therefore should be kept or could be deleted
   *
   * @param $doc The document
   * @param $status 0 is unused document. 1 is document is in use.
   * @return mixed
   */
  public function hook_civicrm_documents_get_status($doc, &$status) {
    return $this->invoke('civicrm_documents_get_status', 2, $doc, $status, CRM_Utils_Hook::$_nullObject, CRM_Utils_Hook::$_nullObject, CRM_Utils_Hook::$_nullObject);
  }

  private function invoke($fnSuffix, $numParams, &$arg1 = null, &$arg2 = null, &$arg3 = null, &$arg4 = null, &$arg5 = null) {
    $hook =  CRM_Utils_Hook::singleton();
    $civiVersion = CRM_Core_BAO_Domain::version();

    if (version_compare($civiVersion, '4.5', '<')) {
      //in CiviCRM 4.4 the invoke function has 5 arguments maximum
      return $hook->invoke($numParams, $arg1, $arg2, $arg3, $arg4, $arg5, $fnSuffix);
    } else {
      //in CiviCRM 4.5 and later the invoke function has 6 arguments
      return $hook->invoke($numParams, $arg1, $arg2, $arg3, $arg4, $arg5, CRM_Utils_Hook::$_nullObject, $fnSuffix);
    }
  }

}