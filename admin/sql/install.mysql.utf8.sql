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

CREATE TABLE IF NOT EXISTS #__hecmailing_log_mails (
	log_id_message integer NOT NULL,
	user_id integer NULL,
	email varchar(100) NOT NULL,
	status smallint DEFAULT 1 COMMENT 'Statut d''envoi, 1 Envoye, 2 Lu, 9 Erreur',
	PRIMARY KEY (log_id_message, email)
) COMMENT='Detail des mails envoyes';


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