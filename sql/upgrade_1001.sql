CREATE TABLE IF NOT EXISTS `civicrm_document_version` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(255) NOT NULL,
  `document_id` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `date_updated` datetime DEFAULT NULL,
  `version` int(11) NOT NULL default '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;