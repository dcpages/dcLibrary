-- MySQL dump 10.13  Distrib 5.5.35, for debian-linux-gnu (x86_64)
-- ------------------------------------------------------

--
-- Table structure for table `app_migrations`
--

DROP TABLE IF EXISTS `app_migrations`;
CREATE TABLE `app_migrations` (
  `timestamp` varchar(14) NOT NULL,
  `description` varchar(100) NOT NULL
);

--
-- Table structure for table `app_versions`
--

DROP TABLE IF EXISTS `app_versions`;
CREATE TABLE `app_versions` (
  `version` varchar(50) NOT NULL,
  `timestamp` varchar(14) NOT NULL,
  KEY `timestamp` (`timestamp`)
);

--
-- Table structure for table `pvt_roles_users`
--

DROP TABLE IF EXISTS `pvt_roles_users`;
CREATE TABLE `pvt_roles_users` (
  `role_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  KEY `fk_pvt_roles_users_role_id` (`role_id`),
  KEY `fk_pvt_roles_users_user_id` (`user_id`),
  CONSTRAINT `fk_pvt_roles_users_role_id` FOREIGN KEY (`role_id`) REFERENCES `user_roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pvt_roles_users_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
);

--
-- Table structure for table `user_roles`
--

DROP TABLE IF EXISTS `user_roles`;
CREATE TABLE `user_roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(40) NOT NULL,
  `description` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
);

--
-- Table structure for table `user_tokens`
--

DROP TABLE IF EXISTS `user_tokens`;
CREATE TABLE `user_tokens` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `token` varchar(40) NOT NULL,
  `type` varchar(20) NOT NULL,
  `created` int(10) NOT NULL,
  `expires` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_user_tokens_user_id` (`user_id`),
  CONSTRAINT `fk_user_tokens_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
);

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(127) NOT NULL,
  `password` varchar(64) NOT NULL,
  `last_login` int(10) DEFAULT NULL,
  `created` int(10) NOT NULL,
  `enabled` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
);

-- Dump completed on 2014-02-28  0:30:22
