-- ---------------------------------------------------------------------------------------------------------------------
-- applications
-- ---------------------------------------------------------------------------------------------------------------------
CREATE TABLE `applications` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `domain` varchar(128) DEFAULT NULL,
  `key` char(32) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
-- ---------------------------------------------------------------------------------------------------------------------
