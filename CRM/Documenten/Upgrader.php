<?php

/**
 * Collection of upgrade steps
 */
class CRM_Documenten_Upgrader extends CRM_Documenten_Upgrader_Base {

  // By convention, functions that look like "function upgrade_NNNN()" are
  // upgrade tasks. They are executed in order (like Drupal's hook_update_N).
  
  protected $activity_type_group_id = false;

  /**
   * Install the Document activity (if it doesn't exist already
   */
  public function install() {
    $this->addActivityType('document', 'Document', array(
        'description' => 'Documenten opslag voor PUM',
        'is_reserved' => 1, 
        'is_active' => 1,
    ));
  }
  
  
  /**
   * Add an activity type to CiviCRM
   * 
   * @param String $name
   * @param String $label
   * @param (optional) array $params additional parameters for the activity type (e.g. 'reserved' => 1)
   * @return type
   */
  protected function addActivityType($name, $label, $params = array()) {
    //try {
      if ($this->activity_type_group_id === false) {
        $this->loadActivityTypeGroupId();
      }
      
      $checkParams['option_group_id'] = $this->activity_type_group_id;
      $checkParams['name'] = $name;
      $checkResult = civicrm_api3('OptionValue', 'get', $checkParams);
      if (isset($checkResult['id']) && $checkResult['id']) {
        //activity type exists, update this one
        $params['id'] = $checkResult['id'];
      } else {
         //if ID is set then unset the id parameter so that we create a new one
        if (isset($params['id'])) {
          unset($params['id']);
        }
      }
      $params['option_group_id'] = $this->activity_type_group_id;
      $params['name'] = $name;
      $params['label'] = $label;
      
      civicrm_api3('OptionValue', 'Create', $params);
      
    //} catch (Exception $ex) {
    //   return; 
   // }
  }
  
  /**
   * Get the id of the activity type option group
   * 
   * @throws Exception when api call fails
   */
  private function loadActivityTypeGroupId() {
    $result = civicrm_api3('OptionGroup', 'getsingle', array('name' => 'activity_type'));
    if (isset($result['id'])) {
      $this->activity_type_group_id = $result['id'];
    }
  }

  /**
   * Example: Run an external SQL script when the module is uninstalled
   *
  public function uninstall() {
   $this->executeSqlFile('sql/myuninstall.sql');
  }

  /**
   * Example: Run a simple query when a module is enabled
   *
  public function enable() {
    CRM_Core_DAO::executeQuery('UPDATE foo SET is_active = 1 WHERE bar = "whiz"');
  }

  /**
   * Example: Run a simple query when a module is disabled
   *
  public function disable() {
    CRM_Core_DAO::executeQuery('UPDATE foo SET is_active = 0 WHERE bar = "whiz"');
  }

  /**
   * Example: Run a couple simple queries
   *
   * @return TRUE on success
   * @throws Exception
   *
  public function upgrade_4200() {
    $this->ctx->log->info('Applying update 4200');
    CRM_Core_DAO::executeQuery('UPDATE foo SET bar = "whiz"');
    CRM_Core_DAO::executeQuery('DELETE FROM bang WHERE willy = wonka(2)');
    return TRUE;
  } // */


  /**
   * Example: Run an external SQL script
   *
   * @return TRUE on success
   * @throws Exception
  public function upgrade_4201() {
    $this->ctx->log->info('Applying update 4201');
    // this path is relative to the extension base dir
    $this->executeSqlFile('sql/upgrade_4201.sql');
    return TRUE;
  } // */


  /**
   * Example: Run a slow upgrade process by breaking it up into smaller chunk
   *
   * @return TRUE on success
   * @throws Exception
  public function upgrade_4202() {
    $this->ctx->log->info('Planning update 4202'); // PEAR Log interface

    $this->addTask(ts('Process first step'), 'processPart1', $arg1, $arg2);
    $this->addTask(ts('Process second step'), 'processPart2', $arg3, $arg4);
    $this->addTask(ts('Process second step'), 'processPart3', $arg5);
    return TRUE;
  }
  public function processPart1($arg1, $arg2) { sleep(10); return TRUE; }
  public function processPart2($arg3, $arg4) { sleep(10); return TRUE; }
  public function processPart3($arg5) { sleep(10); return TRUE; }
  // */


  /**
   * Example: Run an upgrade with a query that touches many (potentially
   * millions) of records by breaking it up into smaller chunks.
   *
   * @return TRUE on success
   * @throws Exception
  public function upgrade_4203() {
    $this->ctx->log->info('Planning update 4203'); // PEAR Log interface

    $minId = CRM_Core_DAO::singleValueQuery('SELECT coalesce(min(id),0) FROM civicrm_contribution');
    $maxId = CRM_Core_DAO::singleValueQuery('SELECT coalesce(max(id),0) FROM civicrm_contribution');
    for ($startId = $minId; $startId <= $maxId; $startId += self::BATCH_SIZE) {
      $endId = $startId + self::BATCH_SIZE - 1;
      $title = ts('Upgrade Batch (%1 => %2)', array(
        1 => $startId,
        2 => $endId,
      ));
      $sql = '
        UPDATE civicrm_contribution SET foobar = whiz(wonky()+wanker)
        WHERE id BETWEEN %1 and %2
      ';
      $params = array(
        1 => array($startId, 'Integer'),
        2 => array($endId, 'Integer'),
      );
      $this->addTask($title, 'executeSql', $sql, $params);
    }
    return TRUE;
  } // */

}
