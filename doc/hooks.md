# Available hooks

## hook_documents_get_status

This hook is called the retrieve the status of a document. A status indicates if a document is (un)used and therefore should be kept or could be deleted

**Spec**

    hook_documents_get_status($doc, &$status);

**Parameters**

- `$doc` the document
- `$status` The returned status is 0 (for doc is not in use) or 1 (document is in use).

**Return value**

The return value is passed by the parameter status.

## hook_civicrm_documents_entity_ref_spec

This hook returns specifications for linking a document to an entity (e.g. a case or an activity etc...)

**Spec**

    hook_civicrm_documents_entity_ref_spec();

**Parameters**

*None*

**Return value**

Returns an array with instances of classes which implements the interface ~CRM_Documents_Interface_EntityRefSpec~

**Example**

*Entity ref spec for projects*
    
    class CRM_ThreepeasDocuments_ProjectRefSpec implements CRM_Documents_Interface_EntityRefSpec {

      public function getSystemName() {
        return 'project';
      }

      public function getHumanName() {
        return 'Project';
      }

      public function getBAO() {
        'CRM_Threepeas_BAO_PumProject';
      }

      public function getEntityTableName() {
        return 'civicrm_project';
      }

      public function getActiveEntities() {
        $projects = CRM_Threepeas_BAO_PumProject::getValues(array('is_active' => '1'));
        $return = array();
        foreach($projects as $project) {
          $return[$project['id']] = $project['title'];
        }
        return $return;
      }

      public function getEntityLabelByEntityId($entity_id) {
        $dao = new CRM_Threepeas_BAO_PumProject();
        $dao->id = $entity_id;
        if ($dao->find(true)) {
          return $dao->title;
        }
        return false;
      }
      
      public function isSingleEntity() {
        return false;
      }

    }


*The hook*

    function mymodule_civicrm_documents_entity_ref_spec() {
        return array('project' => new CRM_ThreepeasDocuments_ProjectRefSpec());
    }