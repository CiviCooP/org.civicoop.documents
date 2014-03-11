<?php

/* 
 * This file shows all the versions of a document
 */


class CRM_Documents_Page_Versions extends CRM_Core_Page {
  
  protected $_contactId;
  
  protected $document;

  
  function run() {
    $this->preProcess();
    
    CRM_Utils_System::setTitle(ts("All versios for '".$this->document->getSubject()."'"));
    
    $this->assign('versions', $this->document->getVersions());
    
    $this->assign('permission', 'edit');
    parent::run();
  }
  
  protected function preProcess() {
    $this->_contactId = CRM_Utils_Request::retrieve('cid', 'Positive', $this, TRUE);
    $this->assign('contactId', $this->_contactId);
    
    $docId = CRM_Utils_Request::retrieve('id', 'Positive', $this, TRUE);
    $documentRepo = CRM_Documents_Entity_DocumentRepository::singleton();
    $this->document = $documentRepo->getDocumentById($docId);
    
    $this->assign('document', $this->document);
    
    $this->setUserContext();
  }
  
  protected function setUserContext() {
    $session = CRM_Core_Session::singleton();
    $userContext = CRM_Utils_System::url('civicrm/contact/view', 'cid='.$this->_contactId.'&selectedChild=contact_documents&reset=1');
    $session->pushUserContext($userContext);
  }
}