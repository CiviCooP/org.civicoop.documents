<?php

use CRM_Documents_ExtensionUtil as E;

/* 
 * This file shows all the versions of a document
 */


class CRM_Documents_Page_Versions extends CRM_Core_Page {
  
  protected $_contactId;
  
  protected $caseId;
  
  protected $document;

  
  function run() {
    $this->preProcess();
    
    CRM_Utils_System::setTitle(E::ts("All versions for '%1'", array(
      1 => $this->document->getSubject()
    )));
    
    $this->assign('versions', $this->document->getVersions());
    
    $this->assign('permission', 'view');
    if ($this->_contactId && CRM_Contact_BAO_Contact_Permission::allow(CRM_Core_Permission::EDIT, $this->_contactId)) {
      $this->assign('permission', 'edit');
    }
    parent::run();
  }
  
  protected function preProcess() {
    $this->_contactId = CRM_Utils_Request::retrieve('cid', 'Positive', $this, false, false);
    $this->caseId = CRM_Utils_Request::retrieve('caseId', 'Positive', $this, false, false);
    $this->assign('contactId', $this->_contactId);
    $this->assign('caseId', $this->caseId);
    
    $docId = CRM_Utils_Request::retrieve('id', 'Positive', $this, TRUE);
    $documentRepo = CRM_Documents_Entity_DocumentRepository::singleton();
    $this->document = $documentRepo->getDocumentById($docId);
    
    $this->assign('document', $this->document);
    
    //set to url for the back button
    $session = CRM_Core_Session::singleton();
    $goBackUrl = $session->readUserContext();
    $this->assign('goBackUrl', $goBackUrl);
    
    $this->setUserContext();
  }
  
  protected function setUserContext() {
    $session = CRM_Core_Session::singleton();
    if ($this->caseId) {
      $context = CRM_Utils_Request::retrieve('context', 'String', $this, FALSE, 'home');
      $action = CRM_Core_Action::description(CRM_Utils_Request::retrieve('action', 'String', $this, FALSE, CRM_Core_Action::VIEW));
      $userContext = CRM_Utils_System::url('civicrm/contact/view/case', 'action='.$action.'&cid='.$this->_contactId.'&reset=1&id='.$this->caseId.'&context='.$context);
    } else {
      $userContext = CRM_Utils_System::url('civicrm/contact/versions', 'cid='.$this->_contactId.'&id='&$this->document->getId().'&reset=1');
    }
    $session->pushUserContext($userContext);
  }
}
