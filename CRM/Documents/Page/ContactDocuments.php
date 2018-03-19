<?php

require_once 'CRM/Core/Page.php';
use CRM_Documents_ExtensionUtil as E;


class CRM_Documents_Page_ContactDocuments extends CRM_Core_Page {
  
  protected $_contactId;
  
  function run() {
    $this->preProcess();
    
    CRM_Utils_System::setTitle(E::ts('Documents'));
    
    $documentRepo = CRM_Documents_Entity_DocumentRepository::singleton();
    $documents = $documentRepo->getDocumentsByContactId($this->_contactId, false);
    
    $this->assign('documents', $documents);
    
    $this->assign('permission', 'view');
    if (CRM_Contact_BAO_Contact_Permission::allow($this->_contactId, CRM_Core_Permission::EDIT)) {
      $this->assign('permission', 'edit');
    }
    
    parent::run();
  }
  
  protected function preProcess() {
    $this->_contactId = CRM_Utils_Request::retrieve('cid', 'Positive', $this, TRUE);
    $this->assign('contactId', $this->_contactId);
    
    $this->setUserContext();
  }
  
  protected function setUserContext() {
    $session = CRM_Core_Session::singleton();
    $userContext = CRM_Utils_System::url('civicrm/contact/view', 'cid='.$this->_contactId.'&selectedChild=contact_documents&reset=1');
    $session->pushUserContext($userContext);
  }
}
