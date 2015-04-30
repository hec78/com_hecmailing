
			
ALTER TABLE #__hecmailing_groups 
ADD COLUMN published tinyint(4)   NOT NULL default '1' COMMENT '1=Publie/0=Non Publie',
ADD COLUMN checked_out tinyint(4) NOT NULL DEFAULT '1',
ADD COLUMN checked_out_time datetime;
			
ALTER TABLE #__hecmailing_save 
ADD COLUMN published tinyint(4)   NOT NULL default '1' COMMENT '1=Publie/0=Non Publie',
ADD COLUMN checked_out tinyint(4) NOT NULL DEFAULT '1',
ADD COLUMN 	checked_out_time datetime ;

ALTER TABLE  #__hecmailing_contact 
ADD COLUMN published tinyint(4)   NOT NULL default '1' COMMENT '1=Publie/0=Non Publie',
ADD COLUMN checked_out tinyint(4) NOT NULL DEFAULT '1',
ADD COLUMN checked_out_time datetime ;
  				