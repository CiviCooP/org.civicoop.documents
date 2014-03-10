<?php

require_once 'CRM/Core/Form.php';

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Documents_Form_Document extends CRM_Core_Form {
  
  protected $document;
  
  protected $cid;
  
  protected $documentId = false;
  
  protected $_action;
  
  function preProcess() {
    parent::preProcess();
    
    $this->documentId = CRM_Utils_Request::retrieve('id', 'Positive', $this, FALSE);
    $this->add('hidden', 'id', $this->documentId);
    
    $this->cid = CRM_Utils_Request::retrieve('cid', 'Positive', $this, TRUE);
    $this->add('hidden', 'cid', $this->cid);
    
    //retrieve action
    $this->_action = CRM_Utils_Request::retrieve('action', 'String', $this);
    $this->assign('action', $this->_action);
    
    if ($this->documentId) {
      $documentsRepo = CRM_Documents_Entity_DocumentRepository::singleton();
      $this->document = $documentsRepo->getDocumentById($this->documentId);       
    } else {
      $this->document = new CRM_Documents_Entity_Document;
      $this->document->setContactIds(array($this->cid));
    }
    $this->assign('document', $this->document);
    
    $this->assign('selectedContacts', implode(",", $this->document->getContactIds()));
    
    
  }
  
  function setDefaultValues() {
    parent::setDefaultValues();
    
  }
  
  function buildQuickForm() {
    if ($this->_action == CRM_Core_Action::DELETE) {
      $this->addButtons(array(
        array(
          'type' => 'next',
          'name' => ts('Delete'),
          'spacing' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
          'isDefault' => TRUE
        ),
        array(
          'type' => 'cancel',
          'name' => ts('Cancel')
        )
      ));
      return;
    }
    $this->add(
         'text', 
        'subject', 
        ts('Subject'), 
        array(
          'value' => $this->document->getSubject(),
          'maxlength' => 255,
          'size' => CRM_Utils_Type::HUGE,
        ),
        true
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
    
   CRM_Contact_Form_NewContact::buildQuickForm($this);
   
   CRM_Core_BAO_File::buildAttachment($this, 'civicrm_document', $this->document->getId(), 1, TRUE);
    
    parent::buildQuickForm();
  }

  function postProcess() {
    $documentsRepo = CRM_Documents_Entity_DocumentRepository::singleton();
    if ($this->_action & CRM_Core_Action::DELETE) {
      //delete the attachment
      CRM_Core_BAO_File::deleteEntityFile('civicrm_document', $this->document->getId());
      //delete the document
      $documentsRepo->remove($this->document);
      
      CRM_Core_Session::setStatus(ts("Selected document has been deleted successfully."), ts('Record Deleted'), 'success');
      return;
    }
    
    
    
    $values = $this->controller->exportValues();
    
    $contact_ids = array();
    // format with contact (target contact) values
    if (isset($values['contact'][1])) {
      $contact_ids = explode(',', $values['contact'][1]);
    }
    
    $this->document->setSubject($this->exportValue('subject'));
    $this->document->setContactIds($contact_ids);
        
    $params = array(); //used for attachments
    // add attachments as needed
    CRM_Core_BAO_File::formatAttachment($values,
      $params,
      'civicrm_document',
      $this->document->getId()
    );
    
    //save document
    $documentsRepo->persist($this->document);
    CRM_Core_BAO_File::processAttachment($params, 'civicrm_document', $this->document->getId());
    
    /*$options = $this->getColorOptions();
    CRM_Core_Session::setStatus(ts('You picked color "%1"', array(
      1 => $options[$values['favorite_color']]
    )));*/
    parent::postProcess();
    
    //redirect
    /*$session = CRM_Core_Session::singleton();
    $url = $session->popUserContext();
    CRM_Utils_System::redirect($url);*/
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
}
