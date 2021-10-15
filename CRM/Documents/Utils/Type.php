<?php
/**
 * @author Jaap Jansma <jaap.jansma@civicoop.org>
 * @license AGPL-3.0
 */

class CRM_Documents_Utils_Type {

  public static function getTypes() {
    $optionValues = civicrm_api3('OptionValue', 'get', ['option_group_id' => 'document_type', 'options' => ['limit' => 0]]);
    $return = [];
    foreach ($optionValues['values'] as $optionValue) {
      $return[$optionValue['value']] = $optionValue['label'];
    }
    return $return;
  }

}
