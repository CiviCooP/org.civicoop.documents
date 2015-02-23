<?php

require_once('form.inc');

function views_civicrm_documents_menu() {
    $items = array();
    $items['documents/new-version/%'] = array( //this creates a URL that will call this form at "examples/form-example"
        'title' => 'Upload new version', //page title
        'description' => '',
        'page callback' => 'drupal_get_form', //this is the function that will be called when the page is accessed.  for a form, use drupal_get_form
        'page arguments' => array('views_civicrm_documents_new_version', 2), //put the name of the form here
        'access callback' => '_views_civicrm_documents_user_has_role',
        'access arguments' => array(array('authenticated user')), // arg 1 loads the user, arg 2 is the role name
    );
    return $items;
}

function _views_civicrm_documents_user_has_role($roles = array()) {
    global $user;
    foreach ($roles as $role) {
        if (in_array($role, $user->roles)) {
            return TRUE;
        }
    }
    return FALSE;
}