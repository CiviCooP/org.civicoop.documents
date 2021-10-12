<?php

namespace Civi\Documents\ActionProvider\Action;

use \Civi\ActionProvider\Action\AbstractAction;
use Civi\ActionProvider\Parameter\FileSpecification;
use \Civi\ActionProvider\Parameter\ParameterBagInterface;
use \Civi\ActionProvider\Parameter\SpecificationBag;
use \Civi\ActionProvider\Parameter\Specification;
use \Civi\ActionProvider\Utils\CustomField;

use CRM_Documents_ExtensionUtil as E;

class UploadNewDocument extends AbstractAction {

  /**
   * Run the action
   *
   * @param ParameterBagInterface $parameters
   *   The parameters to this action.
   * @param ParameterBagInterface $output
   *   The parameters this action can send back
   * @return void
   */
  protected function doAction(ParameterBagInterface $parameters, ParameterBagInterface $output) {
    $documentsRepo = \CRM_Documents_Entity_DocumentRepository::singleton();
    $document = new \CRM_Documents_Entity_Document();
    if ($parameters->getParameter('subject')) {
      $document->setSubject($parameters->getParameter('subject'));
    } elseif ($this->configuration->getParameter('subject')) {
      $document->setSubject($this->configuration->getParameter('subject'));
    }
    if (is_array($parameters->getParameter('contact_ids')) && count($parameters->getParameter('contact_ids'))) {
      $document->setContactIds($parameters->getParameter('contact_ids'));
    }
    if ($parameters->getParameter('case_id')) {
      $case = civicrm_api3('Case', 'getsingle', array("case_id"=>$parameters->getParameter('case_id')));
      $document->addCaseid($parameters->getParameter('case_id'));
      $document->addContactId($case['client_id']);
    }
    $version = $document->addNewVersion();
    $version->setDescription($parameters->getParameter('description'));
    $documentsRepo->persist($document);

    $file = $parameters->getParameter('file');
    $uploadNewOne = true;
    if (empty($file)) {
      $uploadNewOne = false;
    } elseif (isset($file['id'])) {
      $uploadNewOne = false;
    }
    try {
      if (isset($file['id'])) {
        civicrm_api3('Attachment', 'delete', array('id' => $file['id']));
      }
    } catch (\Exception $e) {
      // Do nothing
    }

    $content = '';
    if (isset($file['content'])) {
      $content = base64_decode($file['content']);
    } elseif (isset($file['url'])) {
      $content = file_get_contents($file['url']);
    }
    if (empty($content)) {
      return;
    }

    if ($uploadNewOne) {
      $config = \CRM_Core_Config::singleton();
      $directoryName = $config->customFileUploadDir;
      $fileName = \CRM_Utils_File::makeFileName($file['name']);
      $uri = $fileName;
      \CRM_Utils_File::createDir($directoryName);
      $fileParams = [
        'name' => $file['name'],
        'mime_type' => $file['mime_type'],
        'uri' => $uri,
      ];
      if ($parameters->doesParameterExists('description')) {
        $fileParams['description'] = $parameters->getParameter('description');
      }
      $fileDao = \CRM_Core_BAO_File::create($fileParams);
      $fileDao->find(TRUE);

      $entityFileDao = new \CRM_Core_DAO_EntityFile();
      $entityFile['entity_table'] = 'civicrm_document_version';
      $entityFile['entity_id'] = $version->getId();
      $entityFileDao->copyValues($entityFile);
      $entityFileDao->file_id = $fileDao->id;
      $entityFileDao->save();

      $path = $config->customFileUploadDir . $uri;
      if (is_string($content)) {
        file_put_contents($path, $content);
      }
    }

    $output->setParameter('document_id', $document->getId());
  }

  /**
   * Returns the specification of the configuration options for the actual action.
   *
   * @return SpecificationBag
   */
  public function getConfigurationSpecification() {
    return new SpecificationBag([
      new Specification('subject', 'String', E::ts('Subject'), false),
    ]);
  }

  /**
   * Returns the specification of the parameters of the actual action.
   *
   * @return SpecificationBag
   */
  public function getParameterSpecification() {
    $specs = new SpecificationBag();
    $specs->addSpecification(new Specification('subject', 'String', E::ts('Subject'), false));
    $specs->addSpecification(new Specification('contact_ids', 'Integer', E::ts('Contact IDs'), false, null, null, null, true));
    $specs->addSpecification(new Specification('case_id', 'Integer', E::ts('Case ID'), false, null, null, null, false));
    $specs->addSpecification(new FileSpecification('file', E::ts('File'), false));
    $specs->addSpecification(new Specification('description', 'String', E::ts('Description'), false));
    return $specs;
  }

  /**
   * Returns the specification of the output parameters of this action.
   *
   * This function could be overriden by child classes.
   *
   * @return SpecificationBag
   */
  public function getOutputSpecification() {
    return new SpecificationBag([
      new Specification('document_id', 'Integer', E::ts('Document ID')),
    ]);
  }


}
