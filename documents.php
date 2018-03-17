<?php

require_once 'documents.civix.php';
use CRM_Documents_ExtensionUtil as E;


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
 * Implementation of hook_civicrm_navigationMenu
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 */
function documents_civicrm_navigationMenu( &$params ) {  
  $item = array (
    "name"=> E::ts('Find documents'),
    "url"=> "civicrm/documents/search",
    "permission" => "administer CiviCRM",
    "weight" => 5,
  );
  _documents_civix_insert_navigation_menu($params, "Search...", $item);
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
    $DocumentCount = count($documentRepo->getDocumentsByContactId($contactID, false));
    
    $tabs[] = array( 'id'    => 'contact_documents',
                     'url'   => $url,
                     'count' => $DocumentCount,
                     'title' => E::ts('Documents'),
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
  return array('documents' => array('value' => $content, 'label' => E::ts('Documents')));
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
  $repo = CRM_Documents_Entity_DocumentRepository::singleton();
  if ($objectName == 'Individual' || $objectName == 'Household' || $objectName == 'Organization') {
    if ($op == 'delete') {
      try {
        $contact = civicrm_api3('Contact', 'getsingle', array('contact_id' => $id, 'is_deleted' => '1'));
        //contact is in trash so this deletes the contact permanenty
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
  
  if ($op == 'delete') {
    $refspec = CRM_Documents_Utils_EntityRef::singleton();
    $ref = $refspec->getRefByObjectName($objectName);
    if ($ref) {
      $documents = $repo->getDocumentsByEntityId($ref->getEntityTableName(), $id);
      foreach($documents as $doc) {
        $entity = new CRM_Documents_Entity_DocumentEntity($doc);
        $entity->setEntityId($id);
        $entity->setEntityTable($ref->getEntityTableName());
        $doc->removeEntity($entity);
        
        $repo->persist($doc);
      }
    }
  }
}

function documents_civicrm_postSave_civicrm_case($dao) {
  $repo = CRM_Documents_Entity_DocumentRepository::singleton();
  if (!$dao->id) {
    return;
  }
  $case = civicrm_api('Case', 'getsingle', array('id' => $dao->id, 'version' => 3));

  $docs = $repo->getDocumentsByCaseId($dao->id);
  foreach($docs as $doc) {
    //only remove the contacts from the document because the case is never completly deleted
    //so that we can set the contacts on a restore of a case
    foreach($case['client_id'] as $cid) {
      if ($case['is_deleted']) {
        $doc->removeContactId($cid);
      } else {
        $doc->addContactId($cid);
      }
    }        
    $repo->persist($doc);
  }
}

/**
 * This hook is available through a patch from issue #CRM-14409
 * 
 * @link https://issues.civicrm.org/jira/browse/CRM-14409
 * 
 * @param type $mainContactId
 * @param type $mainCaseId
 * @param type $otherContactId
 * @param type $otherCaseId
 * @param type $changeClient
 */
function documents_civicrm_post_case_merge($mainContactId, $mainCaseId = NULL, $otherContactId = NULL, $otherCaseId = NULL, $changeClient = FALSE) { 
  $repo = CRM_Documents_Entity_DocumentRepository::singleton();
  if (!empty($mainCaseId) && !empty($otherCaseId)) {
    $docs = $repo->getDocumentsByCaseId($otherCaseId);
    $case = civicrm_api('Case', 'getsingle', array('id' => $otherCaseId, 'version' => 3));
    foreach($docs as $doc) {
      $doc->addCaseId($mainCaseId);
      if ($changeClient) {
        $doc->removeCaseId($otherCaseId); //remove the old case
      }
      foreach($case['client_id'] as $cid) {
        $doc->addContactId($cid);
      }      
      $repo->persist($doc);
    }
  }
}
