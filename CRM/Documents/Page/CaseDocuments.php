<?php

/* 
 * This class is used to display the documents which belongs to a case
 * 
 */

class CRM_Documents_Page_CaseDocuments extends CRM_Core_Page {
  
  protected $caseId;
  
  protected $clientId;
  
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
    
    $case = civicrm_api3('Case', 'getsingle', array("case_id"=>$this->caseID ));
    $this->clientId = reset($case['client_id']);
  }
  
}

