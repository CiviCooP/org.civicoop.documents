<?php

/**
 * A custom contact search
 */
class CRM_Documents_Form_Search_DocumentSearch extends CRM_Contact_Form_Search_Custom_Base implements CRM_Contact_Form_Search_Interface {
  
  function __construct(&$formValues) {
    parent::__construct($formValues);
  }

  /**
   * Prepare a set of search fields
   *
   * @param CRM_Core_Form $form modifiable
   * @return void
   */
  function buildForm(&$form) {
    CRM_Utils_System::setTitle(ts('Search documents'));

    $form->add('text',
      'subject',
      ts('Subject'),
      TRUE
    );

    /*$stateProvince = array('' => ts('- any state/province -')) + CRM_Core_PseudoConstant::stateProvince();
    $form->addElement('select', 'state_province_id', ts('State/Province'), $stateProvince);*/

    // Optionally define default search values
    $form->setDefaults(array(
      'subject' => '',
    ));

    /**
     * if you are using the standard template, this array tells the template what elements
     * are part of the search criteria
     */
    $form->assign('elements', array('subject'));
    
    CRM_Contact_Form_NewContact::buildQuickForm($form);
    
    //push the current context
    /*$url = CRM_Utils_System::currentPath();
    $session = CRM_Core_Session::singleton();
    $session->pushUserContext($url);*/
  }

  /**
   * Get a list of summary data points
   *
   * @return mixed; NULL or array with keys:
   *  - summary: string
   *  - total: numeric
   */
  function summary() {
    return NULL;
    // return array(
    //   'summary' => 'This is a summary',
    //   'total' => 50.0,
    // );
  }
  
  function count() {
    return CRM_Core_DAO::singleValueQuery($this->sql('count(distinct doc.id) as total'));
  }

  /**
   * Get a list of displayable columns
   *
   * @return array, keys are printable column headers and values are SQL column names
   */
  function &columns() {
    // return by reference
    $columns = array(
      ts('Document ID') => 'id',
      ts('Contacts') => 'contacts',
      ts('Subject') => 'subject',
      ts('Date added') => 'date_added',
      ts('Added by') => 'added_by',
      ts('Date updated') => 'date_updated',
      ts('Updated by') => 'updated_by'
    );
    return $columns;
  }

  /**
   * Construct a full SQL query which returns one page worth of results
   *
   * @return string, sql
   */
  function all($offset = 0, $rowcount = 0, $sort = NULL, $includeContactIDs = FALSE, $justIDs = FALSE) {
    // delegate to $this->sql(), $this->select(), $this->from(), $this->where(), etc.
    return $this->sql($this->select(), $offset, $rowcount, $sort, $includeContactIDs, NULL);
  }

  /**
   * Construct a SQL SELECT clause
   *
   * @return string, sql fragment with SELECT arguments
   */
  function select() {
    return "
      DISTINCT `doc`.`id` as `id`,
      '' as `contacts`,
      `doc`.`subject` as `subject`,
      `doc`.`date_added` as `date_added`,
      `doc`.`added_by` as `added_by`,
      `doc`.`date_updated` as `date_updated`,
      `doc`.`updated_by` as `updated_by`,
      `contact_a`.`id` as `contact_id`
    ";
  }

  /**
   * Construct a SQL FROM clause
   *
   * @return string, sql fragment with FROM and JOIN clauses
   */
  function from() {
    return "
      FROM `civicrm_document` `doc` 
      INNER JOIN `civicrm_document_contact` `doc_contact` ON `doc`.`id` = `doc_contact`.`document_id`
      LEFT JOIN `civicrm_contact` `contact_a` ON `doc_contact`.`contact_id` = `contact_a`.`id`
    ";
  }

  /**
   * Construct a SQL WHERE clause
   *
   * @return string, sql fragment with conditional expressions
   */
  function where($includeContactIDs = FALSE) {
    $params = array();
    $where = " 1 ";
    
    $count  = 1;
    $clause = array();
    $subject   = CRM_Utils_Array::value('subject', $this->_formValues);
    if ($subject != NULL) {
      if (strpos($subject, '%') === FALSE) {
        $subject = "%{$subject}%";
      }
      $params[$count] = array($subject, 'String');
      $clause[] = "doc.subject LIKE %{$count}";
      $count++;
    }
    
    if (isset($this->_formValues['contact'][1]) && strlen($this->_formValues['contact'][1])) {
      $contactIds = $this->_formValues['contact'][1];
      $clause[] = "(doc_contact.contact_id IN (".$contactIds.") OR doc.added_by IN (".$contactIds.") OR doc.updated_by IN (".$contactIds."))";
    }

    if (!empty($clause)) {
      $where .= ' AND ' . implode(' AND ', $clause);
    }

    return $this->whereClause($where, $params);
  }

  /**
   * Determine the Smarty template for the search screen
   *
   * @return string, template path (findable through Smarty template path)
   */
  function templateFile() {
    return 'CRM/Documents/Form/Search/Custom.tpl';
  }

  /**
   * Modify the content of each row
   *
   * @param array $row modifiable SQL result row
   * @return void
   */
  function alterRow(&$row) {
    
    $session = CRM_Core_Session::singleton();
    $cid = $session->get('userID');
    
    $doc = CRM_Documents_Entity_ArrayToDocumentConverter::convert($row);
    
    $row['date_added'] = $doc->getFormattedDateAdded();
    $row['added_by'] = $doc->getFormattedAddedBy();
    $row['updated_by'] = $doc->getFormattedUpdatedBy();
    $row['date_updated'] = $doc->getFormattedDateUpdated();
    
    $row['contacts'] = $doc->getFormattedContacts();
    $row['doc'] = $doc;
    $row['cid'] = $cid;
    
  }
}
