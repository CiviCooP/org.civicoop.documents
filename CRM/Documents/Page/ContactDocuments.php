<?php

require_once 'CRM/Core/Page.php';

class CRM_Documents_Page_ContactDocuments extends CRM_Core_Page {
  
  protected $_contactId;
  
  function run() {
    $this->preProcess();
    
    CRM_Utils_System::setTitle(ts('Documents'));
    
    $documentRepo = CRM_Documents_Entity_DocumentRepository::singleton();
    $documents = $documentRepo->getDocumentsByContactId($this->_contactId);
    
    $this->assign('documents', $documents);
    
    $this->assign('permission', 'edit');
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
