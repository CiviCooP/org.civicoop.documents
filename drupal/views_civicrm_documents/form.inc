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
        '#type' => 'managed_file',
        '#required' => true,
        '#title' => t('File'),
    );

    $form['submit_button'] = array(
        '#type' => 'submit',
        '#value' => t('Save'),
    );

    $form_state['redirect'] = array(
        array('query' => drupal_get_destination()),
    );

    return $form;
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
    $file = file_load($form_state['values']['upload']);
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
        '#type' => 'managed_file',
        '#required' => true,
        '#title' => t('File'),
    );

    $form['submit_button'] = array(
        '#type' => 'submit',
        '#value' => t('Save'),
    );

    $form_state['redirect'] = array(
        array('query' => drupal_get_destination()),
    );

    return $form;
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
    $file = file_load($form_state['values']['upload']);
    $wrapper = file_stream_wrapper_get_instance_by_uri($file->uri);
    $filepath = $wrapper->realpath();
    CRM_Documents_Utils_File::copyFileToDocument($filepath, $file->filemime, $document);
}