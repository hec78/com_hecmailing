<?php 
/**
* @version 1.8.0
* @package hecMailing for Joomla
* @copyright Copyright (C) 2013 Hecsoft All rights reserved.
* @license GNU/GPL
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
defined('_JEXEC') or die ('restricted access'); 

jimport('joomla.application.component.model'); 

/**
 * hecMailing Component Form Model
 *
 * @package		Joomla
 */
class hecMailingModelForm extends JModelLegacy 
{ 
   /**
	 * User id
	 *
	 * @var int
	 */
   var $_id = 0; 
   var $_object=null;
   
   /**
   *    Contructor 
   **/
   function __construct() 
   { 
  	  parent::__construct(); 
   	  $this->params = JComponentHelper::getParams( 'com_hecmailing' );
      $this->isLog = ($this->params->get('debug') == 1);
      $this->isLog = false;
      if ($this->isLog)
      {
        $this->_log = JLog::getInstance('com_hecmailing.log.php');
      }
   } 

   
/**
 * Method to write text into component log
 *
 * @access	public
 * @param	string Text to write
 */
function Log($text)
{  
	if ($this->isLog)
    {
    	$this->_log->addEntry(array('comment' => $text));
    }
}

/**
 * @method getGroupeQuery : Return query for a group
 * @param int $groupe : HECMailing Group Id
 * @param int $douseprofil : 1 use profil , 0 don't use profil
 * @param string $blockcond1 : First Block condition
 * @param string $blockcond2 : 2nd Block condition
 * @return string
 */
   function getGroupeQuery($groupe, $douseprofile, $blockcond1, $blockcond2)
   {
		if (intval($douseprofile)==1)
		  $useprofile= " AND u.sendEmail=1 ";
		else
		  $useprofile="";
	      
		// Cas des id user joomla
	    $query = "SELECT u.id,u.email, u.name
	              FROM #__users u inner join #__hecmailing_groupdetail gd ON u.id=gd.gdet_id_value AND gd.gdet_cd_type=2
	              WHERE gd.grp_id_groupe=".$groupe. $useprofile;
	    // Cas des username
	    $query .= " UNION SELECT u.id,u.email, u.name
	                FROM #__users u inner join #__hecmailing_groupdetail gd ON u.username=gd.gdet_vl_value AND gd.gdet_cd_type=1
	                WHERE gd.grp_id_groupe=".$groupe. $useprofile;
	    // Cas des groupes joomla
	    if(version_compare(JVERSION,'1.6.0','<')){
		   //Code pour Joomla! 1.5  
			$query .= " UNION SELECT u.id,u.email as email, u.name as name
						FROM #__users u 
						inner join #__core_acl_aro c on c.value=u.id
						inner join #__core_acl_groups_aro_map gm on gm.aro_id=c.id AND c.section_value='users'
						inner join #__hecmailing_groupdetail gd ON gd.gdet_cd_type=3 AND gd.gdet_id_value=gm.group_id
						WHERE gd.grp_id_groupe=".$groupe. $useprofile.$blockcond1;
	      }
	      else {
	      	   //Code pour Joomla! 1.6+ 
            $query .= " UNION SELECT u.id,u.email, u.name
	                FROM #__users u inner join #__user_usergroup_map m ON u.id=m.user_id inner join #__hecmailing_groupdetail gd ON m.group_id=gd.gdet_id_value AND gd.gdet_cd_type=3 
	                WHERE gd.grp_id_groupe=".$groupe. $useprofile.$blockcond1;
	      }
	      // Cas des adresse e-mail
	      $query .= " UNION SELECT 0,gd.gdet_vl_value as email, gd.gdet_vl_value as name
	                FROM #__hecmailing_groupdetail gd 
	                WHERE gd.gdet_cd_type=4 AND gd.grp_id_groupe=".$groupe;
		return $query;
   }

/**
* Method to get email list from a group
*
* @access	public
* @param	int Group Identifier
* @param 	int useprofile => if 1 send email only if JUser sendEmail
*                 field is 1 
* @return array [email,name]     	 
*/
function getMailAdrFromGroupe($groupe, $douseprofile)
{
    $db=$this->getDBO();
    $block_mode = $this->params->get('send_to_blocked');
    switch ($block_mode)
    {
      	case 0:
      		$blockcond1=" AND u.block=0 ";
      		$blockcond2=True;
      		break;
      	case 1: // YES, IF ALL USERS OR JOOMLA GROUP	
      		$blockcond1=" AND u.block=0 ";
      		$blockcond2=False;
      		break;
      	case 2: // YES, IF USER LIST	
      		$blockcond1="";
      		$blockcond2=True;
      		break;
      	case 3: // YES, FOR ALL	
      		$blockcond1="";
      		$blockcond2=False;
      		break;
    }
    if ($groupe>0)
    {
      	$query = $this->getGroupeQuery($groupe, $douseprofile, $blockcond1, $blockcond2);
		
	}
	else	/* Tous les utilisateurs de la base (Actifs, non block�s) */
	{
		if (intval($useprofile)==1)
		  $useprofile= " WHERE u.sendEmail=1 ".$blockcond2;
		else
		{
			if ($blockcond2)
			{
				$useprofile=" WHERE u.block=0 ";
			}
			else
			{
				$useprofile="";
			}
		  
		}
		$query = "SELECT email, name FROM #__users u " . $useprofile;
	}
	$db->setQuery($query);
    if (!$rows = $db->loadRowList())
    {
        return false;
    }
      
	// Cas des groupes HEC Mailing (groupe de groupe)
	$query = "SELECT gdet_id_value FROM #__hecmailing_groupdetail 
				WHERE gdet_cd_type=5 AND grp_id_groupe=".$groupe ;

	$db->setQuery($query);
	$rowsfromgroupes = array();
	if ($rows2 = $db->loadRowList())
	{
		$rowgrp=array();
		foreach($rows2 as $item)
		{
			
			$query = $this->getGroupeQuery($item[0], $douseprofil, $blockcond1, $blockcond2);
			$db->setQuery($query);
			if ($rowsgrp = $db->loadRowList())
			{
				$rowsfromgroupes = array_merge($rowsfromgroupes, $rowsgrp);
				$this->Log("Append group ".$item[0]. " : ". count($rowsgrp)." email found");
			}
		}

		$rows = array_merge($rows,$rowsfromgroupes);
	}
	// Supprime les doublons
	$rowsout=array();
	foreach($rows as $r)
	{
		$rowsout[$r[1]] = $r;
	}
	
    return $rowsout;
}

  /**
	 * Method to get userType option list
	 *
	 * @access	public
	 * @param	int if 1 add All option
	 * @return array of Html select option of existing user Type     	 
	 */
   function getUserType($tous)
   {
      $db=$this->getDBO();
      if(version_compare(JVERSION,'1.6.0','<')){
		 //Code pour Joomla! 1.5  
      	$query = "SELECT distinct userType FROM #__users";
      } else {
      	$query = "SELECT distinct Title FROM #__user_usergroup";
      }
                              
      $db->setQuery($query);
      if (!$rows = $db->loadRowList())
      {
          return false;
      }
     $val = array();
     foreach($rows as $r)
     {
        $val[$no][] = JHTML::_('select.option', $r[0], $r[0], 'usertype', 'usertype');

      }

   }
    
    
    
    /**
	 * Method to get mailing groups as HTML select
	 *
	 * @access	public
	 * @param	int if =1 Add a row for all members
	 * @param int if =1 Add a row for saving
	 * @return array of Html option     	 
	 */
   function getGroupes($tous, $save, $askselect)
   {
      $db=$this->getDBO();
      $user =JFactory::getUser();
      $admintype = $this->params->get('usertype');
      
      $admingroup = $this->params->get('groupaccess');
      if ($this->isInGroupe($admingroup) || $this->isAdminUserType($admintype))
      	$query = "SELECT grp_id_groupe, grp_nm_groupe FROM #__hecmailing_groups Where published=1 order by grp_nm_groupe";
      else
      {
      	if(version_compare(JVERSION,'1.6.0','<')){
		 //Code pour Joomla! 1.5 
		 $query = "SELECT DISTINCT g.grp_id_groupe, grp_nm_groupe,1 as flag FROM #__hecmailing_groups g
 			INNER JOIN #__hecmailing_rights r ON r.grp_id_groupe=g.grp_id_groupe
 			LEFT JOIN #__users u ON u.id=r.userid
			WHERE published=1 AND (r.userid=".$user->id." OR r.groupid=".$user->gid.") ORDER BY grp_nm_groupe";
      	}else { 
      		$query = "SELECT DISTINCT g.grp_id_groupe, grp_nm_groupe, r.flag FROM #__hecmailing_groups g
	 			INNER JOIN #__hecmailing_rights r ON r.grp_id_groupe=g.grp_id_groupe
	 			LEFT JOIN #__users u ON u.id=r.userid LEFT JOIN #__user_usergroup_map m ON  m.group_id=r.groupid AND m.user_id=".$user->id."
				WHERE published=1 AND ((r.flag AND 1)=1) AND ((r.userid=".$user->id." AND ifnull(r.groupid,0)=0) OR (r.groupid=m.group_id AND ifnull(r.userid,0)=0)) ORDER BY grp_nm_groupe";
      	}
      }                        
      $db->setQuery($query);
      if (!$rows = $db->loadRowList())
      {
          return false;
      }
     $val = array();
     $rights = array();
     $save=false;
     if ($askselect)
     {
        $val[] = JHTML::_('select.option', -2, "{".JText::_('COM_HECMAILING_SELECT_GROUP')."}", 'grp_id_groupe', 'grp_nm_groupe');
     }
     if ($save)
     {
        $val[] = JHTML::_('select.option', -1, "{".JText::_('COM_HECMAILING_SAVE')."}", 'grp_id_groupe', 'grp_nm_groupe');
     }
     if ($tous=='1' && ($this->isAdminUserType($admintype) || $this->isInGroupe($admingroup) ))
     {	// On n'affiche la ligne Tous les utilisateur que si l'utilisateur actuellement connect� est admin ou fait partie du groupe d'admin (groupe HEC Mailing)
        $val[] = JHTML::_('select.option', 0, '{'.JText::_('COM_HECMAILING_ALL_USERS').'}', 'grp_id_groupe', 'grp_nm_groupe');
     }
     foreach($rows as $r)
     {
     	if (($r[2] & 6)>0)
     	{
     		$val[] = JHTML::_('select.option', $r[0], $r[1]."*", 'grp_id_groupe', 'grp_nm_groupe');
     		$rights[]=$r[0].":".$r[2];
     	}
     	else
     	{
        	$val[] = JHTML::_('select.option', $r[0], $r[1], 'grp_id_groupe', 'grp_nm_groupe');
     	}
     }
     
     if ($tous=='2' && ($this->isAdminUserType($admintype) || $this->isInGroupe($admingroup) ))
     {  // On n'affiche la ligne Tous les utilisateur que si l'utilisateur actuellement connect� est admin ou fait partie du groupe d'admin (groupe HEC Mailing)
        $val[] = JHTML::_('select.option', 0, '{'.JText::_('COM_HECMAILING_ALL_USERS').'}', 'grp_id_groupe', 'grp_nm_groupe');
     }
     
     return array($val,$rights);

   }

    /**
	 * Method to get the mailfrom list
	 *
	 * @access	public
	 * @return array of Html select option of emails     	 
	 */   
    function getFrom()
   {
		// Modif Joomla 1.6+
		$mainframe = JFactory::getApplication();

		$user =JFactory::getUser();
		$MailFrom 	= $mainframe->getCfg('mailfrom');
		$FromName 	= $mainframe->getCfg('fromname');
		$val = array();
		$val[] = JHTML::_('select.option', $user->email.';'.$user->name, $user->name, 'email', 'name');
		$val[] = JHTML::_('select.option', $MailFrom.';'.$FromName, JText::_('COM_HECMAILING_DEFAULT').'('.$FromName.')', 'email', 'name');
        
		return $val;
   }

   /**
	 * Method to know if current logged user is in a mailing group
	 *
	 * @access	public
	 * @param	int Groupe identifier
	 * @return true is current user is in the group and false else     	 
	 */
  function isInGroupe($groupe)
   {
      $db=$this->getDBO();
      $user =JFactory::getUser();
      $query = "SELECT *
                FROM #__hecmailing_groupdetail gd inner join  #__hecmailing_groups g on gd.grp_id_groupe=g.grp_id_groupe
                WHERE g.grp_nm_groupe=".$db->Quote($groupe)." AND gdet_id_value=".$user->id." AND gdet_cd_type=2";
                                   
      $db->setQuery($query);
      if (!$rows = $db->loadRow())
      {
          return false;
      }
      
      return true;
   }

   function isAdminUserType($admintype)
   {
        if(version_compare(JVERSION,'1.6.0','<')){
            //Code pour Joomla! 1.5  
            return strpos($admintype, $user->usertype);
        }else{
          //Code pour Joomla >= 1.6.0
          $db=$this->getDBO();
          $user =JFactory::getUser();
          $userid = $user->get( 'id' );
          $listUserTypeAllowed = explode(";",$admintype);
          $query = "select count(*) FROM #__usergroups g LEFT JOIN #__user_usergroup_map AS map ON map.group_id = g.id ";
          $query.= "WHERE map.user_id=".(int) $userid." AND g.title IN ('".join("','",$listUserTypeAllowed)."')";
          $db->setQuery($query);
          $rows=$db->loadRow();
          if (!$rows)
  	      {
  	          return false;
  	      }
  	      if ($rows[0]==0)
  	      {
  	      	return false;
	        }
	        return true;
      }
   }

	function hasGroupe()
	{
		$db=$this->getDBO();
      $user =JFactory::getUser();
      $admintype = $this->params->get('usertype');
      
      $admingroup = $this->params->get('groupaccess');
      if ($this->isInGroupe($admingroup) || $this->isAdminUserType($admintype))
      {
      	$query = "SELECT grp_id_groupe FROM #__hecmailing_groups Where published=1 order by grp_nm_groupe";
 	  }
 	  else
 	  {
 	      if(version_compare(JVERSION,'1.6.0','<')){
        		$query = "SELECT g.grp_id_groupe FROM #__hecmailing_groups g
           			INNER JOIN #__hecmailing_rights r ON r.grp_id_groupe=g.grp_id_groupe
           			LEFT JOIN #__users u ON u.id=r.userid
          			WHERE published=1 AND (r.userid=".$user->id." OR r.groupid=".$user->gid.") ORDER BY grp_nm_groupe";
        }
        else
        {
            $query = "SELECT g.grp_id_groupe FROM #__hecmailing_groups g
           			INNER JOIN #__hecmailing_rights r ON r.grp_id_groupe=g.grp_id_groupe
           			WHERE published=1 AND r.userid=".$user->id." 
                UNION SELECT r.grp_id_groupe FROM #__hecmailing_rights r INNER JOIN #__user_usergroup_map map 
                ON r.groupid=map.group_id WHERE map.user_id=".$user->id;        
        
        }  	
		}
		  $db->setQuery($query);
		  $rows = $db->loadRow();
	      if (!$rows)
	      {
	          return false;
	      }
	      
	      
      return true;
	}

   /**
	 * Method to get Html select option of saved templates
	 *
	 * @access	public
	 * @return array of Html option     	 
	 */
  function getSavedMails()
  {
        $db=$this->getDBO();
      $query = "SELECT msg_id_message,ifnull(msg_lb_message,msg_vl_subject) FROM #__hecmailing_save";
                              
      $db->setQuery($query);
      if (!$rows = $db->loadRowList())
      {
          return false;
      }
     $val = array();
     $val[] = JHTML::_('select.option', 0, JText::_('COM_HECMAILING_NONE'), 'msg_id_message', 'msg_lb_message');
     
     foreach($rows as $r)
     {
        $val[] = JHTML::_('select.option', $r[0], $r[1], 'msg_id_message', 'msg_lb_message');
     }
     return $val;
  }
  
     /**
	 * Method to get a template saved
	 *
	 * @access	public
	 * @param	int Template mail identifier
	 * @return array with Message Id, Subject, Body     	 
	 */
  function getSavedMail($idmsg)
  {
        $db=$this->getDBO();
      $query = "SELECT msg_id_message,msg_vl_subject,msg_vl_body FROM #__hecmailing_save where msg_id_message=".$idmsg;
                              
      $db->setQuery($query);
      if (!$row = $db->loadRow())
      {
          return false;
      }
     return $row;
  }

   /**
	 * Method to get mailing groups as HTML select
	 *
	 * @access	public
	 * @param	int if =1 Add a row for all members
	 * @param int if =1 Add a row for saving
	 * @return array of Html option     	 
	 */
   function getLogDetail($idlog)
   {
      $db=$this->getDBO();
      $query = "SELECT l.log_id_message, l.log_dt_sent,l.log_vl_from,l.log_vl_subject,l.log_vl_body,l.grp_id_groupe,	l.usr_id_user ,
  						l.log_nb_ok,l.log_nb_errors, u.name,u.username, g.grp_nm_groupe,log_vl_mailok,log_vl_mailerr 
										FROM #__hecmailing_log l LEFT JOIN #__hecmailing_groups g ON l.grp_id_groupe=g.grp_id_groupe 
										LEFT JOIN #__users u on l.usr_id_user=u.id Where l.log_id_message=".$idlog;                        
      $db->setQuery($query);
       if (!$row = $db->loadObject())
      {
          return false;
      }
      $query = "SELECT l.log_nm_file
										FROM #__hecmailing_log_attachment l Where l.log_id_message=".$idlog;                        
      $db->setQuery($query);
      $attach = $db->loadObjectList();
      $attlist=array();
      foreach($attach as $r)
      {
      	$attlist[]= $r->log_nm_file;
      }
      $row->attachment = $attlist;
     return $row;
     

   }


} 

?> 

