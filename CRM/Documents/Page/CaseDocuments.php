<?php

/* 
 * This class is used to display the documents which belongs to a case
 * 
 */

class CRM_Documents_Page_CaseDocuments extends CRM_Core_Page {
  
  protected $caseId;
  
  protected $clientId;
  
  protected $context;
  
  protected $action;
  
  public function __construct($caseId) {
    parent::__construct();
    
    $this->caseId = $caseId;
  }
  
  public function run() {
    $this->preProcess();
    
    //get template file name
    $pageTemplateFile = $this->getHookedTemplateFileName();
    
    //do the magic 
    $documentRepo = CRM_Documents_Entity_DocumentRepository::singleton();
    $documents = $documentRepo->getDocumentsByCaseId($this->caseId);
        
    $this->assign('caseId', $this->caseId);
    $this->assign('clientId', $this->clientId);
    $this->assign('documents', $documents);    
    $this->assign('permission', 'edit');
    
    //render the template
    $content = self::$_template->fetch($pageTemplateFile);
    
    CRM_Utils_System::appendTPLFile($pageTemplateFile, $content);

    //its time to call the hook.
    CRM_Utils_Hook::alterContent($content, 'page', $pageTemplateFile, $this);
    
    return $content;  
  }
  
  protected function preProcess() {
    //retrieve the client contactId
    
    $case = civicrm_api3('Case', 'getsingle', array("case_id"=>$this->caseId ));
    $this->clientId = reset($case['client_id']);
    
    $this->context = CRM_Utils_Request::retrieve('context', 'String', $this, FALSE, 'home');
    $this->action = CRM_Utils_Request::retrieve('action', 'String', $this, FALSE, CRM_Core_Action::VIEW);
    $this->assign('context', $this->context);
    
    $this->setUserContext();
  }
  
  protected function setUserContext() {    
    $action = CRM_Core_Action::description($this->action);
    $session = CRM_Core_Session::singleton();
    $userContext = CRM_Utils_System::url('civicrm/contact/view/case', 'action='.$action.'&cid='.$this->clientId.'&&reset=1&id='.$this->caseId.'&context='.$this->context);
    $session->pushUserContext($userContext);
  }
}

