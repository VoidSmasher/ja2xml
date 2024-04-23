CREATE TABLE `deferred_actions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `action` int(11) unsigned NOT NULL,
  `priority` int(11) DEFAULT '0',
  `data` longtext,
  `tries` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `canceled_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `executed_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;