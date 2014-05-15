<?php

/**
 * Helper class with function for file movements
 * 
 */

class CRM_Documents_Utils_File {
  
  static function copyFileToDocument($file, $mimeType, CRM_Documents_Entity_Document $document) {
     $config = CRM_Core_Config::singleton();

     $path = explode('/', $file);
     $filename = $path[count($path) - 1];
     $directoryName = $config->customFileUploadDir;
     CRM_Utils_File::createDir($directoryName);

     if (!copy($file, $directoryName . DIRECTORY_SEPARATOR . $filename)) {
       throw new CRM_Documents_Exception_FileCopy('Could not copy file from '.$file.' to '.$directoryName . DIRECTORY_SEPARATOR . $filename);
     }

     $entityTable = 'civicrm_document_version';
     $entityID = $document->getCurrentVersion()->getId();

     list($sql, $params) = CRM_Core_BAO_File::sql($entityTable, $entityID, 0);

     $dao = CRM_Core_DAO::executeQuery($sql, $params);
     $dao->fetch();

     $fileDAO = new CRM_Core_DAO_File();
     $op = 'create';
     if (isset($dao->cfID) && $dao->cfID) {
       $op = 'edit';
       $fileDAO->id = $dao->cfID;
       unlink($directoryName . DIRECTORY_SEPARATOR . $dao->uri);
     }

     $fileDAO->uri = $filename;
     $fileDAO->mime_type = $mimeType;
     $fileDAO->upload_date = date('Ymdhis');
     $fileDAO->save();

     // need to add/update civicrm_entity_file
     $entityFileDAO = new CRM_Core_DAO_EntityFile();
     if (isset($dao->cefID) && $dao->cefID) {
       $entityFileDAO->id = $dao->cefID;
     }
     $entityFileDAO->entity_table = $entityTable;
     $entityFileDAO->entity_id = $entityID;
     $entityFileDAO->file_id = $fileDAO->id;
     $entityFileDAO->save();

     // lets call the post hook here so attachments code can do the right stuff
     CRM_Utils_Hook::post($op, 'File', $fileDAO->id, $fileDAO);
   }
  
}