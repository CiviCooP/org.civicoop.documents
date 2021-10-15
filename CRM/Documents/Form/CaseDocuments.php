<?php
/**
 * @author Jaap Jansma <jaap.jansma@civicoop.org>
 * @license AGPL-3.0
 */

use CRM_Documents_ExtensionUtil as E;

class CRM_Documents_Form_CaseDocuments extends CRM_Core_Form {

  protected $caseId;

  protected $clientId;

  /**
   * Preprocess form.
   *
   * This is called before buildForm. Any pre-processing that
   * needs to be done for buildForm should be done here.
   *
   * This is a virtual function and should be redefined if needed.
   */
  public function preProcess() {
    parent::preProcess();

    $this->caseId = CRM_Utils_Request::retrieve('case_id', 'Integer', $this, TRUE);
    $case = civicrm_api3('Case', 'getsingle', array("case_id"=>$this->caseId ));
    $this->clientId = reset($case['client_id']);

    $type_id = CRM_Utils_Request::retrieve('type_id', 'String');
    $type_ids = null;
    if ($type_id) {
      $type_ids = explode(",", $type_id);
    }
    $status_id = CRM_Utils_Request::retrieve('status_id', 'String');
    $status_ids = null;
    if ($status_id) {
      $status_ids = explode(",", $status_id);
    }
    $documentRepo = CRM_Documents_Entity_DocumentRepository::singleton();
    $documents = $documentRepo->getDocumentsByCaseId($this->caseId, $type_ids, $status_ids);
    $this->assign('documents', $documents);
    $this->assign('clientId', $this->clientId);
    $this->assign('permission', 'edit');
  }

  public function buildQuickForm() {

  }


}
