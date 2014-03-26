<?php

class CRM_Documents_StateMachine_Search extends CRM_Core_StateMachine {

  /**
   * The task that the wizard is currently processing
   *
   * @var string
   * @protected
   */
  protected $_task;

  /**
   * class constructor
   */ function __construct($controller, $action = CRM_Core_Action::NONE) {
    parent::__construct($controller, $action);

    $this->_pages = array();

    $this->_pages['CRM_Documents_Form_Search'] = NULL;
    
    $this->addSequentialPages($this->_pages, $action);
  }

  /**
   * Since this is a state machine for search and we want to come back to the same state
   * we dont want to issue a reset of the state session when we are done processing a task
   *
   */
  function shouldReset() {
    return FALSE;
  }
}

