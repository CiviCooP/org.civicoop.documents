<?php

$view = new view();
$view->name = 'case_documents';
$view->description = '';
$view->tag = 'default';
$view->base_table = 'civicrm_document';
$view->human_name = 'Case documents';
$view->core = 7;
$view->api_version = '3.0';
$view->disabled = FALSE; /* Edit this to true to make a default view disabled initially */

/* Display: Master */
$handler = $view->new_display('default', 'Master', 'default');
$handler->display->display_options['title'] = 'Documents';
$handler->display->display_options['use_more_always'] = FALSE;
$handler->display->display_options['access']['type'] = 'role';
$handler->display->display_options['access']['role'] = array(
    2 => '2',
);
$handler->display->display_options['cache']['type'] = 'none';
$handler->display->display_options['query']['type'] = 'views_query';
$handler->display->display_options['exposed_form']['type'] = 'basic';
$handler->display->display_options['pager']['type'] = 'full';
$handler->display->display_options['pager']['options']['items_per_page'] = '10';
$handler->display->display_options['style_plugin'] = 'table';
$handler->display->display_options['style_options']['columns'] = array(
    'subject' => 'subject',
    'date_added' => 'date_added',
    'date_updated' => 'date_updated',
    'display_name' => 'display_name',
    'display_name_1' => 'display_name_1',
    'id' => 'id',
    'view' => 'view',
    'download_link' => 'download_link',
);
$handler->display->display_options['style_options']['default'] = 'subject';
$handler->display->display_options['style_options']['info'] = array(
    'subject' => array(
        'sortable' => 1,
        'default_sort_order' => 'asc',
        'align' => '',
        'separator' => '',
        'empty_column' => 0,
    ),
    'date_added' => array(
        'sortable' => 1,
        'default_sort_order' => 'asc',
        'align' => '',
        'separator' => '',
        'empty_column' => 0,
    ),
    'date_updated' => array(
        'sortable' => 1,
        'default_sort_order' => 'asc',
        'align' => '',
        'separator' => '',
        'empty_column' => 0,
    ),
    'display_name' => array(
        'sortable' => 1,
        'default_sort_order' => 'asc',
        'align' => '',
        'separator' => '',
        'empty_column' => 0,
    ),
    'display_name_1' => array(
        'sortable' => 1,
        'default_sort_order' => 'asc',
        'align' => '',
        'separator' => '',
        'empty_column' => 0,
    ),
    'id' => array(
        'sortable' => 0,
        'default_sort_order' => 'asc',
        'align' => '',
        'separator' => '',
        'empty_column' => 0,
    ),
    'view' => array(
        'align' => '',
        'separator' => '',
        'empty_column' => 0,
    ),
    'download_link' => array(
        'sortable' => 0,
        'default_sort_order' => 'asc',
        'align' => '',
        'separator' => '',
        'empty_column' => 0,
    ),
);
/* Header: Global: Text area */
$handler->display->display_options['header']['area']['id'] = 'area';
$handler->display->display_options['header']['area']['table'] = 'views';
$handler->display->display_options['header']['area']['field'] = 'area';
$handler->display->display_options['header']['area']['empty'] = TRUE;
$handler->display->display_options['header']['area']['content'] = '<a href="/case-documents/new-document/!1?destination=/case-documents/!1" class="button">Add new document</a>';
$handler->display->display_options['header']['area']['format'] = 'filtered_html';
$handler->display->display_options['header']['area']['tokenize'] = TRUE;
/* Relationship: CiviCRM Document contact: Contact ID of document contact */
$handler->display->display_options['relationships']['contact_id']['id'] = 'contact_id';
$handler->display->display_options['relationships']['contact_id']['table'] = 'civicrm_document_contact';
$handler->display->display_options['relationships']['contact_id']['field'] = 'contact_id';
$handler->display->display_options['relationships']['contact_id']['required'] = TRUE;
/* Relationship: CiviCRM Document: Added by */
$handler->display->display_options['relationships']['added_by']['id'] = 'added_by';
$handler->display->display_options['relationships']['added_by']['table'] = 'civicrm_document';
$handler->display->display_options['relationships']['added_by']['field'] = 'added_by';
$handler->display->display_options['relationships']['added_by']['label'] = 'Added by';
$handler->display->display_options['relationships']['added_by']['required'] = TRUE;
/* Relationship: CiviCRM Document: Updated by */
$handler->display->display_options['relationships']['updated_by']['id'] = 'updated_by';
$handler->display->display_options['relationships']['updated_by']['table'] = 'civicrm_document';
$handler->display->display_options['relationships']['updated_by']['field'] = 'updated_by';
$handler->display->display_options['relationships']['updated_by']['label'] = 'Updated by';
$handler->display->display_options['relationships']['updated_by']['required'] = TRUE;
/* Relationship: CiviCRM Document case: Case ID of document case */
$handler->display->display_options['relationships']['case_id']['id'] = 'case_id';
$handler->display->display_options['relationships']['case_id']['table'] = 'civicrm_document_case';
$handler->display->display_options['relationships']['case_id']['field'] = 'case_id';
$handler->display->display_options['relationships']['case_id']['label'] = 'Case';
$handler->display->display_options['relationships']['case_id']['required'] = TRUE;
/* Field: CiviCRM Document: Document ID */
$handler->display->display_options['fields']['id']['id'] = 'id';
$handler->display->display_options['fields']['id']['table'] = 'civicrm_document';
$handler->display->display_options['fields']['id']['field'] = 'id';
$handler->display->display_options['fields']['id']['label'] = '';
$handler->display->display_options['fields']['id']['exclude'] = TRUE;
$handler->display->display_options['fields']['id']['element_label_colon'] = FALSE;
$handler->display->display_options['fields']['id']['separator'] = '';
/* Field: CiviCRM Document: Subject */
$handler->display->display_options['fields']['subject']['id'] = 'subject';
$handler->display->display_options['fields']['subject']['table'] = 'civicrm_document';
$handler->display->display_options['fields']['subject']['field'] = 'subject';
/* Field: CiviCRM Document: Date added */
$handler->display->display_options['fields']['date_added']['id'] = 'date_added';
$handler->display->display_options['fields']['date_added']['table'] = 'civicrm_document';
$handler->display->display_options['fields']['date_added']['field'] = 'date_added';
$handler->display->display_options['fields']['date_added']['date_format'] = 'short';
/* Field: CiviCRM Contacts: Display Name */
$handler->display->display_options['fields']['display_name']['id'] = 'display_name';
$handler->display->display_options['fields']['display_name']['table'] = 'civicrm_contact';
$handler->display->display_options['fields']['display_name']['field'] = 'display_name';
$handler->display->display_options['fields']['display_name']['relationship'] = 'added_by';
$handler->display->display_options['fields']['display_name']['label'] = 'Added by';
$handler->display->display_options['fields']['display_name']['link_to_civicrm_contact'] = 0;
/* Field: CiviCRM Document: Date updated */
$handler->display->display_options['fields']['date_updated']['id'] = 'date_updated';
$handler->display->display_options['fields']['date_updated']['table'] = 'civicrm_document';
$handler->display->display_options['fields']['date_updated']['field'] = 'date_updated';
$handler->display->display_options['fields']['date_updated']['date_format'] = 'short';
/* Field: CiviCRM Contacts: Display Name */
$handler->display->display_options['fields']['display_name_1']['id'] = 'display_name_1';
$handler->display->display_options['fields']['display_name_1']['table'] = 'civicrm_contact';
$handler->display->display_options['fields']['display_name_1']['field'] = 'display_name';
$handler->display->display_options['fields']['display_name_1']['relationship'] = 'updated_by';
$handler->display->display_options['fields']['display_name_1']['label'] = 'Updated by';
$handler->display->display_options['fields']['display_name_1']['link_to_civicrm_contact'] = 0;
/* Field: CiviCRM Document: Document Download link */
$handler->display->display_options['fields']['download_link']['id'] = 'download_link';
$handler->display->display_options['fields']['download_link']['table'] = 'civicrm_document';
$handler->display->display_options['fields']['download_link']['field'] = 'download_link';
$handler->display->display_options['fields']['download_link']['label'] = 'Download';
$handler->display->display_options['fields']['download_link']['separator'] = '';
$handler->display->display_options['fields']['download_link']['download_link'] = TRUE;
/* Field: Global: Custom text */
$handler->display->display_options['fields']['nothing']['id'] = 'nothing';
$handler->display->display_options['fields']['nothing']['table'] = 'views';
$handler->display->display_options['fields']['nothing']['field'] = 'nothing';
$handler->display->display_options['fields']['nothing']['label'] = 'Versions';
$handler->display->display_options['fields']['nothing']['alter']['text'] = 'View versions';
$handler->display->display_options['fields']['nothing']['alter']['make_link'] = TRUE;
$handler->display->display_options['fields']['nothing']['alter']['path'] = 'case-documents/versions/[id]/!1';
$handler->display->display_options['fields']['nothing']['alter']['absolute'] = TRUE;
/* Contextual filter: CiviCRM Cases: Case ID */
$handler->display->display_options['arguments']['id']['id'] = 'id';
$handler->display->display_options['arguments']['id']['table'] = 'civicrm_case';
$handler->display->display_options['arguments']['id']['field'] = 'id';
$handler->display->display_options['arguments']['id']['relationship'] = 'case_id';
$handler->display->display_options['arguments']['id']['default_action'] = 'not found';
$handler->display->display_options['arguments']['id']['default_argument_type'] = 'fixed';
$handler->display->display_options['arguments']['id']['summary']['number_of_records'] = '0';
$handler->display->display_options['arguments']['id']['summary']['format'] = 'default_summary';
$handler->display->display_options['arguments']['id']['summary_options']['items_per_page'] = '25';

/* Display: Page */
$handler = $view->new_display('page', 'Page', 'page');
$handler->display->display_options['path'] = 'case-documents';
$translatables['case_documents'] = array(
    t('Master'),
    t('Documents'),
    t('more'),
    t('Apply'),
    t('Reset'),
    t('Sort by'),
    t('Asc'),
    t('Desc'),
    t('Items per page'),
    t('- All -'),
    t('Offset'),
    t('« first'),
    t('‹ previous'),
    t('next ›'),
    t('last »'),
    t('Contact ID of document contact'),
    t('Drupal User'),
    t('Added by'),
    t('Updated by'),
    t('Case'),
    t('.'),
    t('Subject'),
    t('Date added'),
    t('Date updated'),
    t('Download'),
    t('Versions'),
    t('View versions'),
    t('All'),
    t('Page'),
);
