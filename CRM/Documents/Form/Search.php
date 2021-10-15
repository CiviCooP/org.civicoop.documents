<?php

use CRM_Documents_ExtensionUtil as E;

/*
 * This file holds the search functionality for documents
 */

class CRM_Documents_Form_Search extends CRM_Core_Form {

  /**
   * have we already done this search
   *
   * @access protected
   * @var boolean
   */
  protected $_done;

  /**
   * name of search button
   *
   * @var string
   * @access protected
   */
  protected $_searchButtonName;

  /**
   * name of action button
   *
   * @var string
   * @access protected
   */
  protected $_actionButtonName;


  protected $_limit = NULL;

  /**
   * what context are we being invoked from
   *
   * @access protected
   * @var string
   */
  protected $_context = NULL;

  function preProcess() {

    /**
     * set the button names
     */
    $this->_searchButtonName = $this->getButtonName('refresh');
    $this->_actionButtonName = $this->getButtonName('next', 'action');

    $this->_context = CRM_Utils_Request::retrieve('context', 'String', $this, FALSE, 'search');
    $this->assign("context", $this->_context);

    $this->_limit   = CRM_Utils_Request::retrieve('limit', 'Positive', $this);
    $this->assign("limit", $this->_limit);

    $this->_done = FALSE;

    $sortID = NULL;
    if ($this->get(CRM_Utils_Sort::SORT_ID)) {
      $sortID = CRM_Utils_Sort::sortIDValue($this->get(CRM_Utils_Sort::SORT_ID),
        $this->get(CRM_Utils_Sort::SORT_DIRECTION)
      );
    }

    $selector = new CRM_Documents_Selector_Search($this->_queryParams,
      $this->_action,
      NULL,
      $this->_limit,
      $this->_context
    );

    $controller = new CRM_Core_Selector_Controller($selector,
      $this->get(CRM_Utils_Pager::PAGE_ID),
      $sortID,
      CRM_Core_Action::VIEW,
      $this,
      CRM_Core_Selector_Controller::TRANSFER,
      NULL
    );

    $controller->setEmbedded(TRUE);
    $controller->moveFromSessionToTemplate();
  }

  function buildQuickForm() {
    $this->addElement('text',
      'sort_name',
      E::ts('Contact Name or Email'),
      CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Contact',
        'sort_name'
      )
    );

    CRM_Core_Form_Date::buildDateRange($this, 'document_date', 1, '_low', '_high', E::ts('From:'), FALSE);

    $this->add('text',
      'subject',
      E::ts('Subject'),
      TRUE
    );

    $this->add('text',
      'subject',
      E::ts('Subject'),
      TRUE
    );

    $types = CRM_Core_OptionGroup::values('document_type');
    $this->add('select', 'type_id', E::ts('Type'), $types, FALSE, ['class' => 'huge crm-select2', 'data-option-edit-path' => 'civicrm/admin/options/document_type', 'multiple' => true]);

    $statuses = CRM_Core_OptionGroup::values('document_status');
    $this->add('select', 'status_id', E::ts('Status'), $statuses, FALSE, ['class' => 'huge crm-select2', 'data-option-edit-path' => 'civicrm/admin/options/document_status', 'multiple' => true]);

    // add buttons
    $this->addButtons(array(
        array(
          'type' => 'refresh',
          'name' => E::ts('Search'),
          'isDefault' => TRUE,
        ),
      )
    );
  }

  /**
   * The post processing of the form gets done here.
   *
   * Key things done during post processing are
   *      - check for reset or next request. if present, skip post procesing.
   *      - now check if user requested running a saved search, if so, then
   *        the form values associated with the saved search are used for searching.
   *      - if user has done a submit with new values the regular post submissing is
   *        done.
   * The processing consists of using a Selector / Controller framework for getting the
   * search results.
   *
   * @param
   *
   * @return void
   * @access public
   */
  function postProcess() {
    if ($this->_done) {
      return;
    }

    $this->_done = TRUE;

    if (!empty($_POST)) {
      $this->_formValues = $this->controller->exportValues($this->_name);
    }

    $this->_queryParams = CRM_Contact_BAO_Query::convertFormValues($this->_formValues);

    $this->set('formValues', $this->_formValues);
    $this->set('queryParams', $this->_queryParams);

    $buttonName = $this->controller->getButtonName();
    if ($buttonName == $this->_actionButtonName) {
      // check actionName and if next, then do not repeat a search, since we are going to the next page

      // hack, make sure we reset the task values
      $stateMachine = $this->controller->getStateMachine();
      $formName = $stateMachine->getTaskFormName();
      $this->controller->resetPage($formName);
      return;
    }


    $sortID = NULL;
    if ($this->get(CRM_Utils_Sort::SORT_ID)) {
      $sortID = CRM_Utils_Sort::sortIDValue($this->get(CRM_Utils_Sort::SORT_ID),
        $this->get(CRM_Utils_Sort::SORT_DIRECTION)
      );
    }

    $selector = new CRM_Documents_Selector_Search($this->_queryParams,
      $this->_action,
      NULL,
      $this->_limit,
      $this->_context
    );
    $selector->setKey($this->controller->_key);

    /*$prefix = NULL;
    if ($this->_context == 'basic' || $this->_context == 'user') {
      $prefix = $this->_prefix;
    }*/

    $userContext = CRM_Utils_System::url('civicrm/documents/search', array(
      '_qf_Search_display'=>true,
      'qfKey' =>$this->controller->_key
    ));
    $session = CRM_Core_Session::singleton();
    $session->pushUserContext($userContext);

    $controller = new CRM_Core_Selector_Controller($selector,
      $this->get(CRM_Utils_Pager::PAGE_ID),
      $sortID,
      CRM_Core_Action::VIEW,
      $this,
      CRM_Core_Selector_Controller::SESSION,
      NULL
    );
    $controller->setEmbedded(TRUE);

    $controller->run();
  }

}
