<?php

require_once 'CRM/Core/Page.php';
use CRM_Documents_ExtensionUtil as E;


class CRM_Documents_Page_ContactDocuments extends CRM_Core_Page {

  protected $_contactId;

  function run() {
    $this->preProcess();

    CRM_Utils_System::setTitle(E::ts('Documents'));

    $types = CRM_Core_OptionGroup::values('document_type');
    $statuses = CRM_Core_OptionGroup::values('document_status');
    $this->assign('document_types', $types);
    $this->assign('document_statuses', $statuses);

    $snippet = CRM_Utils_Request::retrieve('snippet', 'String');
    $filter = CRM_Utils_Request::retrieve('filter', 'String');
    $this->assign('snippet', $snippet && $filter ? true : false);
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
    $documents = $documentRepo->getDocumentsByContactId($this->_contactId, false, false, $type_ids, $status_ids);

    $this->assign('documents', $documents);
    $this->assign('selected_document_types', $type_ids);
    $this->assign('selected_document_status', $status_ids);

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
