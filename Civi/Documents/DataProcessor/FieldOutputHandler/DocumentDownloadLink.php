<?php
/**
 * Copyright (C) 2021  Jaap Jansma (jaap.jansma@civicoop.org)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Civi\Documents\DataProcessor\FieldOutputHandler;

use Civi\DataProcessor\FieldOutputHandler\AbstractSimpleFieldOutputHandler;
use Civi\DataProcessor\FieldOutputHandler\FieldOutput;
use Civi\DataProcessor\FieldOutputHandler\HTMLFieldOutput;
use CRM_Documents_ExtensionUtil as E;

class DocumentDownloadLink extends AbstractSimpleFieldOutputHandler {

  /**
   * @var bool
   */
  protected $returnUrl = false;

  /**
   * Returns the label of the field for selecting a field.
   *
   * This could be override in a child class.
   *
   * @return string
   */
  protected function getFieldTitle() {
    return E::ts('Document ID Field');
  }

  /**
   * Returns the data type of this field
   *
   * @return String
   */
  protected function getType() {
    return 'String';
  }

  /**
   * Returns the formatted value
   *
   * @param $rawRecord
   * @param $formattedRecord
   *
   * @return \Civi\DataProcessor\FieldOutputHandler\FieldOutput
   */
  public function formatField($rawRecord, $formattedRecord) {
    $documentId = $rawRecord[$this->inputFieldSpec->alias];
    $repo = \CRM_Documents_Entity_DocumentRepository::singleton();
    $document = $repo->getDocumentById($documentId);
    $rawValue = $document->getCurrentVersion()->getAttachment()->url;

    if ($this->returnUrl) {
      $output = new FieldOutput($rawValue);
    } else {
      $output = new HTMLFieldOutput($rawValue);
    }
    if ($rawValue) {
      $output->formattedValue = $document->getCurrentVersion()->getAttachment()->url;
      if (!$this->returnUrl) {
        $output->setHtmlOutput('<a href="' . $document->getCurrentVersion()->getAttachment()->url . '" title="' . $document->getCurrentVersion()->getAttachment()->cleanname . '"><i class="crm-i ' . $document->getIcon() . '">&nbsp;</i>' . $document->getCurrentVersion()->getAttachment()->cleanname . '</a>');
      }
    }
    return $output;
  }

  /**
   * Initialize the processor
   *
   * @param String $alias
   * @param String $title
   * @param array $configuration
   * @param \Civi\DataProcessor\ProcessorType\AbstractProcessorType $processorType
   */
  public function initialize($alias, $title, $configuration) {
    parent::initialize($alias, $title, $configuration);
    $this->returnUrl = isset($configuration['return_url']) ? $configuration['return_url'] : false;
  }

  /**
   * When this handler has additional configuration you can add
   * the fields on the form with this function.
   *
   * @param \CRM_Core_Form $form
   * @param array $field
   */
  public function buildConfigurationForm(\CRM_Core_Form $form, $field=array()) {
    parent::buildConfigurationForm($form, $field);
    $form->add('checkbox', 'return_url', E::ts('Only return URL'));
    if (isset($field['configuration'])) {
      $configuration = $field['configuration'];
      $defaults = array();
      if (isset($configuration['return_url'])) {
        $defaults['return_url'] = $configuration['return_url'];
      }
      $form->setDefaults($defaults);
    }
  }

  /**
   * When this handler has configuration specify the template file name
   * for the configuration form.
   *
   * @return false|string
   */
  public function getConfigurationTemplateFileName() {
    return "CRM/Documents/DataProcessor/FieldoutputHandler/Configuration/DocumentDownloadLink.tpl";
  }


  /**
   * Process the submitted values and create a configuration array
   *
   * @param $submittedValues
   * @return array
   */
  public function processConfiguration($submittedValues) {
    $configuration = parent::processConfiguration($submittedValues);
    $configuration['return_url'] = isset($submittedValues['return_url']) ? $submittedValues['return_url'] : false;
    return $configuration;
  }

}
