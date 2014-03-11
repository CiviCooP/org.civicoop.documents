<?php

/* 
 * This class holds the information for an attachment
 * 
 * The data in this class correspondents with the data in the entity file table
 * 
 */

class CRM_Documents_Entity_DocumentFile {
  
  public $fileID;
  
  public $entityID;
  
  public $mime_type;
  
  public $filename;
  
  public $description;
  
  public $cleanname; //clean filename
  
  public $fullPath; //full path to the file
  
  public $url; //download url
  
  public $href; //download url in a <a href> tag
  
  
  public function setFromArray($data) {
    $this->fileID = $data['fileID'];
    $this->entityID = $data['entityID'];
    $this->mime_type = $data['mime_type'];
    $this->filename = $data['fileName'];
    $this->description = $data['description'];
    $this->cleanname = $data['cleanName'];
    $this->fullPath = $data['fullPath'];
    $this->url = $data['url'];
    $this->href = $data['href'];
  }
  
}
