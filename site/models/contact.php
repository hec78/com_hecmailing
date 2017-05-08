<?php
/**
* @version 1.7.0
* @package hecMailing for Joomla
* @copyright Copyright (C) 2009 Hecsoft All rights reserved.
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
* Change Log
*     1.7.0 : Compatible Joomla 1.6+
* 		0.6.0 : Ajout fonctionnalite contact
*		0.5.0 : Correction probleme adresse/nom envoye par
*						Sauvegarde des emails envoyes
*		0.4.0 : Correction suppression item dans groupe
*		0.3.0 : Prise en charge groupe 0 (tous membres) dans getMailAdrFromGroupe
*		0.1.0 : Version d'origine
*/
defined('_JEXEC') or die ('restricted access'); 
jimport('joomla.application.component.model'); 
jimport('joomla.log.log');


/**
 * hecMailing Component Form Model
 *
 * @package		Joomla
 */
class HecMailingModelContact extends JModelForm 
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
      $this->isLog = $this->params->get('Log');
      //$this->isLog = true;
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
	 * Method to get email list from a group
	 *
	 * @access	public
	 * @param	int Group Identifier
	 * @param int useprofile => if 1 send email only if JUser sendEmail
	 *                 field is 1 
	 * @return array [email,name]     	 
	 */
   function getMailAdrFromGroupe($groupe, $useprofile)
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
			if (intval($useprofile)==1)
				$useprofile= " AND u.sendEmail=1 ";
			else
				$useprofile="";
			// Cas des id user joomla
			$query = "SELECT email, name
					FROM #__users u inner join #__hecmailing_groupdetail gd ON u.id=gd.gdet_id_value AND gd.gdet_cd_type=2
					WHERE gd.grp_id_groupe=".$groupe. $useprofile;
			// Cas des username
			$query .= " UNION SELECT email, name
					FROM #__users u inner join #__hecmailing_groupdetail gd ON u.username=gd.gdet_vl_value AND gd.gdet_cd_type=1
					WHERE gd.grp_id_groupe=".$groupe. $useprofile;
			// Cas des groupes joomla
			$query .= " UNION SELECT email, name
					FROM #__users u inner join #__hecmailing_groupdetail gd ON u.usertype=gd.gdet_vl_value AND gd.gdet_cd_type=3
					WHERE u.block=0 AND gd.grp_id_groupe=".$groupe. $useprofile.$blockcond1;
			// Cas des adresse e-mail
			$query .= " UNION SELECT gd.gdet_vl_value as email, gd.gdet_vl_value as name
					FROM #__hecmailing_groupdetail gd 
					WHERE gd.gdet_cd_type=4 AND gd.grp_id_groupe=".$groupe;
		}
		$db->setQuery($query);
		if (!$rows = $db->loadRowList())  return false;
		return $rows;
	}

	/**
	 * Method to get contact list as HTML select
	 *
	 * @access	public
	 * @return array of Html option     	 
	 */
   function getContacts()
   {
		$db=$this->getDBO();
		$query = "SELECT ct_id_contact,ct_nm_contact, ct_vl_info FROM #__hecmailing_contact order by ct_nm_contact";
		$db->setQuery($query);
		if (!$rows = $db->loadRowList()) return false;
		$val = array();
		$infos = array();
		$names = array();
		$val[] = JHTML::_('select.option',-1, JText::_('SET_CONTACT'), 'ct_id_contact', 'ct_nm_contact');

		foreach($rows as $r)
		{
			$val[] = JHTML::_('select.option', $r[0], $r[1], 'ct_id_contact', 'ct_nm_contact');
	        $infos[] = $r[2];
			$id[] = $r[0];
			$names[] = $r[1]; 
		}
	    return array($val,$infos, $id,$names);
	}

   	/**
	 * Method to get contact list as HTML select
	 *
	 * @access	public
	 * @param $idcontact : int Contact Id
	 * @return array of Html option     	 
	 */
	function getContactInfo($idcontact)
	{
		$db=$this->getDBO();
		$query = "SELECT ct_id_contact,grp_id_groupe,	ct_nm_contact ,	ct_cm_contact ,ct_vl_info , ct_vl_template , ct_vl_prefixsujet 
			From #__hecmailing_contact Where ct_id_contact=".$idcontact;
		$db->setQuery($query);
		if (!$data = $db->loadObject())  return false;
		return $data;
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
		JFactory::getApplication()->setUserState('com_hecmailing.contact.data', $data);
		$form = $this->loadForm('com_hecmailing.contact', 'contact', array('control' => 'jform', 'load_data' => true));
		
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
		$data = (array) JFactory::getApplication()->getUserState('com_hecmailing.contact.data', array());
		$currentuser=JFactory::getUser();
		if (!$currentuser->guest)
		{
			if (count($data)>0)
			{
				if($data['email']=='')
					$data['email']= $currentuser->email;
				if ($data['name']=='')
					$data['name']= $currentuser->name;
			}
			else
			{
				$data['email']= $currentuser->email;
				$data['name']= $currentuser->name;
			}
			
		}
		
		
		$this->preprocessData('com_hecmailing.contact', $data);
		return $data;
	}
	
} 
?> 



