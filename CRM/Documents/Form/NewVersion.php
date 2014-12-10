<?php

require_once 'CRM/Core/Form.php';

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Documents_Form_NewVersion extends CRM_Core_Form {
  
  protected $document;
  
  protected $cid;
  
  protected $documentId = false;
  
  protected $_action;
  
  protected $replaceCurrent = false;
  
  function preProcess() {
    parent::preProcess();
    
    $session = CRM_Core_Session::singleton();
    
    $this->documentId = CRM_Utils_Request::retrieve('id', 'Positive', $this, TRUE);
    $this->add('hidden', 'id', $this->documentId);
    
    $this->cid = CRM_Utils_Request::retrieve('cid', 'Positive', $this, false, false);
    $this->add('hidden', 'cid', $this->cid);
    
    //retrieve action
    $this->_action = CRM_Utils_Request::retrieve('action', 'String', $this);
    $this->assign('action', $this->_action);
    
    if ($this->documentId) {
      $documentsRepo = CRM_Documents_Entity_DocumentRepository::singleton();
      try {
        $this->document = $documentsRepo->getDocumentById($this->documentId);        
      } catch (Exception $e) {
        CRM_Core_Session::setStatus('Error during opening document', '', 'error');
        $url = $session->popUserContext();
        CRM_Utils_System::redirect($url);
      }
    } else {
      CRM_Core_Session::setStatus('Error during opening document', '', 'error');
      $url = $session->popUserContext();
      CRM_Utils_System::redirect($url);
    }
    $this->assign('document', $this->document);
    
    //Set page title based on action
    $this->setPageTitle();
    
  }
  
  function setDefaultValues() {
    parent::setDefaultValues();
    
  }
  
  function buildQuickForm() {
    
    //always use a new version for the buildAttachment
    //otherwise there is no upload field
    $version = new CRM_Documents_Entity_DocumentVersion($this->document);
    
    $this->add(
         'text', 
        'description', 
        ts('Description'), 
        array(
          'value' => $version->getDescription(),
          'maxlength' => 255,
          'size' => CRM_Utils_Type::HUGE,
        ),
        true
    );
    $this->addCheckBox(
        'replaceCurrent', 
        ts('Replace current version'), 
        array('' => '1')
    );
    
    $this->addButtons(array(
      array(
        'type' => 'upload',
        'name' => ts('Submit'),
        'isDefault' => TRUE,
      ),
      array(
        'type' => 'cancel',
        'name' => ts('Cancel'),
        'isDefault' => TRUE,
      ),
    ));

    // export form elements
    $this->assign('elementNames', $this->getRenderableElementNames());
   
    CRM_Core_BAO_File::buildAttachment($this, 'civicrm_document_version', $version->getId(), 1, TRUE);
    
    parent::buildQuickForm();
  }

  function postProcess() {
    $documentsRepo = CRM_Documents_Entity_DocumentRepository::singleton();
    
    $values = $this->controller->exportValues();
    
    $replaceCurrent = false;
    if (isset($values['replaceCurrent']) && isset($values['replaceCurrent'][1]) && $values['replaceCurrent'][1]) {
      $replaceCurrent = true;
    }
    
    if ($replaceCurrent) {
      $version = $this->document->getCurrentVersion();
      //remove the old attachment so that the new attachment replaces the old one
      CRM_Core_BAO_File::deleteEntityFile('civicrm_document_version', $version->getId());
    } else {
      $version = $this->document->addNewVersion();
    }
    
    $version->setDescription($this->exportValue('description'));
        
    $params = array(); //used for attachments
    // add attachments as needed
    CRM_Core_BAO_File::formatAttachment($values,
      $params,
      'civicrm_document_version',
      $version->getId()
    );
    
    //save document
    $documentsRepo->persist($this->document);
    CRM_Core_BAO_File::processAttachment($params, 'civicrm_document_version', $version->getId());
    
    parent::postProcess();
    
  }

  /**
   * Get the fields/elements defined in this form.
   *
   * @return array (string)
   */
  function getRenderableElementNames() {
    // The _elements list includes some items which should not be
    // auto-rendered in the loop -- such as "qfKey" and "buttons".  These
    // items don't have labels.  We'll identify renderable by filtering on
    // the 'label'.
    $elementNames = array();
    foreach ($this->_elements as $element) {
      $label = $element->getLabel();
      if (!empty($label)) {
        $elementNames[] = $element->getName();
      }
    }
    return $elementNames;
  }
  
  protected function setPageTitle() {
    CRM_Utils_System::setTitle(ts('Upload new version'));
  }
}
