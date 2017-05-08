<?php 
/**
* @version   3.4.0
* @package   HEC Mailing for Joomla
* @copyright Copyright (C) 1999-2017 Hecsoft All rights reserved.
* @author    Hervé CYR
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
defined('_JEXEC') or die ('restricted access'); 

jimport('joomla.application.component.model'); 
JLoader::register('HecMailingGroupsFrontendHelper',JPATH_COMPONENT.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'groups.php');
JLoader::register('HecMailingMailFrontendHelper',JPATH_COMPONENT.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mail.php');
/**
 * hecMailing Component Form Model
 *
 * @package		Joomla
 */
class hecMailingModelSend extends JModelForm 
{ 
   /**
	 * User id
	 *
	 * @var int
	 */
   var $_id = 0; 
   var $_object=null;
   var $_item=null;
   /**
   *    Contructor 
   **/
   function __construct() 
   { 
  	  parent::__construct(); 
   	  $this->params = JComponentHelper::getParams( 'com_hecmailing' );
      $this->isLog = ($this->params->get('debug') == 1);
      if ($this->isLog)
      {
      	JLog::addLogger(array(	// Set the name of the log file
      			'text_file' => 'com_hecmailing.log.php'	));
      
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
   		JLog::add($text);
   		 
   	}
   }
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState()
	{
		$app = JFactory::getApplication('site');

		// Load state from the request.
		$pk = $app->input->getInt('idmessage');
		$this->setState('send.id', $pk);

	}

	/**
	 * Method to get the contact form.
	 * The base form is loaded from XML and then an event is fired
	 *
	 * @param   array    $data      An optional array of data for the form to interrogate.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  JForm  A JForm object on success, false on failure
	 *
	 * @since   1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_hecmailing.send', 'send', array('control' => 'jform', 'load_data' => true));

		if (empty($form)){	return false;	}
		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  array    The default data is an empty array.
	 *
	 * @since   1.6.2
	 */
	protected function loadFormData()
	{
		$data = (array) JFactory::getApplication()->getUserState('com_hecmailing.send.data', array());
		$this->preprocessData('com_hecmailing.send', $data);
		return $data;
	}

	/**
	 * Gets a contact
	 *
	 * @param   integer  $pk  Id for the contact
	 *
	 * @return  mixed Object or null
	 *
	 * @since   1.6.0
	 */
	public function &getItem($pk = null)
	{
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('send.id');

		if ($this->_item === null)
		{
			$this->_item = array();
		}

		if (!isset($this->_item[$pk]))
		{
			try
			{
				
				$this->_item[$pk] = $this->getMessage($pk);
				if ($this->_item[$pk])
					$this->_item[$pk]->groupe=$this->_item[$pk]->group_id;
				JFactory::getApplication()->setUserState('com_hecmailing.send.data', $this->_item[$pk]);
			}
			catch (Exception $e)
			{
				$this->setError($e);
				$this->_item[$pk] = false;
			}
		}

		return $this->_item[$pk];
	}

	

   


/**
 * @method getGroupeQuery : Return query for a group
 * @param int $groupe : HECMailing Group Id
 * @param string $blockcond1 : First Block condition
 * @param string $blockcond2 : 2nd Block condition
 * @return string
 */
   function getGroupeQuery($groupe,  $blockcond1, $blockcond2)
   {
		$useprofile="";
	      
		// Cas des id user joomla
	    $query = "SELECT u.id,u.email, u.name
	              FROM #__users u inner join #__hecmailing_groupdetail gd ON u.id=gd.gdet_id_value AND gd.gdet_cd_type=2
	              WHERE gd.grp_id_groupe=".$groupe. $useprofile;
	    // Cas des username
	    $query .= " UNION SELECT u.id,u.email, u.name
	                FROM #__users u inner join #__hecmailing_groupdetail gd ON u.username=gd.gdet_vl_value AND gd.gdet_cd_type=1
	                WHERE gd.grp_id_groupe=".$groupe. $useprofile;
	   
	      	   //Code pour Joomla! 1.6+ 
            $query .= " UNION SELECT u.id,u.email, u.name
	                FROM #__users u inner join #__user_usergroup_map m ON u.id=m.user_id inner join #__hecmailing_groupdetail gd ON m.group_id=gd.gdet_id_value AND gd.gdet_cd_type=3 
	                WHERE gd.grp_id_groupe=".$groupe. $useprofile.$blockcond1;
	   
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
* @param	int HecMailing Group Identifier
* @return array ['id'=>id,'email'=>email,'name'=>joomla name]     	 
*/
function getMailAdrFromGroupe($groupe)
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
    {	// HEC Mailing groupe selected
      	$query = $this->getGroupeQuery($groupe, $blockcond1, $blockcond2);
	}
	else
	{	// All Joomla users selected (Actives and not Blocked)
		if ($blockcond2){$useprofile=" WHERE u.block=0 ";}else{	$useprofile="";	}
	    $query = "SELECT id,email, name FROM #__users u " . $useprofile;
	}
	$db->setQuery($query);
	// Error
    if (!$rows = $db->loadObjectList()) { return false; }
      
	// HEC Mailing group item (group of group)
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
			if ($rowsgrp = $db->loadObjectList())
			{
				$rowsfromgroupes = array_merge($rowsfromgroupes, $rowsgrp);
				$this->Log("Append group ".$item[0]. " : ". count($rowsgrp)." email found");
			}
		}
		$rows = array_merge($rows,$rowsfromgroupes);
	}
	// Delete doublons
	$rowsout=array();
	foreach($rows as $r){$rowsout[$r->email] = $r;}
	
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
		 // for Joomla! 1.5  
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
     foreach($rows as $r) { $val[$no][] = JHTML::_('select.option', $r[0], $r[0], 'usertype', 'usertype'); }
	 return $val;
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
     		$val[] = JHTML::_('select.option', intval($r[0]), $r[1]."*", 'grp_id_groupe', 'grp_nm_groupe');
     		$rights[]=$r[0].":".$r[2];
     	}
     	else
     	{
        	$val[] = JHTML::_('select.option', intval($r[0]), $r[1], 'grp_id_groupe', 'grp_nm_groupe');
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
		$app = JFactory::getApplication();

		$user =JFactory::getUser();
		$MailFrom 	= $app->get('mailfrom');
		$FromName 	= $app->get('fromname');
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
	 * Method to get message information
	 *
	 * @access	public
	 * @param	int if =1 Add a row for all members
	 * @param int if =1 Add a row for saving
	 * @return array of Html option     	 
	 */
   function getMessage($idmessage)
   {
      $db=$this->getDBO();
      $query = "SELECT * FROM #__hecmailing_message Where id=".$idmessage;                        
      $db->setQuery($query);
       if (!$row = $db->loadObject())
      {
          return false;
      }
      $query = "SELECT * FROM #__hecmailing_message_attachment Where message_id=".$idmessage;                        
      $db->setQuery($query);
      $attach = $db->loadObjectList();
      $attachment=array();
      
      foreach ($attach as $att)
      {
      	if ($att->cid!='')
      		$row->message_body = str_replace("cid:".$att->cid, $att->file, $row->message_body);
      	else
      		$attachment[]=$att->file;
      }
      $row->attachment = $attachment;
      return $row;
     

   }

   /**
    * Method to send current mail to selected group
    *
    * @access	public
    */
   function prepare_send($data, $files)
   {
	   	
	   	$app = JFactory::getApplication();
	   	// Check for request forgeries
	   	JSession::checkToken() or jexit( 'Invalid Token' );
	   	//get a refrence of the page instance in joomla
	   	$document=JFactory::getDocument();
	   	
	   	$attach_path='';
	   	$session =JFactory::getSession();
	   	$db	=JFactory::getDBO();
	   
	   	jimport( 'joomla.mail.helper' );
	   
	   	$SiteName 	= $app->get('sitename');
	   	$MailFrom 	= $app->get('mailfrom');
	   	$FromName 	= $app->get('fromname');
	    $formdata   = $data['jform']; 
	   	$link 		= base64_decode( $app->input->get( 'link', '', 'post', 'base64' ) );
	   
	   	$params = JComponentHelper::getParams( 'com_hecmailing' );
	   
	   
	   	// An array of e-mail headers we do not want to allow as input
	   	$headers = array (	'Content-Type:',
	   			'MIME-Version:',
	   			'Content-Transfer-Encoding:',
	   			'bcc:',
	   			'cc:');
	   
	   	// An array of the input fields to scan for injected headers
	   	$fields = array ('mailto',
	   			'sender',
	   			'from',
	   			'subject');
	   
	   	/*
	   	 * Here is the meat and potatoes of the header injection test.  We
	   	 * iterate over the array of form input and check for header strings.
	   	 * If we find one, send an unauthorized header and die.
	   	 */
	   	foreach ($fields as $field)
	   	{
	   		foreach ($headers as $header)
	   		{
	   			if (array_key_exists ( $field , $data ))
	   			{
	   				if (strpos($data[$field], $header) !== false)
	   				{
	   					raise (new Exception('', 403));
	   				}
	   			}
	   		}
	   	}
	   
	   	/*
	   	 * Free up memory
	   	 */
	   	unset ($headers, $fields);
	   
	   	/* Get options from post */
	   	if (isset($formdata['incorpore']))	$image_incorpore = $formdata['incorpore']=='1'; else $image_incorpore = false;
	   	   
	   	// Get from field and decode name and email from it (from;sender)
	   	if (isset($formdata["from"]))
	   		$fromvalue 		 = $formdata['from'];
	   	else 
	   		$fromvalue = $MailFrom.';'.$FromName;
	   	$tmp = explode(";" , $fromvalue);
	   	$from=$tmp[0];
	   	$sender=$tmp[1];
	   
	   	// Get subject and body
	   	$subject_default 	= JText::sprintf('COM_HECMAILING_DEFAULT_SUBJECT', $sender);
	   	if (isset($formdata['message_subject'])) $subject= $formdata['message_subject']; else $subject=$subject_default;
	   	if (isset($formdata['message_body']))	$body = $formdata['message_body']; else $body='';
	   	if (isset($formdata['groupe'])) $groupe	= $formdata['groupe'];
	   	$sendcount = intval($params->get('send_count', 1));
	   
	   	// Get attachments
	   	$attach = array();
	   	
	   	$pj_uploaded = array();
	   	$nbattach = intval($data['jform_attachment_localcount']);	// attachment count
	   
	   	// if email must be saved, temporary attachment path become saved attachment path
	   	// in order to be able to send again the mail and is attachments
	   	$attach_path=$params->get('attach_path', $attach_path);
	   	$path = realpath(JPATH_ROOT).DS;	// Get path root
	   		
	   	// Add read TAG
	   	//$body .= "<img=\"".JURI::base(false)."index.php?option=com_hecmailing&task=send.mail_read&email={EMAIL}&mail_id={MAIL_ID}\" alt=\"READ TAG\" />";
	   
	   	// Create attachment directory if doesn't exist
	   	if (!JFolder::exists($path.$attach_path))
	   	{
	   		// Create directory
	   		if (!JFolder::create($path.$attach_path))
	   		{
	   			$error	= JText::sprintf('COM_HECMAILING_CANT_CREATE_DIR', $path.$attach_path);
	   			raise (new Exception($error) );
	   		}
	   		// Create dummy index.html file for prevent list directory content
	   		$text="<html><body bgcolor=\"#FFFFFF\"></body></html>";
	   		JFile::write($path.$attach_path.DIRECTORY_SEPARATOR."index.html",$text);
	   	}
	   
	   	// Process uploaded files
	   	if (isset($files['jform_attachment']))
	   	{
		   	$files=$files['jform_attachment'];
		   	for($i=0;$i<count($files['name']);$i++)
		   	{
		   		// Get uploaded files
		   		$file = $files['name'][$i];
		   		$filename = JFile::makeSafe($file);
		   		$src = $files['tmp_name'][$i];
		   		if ($src!='')
		   		{
		   			//Set up the source and destination of the file
		   			$dest = $attach_path.DIRECTORY_SEPARATOR.$file;
		   			// Upload uploaded file to attchment directory (temp or saved dir)
		   			JFile::upload($src, $path.$dest, false,true);
		   			$attach[] = array('file'=>$dest, 'type'=>1,'filename'=>$file, 'mime'=>'','cid'=>'');
		   			
		   			// Bug #3013589 : Delete Failed message
		   			//$pj_uploaded[] = $path.DS.$dest;
		   			$pj_uploaded[] = $path.$dest;
		   		}
		   	}
	   	}
	   	// Process hosted files attachment
	   	$local = array();
	   	$img="";
	   
	   	// for each file ...
	   	foreach($data['jform_attachment'] as $att)
	   	{
	   		// Check if checkbox is checked yet (not canceled)
	   		$file = $att;
	   		$filename = basename($file);
		   	// add it in attachment list
	   		$attach[] = array('file'=>$file,'filename'=>$filename, 'mime'=>'', 'type'=>1, 'cid'=>'');
	   		$files[]=$filename;
	   		
	   	}
	   	// Check for a valid to address
	   	$error	= false;
	   	$body = "<html><head></head><body>".$body."</body></html>";
	   
	   	// Check for a valid from address
	   	if ( ! $from || ! JMailHelper::isEmailAddress($from) )
	   	{
	   		$error	= JText::sprintf('COM_HECMAILING_EMAIL_INVALID', $fromvalue ."/".$from."/".$sender);
	   		raise (new Exception( $error ));
	   	}
	   
	   	// Clean the email data
	   	$subject = JMailHelper::cleanSubject($subject);
	   	$body	 = JMailHelper::cleanBody($body);
	   	$sender	 = JMailHelper::cleanAddress($sender);
	   
	   	$inline=array();
	   	$bodytolog = $body;
	   
	   	// Check answers
	   	$answers = HecMailingMailFrontendHelper::extractQuestionsFromHTML($body);
	   	$body=$answers->html;
	   	
	   	
	   	// if embedded image is enabled
	   	if ($image_incorpore)
	   	{
	   		$ei = HecMailingMailFrontendHelper::extractEmbeddedImagesFromHTML($body);
	   		//("html"=>$doc->saveHTML(), "files"=>$image_list);
	   		$body=$ei['html'];
	   		$embed=$ei['files'];
	   		foreach($embed as $elmt){
	   			//array ($cidname,$file, $filename, $mime);
	   			$attach[] = array('file'=>$elmt['file'],'cid'=>$elmt['cid'], 'type'=>2, 'filename'=>$elmt['filename'], 'mime'=>$elmt['mime']);
	   		}
	   	}
	   	else	// if embedded image is disabled -> link use
	   	{
	   		// search and replace relative image url (without http://) by absolute image url
	   		// by concat relative path with site url
	   		$body = HecMailingMailFrontendHelper::AddSitePrefixForImagesFromHTML($body);
	   	}
	   
	   	// Process hyperlink : Replace relative url by absolute url for link with relative path (without http://)
	   	$body=HecMailingMailFrontendHelper::AddSitePrefixForLinksFromHTML($body);
	   	
	   	$errors=0;
	   	$lstmailok=array();
	   	$lstmailerr=array();
	   	$list=array();
	   	$listElmt=array();
	   	$user =JFactory::getUser();
	   	$messageid=false;
	   	if ($groupe>=0)	// if group selected
	   	{
	   		// Get email list from groupe
	   		$detail = $this->getMailAdrFromGroupe($groupe);
	   			
	   			
	   		// Insert email info
	   		//Create data object
	   		$rowdetail = new stdClass();
	   		/*if(version_compare(JVERSION,'1.6.0','<')){ $rowdetail->log_dt_sent = JFactory::getDate()->toFormat(); }
	   		 else { 	$rowdetail->log_dt_sent = JFactory::getDate()->format("%Y-%M-%d %H:%M:%S"); }*/
	   		$rowdetail->message_date = JFactory::getDate();
	   		if(version_compare(JVERSION,'1.6.0','<')){ $rowdetail->message_date = $rowdetail->message_date->toFormat(); }
	   		else { $rowdetail->message_date = $rowdetail->message_date->format("Y-m-d H:i:s"); }
	   		$rowdetail->message_subject = $subject ;
	   		$rowdetail->message_body = $body ;
	   		$rowdetail->message_from = $from   ;
	   		$rowdetail->message_read_notification = $formdata["message_read_notification"];
	   		$rowdetail->group_id =  $groupe    ;
	   		$rowdetail->user_id =  $user->id  ;
	   		   			
	   		//Insert new record into groupdetail table.
	   		$ret = $db->insertObject('#__hecmailing_message', $rowdetail);
	   		$messageid = $db->insertid();
	   
	   		// Insert attachments
	   		foreach($attach as $att)
	   		{
	   			$rowfile = new stdClass();
	   			$rowfile->message_id =$messageid  ;
	   			$rowfile->file = $att['file']  ;
	   			$rowfile->filename = $att['filename']  ;
	   			$rowfile->cid = $att['cid']  ;
	   			$ret = $db->insertObject('#__hecmailing_message_attachment', $rowfile);
	   		}
	   			
	   			
	   		$nb=0;
	   		foreach($detail as $elmt)	// send to each email
	   		{
	   			$email = $elmt->email;
	   			// check email
	   			if ( ! $email  || ! JMailHelper::isEmailAddress($email) )
	   			{
	   				// Bad email --> Add warning and add error counter, but don't send email
	   				$error	= JText::sprintf('COM_HECMAILING_EMAIL_INVALID', $email, $elmt->name);
	   				$status=9;
	   				$errors++;
	   			}
	   			else
	   			{
	   				$status=0;
	   				$error="";
	   			}

	   			
	   			// Insert emails
	   			$rowuser = new stdClass();
	   			$rowuser->message_id =$messageid ;
	   			$rowuser->userid = $elmt->id  ;
	   			$rowuser->email = $email;
	   			$rowuser->name = $elmt->name ;
	   			$rowuser->status = $status;
	   			$rowuser->error = $error;
	   			$rowuser->params = "";
	   			$ret = $db->insertObject('#__hecmailing_message_recipient', $rowuser);
	   			$recipient_id = $db->insertid();
	   			
	   			// Insert Answers
	   			$token=HecMailingMailFrontendHelper::random_str(32);
	   			foreach($answers->questions as $question)
	   			{
	   			
	   				$question->message_id =$messageid ;
	   				$question->recipient_id = $recipient_id;
	   				$question->token=$token;
	   				$question->answer_list = implode(";", $question->answers);
	   				$ret = $db->insertObject('#__hecmailing_answers', $question);
	   			}
	   			
	   			
	   				
	   
	   		}
	   		return $messageid;
	   	}
	   	 
	   	 
	   	 
	   	//$msg=JText::_("COM_HECMAILING_EMAIL_SENT"). "(".$nb.JText::_("COM_HECMAILING_SEND_OK")."/".$errors.JText::_("COM_HECMAILING_SEND_ERR").")";
	   	
	   return false;		
	   
   }

} 

?> 

