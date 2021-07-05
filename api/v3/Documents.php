<?php

use CRM_Documents_ExtensionUtil as E;

/**
 * Requirement to execute the API Documents Import
 *
 * @param array $params
 * @return void
 */
function _civicrm_api3_documents_import_spec(array &$params) : void {
  $params['subject'] = [
    'type' => CRM_Utils_Type::T_STRING,
    'title' => E::ts('Subject'),
    'api.required' => 'TRUE',
  ];

  $params['custom_field_id'] = [
    'type' => CRM_Utils_Type::T_INT,
    'title' => E::ts('Custom Field ID'),
    'api.required' => 'TRUE',
  ];
}

/**
 * Define API Documents Import
 *
 * @param array $params
 * @return array
 */
function civicrm_api3_documents_import(array $params) : array {

  $customFieldId = $params['custom_field_id'];
  $subject = $params['subject'];

  try{
    $customField = civicrm_api3('CustomField', 'getSingle', [
      'id' => $customFieldId,
      'api.CustomGroup.getSingle' => [],
    ]);
  }
  catch(Exception $e){
    return civicrm_api3_create_error(E::ts("The parameter custom_field_id does not exists"), $params);
  }

  if($customField['data_type'] != "File"){
    return civicrm_api3_create_error(E::ts("The parameter custom_field_id is not type File"), $params);
  }
  
  $extends = $customField['api.CustomGroup.getSingle']['extends'];

  $availableEntities = [
    'Individual' => 'Contact',
    'Organization' => 'Contact',
    'Household' => 'Contact',
    'Contact' => 'Contact',
    'Participant' => 'Participant',
    'Contribution' => 'Contribution',
    'Membership' => 'Membership',
  ];

  if (!isset($availableEntities[$extends])) {    
    return civicrm_api3_create_error(E::ts("The entity isn't implemented yet: ") . $extends, $params);
  }

  $entity = $availableEntities[$extends];

  $valuesEntity = civicrm_api3($entity, 'get', [
    'return' => ["custom_{$customFieldId}"],
    "custom_{$customFieldId}" => ['IS NOT NULL' => 1],
    'options' => ['limit' => 0],
  ]);

  $count = 0;
  foreach ($valuesEntity['values'] as $valueEntity) {
    $file_ori = civicrm_api3('File', 'getSingle', [
      'sequential' => 1,
      'id' => $valueEntity["custom_{$customFieldId}"]['fid'],
    ]);

    $filePath = Civi::paths()->getPath("[civicrm.files]/custom/{$file_ori['uri']}");
    $newPath = CRM_Utils_File::duplicate($filePath);

    $repositoryDocument = CRM_Documents_Entity_DocumentRepository::singleton();
    $document = new CRM_Documents_Entity_Document();
    $document->setSubject($subject);
    $document->setContactIds([$valueEntity['contact_id']]);
    $versionDocument = $document->getCurrentVersion();
    $versionDocument->setDescription($subject);
    $repositoryDocument->persist($document);

    $valuesFile = [
      'attachFile_1' => [
        'uri' => $newPath,
        'type' => $file_ori['mime_type'],
        'location' => $newPath
        ]
    ];

    $paramsFile = []; // Used for attachments

    // Add attachments as needed
    CRM_Core_BAO_File::formatAttachment($valuesFile,
      $paramsFile,
      'civicrm_document_version',
      $document->getCurrentVersion()->getId()
    );

    $paramsFile['attachFile_1']['uri'] = $newPath;
    $paramsFile['attachFile_1']['location'] = $newPath;

    CRM_Core_BAO_File::processAttachment($paramsFile, 'civicrm_document_version', $document->getCurrentVersion()->getId());
    $count++;
  }
  // ToDo better return of API
  return civicrm_api3_create_success(['count' => $count], $params, 'Documents', 'import');
}
