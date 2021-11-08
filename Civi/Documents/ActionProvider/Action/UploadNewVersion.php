<?php

namespace Civi\Documents\ActionProvider\Action;

use \Civi\ActionProvider\Action\AbstractAction;
use Civi\ActionProvider\Parameter\FileSpecification;
use \Civi\ActionProvider\Parameter\ParameterBagInterface;
use \Civi\ActionProvider\Parameter\SpecificationBag;
use \Civi\ActionProvider\Parameter\Specification;
use \Civi\ActionProvider\Utils\CustomField;

use CRM_Documents_ExtensionUtil as E;

class UploadNewVersion extends AbstractAction {

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
    $document_id = $parameters->getParameter('document_id');
    $document = $documentsRepo->getDocumentById($document_id);
    $replaceCurrent = $this->configuration->getParameter('replace_current') ? true : false;
    if ($replaceCurrent) {
      $version = $document->getCurrentVersion();
      //remove the old attachment so that the new attachment replaces the old one
      \CRM_Core_BAO_File::deleteEntityFile('civicrm_document_version', $version->getId());
    } else {
      $version = $document->addNewVersion();
    }
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
  }

  /**
   * Returns the specification of the configuration options for the actual action.
   *
   * @return SpecificationBag
   */
  public function getConfigurationSpecification() {
    return new SpecificationBag([
      new Specification('replace_current', 'Boolean', E::ts('Replace current version'), TRUE),
    ]);
  }

  /**
   * Returns the specification of the parameters of the actual action.
   *
   * @return SpecificationBag
   */
  public function getParameterSpecification() {
    $specs = new SpecificationBag();
    $specs->addSpecification(new Specification('document_id', 'Integer', E::ts('Document ID'), true));
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
    return new SpecificationBag();
  }


}
