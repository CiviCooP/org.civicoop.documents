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

namespace Civi\Documents;

use Civi\ActionProvider\Action\AbstractAction;
use CRM_Documents_ExtensionUtil as E;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CompilerPass implements CompilerPassInterface {

  /**
   * You can modify the container here before it is dumped to PHP code.
   */
  public function process(ContainerBuilder $container) {
    if ($container->hasDefinition('data_processor_factory')) {
      $factoryDefinition = $container->getDefinition('data_processor_factory');
      $factoryDefinition->addMethodCall('addDataSource', [
        'document',
        'Civi\Documents\DataProcessor\Source\DocumentSource',
        E::ts('Document'),
      ]);
      $factoryDefinition->addMethodCall('addDataSource', [
        'document_version',
        'Civi\Documents\DataProcessor\Source\DocumentVersionSource',
        E::ts('Document Version'),
      ]);
      $factoryDefinition->addMethodCall('addDataSource', [
        'document_entity',
        'Civi\Documents\DataProcessor\Source\DocumentEntitySource',
        E::ts('Document Entity'),
      ]);
      $factoryDefinition->addMethodCall('addDataSource', [
        'document_contact',
        'Civi\Documents\DataProcessor\Source\DocumentContactSource',
        E::ts('Document Contact'),
      ]);
      $factoryDefinition->addMethodCall('addDataSource', [
        'document_case',
        'Civi\Documents\DataProcessor\Source\DocumentCaseSource',
        E::ts('Document Case'),
      ]);
      $factoryDefinition->addMethodCall('addOutputHandler', [
        'document_download_link',
        'Civi\Documents\DataProcessor\FieldOutputHandler\DocumentDownloadLink',
        E::ts('Document Download Link'),
      ]);
    }
  }

}
