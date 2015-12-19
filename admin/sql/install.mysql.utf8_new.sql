/*
SQLyog Community v12.15 (64 bit)
MySQL - 5.6.17 : Database - joomla3
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`joomla3` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `joomla3`;

/*Table structure for table `j3_hecmailing_message` */

DROP TABLE IF EXISTS `j3_hecmailing_message`;

CREATE TABLE `j3_hecmailing_message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message_date` datetime NOT NULL,
  `message_subject` varchar(200) NOT NULL,
  `message_body` text NOT NULL,
  `message_from` varchar(150) NOT NULL,
  `message_fromname` varchar(150) DEFAULT NULL,
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message_read_notification` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `grp_id_groupe` (`group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=latin1 COMMENT='Log des mails envoyes';

/*Table structure for table `j3_hecmailing_message_attachment` */

DROP TABLE IF EXISTS `j3_hecmailing_message_attachment`;

CREATE TABLE `j3_hecmailing_message_attachment` (
  `message_id` int(11) NOT NULL,
  `file` varchar(250) NOT NULL,
  `cid` varchar(30) NOT NULL,
  `filename` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`message_id`,`file`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Pieces jointes des mails envoyes';

/*Table structure for table `j3_hecmailing_message_recipient` */

DROP TABLE IF EXISTS `j3_hecmailing_message_recipient`;

CREATE TABLE `j3_hecmailing_message_recipient` (
  `message_id` int(11) NOT NULL DEFAULT '0' COMMENT 'Identifiant Utilisateur Joomla',
  `userid` int(11) NOT NULL DEFAULT '0' COMMENT 'Identifiant de l''utilisateur Joomla',
  `email` varchar(200) NOT NULL COMMENT 'Adresse E-Mail',
  `name` varchar(200) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '0' COMMENT 'Status d''envoi : 0 A Envoyer, 1 Envoyé, 9 Erreur Envoi,8 Erreur Email',
  `error` text,
  `timestamp` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `params` text,
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identifiant du recipient',
  PRIMARY KEY (`message_id`,`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=latin1 COMMENT='Liste des destinataires';

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
