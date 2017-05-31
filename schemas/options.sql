﻿# SQL Manager 2007 for MySQL 4.4.1.2
# ---------------------------------------
# Host     : localhost
# Port     : 3306
# Database : retailweb


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

SET FOREIGN_KEY_CHECKS=0;

#
# Structure for the `options` table : 
#

DROP TABLE IF EXISTS `options`;

CREATE TABLE `options` (
  `id` INTEGER(10) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(250) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `description` VARCHAR(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `option_group` INTEGER(10) DEFAULT NULL,
  `image` TEXT COLLATE utf8_unicode_ci,
  `price` FLOAT(9,2) DEFAULT '0.00',
  `sold_by` VARCHAR(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `oum` VARCHAR(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
)ENGINE=InnoDB
AUTO_INCREMENT=44 CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci';


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;