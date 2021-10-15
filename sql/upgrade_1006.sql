ALTER TABLE  `civicrm_document`
  CHANGE `id` `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'Unique Document ID';

ALTER TABLE  `civicrm_document_version`
  CHANGE `id` `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'Unique Document ID';

ALTER TABLE  `civicrm_document_case`
  CHANGE `id` `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'Unique Document ID';

ALTER TABLE  `civicrm_document_contact`
  CHANGE `id` `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'Unique Document ID';

ALTER TABLE  `civicrm_document_entity`
  CHANGE `id` `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'Unique Document ID';
