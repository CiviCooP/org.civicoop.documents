<?php

require_once 'documents.civix.php';

/**
 * Implementation of hook_civicrm_config
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function documents_civicrm_config(&$config) {
  _documents_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function documents_civicrm_xmlMenu(&$files) {
  _documents_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function documents_civicrm_install() {
  return _documents_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function documents_civicrm_uninstall() {
  return _documents_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function documents_civicrm_enable() {
  return _documents_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function documents_civicrm_disable() {
  return _documents_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function documents_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _documents_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function documents_civicrm_managed(&$entities) {
  return _documents_civix_civicrm_managed($entities);
}

/**
 * Implementation of hook_civicrm_caseTypes
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function documents_civicrm_caseTypes(&$caseTypes) {
  _documents_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implementation of hook_civicrm_alterSettingsFolders
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function documents_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _documents_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implementatio of hook__civicrm_tabs
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_tabs
 */
function documents_civicrm_tabs( &$tabs, $contactID ) { 
    // add a tab with the linked cities
    $url = CRM_Utils_System::url( 'civicrm/contact/view/documents',
                                  "cid=$contactID&snippet=1" );
    
    //Count number of documents
    $documentRepo = CRM_Documents_Entity_DocumentRepository::singleton();
    $DocumentCount = count($documentRepo->getDocumentsByContactId($contactID));
    
    $tabs[] = array( 'id'    => 'contact_documents',
                     'url'   => $url,
                     'count' => $DocumentCount,
                     'title' => ts('Documents'),
                     'weight' => 1 );
}

/**
 * Display the documents linked to a case
 * 
 * Implementatio of hook_civicrm_caseSummary
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseSummary
 */
function documents_civicrm_caseSummary($caseId) {
  $page = new CRM_Documents_Page_CaseDocuments($caseId);
  $content = $page->run();
  return array('documents' => array('value' => $content));
}

/**
 * Removes the contact from a document as soon as a contact is deleted permanently.
 * 
 * @param type $op
 * @param type $objectName
 * @param type $id
 * @param type $params
 */
function documents_civicrm_pre( $op, $objectName, $id, &$params ) {
  if ($objectName == 'Individual' || $objectName == 'Household' || $objectName == 'Organization') {
    if ($op == 'delete') {
      try {
        $contact = civicrm_api3('Contact', 'getsingle', array('contact_id' => $id, 'is_deleted' => '1'));
        //contact is in trash so this deletes the contact permanenty
        $repo = CRM_Documents_Entity_DocumentRepository::singleton();
        $docs = $repo->getDocumentsByContactId($id);
        foreach($docs as $doc) {
          $doc->removeContactId($id);
          $repo->persist($doc);
        }
      } catch (Exception $e) {
        //contact not found, or contact is in transh
      }
    }
  }
}