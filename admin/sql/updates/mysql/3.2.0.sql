CREATE TABLE IF NOT EXISTS jnew_hecmailing_message (
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
CREATE TABLE IF NOT EXISTS jnew_hecmailing_message_attachment (
  `message_id` int(11) NOT NULL COMMENT 'Id du message (champ id table message)',
  `file` varchar(250) NOT NULL COMMENT 'Emplacement du fichier sur le serveur',
  `cid` varchar(30) NOT NULL COMMENT 'Valeur du CID pour les images incorporees, NULL sinon',
  `filename` varchar(200) DEFAULT NULL COMMENT 'Nom du fichier (sans le chemin)',
  PRIMARY KEY (`message_id`,`file`,`cid`),
  KEY `fk_message_attachment_message` (`message_id`)
) COMMENT='Pieces jointes des messages envoyes';

/*Table structure for table `j3_hecmailing_message_recipient` */



CREATE TABLE IF NOT EXISTS jnew_hecmailing_message_recipient (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identifiant du recipient',
  `message_id` int(11) NOT NULL DEFAULT '0' COMMENT 'Identifiant du message (champ id table message)',
  `userid` int(11) NOT NULL DEFAULT '0' COMMENT 'Identifiant de l''utilisateur Joomla ou null',
  `email` varchar(200) NOT NULL COMMENT 'Adresse E-Mail',
  `name` varchar(200) DEFAULT NULL COMMENT 'Nom d''affichage de l''utilsateur',
  `status` int(11) NOT NULL DEFAULT '0' COMMENT 'Status d''envoi : 0 A Envoyer, 1 Envoyé, 9 Erreur Envoi,8 Domaine Exclus',
  `error` text COMMENT 'Message d''erreur',
  `timestamp` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Date et Heure Soumission, Envoi ou Lecture',
  `params` text COMMENT 'Parametres a utiliser pour le remplacement des variables presentes dans le corps',
  PRIMARY KEY (`id`),
  KEY `fk_message_recipient_message` (`message_id`),
)  COMMENT='Liste des destinataires';
  				