-- ---------------------------------------------------------------------------------------------------------------------
-- articles
-- ---------------------------------------------------------------------------------------------------------------------
CREATE TABLE `articles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(128) NOT NULL DEFAULT '',
  `alias` varchar(128) DEFAULT '',
  `description` text,
  `content` text,
  `tags_cache` text,
  `is_published` tinyint(1) NOT NULL DEFAULT '0',
  `image` varchar(255) DEFAULT NULL,
  `sort_field` int(11) unsigned NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `author_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
-- ---------------------------------------------------------------------------------------------------------------------
-- tags
-- ---------------------------------------------------------------------------------------------------------------------
CREATE TABLE `tags` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(25) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `title` (`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
-- ---------------------------------------------------------------------------------------------------------------------
-- articles_tags
-- ---------------------------------------------------------------------------------------------------------------------
CREATE TABLE `articles_tags` (
  `article_id` int(11) unsigned NOT NULL,
  `tag_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`article_id`,`tag_id`),
  KEY `tag_id` (`tag_id`),
  CONSTRAINT `articles_tags_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `articles_tags_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
-- ---------------------------------------------------------------------------------------------------------------------
