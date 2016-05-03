<?php
/**
 * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 */

class CRM_Documents_Utils_CiviVersion {

  /**
   * Returns whether the civi version is less or eq to 4.4
   * @return bool
   */
  public static function is44() {
    $civiVersion = CRM_Core_BAO_Domain::version();

    if (version_compare($civiVersion, '4.5', '<')) {
      return true;
    }
    return false;
  }

}