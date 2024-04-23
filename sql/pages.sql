-- ---------------------------------------------------------------------------------------------------------------------
-- pages
-- ---------------------------------------------------------------------------------------------------------------------
CREATE TABLE `pages` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `alias` varchar(150) NOT NULL DEFAULT '',
  `title` varchar(100) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `is_published` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
-- ---------------------------------------------------------------------------------------------------------------------
