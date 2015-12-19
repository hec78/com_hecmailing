<?php
/**
* @version 1.8.3
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
* ChangeLog :
*  3.0.0   26 dec. 2014  Joomla 3 compliant
*                        Add email status and read tag  
*  1.8.3   26 sep. 2014  Correct group email problem
*  1.8.2   01 jun. 2013  Correct autoupdate problem
*                        Correct email address list of email sent is "array" when send by more than one
*  1.8.1   07 apr. 2013  Correct Package problem
*  1.8.0   31 mar. 2013  Use of JQuery for Webservice and Dialogs
*                        Add HECMailing group use in a group (group of group)
*                        Change group add (admin). We use a web service for list group detail (joomla or hecmailing)
*                        Simplify javascript code
*  1.7.8   25 mar. 2013  Fixe Import problem
*                        Add support for Mac File import
*  1.7.7   26 jan. 2013  Fixe Joomla 2.5 send mail problem (real name)
*                        Fixe Install problem when MySQL is not UTF8 charset
*                        Fixe IsAdmin problem on J1.6+
*  1.7.6   30 aug. 2012  Fixe Can't send mail to blocked users
*                        Feature Add Recipient Name 
*  1.7.5   06 apr. 2012  Fixe double message when send Contact
*                        Fixe Admin Menu Translation Problem with joomla 1.5
*  1.7.4   28 mar. 2012  Fixe component admin parameter problem on Joomla 1.5.x
*                        Fixe Group right checkbox problem on joomla 1.5.x
*                        Fixe use authorization issue on joomla 1.5.x
*  1.7.3   23 jan. 2012  Correct use of joomla group problem
*  1.7.2   13 jan. 2012  de-DE language by Georg Straube 
*  1.7.1   28 dec. 2011  Correct Group Management problems
*                        Add template for contact
*						Correct Admin menu problem
* 1.7.0		25 aug 2011			Joomla 1.7.x compliant
* 0.13.4    24 may 2011          Choose default upload input count
*                                Change Add attachment method to be IE8 compliant 
* 0.13.3    19 may 2011         Php 5.0.x compatibility
* 0.13.2		14 jan. 2011				Admin regression fixe
* 0.13.1 		11 jan. 2011				German translations by Ingo
* 0.13.0		10 dec. 2010				Import email list from file to group (which was accidentaly removed in 0.12.0)
*	0.12.0		14 nov 2010				Bug : Contact send to all users and not selected group
*	0.11.0		15 nov. 2010      Contact : 	Add tooltips
*								 			     Allow to display html contact description
*								           Send Mail :	Block send mail page access if no group permission
*								           Joomfish compatible (2 xml files)
*	0.10.0		20 oct. 2010	Group permission (user or joomla group) to show only allowed groups un group list
*	0.9.1       14 aug. 2010	Mode install.hecmailing.php from admin to root (Install problem) 
*								Added Backend Deutch translation (yahoo translator :( )
*							Modifications by Arjan Mels :
*		                        Added Dutch translation
*								Corrected many links/paths (in controller.php & form views default.php) to allow joomla installation in subdirectory
*								Small corrections to installtion xml file (to include some missing images)
*								getdir integrated into hecmailing.php to be able to use Joomla security features
*
* 0.9.0		15 jun 2010		Request #3016614 : Suppress sent email
*   							Bug #3013589 : Delete Failed message
*   							Bug # 2970606 Contact : Can't add or edit contact
*								Bug #2975181 General : Add some missing translations
*								Request #2975177 General : Use jt dialog box instead of self made
*								Request #2975179 Contact : Change captcha --> Use ReCaptcha
*								Request #2975183 Contact : If user is logged, fill name and email fields
*								Bug #2975175 Contact : No body for contact email
*
*	0.8.2      	10 feb 2010		Bug #2913937 Problem with group
*				                Translate buttons (Add user, Add email, Add Group and Delete) in English
*
*	0.8.1		28 jan 2010		Bug #18750	Bad URL for link / URL lien erron�e
*								Bug #19566	LogDetail : mail sent ok list is too large / Liste des destinataire ok et erreur trop large
*								Bug #19567	E-mail sent : Embedded image is not shown in email sent detail / Les images incorpor�es ne sont pas visibles dans le d�tail des eamil envoy�s
*								Bug #19568	E-mail sent detail : error when no attachment / Erreur lorsqu'il n'y a aucun fichier joint
*								Bug #19569	send again email : error when no attachment/ Erreur lorsqu'il n'y a aucun fichier joint
*
*	0.8.0 		12 jan 2010		Added embedded image feature
* 0.7.0 : 					Added attachment feature
* 0.6.0 : 					Added contact feature (in beta)
*	0.5.0 : 					Bug : Bad sender name/e-mail
*								Save sent emails
*	0.4.0 : 					Bug fixed suppress group item
*	0.3.0 : 					Bug fixed Send to all users 
*								Added sent email count and no sent email count
*	0.2.0 : 					Bug fixed Image in template
*	0.1.0 : 					Original version
***************************************************************************************************/
// no direct access
defined ('_JEXEC') or die ('restricted access');
  
   
jimport('joomla.application.component.controller'); 
jimport('joomla.error.log');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
require_once('components/com_hecmailing/libraries/recaptcha/recaptchalib.php');
// Include dependancies

                                                          
/**
 * hecMailing Controller class
 *
 * @package hecMailing
 */
 
class hecMailingController extends JControllerLegacy
{
	var $_db =null;
	var $_err="";
	var $_params= null;
	var $_log=null;
	protected $default_view = 'form';
	 /**
	 * Constructor
	 *
	 * @access	public
	 */
	public function __construct($config = array())
	{
		$this->input = JFactory::getApplication()->input;
		$this->params = JComponentHelper::getParams( 'com_hecmailing' );
		parent::__construct($config);
	}
	
    

    function addLogEntry($msg)
    {
    	
    }
    
   /**
	 * Method to display a view
	 *
	 * @access	public
	 */
// /*  /*   function display($cachable = false, $urlparams = Array())
//     {
//       //get a refrence of the page instance in joomla 
//       $document= JFactory::getDocument(); 
//       //get the view name from the query string 
//       $viewName = JRequest::getVar('view', ''); 
	  
//       $viewType= $document->getType(); 
//       $check_right=true;
		
// 		  //get the model base
//       $modelbase = $this->getModel('form', 'ModelhecMailing');

//  	    // interceptors to support legacy urls
//      	//$task = $this->getTask();
//      	$task = JRequest::getVar('task','');
//      	$layout="default";
//      	if($task=='' && $viewName=='contact')
//      	{
//      		$task='contact';
//      	}
//      	if ($task=='' && $viewName=='')
//      	{
//      		$task='form';
//      	}	
//      	if ($task!='')
//      	{
// 	  		switch ($task)
// 	  		{

// 	  			case 'form':	// send e-mail display
// 	  				$viewName	= 'form';
// 	  				$layout		= 'default';
// 	  				/*$ok = $modelbase->hasGroupe();
//       				if (!$ok)
//       				{
//       					$msg=JText::_("COM_HECMAILING_NO PERMISSION");
// 						  $return = JURI::root();		// Redirect to Home
// 				     	$this->setRedirect( $return, $msg );
// 					}*/	
// 	  				break;
// 	  			case 'sent':	// sent email list display
// 	  			 	$viewName	= 'sent';
// 	  				$layout		= 'default';
// 	  				break;
// 	  			case 'load':	// load template display --> show send email display
	  			  
// 	  				$viewName	= 'form';
// 	  				$layout		= 'default';
// 	  			case 'log':		// log list display
// 	  			  	$viewName	= 'log';
// 	  				$layout		= 'default';
// 	  				break;
// 	  			case 'dellog':	// Delete a sent email
// 	  				$cid 	= JRequest::getVar('cid', array(0), 'post', 'array');
// 	  				dellog($cid);	// Delete the saved email
// 	  			  	$viewName	= 'log';	// --> Show log list
// 	  				$layout		= 'default';
// 	  				break;
// 	  			case 'contact':	// Contact display
// 	  				$check_right=false;
// 	  			  	$viewName	= 'contact';
// 	  				$layout	= 'default';
// 	  				break;
// 	  			case 'viewlog':	// view log sent email list
// 	  			  	$viewName	= 'logdetail';
// 	  				$layout		= 'default';
// 	  				break;
// 	  			case 'save':	// Save this email as template
// 		  			save();
// 		  			return;
// 	  			 	break;
// 	  			case 'sendContact':	// Send a contact demande
// 	  				$check_right=false;
// 	  				$viewName	= 'contact';
// 	  				$layout	= 'default';
// 	  				$task = 'contact';
// 	  				JRequest::setVar('task' , $task);
	  				
// 	  				if ($this->sendAContact())
// 	  				{
// 	  					$msg=JText::_("COM_HECMAILING_CONTACT_SENT");
// 						  $return = JURI::root();		// Redirect to Home
// 				     	$this->setRedirect( $return, $msg );
// 	  				}
// 	  			   break;
// 	  			case 'manage_group':
// 	  				$viewName	= 'group';
// 	  				$layout		= 'default';
// 	  				break;
// 	  			case 'save_group':
// 	  				$this->saveGroup();
// 	  				$viewName	= 'group';
// 	  				$layout		= 'default';
// 	  				break;
// 	  		}
//      	}
				 
//      	JRequest::setVar('view' , $viewName);
//     	JRequest::setVar('layout', $layout);
      
//       //get our view 
//       $view = $this->getView($viewName, $viewType); 
      
//       //get the model 
//       $model = $this->getModel($viewName, 'ModelhecMailing');
      
      
//       //some error check 
//       if (!JError::isError($model)) 
//       { 
//          $view->setModel ($model, true); 
//       }

//       // Check acces right (for send email)
//       if ($check_right)
//       {
// 		    $params = JComponentHelper::getParams( 'com_hecmailing' );
// 		    $usr =JFactory::getUser();
		    
// 		    // Check if current user is in authorized joomla groups
// 			  $adminType = $params->get('usertype','ADMINISTRATOR;SUPER ADMINISTRATOR');
  			
//   			// Check if current user is in admin hec Mailing group (groupaccess hec Mailing parameter)				
//   			$usrgrp = $params->get('groupaccess','MailingGroup');
  			
//   			if (!$modelbase->isAdminUserType($adminType) && !$modelbase->isInGroupe($usrgrp) && !$modelbase->hasGroupe())
//   			{
// 		 		   $msg=JText::sprintf("COM_HECMAILING_NO_RIGHT",$usrgrp);
// 			     $return = JURI::root();		// redirect to home if no right
// 			     $this->setRedirect( $return, $msg );
// 			  }
// 	    }
     
//       //set the template and display it 
//       $view->setLayout($layout); 
//       $view->display(); 

//     } */
// Push the model into the view (as default).

		public function display($cachable = false, $urlparams = false)
		{
		
		
			// Set the default view name and format from the Request.
			// Note we are using a_id to avoid collisions with the router and the return page.
			// Frontend is a bit messier than the backend.
			
			$vName = $this->input->get('view', 'form');
			$this->input->set('view', $vName);
		
			$user = JFactory::getUser();
		
			$safeurlparams = array('catid' => 'INT', 'id' => 'INT', 'cid' => 'ARRAY', 'year' => 'INT', 'month' => 'INT', 'limit' => 'UINT', 'limitstart' => 'UINT',
					'showall' => 'INT', 'return' => 'BASE64', 'filter' => 'STRING', 'filter_order' => 'CMD', 'filter_order_Dir' => 'CMD', 'filter-search' => 'STRING', 'print' => 'BOOLEAN', 'lang' => 'CMD', 'Itemid' => 'INT');
		
// 			// Check for edit form.
// 			if ($vName == 'form' && !$this->checkEditId('com_content.edit.article', $id))
// 			{
// 				// Somehow the person just went to the form - we don't allow that.
// 				return JError::raiseError(403, JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
// 			}
			
			parent::display($cachable, $safeurlparams);
			return $this;
		}
    
 
 

  

	
 
	
	function saveGroup()
	{
		echo '<script language="javascript" type="text/javascript">alert("Groupe enregistre");</script>';
		echo '<script language="javascript" type="text/javascript">window.close();</script>';
	
	}	
}
?>
