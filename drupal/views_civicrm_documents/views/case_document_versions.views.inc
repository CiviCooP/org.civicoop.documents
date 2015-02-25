<?php

$view = new view();
$view->name = 'case_document_versions';
$view->description = '';
$view->tag = 'default';
$view->base_table = 'civicrm_document_version';
$view->human_name = 'Document versions';
$view->core = 7;
$view->api_version = '3.0';
$view->disabled = FALSE; /* Edit this to true to make a default view disabled initially */

/* Display: Master */
$handler = $view->new_display('default', 'Master', 'default');
$handler->display->display_options['title'] = 'Document versions';
$handler->display->display_options['use_more_always'] = FALSE;
$handler->display->display_options['access']['type'] = 'role';
$handler->display->display_options['access']['role'] = array(
    2 => '2', //authenticated
);
$handler->display->display_options['cache']['type'] = 'none';
$handler->display->display_options['query']['type'] = 'views_query';
$handler->display->display_options['exposed_form']['type'] = 'basic';
$handler->display->display_options['pager']['type'] = 'none';
$handler->display->display_options['style_plugin'] = 'table';
$handler->display->display_options['style_options']['columns'] = array(
    'id' => 'id',
    'date_updated' => 'date_updated',
    'description' => 'description',
    'version' => 'version',
);
$handler->display->display_options['style_options']['default'] = 'version';
$handler->display->display_options['style_options']['info'] = array(
    'id' => array(
        'sortable' => 0,
        'default_sort_order' => 'asc',
        'align' => '',
        'separator' => '',
        'empty_column' => 0,
    ),
    'date_updated' => array(
        'sortable' => 0,
        'default_sort_order' => 'asc',
        'align' => '',
        'separator' => '',
        'empty_column' => 0,
    ),
    'description' => array(
        'sortable' => 0,
        'default_sort_order' => 'asc',
        'align' => '',
        'separator' => '',
        'empty_column' => 0,
    ),
    'version' => array(
        'sortable' => 1,
        'default_sort_order' => 'desc',
        'align' => '',
        'separator' => '',
        'empty_column' => 0,
    ),
);
/* Header: Global: Text area */
$handler->display->display_options['header']['area_1']['id'] = 'area';
$handler->display->display_options['header']['area_1']['table'] = 'views';
$handler->display->display_options['header']['area_1']['field'] = 'area';
$handler->display->display_options['header']['area_1']['empty'] = TRUE;
$handler->display->display_options['header']['area_1']['content'] = '<a href="/case-documents/!2">View case documents</a>';
$handler->display->display_options['header']['area_1']['format'] = 'filtered_html';
$handler->display->display_options['header']['area_1']['tokenize'] = TRUE;
/* Header: Global: Text area */
$handler->display->display_options['header']['area']['id'] = 'area';
$handler->display->display_options['header']['area']['table'] = 'views';
$handler->display->display_options['header']['area']['field'] = 'area';
$handler->display->display_options['header']['area']['empty'] = TRUE;
$handler->display->display_options['header']['area']['content'] = '<a href="/documents/new-version/!1?destination=/case-documents/versions/!1/!2" class="button">Add new version</a>';
$handler->display->display_options['header']['area']['format'] = 'filtered_html';
$handler->display->display_options['header']['area']['tokenize'] = TRUE;
/* Relationship: CiviCRM Document versions: Updated by */
$handler->display->display_options['relationships']['updated_by']['id'] = 'updated_by';
$handler->display->display_options['relationships']['updated_by']['table'] = 'civicrm_document_version';
$handler->display->display_options['relationships']['updated_by']['field'] = 'updated_by';
$handler->display->display_options['relationships']['updated_by']['label'] = 'Updated by';
$handler->display->display_options['relationships']['updated_by']['required'] = TRUE;
/* Relationship: CiviCRM Document versions: Document ID of document contact */
$handler->display->display_options['relationships']['document_id']['id'] = 'document_id';
$handler->display->display_options['relationships']['document_id']['table'] = 'civicrm_document_version';
$handler->display->display_options['relationships']['document_id']['field'] = 'document_id';
$handler->display->display_options['relationships']['document_id']['label'] = 'Document';
$handler->display->display_options['relationships']['document_id']['required'] = TRUE;
/* Relationship: CiviCRM Document case: Case ID of document case */
$handler->display->display_options['relationships']['case_id']['id'] = 'case_id';
$handler->display->display_options['relationships']['case_id']['table'] = 'civicrm_document_case';
$handler->display->display_options['relationships']['case_id']['field'] = 'case_id';
$handler->display->display_options['relationships']['case_id']['relationship'] = 'document_id';
$handler->display->display_options['relationships']['case_id']['label'] = 'Case';
$handler->display->display_options['relationships']['case_id']['required'] = TRUE;
/* Field: CiviCRM Document versions: Document version number */
$handler->display->display_options['fields']['version']['id'] = 'version';
$handler->display->display_options['fields']['version']['table'] = 'civicrm_document_version';
$handler->display->display_options['fields']['version']['field'] = 'version';
$handler->display->display_options['fields']['version']['label'] = 'Version';
$handler->display->display_options['fields']['version']['separator'] = '';
/* Field: CiviCRM Document versions: Date updated */
$handler->display->display_options['fields']['date_updated']['id'] = 'date_updated';
$handler->display->display_options['fields']['date_updated']['table'] = 'civicrm_document_version';
$handler->display->display_options['fields']['date_updated']['field'] = 'date_updated';
$handler->display->display_options['fields']['date_updated']['date_format'] = 'short';
/* Field: CiviCRM Contacts: Display Name */
$handler->display->display_options['fields']['display_name']['id'] = 'display_name';
$handler->display->display_options['fields']['display_name']['table'] = 'civicrm_contact';
$handler->display->display_options['fields']['display_name']['field'] = 'display_name';
$handler->display->display_options['fields']['display_name']['relationship'] = 'updated_by';
$handler->display->display_options['fields']['display_name']['label'] = 'Updated by';
$handler->display->display_options['fields']['display_name']['link_to_civicrm_contact'] = 0;
/* Field: CiviCRM Document versions: Description */
$handler->display->display_options['fields']['description']['id'] = 'description';
$handler->display->display_options['fields']['description']['table'] = 'civicrm_document_version';
$handler->display->display_options['fields']['description']['field'] = 'description';
/* Field: CiviCRM Document versions: Document version Download link */
$handler->display->display_options['fields']['download_link']['id'] = 'download_link';
$handler->display->display_options['fields']['download_link']['table'] = 'civicrm_document_version';
$handler->display->display_options['fields']['download_link']['field'] = 'download_link';
$handler->display->display_options['fields']['download_link']['label'] = 'Download';
$handler->display->display_options['fields']['download_link']['separator'] = '';
$handler->display->display_options['fields']['download_link']['download_link'] = TRUE;
/* Contextual filter: CiviCRM Document versions: Document ID */
$handler->display->display_options['arguments']['document_id']['id'] = 'document_id';
$handler->display->display_options['arguments']['document_id']['table'] = 'civicrm_document_version';
$handler->display->display_options['arguments']['document_id']['field'] = 'document_id';
$handler->display->display_options['arguments']['document_id']['default_action'] = 'not found';
$handler->display->display_options['arguments']['document_id']['default_argument_type'] = 'fixed';
$handler->display->display_options['arguments']['document_id']['summary']['number_of_records'] = '0';
$handler->display->display_options['arguments']['document_id']['summary']['format'] = 'default_summary';
$handler->display->display_options['arguments']['document_id']['summary_options']['items_per_page'] = '25';
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
$handler->display->display_options['path'] = 'case-documents/versions';
$translatables['document_versions'] = array(
    t('Master'),
    t('Document versions'),
    t('more'),
    t('Apply'),
    t('Reset'),
    t('Sort by'),
    t('Asc'),
    t('Desc'),
    t('Updated by'),
    t('Document'),
    t('Contact'),
    t('Drupal User'),
    t('Version'),
    t('.'),
    t('Date updated'),
    t('Description'),
    t('Download'),
    t('All'),
    t('Page'),
);
