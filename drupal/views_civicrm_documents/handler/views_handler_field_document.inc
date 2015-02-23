<?php

class views_handler_field_document extends views_handler_field_numeric {

    function construct() {
        parent::construct();
        $this->additional_fields['id'] = 'id';
    }

    function query() {
        if ($this->real_field == 'id') {
            parent::query();
        } else {
            $this->ensure_my_table();
            $this->add_additional_fields();
        }
    }

    function option_definition() {
        $options = parent::option_definition();

        $options['download_link'] = array('default' => FALSE, 'bool' => TRUE);
        $options['download_link_text'] = array('default' => '');

        return $options;
    }

    function options_form(&$form, &$form_state)
    {
        parent::options_form($form, $form_state);
        $form['download_link'] = array(
            '#type' => 'checkbox',
            '#title' => t('Download link'),
            '#description' => t('If checked a download link will be displayed.'),
            '#default_value' => $this->options['download_link'],
        );
        $form['download_link_text'] = array(
            '#type' => 'textfield',
            '#title' => t('Download Link Text'),
            '#description' => t('The text that will display in the field with the link. If you leave this empty the filename will be displayed'),
            '#default_value' => isset($this->options['download_link_text']) ? $this->options['download_link_text'] : '',
        );
    }


    function render($values) {
        if (!empty($this->options['download_link'])) {
            civicrm_initialize();
            $repository = CRM_Documents_Entity_DocumentRepository::singleton();
            $document = $repository->getDocumentById($values->id);
            $version = $document->getCurrentVersion();
            $link_text = (!empty($this->options['download_link_text']) ? $this->options['download_link_text'] : $version->getAttachment()->cleanname);
            return sprintf('<a href="%s" title="%s">%s</a>', $version->getAttachment()->url, $version->getAttachment()->cleanname, $link_text);
        }
        return parent::render($values);
    }

}