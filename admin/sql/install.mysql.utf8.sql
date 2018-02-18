/*
 * @version   3.4.5
 * @package   HEC Mailing for Joomla
 * @copyright Copyright (C) 1999-2017 Hecsoft All rights reserved.
 * @author    Herve CYR
 * @license   GNU/GPL
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
*/
CREATE TABLE IF NOT EXISTS #__hecmailing_groupdetail (
	grp_id_groupe int(11) NOT NULL COMMENT 'Id du groupe',
	gdet_cd_type tinyint(4) NOT NULL COMMENT 'Type de detail (1 : UserName, 2 : UserId, 3 : UserType, 4 : E-mail)',
	gdet_id_value int(11) NOT NULL COMMENT 'Code de la valeur',
	gdet_vl_value varchar(50) NOT NULL COMMENT 'E Mail...',
	gdet_id_detail int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identifiant unique',
	PRIMARY KEY (gdet_id_detail),
	KEY grp_id_groupe (grp_id_groupe)
) COMMENT='Detail des groupe de diffusion';
			
CREATE TABLE IF NOT EXISTS #__hecmailing_groups (
	grp_id_groupe int(11) NOT NULL AUTO_INCREMENT COMMENT 'Id du groupe',
	grp_nm_groupe varchar(30) NOT NULL COMMENT 'nom du groupe',
	grp_cm_groupe varchar(250) NOT NULL COMMENT 'Commentaires',
	published tinyint(4)   NOT NULL default '1' COMMENT '1=Publie/0=Non Publie',
	checked_out tinyint(4) NOT NULL DEFAULT '1',
	checked_out_time datetime ,
	PRIMARY KEY (grp_id_groupe)
) COMMENT='Liste des groupes de mailing';
			
CREATE TABLE IF NOT EXISTS #__hecmailing_save (
	msg_id_message int(11) NOT NULL AUTO_INCREMENT,
	msg_lb_message varchar(30) NOT NULL,
	msg_vl_subject varchar(200) NOT NULL,
	msg_vl_body text NOT NULL,
	msg_vl_from varchar(150) NOT NULL,
	grp_id_groupe int(11) NOT NULL,
	published tinyint(4)   NOT NULL default '1' COMMENT '1=Publie/0=Non Publie',
	checked_out tinyint(4) NOT NULL DEFAULT '1',
	checked_out_time datetime ,
	PRIMARY KEY (msg_id_message),
	  KEY grp_id_groupe (grp_id_groupe)
)  COMMENT='Modeles de mail';

CREATE TABLE IF NOT EXISTS #__hecmailing_log (
	log_id_message int(11) NOT NULL AUTO_INCREMENT,
	log_dt_sent datetime NOT NULL,
	log_vl_subject varchar(200) NOT NULL,
	log_vl_body text NOT NULL,
	log_vl_from varchar(150) NOT NULL,
	grp_id_groupe integer NOT NULL,
	usr_id_user integer NOT NULL,
	log_bl_useprofil smallint NULL,
	log_nb_ok integer NOT NULL,
	log_nb_errors integer NOT NULL,
	log_vl_mailok text NULL,
	log_vl_mailerr text NULL,
	PRIMARY KEY (log_id_message),
	  KEY grp_id_groupe (grp_id_groupe)
) COMMENT='Log des mails envoyes';

CREATE TABLE IF NOT EXISTS #__hecmailing_log_attachment (
	log_id_message integer NOT NULL,
	log_nm_file varchar(250) NOT NULL,
	PRIMARY KEY (log_id_message, log_nm_file)
) COMMENT='Pieces jointes des mails envoyes';

CREATE TABLE IF NOT EXISTS #__hecmailing_contact (
	ct_id_contact integer NOT NULL AUTO_INCREMENT COMMENT 'Identifiant du contact',
	grp_id_groupe integer NOT NULL COMMENT 'Identifiant du groupe associe',
	ct_nm_contact varchar(30) NOT NULL COMMENT 'nom du contact',
	ct_cm_contact varchar(250) NULL COMMENT 'Descriptif du contact',
	ct_vl_info Text   NULL COMMENT 'Infos du contact',
	ct_vl_template TEXT NOT NULL COMMENT 'Template HTML pour l''envoi du message' ,
	ct_vl_prefixsujet VARCHAR(40) NULL DEFAULT '' COMMENT 'Prefixe de sujet', 
	published tinyint(4)   NOT NULL default '1' COMMENT '1=Publie/0=Non Publie',
	checked_out tinyint(4) NOT NULL DEFAULT '1',
	checked_out_time datetime ,
	PRIMARY KEY (`ct_id_contact`),
	KEY `grp_id_groupe` (`grp_id_groupe`)
) COMMENT='Info contact';
  				
CREATE TABLE IF NOT EXISTS #__hecmailing_rights (
	userid integer  NULL COMMENT 'Identifiant Utilisateur Joomla',
	groupid integer NULL COMMENT 'Identifiant du groupe Joomla',
	grp_id_groupe integer NOT NULL COMMENT 'Identifiant du groupe associe',
	flag int NOT NULL DEFAULT 1 COMMENT 'Droits associés : 1 Utiliser, 2 gerer liste, 4 Donner les droits, 8 Modifier libelles'
) COMMENT='Permissions Groupes';


CREATE TABLE IF NOT EXISTS #__hecmailing_message (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identifiant du message',
  `message_date` datetime NOT NULL COMMENT 'Date de soumission',
  `message_subject` varchar(200) NOT NULL COMMENT 'Sujet/Objet',
  `message_body` text NOT NULL COMMENT 'Corps du message',
  `message_from` varchar(150) NOT NULL COMMENT 'Adresse e-mail expediteur',
  `message_fromname` varchar(150) DEFAULT NULL COMMENT 'Nom expediteur',
  `group_id` int(11) NOT NULL COMMENT 'Groupe HECMAILING utilise pour envoyer le message',
  `user_id` int(11) NOT NULL COMMENT 'Utilisateur Joomla ayant fait le message',
  `message_read_notification` smallint(6) DEFAULT '0' COMMENT 'notification de lecture : 0 aucune, 1 notification et TAG place en haut, 2 Notification et TAG place en bas, 3 Notification et variable {readtag} utilisee pour positionner le TAG',
  PRIMARY KEY (`id`),
  KEY `fk_message_group` (`group_id`)
) COMMENT='Liste des message envoyes/a envoyer';

/*Table structure for table `j3_hecmailing_message_attachment` */
CREATE TABLE IF NOT EXISTS #__hecmailing_message_attachment (
  `message_id` int(11) NOT NULL COMMENT 'Id du message (champ id table message)',
  `file` varchar(250) NOT NULL COMMENT 'Emplacement du fichier sur le serveur',
  `cid` varchar(30) NOT NULL COMMENT 'Valeur du CID pour les images incorporees, NULL sinon',
  `filename` varchar(200) DEFAULT NULL COMMENT 'Nom du fichier (sans le chemin)',
  PRIMARY KEY (`message_id`,`file`,`cid`),
  KEY `fk_message_attachment_message` (`message_id`)
) COMMENT='Pieces jointes des messages envoyes';

/*Table structure for table `#__hecmailing_message_recipient` */



CREATE TABLE IF NOT EXISTS #__hecmailing_message_recipient (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identifiant du recipient',
  `message_id` int(11) NOT NULL DEFAULT '0' COMMENT 'Identifiant du message (champ id table message)',
  `userid` int(11) NOT NULL DEFAULT '0' COMMENT 'Identifiant de l''utilisateur Joomla ou null',
  `email` varchar(200) NOT NULL COMMENT 'Adresse E-Mail',
  `name` varchar(200) DEFAULT NULL COMMENT 'Nom d''affichage de l''utilsateur',
  `status` int(11) NOT NULL DEFAULT '0' COMMENT 'Status d''envoi : 0 A Envoyer, 1 Envoyé, 9 Erreur Envoi,8 Domaine Exclus',
  `error` text COMMENT 'Message d''erreur',
  `timestamp` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Date et Heure Soumission, Envoi ou Lecture',
  `params` text COMMENT 'Parametres a utiliser pour le remplacement des variables presentes dans le corps',
  PRIMARY KEY (`id`),
  KEY `fk_message_recipient_message` (`message_id`)
)  COMMENT='Liste des destinataires';

/*Table structure for table `#__hecmailing_answers` */
CREATE TABLE IF NOT EXISTS #__hecmailing_answers (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identifiant du recipient',
  `message_id` int(11) NOT NULL DEFAULT '0' COMMENT 'Identifiant du message (champ id table message)',
  `recipient_id` int(11) NOT NULL DEFAULT '0' COMMENT 'Identifiant du recipient (champ id table message_recipient)',
  `question_code` varchar(20) NOT NULL COMMENT 'Question code',
  `question_title` varchar(80) NOT NULL COMMENT 'Question caption',
  `token` varchar(35) DEFAULT NULL COMMENT 'Token use for answer',
  `answer_list` text COMMENT 'Answers',
  PRIMARY KEY (`id`),
  KEY `fk_answers` (`message_id`,`recipient_id`)
)  COMMENT='Answers';