<?php

function views_civicrm_documents_new_case_document($form, &$form_state, $case_id) {

    $form['#attributes'] = array('enctype' => "multipart/form-data");

    $form['case_id']['#type'] = 'hidden';
    $form['case_id']['#name'] = 'document_id';
    $form['case_id']['#value'] = $case_id;

    $form['subject'] = array(
        '#type' => 'textfield',
        '#required' => true,
        '#title' => t('Subject'),
    );

    $form['upload'] = array(
        '#type' => 'file',
        '#title' => t('File'),
        '#upload_validators' => array(
          'file_validate_extensions' => array('jpg png txt rtf odf pdf doc docx ppt pptx xls xlsx gz rar zip')
        )
    );

    $form['submit_button'] = array(
        '#type' => 'submit',
        '#value' => t('Save'),
    );

    $form['#validate'] = array(
      'views_civicrm_documents_new_case_document_validate'
    );

    $form_state['redirect'] = array(
        array('query' => drupal_get_destination()),
    );

    return $form;
}

function views_civicrm_documents_new_case_document_validate($form, &$form_state) {
  $file = file_save_upload('upload', $form['upload']['#upload_validators']);
  if ($file) {
    $form_state['upload']['file'] = $file;
  } else {
    form_set_error('upload', 'You should upload a document');
  }
}

function views_civicrm_documents_new_case_document_submit($form, &$form_state) {
    civicrm_initialize();
    $case_id = $form_state['values']['case_id'];
    $case = civicrm_api3('Case', 'getsingle', array("case_id"=>$case_id ));

    $repository = CRM_Documents_Entity_DocumentRepository::singleton();
    $document = new CRM_Documents_Entity_Document();
    $document->setSubject($form_state['values']['subject']);
    $document->addCaseid($case_id);
    $document->setContactIds($case['client_id']);
    $version = $document->getCurrentVersion();
    $version->setDescription($form_state['values']['subject']);
    $repository->persist($document);
    //save file upload
    //$file = file_load($form_state['values']['upload']);
    $file = $form_state['upload']['file'];
    $wrapper = file_stream_wrapper_get_instance_by_uri($file->uri);
    $filepath = $wrapper->realpath();
    CRM_Documents_Utils_File::copyFileToDocument($filepath, $file->filemime, $document);
}

function views_civicrm_documents_new_version($form, &$form_state, $document_id) {

    $form['#attributes'] = array('enctype' => "multipart/form-data");

    $form['document_id']['#type'] = 'hidden';
    $form['document_id']['#name'] = 'document_id';
    $form['document_id']['#value'] = $document_id;

    $form['description'] = array(
        '#type' => 'textfield',
        '#required' => true,
        '#title' => t('Description of new version'),
    );

    $form['upload'] = array(
        '#type' => 'file',
        '#title' => t('File'),
        '#upload_validators' => array(
          'file_validate_extensions' => array('jpg png txt rtf odf pdf doc docx ppt pptx xls xlsx gz rar zip')
        )
    );

    $form['submit_button'] = array(
        '#type' => 'submit',
        '#value' => t('Save'),
    );

    $form['#validate'] = array(
      'views_civicrm_documents_new_version_validate'
    );

    $form_state['redirect'] = array(
        array('query' => drupal_get_destination()),
    );

    return $form;
}

function views_civicrm_documents_new_version_validate($form, &$form_state) {
  $file = file_save_upload('upload', $form['upload']['#upload_validators']);
  if ($file) {
    $form_state['upload']['file'] = $file;
  } else {
    form_set_error('upload', 'You should upload a document');
  }
}

function views_civicrm_documents_new_version_submit($form, &$form_state) {
    civicrm_initialize();
    $doc_id = $form_state['values']['document_id'];

    $repository = CRM_Documents_Entity_DocumentRepository::singleton();
    $document = $repository->getDocumentById($doc_id);
    $version = $document->addNewVersion();
    $version->setDescription($form_state['values']['description']);
    $repository->persist($document);
    //save file upload
    $file = $form_state['upload']['file'];
    $wrapper = file_stream_wrapper_get_instance_by_uri($file->uri);
    $filepath = $wrapper->realpath();
    CRM_Documents_Utils_File::copyFileToDocument($filepath, $file->filemime, $document);
}