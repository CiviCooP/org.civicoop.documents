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
  
  protected $context;
  
  protected $entity = false;
  
  function preProcess() {
    parent::preProcess();
    
    $this->assign('is44', CRM_Documents_Utils_CiviVersion::is44());

    $session = CRM_Core_Session::singleton();
    $entityRefs = CRM_Documents_Utils_EntityRef::singleton();

    $this->context = CRM_Utils_Request::retrieve('context', 'String', $this, FALSE, 'contact');
    $this->add('hidden', 'context', $this->context);
    
    $this->documentId = CRM_Utils_Request::retrieve('id', 'Positive', $this, FALSE);
    $this->add('hidden', 'id', $this->documentId);
    
    $this->cid = CRM_Utils_Request::retrieve('cid', 'Positive', $this, FALSE);
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
      $this->document = new CRM_Documents_Entity_Document();
      
      //if it is a case set the caseID and set the clients as contactId's
      if ($this->context == 'case') {
        $caseId = CRM_Utils_Request::retrieve('case_id', 'Positive', $this, TRUE); 
        $this->add('hidden', 'case_id', $caseId);
        $case = civicrm_api3('Case', 'getsingle', array("case_id"=>$caseId ));
        $this->document->addCaseid($caseId);
        $this->document->setContactIds($case['client_id']);
      } else {
         //set the contactId
        $this->document->setContactIds(array($this->cid));
      }      
      
    }
    $this->assign('document', $this->document);
    
    $this->entity = false;
    $entity = CRM_Utils_Request::retrieve('entity', 'String', $this, FALSE);
    $entity_id = CRM_Utils_Request::retrieve('entity_id', 'Positive', $this, FALSE);
    $this->add('hidden','entity', $entity);
    $this->add('hidden','entity_id', $entity_id);
    $ref = false;
    if ($entity && $entity_id) {
      $ref = $entityRefs->getRefBySystemName($entity);
      if ($ref) {
        $this->document->addNewEntity($ref->getEntityTableName(), $entity_id);
      }
    }
    
    //if there is no link to anything not even a contact throw an error
    if ($ref === false && !$this->cid) {
      throw new Exception('Could find valid value for cid');
    }
    
    if ($ref) {
      $active_entities = array(' -- Select '.$ref->getHumanName().' --') + $ref->getActiveEntities();
      $attributes = array();
      if (!$ref->isSingleEntity()) {
        $attributes['multiple'] = 'multiple';
      }
      $this->add('select', $ref->getSystemName(), $ref->getHumanName(), $active_entities, false, $attributes);
    }
    
    $this->assign('selectedContacts', implode(",", $this->document->getContactIds()));
    
    //Set page title based on action
    $this->setPageTitleBasedOnAction();

  }
  
  function setDefaultValues() {
    $return = parent::setDefaultValues();
    
    $entityRefs = CRM_Documents_Utils_EntityRef::singleton();
    foreach($this->document->getEntities() as $entity) {
      $ref = $entityRefs->getRefByTableName($entity->getEntityTable());
      if ($ref) {
        if ($ref->isSingleEntity()) {
          $return[$ref->getSystemName()] = $entity->getEntityId();
        } else {
          $return[$ref->getSystemName()][] = $entity->getEntityId();
        }
      }
    }

    if (!CRM_Documents_Utils_CiviVersion::is44()) {
      $return['contacts'] = $this->document->getContactIds();
    }

    return $return;
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
    
    if (CRM_Documents_Utils_CiviVersion::is44()) {
      CRM_Contact_Form_NewContact::buildQuickForm($this);
    } else {
      $this->addEntityRef('contacts', ts('Contacts'), array('multiple' => TRUE, 'create' => TRUE), true);
    }
   
   CRM_Core_BAO_File::buildAttachment($this, 'civicrm_document_version', $this->document->getCurrentVersion()->getId(), 1, TRUE);
    
    parent::buildQuickForm();
  }

  function postProcess() {
    $documentsRepo = CRM_Documents_Entity_DocumentRepository::singleton();
    if ($this->_action & CRM_Core_Action::DELETE) {
      //delete the document
      $documentsRepo->remove($this->document);
      
      CRM_Core_Session::setStatus(ts("Selected document has been successfully deleted."), ts('Record Deleted'), 'success');
      return;
    }
    
    $values = $this->controller->exportValues();
    
    $contact_ids = array();
    // format with contact (target contact) values
    if (isset($values['contact'][1])) {
      $contact_ids = explode(',', $values['contact'][1]);
    } elseif (!CRM_Documents_Utils_CiviVersion::is44()) {
      $contact_ids = explode(",", $values['contacts']);
    }

    $this->document->setSubject($this->exportValue('subject'));
    $this->document->setContactIds($contact_ids);
    
    $entityRefs = CRM_Documents_Utils_EntityRef::singleton();
    $refs = $entityRefs->getRefs();
    foreach($refs as $ref) {
      if (isset($values[$ref->getSystemName()])) {
        //remove all entities of this type because a new one is submitted
        foreach($this->document->getEntities() as $entity) {
          if ($entity->getEntityTable() == $ref->getEntityTableName()) {
            $this->document->removeEntity($entity);
          }
        }
        
        if (is_array($values[$ref->getSystemName()])) {
          foreach($values[$ref->getSystemName()] as $entity_id) {
            $this->document->addNewEntity($ref->getEntityTableName(), $entity_id);
          }
        } else {
          $this->document->addNewEntity($ref->getEntityTableName(), $values[$ref->getSystemName()]);
        }
      }
    }
    
    foreach($this->document->getEntities() as $entity) {
      $ref = $entityRefs->getRefByTableName($entity->getEntityTable());
      if ($ref) {
        //remove all entities from this ref spec
        if ($ref->isSingleEntity()) {
          $return[$ref->getSystemName()] = $entity->getEntityId();
        } else {
          $return[$ref->getSystemName()][] = $entity->getEntityId();
        }
      }
    }

    $params = array(); //used for attachments
    // add attachments as needed
    CRM_Core_BAO_File::formatAttachment($values,
      $params,
      'civicrm_document_version',
      $this->document->getCurrentVersion()->getId()
    );

    //save document
    $documentsRepo->persist($this->document);
    CRM_Core_BAO_File::processAttachment($params, 'civicrm_document_version', $this->document->getCurrentVersion()->getId());
    
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
  
  protected function setPageTitleBasedOnAction() {
    CRM_Utils_System::setTitle(ts('Add new document'));
    if ($this->_action == CRM_Core_Action::DELETE) {
      CRM_Utils_System::setTitle(ts("Delete document '".$this->document->getSubject()."'"));
    } else if ($this->document->getId()) {
      CRM_Utils_System::setTitle(ts("Edit document '".$this->document->getSubject()."'"));
    }
  }
}
