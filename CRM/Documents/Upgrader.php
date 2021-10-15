<?php

use CRM_Documents_ExtensionUtil as E;

/**
 * Collection of upgrade steps
 */
class CRM_Documents_Upgrader extends CRM_Documents_Upgrader_Base {

  // By convention, functions that look like "function upgrade_NNNN()" are
  // upgrade tasks. They are executed in order (like Drupal's hook_update_N).

  /**
   * Install the Document activity (if it doesn't exist already
   */
  public function install() {
    civicrm_api3('OptionValue', 'create', [
      'option_group_id' => "cg_extend_objects",
      'label' => E::ts('Document'),
      'value' => 'Document',
      'name' => 'civicrm_document',
      'description' => 'CRM_Document_Utils_Type::getTypes;',
    ]);
    civicrm_api3('OptionValue', 'create', [
      'option_group_id' => "cg_extend_objects",
      'label' => E::ts('Document Version'),
      'value' => 'DocumentVersion',
      'name' => 'civicrm_document_version',
      'description' => 'CRM_Document_Utils_Type::getTypes;',
    ]);

    $statusOptionGroupId = civicrm_api3('OptionGroup', 'create', ['name' => 'document_status', 'title' => E::ts('Document Status')]);
    $statusOptionGroupId = $statusOptionGroupId['id'];
    civicrm_api3('OptionValue', 'create', ['value' => 1, 'is_default' => '1', 'name' => 'Submitted', 'label' => E::ts('Submitted'), 'option_group_id' => $statusOptionGroupId]);
    civicrm_api3('OptionValue', 'create', ['value' => 2, 'name' => 'Approved', 'label' => E::ts('Approved'), 'option_group_id' => $statusOptionGroupId]);
    civicrm_api3('OptionValue', 'create', ['value' => 3, 'name' => 'Rejected', 'label' => E::ts('Rejected'), 'option_group_id' => $statusOptionGroupId]);
    civicrm_api3('OptionValue', 'create', ['value' => 4, 'name' => 'Draft', 'label' => E::ts('Draft'), 'option_group_id' => $statusOptionGroupId]);

    $typeOptionGroupId = civicrm_api3('OptionGroup', 'create', ['name' => 'document_type', 'title' => E::ts('Document Type')]);
    $typeOptionGroupId = $typeOptionGroupId['id'];
    civicrm_api3('OptionValue', 'create', ['value' => 1, 'is_default' => '1', 'name' => 'General', 'label' => E::ts('General'), 'option_group_id' => $typeOptionGroupId]);
  }

  public function uninstall() {
    $this->removeCustomExtend('DocumentVersion');
    $this->removeCustomExtend('Document');
    $this->removeOptionGroup('document_status');
    $this->removeOptionGroup('document_type');
  }


  /**
   * Example: Run an external SQL script
   *
   * @return TRUE on success
   * @throws Exception
   */
  public function upgrade_1001() {
    $this->ctx->log->info('Applying update 1001');
    // this path is relative to the extension base dir
    $this->executeSqlFile('sql/upgrade_1001.sql');
    return TRUE;
  } //

  /**
   * Example: Run an external SQL script
   *
   * @return TRUE on success
   * @throws Exception
   */
  public function upgrade_1002() {
    $this->ctx->log->info('Applying update 1002');
    // this path is relative to the extension base dir
    $this->executeSqlFile('sql/upgrade_1002.sql');
    return TRUE;
  } //

  /**
   * Example: Run an external SQL script
   *
   * @return TRUE on success
   * @throws Exception
   */
  public function upgrade_1003() {
    $this->ctx->log->info('Applying update 1003');
    // this path is relative to the extension base dir
    $this->executeSqlFile('sql/upgrade_1003.sql');
    return TRUE;
  } //

  /**
   * Example: Run an external SQL script
   *
   * @return TRUE on success
   * @throws Exception
   */
  public function upgrade_1004() {
    $this->ctx->log->info('Applying update 1004');
    // this path is relative to the extension base dir
    $this->executeSqlFile('sql/upgrade_1004.sql');
    return TRUE;
  } //

  /**
   * Example: Run an external SQL script
   *
   * @return TRUE on success
   * @throws Exception
   */
  public function upgrade_1005() {
    $this->ctx->log->info('Applying update 1005');
    // this path is relative to the extension base dir
    $this->executeSqlFile('sql/upgrade_1005.sql');

    civicrm_api3('OptionValue', 'create', [
      'option_group_id' => "cg_extend_objects",
      'label' => E::ts('Document'),
      'value' => 'Document',
      'name' => 'civicrm_document',
      'description' => 'CRM_Document_Utils_Type::getTypes;',
    ]);
    civicrm_api3('OptionValue', 'create', [
      'option_group_id' => "cg_extend_objects",
      'label' => E::ts('Document Version'),
      'value' => 'DocumentVersion',
      'name' => 'civicrm_document_version',
      'description' => 'CRM_Document_Utils_Type::getTypes;',
    ]);

    $statusOptionGroupId = civicrm_api3('OptionGroup', 'create', ['name' => 'document_status', 'title' => E::ts('Document Status')]);
    $statusOptionGroupId = $statusOptionGroupId['id'];
    civicrm_api3('OptionValue', 'create', ['value' => 1, 'is_default' => '1', 'name' => 'Submitted', 'label' => E::ts('Submitted'), 'option_group_id' => $statusOptionGroupId]);
    civicrm_api3('OptionValue', 'create', ['value' => 2, 'name' => 'Approved', 'label' => E::ts('Approved'), 'option_group_id' => $statusOptionGroupId]);
    civicrm_api3('OptionValue', 'create', ['value' => 3, 'name' => 'Rejected', 'label' => E::ts('Rejected'), 'option_group_id' => $statusOptionGroupId]);
    civicrm_api3('OptionValue', 'create', ['value' => 4, 'name' => 'Draft', 'label' => E::ts('Draft'), 'option_group_id' => $statusOptionGroupId]);

    $typeOptionGroupId = civicrm_api3('OptionGroup', 'create', ['name' => 'document_type', 'title' => E::ts('Document Type')]);
    $typeOptionGroupId = $typeOptionGroupId['id'];
    civicrm_api3('OptionValue', 'create', ['value' => 1, 'is_default' => '1', 'name' => 'General', 'label' => E::ts('General'), 'option_group_id' => $typeOptionGroupId]);

    return TRUE;
  }

  protected function removeOptionGroup($name) {
    $optionGroupId = civicrm_api3('OptionGroup', 'getvalue', ['return' => 'id', 'name' => $name]);
    $optionValues = civicrm_api3('OptionValue', 'get', ['option_group_id' => $optionGroupId, 'options' => ['limit' => 0]]);
    foreach($optionValues['values'] as $optionValue) {
      civicrm_api3('OptionValue', 'delete', ['id' => $optionValue['id']]);
    }
    civicrm_api3('OptionGroup', 'delete', ['id' => $optionGroupId]);
  }

  protected function removeCustomExtend($entity) {
    $customGroups = civicrm_api3('CustomGroup', 'get', [
      'extends' => $entity,
      'options' => ['limit' => 0],
    ]);
    foreach($customGroups['values'] as $customGroup) {
      $customFields = civicrm_api3('CustomField', 'get', [
        'custom_group_id' => $customGroup['id'],
        'options' => ['limit' => 0],
      ]);
      foreach($customFields['values'] as $customField) {
        civicrm_api3('CustomField', 'delete', ['id' => $customField['id']]);
      }
      civicrm_api3('CustomGroup', 'delete', ['id' => $customGroup['id']]);
    }
    $cgExtendOptionId = civicrm_api3('OptionValue', 'getvalue', [
      'option_group_id' => "cg_extend_objects",
      'value' => $entity,
      'return' => 'id',
    ]);
    civicrm_api3('OptionValue', 'delete', ['id' => $cgExtendOptionId]);
  }

}
