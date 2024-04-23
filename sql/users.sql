-- ---------------------------------------------------------------------------------------------------------------------
-- users
-- ---------------------------------------------------------------------------------------------------------------------
CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `username` varchar(32) NOT NULL DEFAULT '',
  `password` varchar(64) NOT NULL,
  `logins` int(10) unsigned NOT NULL DEFAULT '0',
  `last_login` int(10) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `hash` char(32) DEFAULT NULL,
  `name` varchar(40) DEFAULT NULL,
  `surname` varchar(40) DEFAULT NULL,
  `patronymic` varchar(40) DEFAULT NULL,
  `is_confirmed` tinyint(1) NOT NULL DEFAULT '0',
  `avatar` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_username` (`username`),
  UNIQUE KEY `uniq_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
-- ---------------------------------------------------------------------------------------------------------------------
-- roles
-- ---------------------------------------------------------------------------------------------------------------------
CREATE TABLE `roles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
-- ---------------------------------------------------------------------------------------------------------------------
-- roles_users
-- ---------------------------------------------------------------------------------------------------------------------
CREATE TABLE `roles_users` (
  `user_id` int(11) unsigned NOT NULL,
  `role_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `fk_role_id` (`role_id`),
  CONSTRAINT `roles_users_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `roles_users_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
-- ---------------------------------------------------------------------------------------------------------------------
-- user_tokens
-- ---------------------------------------------------------------------------------------------------------------------
CREATE TABLE `user_tokens` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `user_agent` varchar(40) NOT NULL,
  `token` varchar(40) NOT NULL,
  `type` varchar(100) NOT NULL,
  `created` int(10) unsigned NOT NULL,
  `expires` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_token` (`token`),
  KEY `fk_user_id` (`user_id`),
  CONSTRAINT `user_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
-- ---------------------------------------------------------------------------------------------------------------------
INSERT INTO `roles` (`id`, `name`, `description`)
VALUES
	(1, 'login', 'Login privileges, granted after account confirmation'),
	(2, 'admin', 'Administrative user, has access to everything.'),
	(3, 'developer', 'Developer user, has access to special developer zone, forbidden to other users');
-- ---------------------------------------------------------------------------------------------------------------------
