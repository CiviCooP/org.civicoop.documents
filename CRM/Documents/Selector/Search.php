<?php

/**
 * This class is used to retrieve and display a range of
 * contacts that match the given criteria (specifically for
 * results of advanced search options.
 *
 */
class CRM_Documents_Selector_Search extends CRM_Core_Selector_Base implements CRM_Core_Selector_API {


  /**
   * we use desc to remind us what that column is, name is used in the tpl
   *
   * @var array
   * @static
   */
  static $_columnHeaders;

  /**
   * are we restricting ourselves to a single contact
   *
   * @access protected
   * @var boolean
   */
  protected $_limit = NULL;

  /**
   * queryParams is the array returned by exportValues called on
   * the HTML_QuickForm_Controller for that page.
   *
   * @var array
   * @access protected
   */
  public $_queryParams;

  /**
   * represent the type of selector
   *
   * @var int
   * @access protected
   */
  protected $_action;
  
  /**
   * the where clause
   *
   * @var array
   */
  public $_where;


  /**
   * Class constructor
   *
   * @param array $queryParams array of parameters for query
   * @param int   $action - action of search basic or advanced.
   * @param int     $limit  how many documents do we want returned
   *
   * @return CRM_Contact_Selector
   * @access public
   */
  function __construct(&$queryParams,
    $action             = CRM_Core_Action::NONE,
    $limit              = NULL
  ) {

    // submitted form values
    $this->_queryParams = &$queryParams;

    $this->_limit       = $limit;


    // type of selector
    $this->_action = $action;
    $this->_where = array();
  }
  //end of constructor
  
  function select() {
    return "SELECT
      `doc`.`id` as `id`,
      '' as `contacts`,
      `doc`.`subject` as `subject`,
      `doc`.`date_added` as `date_added`,
      `doc`.`added_by` as `added_by`,
      `doc`.`date_updated` as `date_updated`,
      `doc`.`updated_by` as `updated_by`";
  }
  
  function from() {
    return "FROM `civicrm_document` `doc` 
      LEFT JOIN `civicrm_document_contact` `doc_contact` ON `doc`.`id` = `doc_contact`.`document_id`
      LEFT JOIN `civicrm_contact` `contact_a` ON `doc_contact`.`contact_id` = `contact_a`.`id`
      LEFT JOIN `civicrm_email` ON `contact_a`.`id` = `civicrm_email`.`contact_id`";
  }
  
  function where() {
    $this->where[0] = array();
    //var_dump($this->_queryParams); exit();
    if (!empty($this->_queryParams)) {
      foreach (array_keys($this->_queryParams) as $id) {
        if (!CRM_Utils_Array::value(0, $this->_queryParams[$id])) {
          continue;
        }
        $this->whereClauseSingle($this->_queryParams[$id]);
      }
    }
    
    
    $clauses = array();
    $andClauses[] = "1";

    if (!empty($this->_where)) {
      foreach ($this->_where as $grouping => $values) {
        if ($grouping > 0 && !empty($values)) {
          $clauses[$grouping] = ' ( ' . implode(" AND ", $values) . ' ) ';
        }
      }

      if (!empty($this->_where[0])) {
        $andClauses[] = ' ( ' . implode(" AND ", $this->_where[0]) . ' ) ';
      }
      if (!empty($clauses)) {
        $andClauses[] = ' ( ' . implode(' OR ', $clauses) . ' ) ';
      }
    }

    return ' WHERE '.implode(' AND ', $andClauses). ' ';
    
  }
  
  function whereClauseSingle(&$values) {
    list($name, $op, $value, $grouping, $wildcard) = $values;
    
    switch ($values[0]) {
      /*case 'tag':
      case 'contact_tags':
        $this->tag($values);
        return;
        break;*/
      case 'sort_name':
      case 'display_name':
        $this->sortName($values);
        return;
        break;
      case 'document_date':
      case 'document_date_low':
      case 'document_date_high':
        $this->dateQueryBuilder($values, 'doc', 'document_date', 'date_updated');
        return;
        break;
      case 'subject':
        $n = trim($value);
        $value = strtolower(CRM_Core_DAO::escapeString($n));
        if ($wildcard) {
          if (strpos($value, '%') !== FALSE) {
            // only add wild card if not there
            $value = "'$value'";
          }
          else {
            $value = "'%$value%'";
          }
          $op = 'LIKE';
        }
        else {
          $value = "'$value'";
        }
        $wc = ($op != 'LIKE') ? "LOWER(doc.subject)" : "doc.subject";
        $this->_where[$grouping][] = " $wc $op $value";
        return;
        break;
    }
  }
  
  function groupBy() {
    //return "";
    return "GROUP BY `doc`.`id`";
  }

  /**
   * getter for array of the parameters required for creating pager.
   *
   * @param
   * @access public
   */
  function getPagerParams($action, &$params) {
    $params['status'] = ts('Documents') . ' %%StatusMessage%%';
    $params['csvString'] = NULL;
    if ($this->_limit) {
      $params['rowCount'] = $this->_limit;
    }
    else {
      $params['rowCount'] = CRM_Utils_Pager::ROWCOUNT;
    }

    $params['buttonTop'] = 'PagerTopButton';
    $params['buttonBottom'] = 'PagerBottomButton';
  }
  //end of function

  /**
   * Returns total number of rows for the query.
   *
   * @param
   *
   * @return int Total number of rows
   * @access public
   */
  function getTotalCount($action) {
    $sql = "SELECT COUNT(DISTINCT `doc`.`id`) as `total` ".$this->from().$this->where();
    $dao = CRM_Core_DAO::executeQuery($sql);
    if ($dao->fetch()) {
      return $dao->total;
    }
    return 0;
  }

  /**
   * returns all the rows in the given offset and rowCount
   *
   * @param enum   $action   the action being performed
   * @param int    $offset   the row number to start from
   * @param int    $rowCount the number of rows to return
   * @param string $sort     the sql string that describes the sort order
   * @param enum   $output   what should the result set include (web/email/csv)
   *
   * @return int   the total number of rows for this action
   */
  function &getRows($action, $offset, $rowCount, $sort, $output = NULL) {    
    $sql = $this->select().$this->from() . $this->where() . $this->groupBy();
    $result = CRM_Core_DAO::executeQuery($sql);
    
    // process the result of the query
    $rows = array();

    $session = CRM_Core_Session::singleton();
    $cid = $session->get('userID');
    
    $repo = CRM_Documents_Entity_DocumentRepository::singleton();
    While ($result->fetch()) {
      $doc = $repo->getDocumentById($result->id);
          
      $row = array();
      $row['document_id'] = $doc->getId();
      $row['subject'] = $doc->getSubject();
      $row['date_added'] = $doc->getFormattedDateAdded();
      $row['added_by'] = $doc->getFormattedAddedBy();
      $row['updated_by'] = $doc->getFormattedUpdatedBy();
      $row['date_updated'] = $doc->getFormattedDateUpdated();
    
      $row['contacts'] = $doc->getFormattedContacts();
      $row['doc'] = $doc;
      $row['cid'] = $cid;
      
      $row['checkbox'] = CRM_Core_Form::CB_PREFIX . $doc->getId();

      $rows[] = $row;
    }

    return $rows;
  }

  /**
   * returns the column headers as an array of tuples:
   * (name, sortName (key to the sort array))
   *
   * @param string $action the action being performed
   * @param enum   $output what should the result set include (web/email/csv)
   *
   * @return array the column headers that need to be displayed
   * @access public
   */
  public function &getColumnHeaders($action = NULL, $output = NULL) {
    if (!isset(self::$_columnHeaders)) {
      self::$_columnHeaders = array(
        array(
          'name' => ts('Document ID'),
          'field' => 'document_id',
        ),
        array(
          'name' => ts('Contacts'),
          'field'      => 'contacts',
        ),
        array(
          'name' => ts('Subject'),
          'field' => 'subject',
        ),
        array(
          'name' => ts('Date added'),
          'field' => 'date_added',
        ),
        array(
          'name' => ts('Added by'),
          'field' => 'added by',
        ),
        array(
          'name' => ts('Date updated'),
          'field' => 'date_updated',
        ),
        array(
          'name' => ts('Updated by'),
          'field' => 'updated_by',
        ),
      );

    }
    return self::$_columnHeaders;
  }

  /**
   * name of export file.
   *
   * @param string $output type of output
   *
   * @return string name of the file
   */
  function getExportFileName($output = 'csv') {
    return ts('CiviCRM Documents Search');
  }
  
  function sortName(&$values) {
    list($name, $op, $value, $grouping, $wildcard) = $values;

    // handle IS NULL / IS NOT NULL / IS EMPTY / IS NOT EMPTY
    if ( $this->nameNullOrEmptyOp( $name, $op, $grouping ) ) {
      return;
    }

    $newName = $name;
    $name = trim($value);

    if (empty($name)) {
      return;
    }

    $config = CRM_Core_Config::singleton();

    $sub = array();

    //By default, $sub elements should be joined together with OR statements (don't change this variable).
    $subGlue = ' OR ';

    $strtolower = function_exists('mb_strtolower') ? 'mb_strtolower' : 'strtolower';
    $locationType = CRM_Core_PseudoConstant::get('CRM_Core_DAO_Address', 'location_type_id');

    if (substr($name, 0, 1) == '"' &&
      substr($name, -1, 1) == '"'
    ) {
      //If name is encased in double quotes, the value should be taken to be the string in entirety and the
      $value = substr($name, 1, -1);
      $value = $strtolower(CRM_Core_DAO::escapeString($value));
      $wc = ($newName == 'sort_name') ? 'LOWER(contact_a.sort_name)' : 'LOWER(contact_a.display_name)';
      $sub[] = " ( $wc = '$value' ) ";
      if ($config->includeEmailInName) {
        $sub[] = " ( civicrm_email.email = '$value' ) ";
      }
    }
    elseif (strpos($name, ',') !== FALSE) {
      // if we have a comma in the string, search for the entire string
      $value = $strtolower(CRM_Core_DAO::escapeString($name));
      if ($wildcard) {
        if ($config->includeWildCardInName) {
          $value = "'%$value%'";
        }
        else {
          $value = "'$value%'";
        }
        $op = 'LIKE';
      }
      else {
        $value = "'$value'";
      }
      if ($newName == 'sort_name') {
        $wc = self::caseImportant($op) ? "LOWER(contact_a.sort_name)" : "contact_a.sort_name";
      }
      else {
        $wc = self::caseImportant($op) ? "LOWER(contact_a.display_name)" : "contact_a.display_name";
      }
      $sub[] = " ( $wc $op $value )";
      if ($config->includeNickNameInName) {
        $wc = self::caseImportant($op) ? "LOWER(contact_a.nick_name)" : "contact_a.nick_name";
        $sub[] = " ( $wc $op $value )";
      }
      if ($config->includeEmailInName) {
        $sub[] = " ( civicrm_email.email $op $value ) ";
      }
    }
    else {
      // the string should be treated as a series of keywords to be matched with match ANY OR
      // match ALL depending on Civi config settings (see CiviAdmin)

      // The Civi configuration setting can be overridden if the string *starts* with the case
      // insenstive strings 'AND:' or 'OR:'TO THINK ABOUT: what happens when someone searches
      // for the following "AND: 'a string in quotes'"? - probably nothing - it would make the
      // AND OR variable reduntant because there is only one search string?

      // Check to see if the $subGlue is overridden in the search text
      if (strtolower(substr($name, 0, 4)) == 'and:') {
        $name = substr($name, 4);
        $subGlue = ' AND ';
      }
      if (strtolower(substr($name, 0, 3)) == 'or:') {
        $name = substr($name, 3);
        $subGlue = ' OR ';
      }

      $firstChar = substr($name, 0, 1);
      $lastChar = substr($name, -1, 1);
      $quotes = array("'", '"');
      if ((strlen($name) > 2) && in_array($firstChar, $quotes) &&
        in_array($lastChar, $quotes)
      ) {
        $name = substr($name, 1);
        $name = substr($name, 0, -1);
        $pieces = array($name);
      }
      else {
        $pieces = explode(' ', $name);
      }
      foreach ($pieces as $piece) {
        $value = $strtolower(CRM_Core_DAO::escapeString(trim($piece)));
        if (strlen($value)) {
          // Added If as a sanitization - without it, when you do an OR search, any string with
          // double spaces (i.e. "  ") or that has a space after the keyword (e.g. "OR: ") will
          // return all contacts because it will include a condition similar to "OR contact
          // name LIKE '%'".  It might be better to replace this with array_filter.
          $fieldsub = array();
          if ($wildcard) {
            if ($config->includeWildCardInName) {
              $value = "'%$value%'";
            }
            else {
              $value = "'$value%'";
            }
            $op = 'LIKE';
          }
          else {
            $value = "'$value'";
          }
          if ($newName == 'sort_name') {
            $wc = self::caseImportant($op) ? "LOWER(contact_a.sort_name)" : "contact_a.sort_name";
          }
          else {
            $wc = self::caseImportant($op) ? "LOWER(contact_a.display_name)" : "contact_a.display_name";
          }
          $fieldsub[] = " ( $wc $op $value )";
          if ($config->includeNickNameInName) {
            $wc = self::caseImportant($op) ? "LOWER(contact_a.nick_name)" : "contact_a.nick_name";
            $fieldsub[] = " ( $wc $op $value )";
          }
          if ($config->includeEmailInName) {
            $fieldsub[] = " ( civicrm_email.email $op $value ) ";
          }
          $sub[] = ' ( ' . implode(' OR ', $fieldsub) . ' ) ';
          // I seperated the glueing in two.  The first stage should always be OR because we are searching for matches in *ANY* of these fields
        }
      }
    }

    $sub = ' ( ' . implode($subGlue, $sub) . ' ) ';

    $this->_where[$grouping][] = $sub;
  }
  
  function nameNullOrEmptyOp($name, $op, $grouping) {
    switch ( $op ) {
      case 'IS NULL':
      case 'IS NOT NULL':
        $this->_where[$grouping][] = "contact_a.$name $op";
        return true;

      case 'IS EMPTY':
        $this->_where[$grouping][] = "(contact_a.$name IS NULL OR contact_a.$name = '')";
        return true;

      case 'IS NOT EMPTY':
        $this->_where[$grouping][] = "(contact_a.$name IS NOT NULL AND contact_a.$name <> '')";
        return true;

      default:
        return false;
    }
  }
  
  static function caseImportant( $op ) {
    return
      in_array($op, array('LIKE', 'IS NULL', 'IS NOT NULL', 'IS EMPTY', 'IS NOT EMPTY')) ? FALSE : TRUE;
  }
  
  function dateQueryBuilder(
    &$values, $tableName, $fieldName,
    $dbFieldName,
    $appendTimeStamp = TRUE
  ) {
    list($name, $op, $value, $grouping, $wildcard) = $values;

    if (!$value) {
      return;
    }

    if ($name == "{$fieldName}_low" ||
      $name == "{$fieldName}_high"
    ) {
      if (isset($this->_rangeCache[$fieldName])) {
        return;
      }
      $this->_rangeCache[$fieldName] = 1;

      $secondOP = $secondPhrase = $secondValue = $secondDate = $secondDateFormat = NULL;

      if ($name == $fieldName . '_low') {
        $firstOP = '>=';
        $firstPhrase = ts('greater than or equal to');
        $firstDate = CRM_Utils_Date::processDate($value);

        $secondValues = $this->getWhereValues("{$fieldName}_high", $grouping);
        if (!empty($secondValues) && $secondValues[2]) {
          $secondOP = '<=';
          $secondPhrase = ts('less than or equal to');
          $secondValue = $secondValues[2];

          if ($appendTimeStamp && strlen($secondValue) == 10) {
            $secondValue .= ' 23:59:59';
          }
          $secondDate = CRM_Utils_Date::processDate($secondValue);
        }
      }
      elseif ($name == $fieldName . '_high') {
        $firstOP = '<=';
        $firstPhrase = ts('less than or equal to');

        if ($appendTimeStamp && strlen($value) == 10) {
          $value .= ' 23:59:59';
        }
        $firstDate = CRM_Utils_Date::processDate($value);

        $secondValues = $this->getWhereValues("{$fieldName}_low", $grouping);
        if (!empty($secondValues) && $secondValues[2]) {
          $secondOP = '>=';
          $secondPhrase = ts('greater than or equal to');
          $secondValue = $secondValues[2];
          $secondDate = CRM_Utils_Date::processDate($secondValue);
        }
      }

      if (!$appendTimeStamp) {
        $firstDate = substr($firstDate, 0, 8);
      }

      if ($secondDate) {
        if (!$appendTimeStamp) {
          $secondDate = substr($secondDate, 0, 8);
        }
        $secondDateFormat = CRM_Utils_Date::customFormat($secondDate);
      }

      if ($secondDate) {
        $this->_where[$grouping][] = "
( {$tableName}.{$dbFieldName} $firstOP '$firstDate' ) AND
( {$tableName}.{$dbFieldName} $secondOP '$secondDate' )
";
      }
      else {
        $this->_where[$grouping][] = "{$tableName}.{$dbFieldName} $firstOP '$firstDate'";
      }
    }

    if ($name == $fieldName) {
      // $op = '=';
      $date = CRM_Utils_Date::processDate($value);

      if (!$appendTimeStamp) {
        $date = substr($date, 0, 8);
      }

      if ($date) {
        $this->_where[$grouping][] = "{$tableName}.{$dbFieldName} $op '$date'";
      }
      else {
        $this->_where[$grouping][] = "{$tableName}.{$dbFieldName} $op";
      }
    }
  }
  
  function &getWhereValues($name, $grouping) {
    $result = NULL;
    foreach ($this->_queryParams as $values) {
      if ($values[0] == $name && $values[3] == $grouping) {
        return $values;
      }
    }

    return $result;
  }

}

