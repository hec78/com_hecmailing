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