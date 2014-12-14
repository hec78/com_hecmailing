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

                                                          
/**
 * hecMailing Controller class
 *
 * @package hecMailing
 */
 
class hecMailingController extends JController
{
	var $_db =null;
	var $_err="";
	var $_params= null;
	var $_log=null;
		
	 /**
	 * Constructor
	 *
	 * @access	public
	 */
    function hecMailingController()
    {
    	$this->_log = &JLog::getInstance('com_hecmailing.log.php');
      parent::__construct();
		  $this->params = &JComponentHelper::getParams( 'com_hecmailing' );
    }

   /**
	 * Method to display a view
	 *
	 * @access	public
	 */
    function display($tmpl = null)
    {
      //get a refrence of the page instance in joomla 
      $document=& JFactory::getDocument(); 
      //get the view name from the query string 
      $viewName = JRequest::getVar('view', ''); 
	  
      $viewType= $document->getType(); 
      $check_right=true;
		
		  //get the model base
      $modelbase = &$this->getModel('form', 'ModelhecMailing');

 	    // interceptors to support legacy urls
     	//$task = $this->getTask();
     	$task = JRequest::getVar('task','');
     	$layout="default";
     	if($task=='' && $viewName=='contact')
     	{
     		$task='contact';
     	}
     	if ($task=='' && $viewName=='')
     	{
     		$task='form';
     	}	
     	if ($task!='')
     	{
	  		switch ($task)
	  		{

	  			case 'form':	// send e-mail display
	  				$viewName	= 'form';
	  				$layout		= 'default';
	  				/*$ok = $modelbase->hasGroupe();
      				if (!$ok)
      				{
      					$msg=JText::_("COM_HECMAILING_NO PERMISSION");
						  $return = JURI::root();		// Redirect to Home
				     	$this->setRedirect( $return, $msg );
					}*/	
	  				break;
	  			case 'sent':	// sent email list display
	  			 	$viewName	= 'sent';
	  				$layout		= 'default';
	  				break;
	  			case 'load':	// load template display --> show send email display
	  			  
	  				$viewName	= 'form';
	  				$layout		= 'default';
	  			case 'log':		// log list display
	  			  	$viewName	= 'log';
	  				$layout		= 'default';
	  				break;
	  			case 'dellog':	// Delete a sent email
	  				$cid 	= JRequest::getVar('cid', array(0), 'post', 'array');
	  				dellog($cid);	// Delete the saved email
	  			  	$viewName	= 'log';	// --> Show log list
	  				$layout		= 'default';
	  				break;
	  			case 'contact':	// Contact display
	  				$check_right=false;
	  			  	$viewName	= 'contact';
	  				$layout	= 'default';
	  				break;
	  			case 'viewlog':	// view log sent email list
	  			  	$viewName	= 'logdetail';
	  				$layout		= 'default';
	  				break;
	  			case 'save':	// Save this email as template
		  			save();
		  			return;
	  			 	break;
	  			case 'sendContact':	// Send a contact demande
	  				$check_right=false;
	  				$viewName	= 'contact';
	  				$layout	= 'default';
	  				$task = 'contact';
	  				JRequest::setVar('task' , $task);
	  				
	  				if ($this->sendAContact())
	  				{
	  					$msg=JText::_("COM_HECMAILING_CONTACT_SENT");
						  $return = JURI::root();		// Redirect to Home
				     	$this->setRedirect( $return, $msg );
	  				}
	  			   break;
	  			case 'manage_group':
	  				$viewName	= 'group';
	  				$layout		= 'default';
	  				break;
	  			case 'save_group':
	  				$this->saveGroup();
	  				$viewName	= 'group';
	  				$layout		= 'default';
	  				break;
	  		}
     	}
				 
     	JRequest::setVar('view' , $viewName);
    	JRequest::setVar('layout', $layout);
      
      //get our view 
      $view = &$this->getView($viewName, $viewType); 
      
      //get the model 
      $model = &$this->getModel($viewName, 'ModelhecMailing');
      
      
      //some error check 
      if (!JError::isError($model)) 
      { 
         $view->setModel ($model, true); 
      }

      // Check acces right (for send email)
      if ($check_right)
      {
		    $params = &JComponentHelper::getParams( 'com_hecmailing' );
		    $usr =& JFactory::getUser();
		    $utype = $usr->usertype;
		    // Check if current user is in authorized joomla groups
			  $adminType = $params->get('usertype','ADMINISTRATOR;SUPER ADMINISTRATOR');
  			
  			// Check if current user is in admin hec Mailing group (groupaccess hec Mailing parameter)				
  			$usrgrp = $params->get('groupaccess','MailingGroup');
  			
  			if (!$modelbase->isAdminUserType($adminType) && !$modelbase->isInGroupe($usrgrp) && !$modelbase->hasGroupe())
  			{
		 		   $msg=JText::sprintf("COM_HECMAILING_NO_RIGHT",$usrgrp);
			     $return = JURI::root();		// redirect to home if no right
			     $this->setRedirect( $return, $msg );
			  }
	    }
     
      //set the template and display it 
      $view->setLayout($layout); 
      $view->display($tmpl); 

    }
    
    
    /**
	 * Method to load a saved template mail
	 *
	 * @access	public
	 */
    function load()
    {
      //get a reference of the page instance in joomla 
      $document=& JFactory::getDocument(); 
      //get the view name from the query string 
      $viewName = JRequest::getVar('view', 'form'); 

      $viewType= $document->getType(); 
      
     $viewName	= 'form';
  	 $layout		= 'default';
  	 JRequest::setVar('view' , $viewName);
    JRequest::setVar('layout', $layout);
      
      //get our view 
      $view = &$this->getView($viewName, $viewType); 
      
      //get the model 
      $model = &$this->getModel($viewName, 'ModelhecMailing');
      
      //some error check 
      if (!JError::isError($model)) 
      { 
         $view->setModel ($model, true); 
      }
       
      //set the template and display it 
      $view->setLayout($layout); 
      $view->display(); 
      
    }
 
  /**
	 * Method to deleted sent mail from de list
	 *
	 * @access	public
	 */
    function dellog(&$cid)
    {
    	// Check for request forgeries
		  JRequest::checkToken() or jexit( 'Invalid Token' );
 		
  		// Initialize variables
  		$db =& JFactory::getDBO();
  		$cid 	= JRequest::getVar('cid', array(0), 'post', 'array');
  		
  		JArrayHelper::toInteger($cid);
		
		
  		if (count( $cid )>0) // if there is email to delete
  		{
  			$cids = implode( ',', $cid );	// get email list from cid
  			// execute log attchments delete query
  			$query = 'DELETE FROM #__hecmailing_log_attachment WHERE log_id_message IN('.$cids.')';
  			$db->setQuery( $query );
  			if (!$db->query()) 
  			{	// Query error!!!
  				echo "<script> alert('".$db->getErrorMsg(true)."'); window.history.go(-1); </script>\n";
  			}
  			// execute log delete query
  			$query = 'DELETE FROM #__hecmailing_log WHERE log_id_message IN('.$cids.')';
  			$db->setQuery( $query );
  			if (!$db->query()) 
  			{
  				echo "<script> alert('".$db->getErrorMsg(true)."'); window.history.go(-1); </script>\n";
  			}
  			$msg=JText::_('COM_HECMAILING_LOG_DELETED');
  		}
  		else
  		{
  			$msg=JText::_('COM_HECMAILING_NO_LOG_DELETED');
  		}
  		$return = JRoute::_(JURI::current().'?task=log');		
		  $this->setRedirect( $return, $msg );
    }
    
  /**
	 * Method to cancel current mail
	 *
	 * @access	public
	 */
   	function cancel()
	   {
  		// Show alert message
  		echo "<script>alert('".JText::_("COM_HECMAILING_CANCEL_ALERT")."');</script>";
  		$userid = JRequest::getVar( 'id', 0, 'post', 'int' );
  		//$this->display();
  		$msg=JText::_('COM_HECMAILING_CANCEL_MSG');

  		JRequest::setVar( 'Itemid', 0 );
  	 	$return = JURI::current();		
  		$this->setRedirect( $return, $msg );
  	}
    
   /**
	 * Method to save current mail as template
	 *
	 * @access	public
	 */
    function save()
    {
       $mainframe = &JFactory::getApplication();

      	// Check for request forgeries
      	JRequest::checkToken() or jexit( 'Invalid Token' );

		  //get a refrence of the page instance in joomla 
      $document=& JFactory::getDocument(); 
      //get the view name from the query string 
      $viewName = JRequest::getVar('view', 'form'); 
	      //get our view 
	    $viewType= $document->getType(); 
      $view = &$this->getView('form', $viewType); 
      $session =& JFactory::getSession();
  		$db	=& JFactory::getDBO();
        	
  		jimport( 'joomla.mail.helper' );
  
  		$SiteName 	= $mainframe->getCfg('sitename');
  		$MailFrom 	= $mainframe->getCfg('mailfrom');
  		$FromName 	= $mainframe->getCfg('fromname');
  
  		$link 		= base64_decode( JRequest::getVar( 'link', '', 'post', 'base64' ) );
   
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
					 'subject',
					 );

	    /*
	    * Here is the meat and potatoes of the header injection test.  We
	    * iterate over the array of form input and check for header strings.
	    * If we find one, send an unauthorized header and die.
	    */
	    foreach ($fields as $field)
	    {
		     foreach ($headers as $header)
		     {
			      if (strpos($_POST[$field], $header) !== false)
			      {
				       JError::raiseError(403, '');
			      }
		     }
	    }

  		/*
  		 * Free up memory
  		 */
  		unset ($headers, $fields);
  
  		
   		$sender 			= JRequest::getString('sender', $MailFrom, 'post');
  		$fromvalue 				= JRequest::getString('from', $MailFrom, 'post');
  		$tmp = split(";" , $fromvalue);
			$from=$tmp[0];
			$sender=$tmp[1];
  		$subject_default 	= JText::sprintf('COM_HECMAILING_ITEM_SENT_BY', $sender);
  		$subject 			= JRequest::getString('subject', $subject_default, 'post');
  		$body 			= JRequest::getVar('body', '', 'post', 'string', JREQUEST_ALLOWRAW);
  		$groupe     		= JRequest::getString('groupe', '', 'post');
  		$name     		= JRequest::getString('saveTemplate',$subject , 'post');
  		
	    // Check for a valid to address
	    $error	= false;
	    $body = "<html><head></head><body>".$body."</body></html>";

	    // Check for a valid from address
	    if ( ! $from || ! JMailHelper::isEmailAddress($from) )
	    {
		     $error	= JText::sprintf('COM_HECMAILING_EMAIL_INVALID', $from);
		     JError::raiseWarning(0, $error );
	    }

  		// Clean the email data
  		$subject = JMailHelper::cleanSubject($subject);
  		$body	 = JMailHelper::cleanBody($body);
  		//$body = str_replace("src=\"","src=\"".JURI::base(),$body);
  		
  		$sender	 = JMailHelper::cleanAddress($sender);
  		//Create data object
      $rowdetail = new JObject();
      $rowdetail->msg_lb_message = $name;
      $rowdetail->msg_vl_subject = $subject;
      $rowdetail->msg_vl_body = $body;
      $rowdetail->msg_vl_from = $from;
      //Insert new record into groupdetail table.
      $ret = $db->insertObject('#__hecmailing_save', $rowdetail);
      
        
      $msg=JText::_("COM_HECMAILING_EMAIL SAVED");

			JRequest::setVar( 'Itemid', 0 );
			$return = JURI::current();		

			$this->setRedirect( $return, $msg );
    }
    
   /**
	 * Method to send a mail (override of joomla sendMail with attachments and embedded images)
	 *
	 * @access	public
	 */
    function sendMail($from, $fromname, $recipient, $subject, $body, $mode=0, $cc=null, $bcc=null, $attachment=null, $replyto=null, $replytoname=null, $inline=null )
    {
         // Get a JMail instance
        $mail =& JFactory::getMailer();
        $mail->setSender(array($from, $fromname));
        $mail->setSubject($subject);
        $mail->setBody($body);

        // Are we sending the email as HTML?
        if ( $mode ) { $mail->IsHTML(true);  }
        if (isset($recipient))
        {
	        if (is_array($recipient))
	        {
	        	foreach($recipient as $adr)
	        	{
	        		$mail->AddAddress($adr[0],$adr[1]);
	        	}
	        }
	        else
	        {
	        	$mail->AddAddress($recipient[0],$recipient[1]);
	        }
        }
    		
    	if (isset($cc))
        {
	        if (is_array($cc))
	        {
	        	foreach($cc as $adr)
	        	{
	        		$mail->addBCC($adr[0],$adr[1]);
	        	}
	        }
	        else
	        {
	        	$mail->addBCC($cc[0],$cc[1]);
	        }
        }
        if (isset($bcc))
        {
	        if (is_array($bcc))
	        {
	        	foreach($bcc as $adr)
	        	{
	        		$mail->addBCC($adr[0],$adr[1]);
	        	}
	        }
	        else
	        {
	        	$mail->addBCC($bcc[0],$bcc[1]);
	        }
        }
         
        // Add embedded images
        foreach ($inline as $att)
        {
        	 $mail->AddEmbeddedImage($att[0], $att[1], $name = $att[2]);
        }
        $mail->addAttachment($attachment);	// Add attachments
       
        // Take care of reply email addresses
        if( is_array( $replyto ) ) 
        {
            $numReplyTo = count($replyto);
          	for ( $i=0; $i < $numReplyTo; $i++)
          	{
                $mail->addReplyTo( array($replyto[$i], $replytoname[$i]) );
            }
        } 
        elseif( isset( $replyto ) ) 
        {
           $mail->addReplyTo( array( $replyto, $replytoname ) );
        }
		// Send email and return Send function return code
        return  $mail->Send();

    }


 	/**
	 * Method to send current mail to selected group
	 *
	 * @access	public
	 */
    function send()
	{
	    $mainframe = &JFactory::getApplication();
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
      	//get a refrence of the page instance in joomla 
      	$document=& JFactory::getDocument(); 
      	//get the view name from the query string 
      	$viewName = JRequest::getVar('view', 'form'); 
	    //get our view 
	    $viewType= $document->getType(); 
      	$view = &$this->getView('form', $viewType); 
      	$viewName = "form";
      	//get the model 
      	$model = &$this->getModel($viewName, 'ModelhecMailing');
      
		$attach_path='';     
		$session =& JFactory::getSession();
	    $db	=& JFactory::getDBO();
	            	
	    jimport( 'joomla.mail.helper' );
	      
  		$SiteName 	= $mainframe->getCfg('sitename');
  		$MailFrom 	= $mainframe->getCfg('mailfrom');
  		$FromName 	= $mainframe->getCfg('fromname');
  		
  		$link 		= base64_decode( JRequest::getVar( 'link', '', 'post', 'base64' ) );
	      		
	    $params = &JComponentHelper::getParams( 'com_hecmailing' );
	      		
		
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
		     	if (array_key_exists ( $field , $_POST ))
		     	  {
				      if (strpos($_POST[$field], $header) !== false)
				      {
					       JError::raiseError(403, '');
				      }
		     	  }
		     }
	    }

  		/*
  		 * Free up memory
  		 */
  		unset ($headers, $fields);

		/* Get options from post */    		
  		$useprofil 		 = JRequest::getString('useprofil', 0, 'post');
  		$image_incorpore = JRequest::getString('incorpore', 0, 'post');
  		$logmail 		 = JRequest::getString('backup_mail', 0, 'post');
		
		// Get from field and decode name and email from it (from;sender)    		
  		$fromvalue 		 = JRequest::getString('from', $MailFrom.';'.$FromName, 'post');
		$tmp = explode(";" , $fromvalue);
		$from=$tmp[0];
		$sender=$tmp[1];
			
		// Get subject and body
  		$subject_default 	= JText::sprintf('COM_HECMAILING_DEFAULT_SUBJECT', $sender);
  		$subject 			= JRequest::getString('subject', $subject_default, 'post');
  		$body 				= JRequest::getVar('body', '', 'post', 'string', JREQUEST_ALLOWRAW);
    	$groupe     		= JRequest::getString('groupe', '', 'post');
        $sendcount = intval($params->get('send_count', 1));
        
	    // Get attachments
  		$attach = array();
  		$files = array();
  		$pj_uploaded = array();
  		$nbattach = JRequest::getInt('attachcount', 0, 'post');	// attachment count
  		
  		// if email must be saved, temporary attachment path become saved attachment path
  		// in order to be able to send again the mail and is attachments
  		if (intval($logmail)==1)	
  		{
  			$attach_path=$params->get('attach_path', $attach_path);
  			$path = realpath(JPATH_ROOT).DS;	// Get path root
  		}
  		else
  		{
  			$attach_path = $mainframe->getCfg('tmp_path');	// temporary attachment path
  			$path="";
  		}
  		
  		
  		// Create attachment directory if doesn't exist
  		if (!JFolder::exists($path.$attach_path))
  		{
  			// Create directory
  			if (!JFolder::create($path.$attach_path))
  			{
  				$error	= JText::sprintf('COM_HECMAILING_CANT_CREATE_DIR', $path.$attach_path);
				JError::raiseWarning(0, $error );
  			}
  			// Create dummy index.html file for prevent list directory content
  			$text="<html><body bgcolor=\"#FFFFFF\"></body></html>";
  			JFile::write($path.$attach_path.DS."index.html",$text);
  		}
  		
  		// Process uploaded files
  		for($i=1;$i<=$nbattach;$i++)
  		{
  			// Get uploaded files 
				$file = JRequest::getVar('attach'.$i, null, 'files', 'array');
				$filename = JFile::makeSafe($file['name']);
		      	$src = $file['tmp_name'];
		      	if ($src!='')
		      	{
		  				//Set up the source and destination of the file
		  				$dest = $attach_path.DS.$file['name'];
		  				// Upload uploaded file to attchment directory (temp or saved dir)
		  				JFile::upload($src, $path.$dest);
		  				$attach[] = $path.DS.$dest;
		  				$files[] = $dest;
		  				// Bug #3013589 : Delete Failed message
		  				//$pj_uploaded[] = $path.DS.$dest;
		  				$pj_uploaded[] = $path.$dest;
				}
   		}
  		// Process hosted files attachment
  		$nblocal = JRequest::getInt('localcount', 0, 'post');
  		$local = array();
  		$img="";
  		
  		// for each file ...
  		for($i=1;$i<=$nblocal;$i++)
  		{
  			// Check if checkbox is checked yet (not canceled)
  			$isok = JRequest::getString('chklocal'.$i, 'c', 'post');
  			if ($isok!='')
  			{
  				// Get file
    			$file = JRequest::getString('local'.$i, '', 'post');
				$filename = $file;
				// add it in attachment list						
				$attach[] = $path.DS.$filename;
				$files[]=$filename;
			}
 		}
		// Check for a valid to address
		$error	= false;
		$body = "<html><head></head><body>".$body."</body></html>";
		
		// Check for a valid from address
		if ( ! $from || ! JMailHelper::isEmailAddress($from) )
		{
			$error	= JText::sprintf('COM_HECMAILING_EMAIL_INVALID', $fromvalue ."/".$from."/".$sender);
			JError::raiseWarning(0, $error );
		}

		// Clean the email data
		$subject = JMailHelper::cleanSubject($subject);
		$body	 = JMailHelper::cleanBody($body);
		$sender	 = JMailHelper::cleanAddress($sender);
		
		$inline=array();
		$bodytolog = $body;

		// if embedded image is enabled
		if ($image_incorpore=='1')
		{
			// Search and replace 'src="' by 'src=cid:...' in order to tell to mail software to use specifique embedded image attachment
			$pos= stripos($body," src=\"");
			while ($pos!==FALSE)
			{
				$pos+=6;
				// search next "
				$posfin= stripos($body,"\"", $pos+1);
				// get src url
				$url = substr($body, $pos, $posfin-$pos);
				// create a dummy cid number
				$cur=count($inline);
				$cid= "12345612345-".$cur;
				// Get file name from url
				$name = JFile::getName($url);
				// save current embedded (cid, name, path)
				$inline[$cur][1]=$cid;
				$inline[$cur][2]=$name;
				$inline[$cur][0]=$path.DS.$url;
				// replace img source by cid number
				$body=substr($body,0,$pos)."cid:".$cid.substr($body,$posfin);
				$posfin++;
				// Next image
				$pos=stripos($body," src=\"",$posfin); 
			}
		}
		else	// if embedded image is disabled -> link use
		{
			// search and replace relative image url (without http://) by absolute image url 
			// by concat relative path with site url
			$pos= stripos($body," src=\"");
			while ($pos!==FALSE)
			{
				$pos+=6;
				// get next double quote "
				$posfin= stripos($body,"\"", $pos+1);
				// get image url
				$url = substr($body, $pos, $posfin-$pos);
				// if relative url (don't start with http://)
				if (stripos($url,"http://")===FALSE)
				{
					$url=JURI::base().$url;  // add  website base url
				}
				
				// Replace image url by new
				$body=substr($body,0,$pos).$url.substr($body,$posfin);
				$posfin++;
				// search next image
				$pos=stripos($body," src=\"",$posfin); 
			}
		}
		
		// Process hyperlink : Replace relative url by absolute url for link with relative path (without http://)
		$pos= stripos($body," href=\"");
		while ($pos!==FALSE)
		{
			$pos+=7;
			// find next double quote "
			$posfin= stripos($body,"\"", $pos+1);
			// get hyperlink url
			$url = substr($body, $pos, $posfin-$pos);
			// if it's relative url
			if (stripos($url,"http://")===FALSE)
			{
				$url=JURI::base().$url; // add website absolute url to relative url
			}
			// replace source hyperlink url by new
			$body=substr($body,0,$pos).$url.substr($body,$posfin);
			$posfin++;
			// search next hyperlink
			$pos=stripos($body," href=\"",$posfin); 
		}
		$errors=0;
		$lstmailok=array();
  		$lstmailerr=array();
    	$list=array();
    	
    	if ($groupe>=0)	// if group selected
    	{
	    	// Get email list from groupe
	        $detail = $model->getMailAdrFromGroupe($groupe,$useprofil);
	        $nb=0;
	        foreach($detail as $elmt)	// send to each email
	        {
		          $email = $elmt[0];
		          // check email
		          if ( ! $email  || ! JMailHelper::isEmailAddress($email) )
		   	   	  {
		    	   		// Bad email --> Add warning and add error counter, but don't send email
		    			$error	= JText::sprintf('COM_HECMAILING_EMAIL_INVALID', $email, $elmt[1]);
		    			JError::raiseWarning(0, $error );
		    			$errors++;
		    	  }
		          else
		          {
		           		// Correction pour J2.5 et envoi par bloc
		          		$emailNamed = array($email,$elmt[1]);
		          		$list[] = $emailNamed;
		    				
		    			// Send the email
		    			if (count($list)>= $sendcount)
		    			{
			        		if ( $this->sendMail($from, $sender, null, $subject, $body,true,null,$list,$attach,null,null, $inline) !== true )
			        		{
			        			// Error while sending email --> Add Error ...
			        		    $error	= JText::sprintf('COM_HECMAILING_EMAIL_NOT_SENT', join(";",$list), count($list));
			        			JError::raiseNotice( 500, $error);
			        			$errors+=count($list);
			        			$lstmailerr = array_merge($lstmailerr,$list);
			        		}
			        		else
			        		{
			        			// email ok ...
			        			$nb+=count($list);
			        			$lstmailok = array_merge($lstmailok,$list);
			        		}
			        		$list=array();
		    			}
		      		}
		      }
		      // Send the email
		      if (count($list)>= 1)
		      {
		      	if ( $this->sendMail($from, $sender, null, $subject, $body,true,null,$list,$attach,null,null, $inline) !== true )
		      	{
		      		// Error while sending email --> Add Error ...
		      		$error	= JText::sprintf('COM_HECMAILING_EMAIL_NOT_SENT', join(";",$list), count($list));
		      		JError::raiseNotice( 500, $error);
		      		$errors+=count($list);
		      		$lstmailerr = array_merge($lstmailerr,$list);
		      	}
		      	else
		      	{
		      		// email ok ...
		      		$nb+=count($list);
		      		$lstmailok = array_merge($lstmailok,$list);
		      	}
		      	$list=array();
		      }
	    }
     
    	// Save sent email if needed
    	if (intval($logmail)==1)
    	{
    		$user =&JFactory::getUser();
    		// Insert email info
    		//Create data object
	        $rowdetail = new JObject();
	        $rowdetail->log_dt_sent = & JFactory::getDate()->toFormat();
	        $rowdetail->log_vl_subject = $subject ;
	        $rowdetail->log_vl_body = $bodytolog  ;
	        $rowdetail->log_vl_from = $from   ;
	        $rowdetail->grp_id_groupe =  $groupe    ;
	        $rowdetail->usr_id_user =  $user->id  ;
	        $rowdetail->log_bl_useprofil =  $useprofil ;
	        $rowdetail->log_nb_ok = $nb  ;
	        $rowdetail->log_nb_errors  =  $errors ;
	        $func = function($value) {
	        	return $value[0];
	        };
	        $rowdetail->log_vl_mailok = join(";", array_map($func,$lstmailok))  ;
	        $rowdetail->log_vl_mailerr =  join(";",array_map($func,$lstmailerr)) ;
        
      		//Insert new record into groupdetail table.
        	$ret = $db->insertObject('#__hecmailing_log', $rowdetail);
    		
    		$logid = $db->insertid(); 
    		// Insert attachments
    		foreach($files as $file)
    		{
    		  $rowfile = new JObject();
	          $rowfile->log_id_message =$logid  ;
	          $rowfile->log_nm_file = $file  ;
	          $ret = $db->insertObject('#__hecmailing_log_attachment', $rowfile);
    		}
	    }
	    else	// if we don't save sent email we can delete uploaded attachments
	    {
	    	JFile::delete($pj_uploaded);
      }
	    
      $msg=JText::_("COM_HECMAILING_EMAIL_SENT"). "(".$nb.JText::_("COM_HECMAILING_SEND_OK")."/".$errors.JText::_("COM_HECMAILING_SEND_ERR").")";
  		if ($logid>0)
      		$return = JRoute::_(JURI::current().'?idlog='.$logid.'&task=viewlog');		
      	else
      		$return = JRoute::_(JURI::base());
  		$this->setRedirect( $return, $msg );
	}
	
  /**
	 * Method to send current mail to selected group
	 *
	 * @access	public
	 */
    function sendAContact()
  	{
     	$mainframe = &JFactory::getApplication();
    	// Check for request forgeries
  		JRequest::checkToken() or jexit( 'Invalid Token' );
	    //get a refrence of the page instance in joomla 
    	$document=& JFactory::getDocument(); 
    	//get the view name from the query string 
     	$viewName = JRequest::getVar('view', 'form'); 
	    //get our view 
	    $viewType= $document->getType();
	    	    
     	$view = &$this->getView($viewName, $viewType); 
      	$errors=0;
	    //get the model 
     	$model = &$this->getModel('form', 'ModelhecMailing');
      	$contactmodel = &$this->getModel('contact', 'ModelhecMailing');
      	
     	// get DB connection
  		$session =& JFactory::getSession();
	    $db	=& JFactory::getDBO();

	    // Import joomla mail module
     	jimport( 'joomla.mail.helper' );
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
					 'subject',
					 );

	    /*
	    * Here is the meat and potatoes of the header injection test.  We
	    * iterate over the array of form input and check for header strings.
	    * If we find one, send an unauthorized header and die.
	    */
	    foreach ($fields as $field)
	    {
		     foreach ($headers as $header)
		     {
		     	  if (array_key_exists ( $field , $_POST ))
		     	  {
				      if (strpos($_POST[$field], $header) !== false)
				      {
					       JError::raiseError(403, '');
				      }
		     	  }
		     }
	    }

  		/* Free up memory */
  		unset ($headers, $fields);
  		
    	# the response from reCAPTCHA
			$resp = null;
			# the error code from reCAPTCHA, if any
			
			$error = null;
			$error	= false;
			$check_captcha = JRequest::getInt('check_captcha',1,'post');
			
			if ($check_captcha==1)
			{
				$privatekey = $this->params->get('captcha_private_key','6LexAQwAAAAAAKTT3bwI9SR2mCAfExdLlS-DHfQt');
    			$recaptcha_challenge_field = JRequest::getString('recaptcha_challenge_field','','post');
    			$recaptcha_response_field= JRequest::getString('recaptcha_response_field','','post');
			  	if ($recaptcha_response_field!='') 
				{
			      $resp = recaptcha_check_answer ($privatekey,
		          $_SERVER["REMOTE_ADDR"],
		          $recaptcha_challenge_field,
		          $recaptcha_response_field);
	
		        if ($resp->is_valid) 
		        {
		        		$error=false;
	  	    	}
	  	    	else
	  	    	{
	  		    	$error=true;
	  	    	}
		    }
       		 else  {  $error=true;  }
    		if ($error)
    		{	
	  				JError::raiseWarning(0, JText::_('COM_HECMAILING_CONTACT_CAPTCHA_ERR') );
	  				$errors++;
        		return false ;
       	}
       }
			
  		// set mail fields from POST form
  		$from = JRequest::getString('email', '', 'post');
  		$name  = JRequest::getString('name', '', 'post');
  		$idcontact = JRequest::getInt('contact', 0, 'post');
  		$contactinfo = $contactmodel->getContactInfo($idcontact)  ;
  		$subject = JRequest::getString('subject', '', 'post');
  		//$body 	= JRequest::getVar('body', '', 'post', 'string', JREQUEST_ALLOWRAW);
  		$msg 	= JRequest::getVar('body', '', 'post', 'string');
  		$msg=nl2br($msg);
  		$search=array();
  		if ($contactinfo->ct_vl_template==null)
  		{
  			$body="{BODY}";
  		}
  		else {
  			$body = $contactinfo->ct_vl_template;
  	    }
		$body_found=stripos($body,"{BODY}");
  		if ($body_found=== FALSE)
  			$body=$body."{BODY}";
  		$subject = $contactinfo->ct_vl_prefixsujet . $subject;
  		$body = str_ireplace("{BODY}",$msg,$body);
  		
  		$body = str_ireplace("{NAME}",$name,$body);
  		$body = str_ireplace("{EMAIL}",$from,$body);
  		//echo htmlentities($body);
    	//$body = html_entity_decode($body);
  		$backup_mail = JRequest::getInt('backup_mail','0','post');
	   	$backup_mail = ($backup_mail==1);
		
		$groupe = $contactinfo->grp_id_groupe;

  		// Check for a valid from address
  		if ( ! $from || ! JMailHelper::isEmailAddress($from) )
  		{
  			$error	= JText::sprintf('COM_HECMAILING_EMAIL_INVALID', $from);
  			JError::raiseWarning(0, $error );
  			return false;
  		}
  		else
  		{
			// Clean the email data
			$subject = JMailHelper::cleanSubject($subject);
			$body	 = JMailHelper::cleanBody($body);
			$name	 = JMailHelper::cleanAddress($name);
	    if ($groupe>0)     // Can't send to all users ($groupe>=0 changed with $groupe>0)
	    	{
	
	        	$detail = $model->getMailAdrFromGroupe($groupe,false);
	        	$nb=0;
	        	$errors=0;
	        	$recipient = array();
		        foreach($detail as $elmt)
		        {
		          $email = $elmt[0];
		          // Check email
		          if ( ! $email  || ! JMailHelper::isEmailAddress($email) )
		    	  {
		    			   $error	= JText::sprintf('COM_HECMAILING_EMAIL_INVALID', $email, $elmt[1]);
		    			   JError::raiseWarning(0, $error );
		    			   $errors++;
		          }
		          else
		          {
		        	$recipient[] = $email;	
		        	$this->_log->addEntry(array('comment'=>"Send contact to ".$email));
		          }
		        }
		        if 	(count($recipient)>0)	// if there is at least one email ok ...
		        {
		        		// Send the email
		        		if ( JUtility::sendMail($from, $name, $recipient, $subject, $body,true) !== true )
		        		{
		        		  	$error	= JText::sprintf('COM_HECMAILING_EMAIL_NOT_SENT', $email, $elmt[1]);
		        			JError::raiseNotice( 500, $error);
		       				$errors++;
		        		}
		       			else
		       			{
		       				$nb+=count($recipient);
		       				$this->_log->addEntry(array('comment'=>"Send contact to ".";".join($recipient)));
		       			}
		      	}
		        else
		        {
        		  	$error	= JText::sprintf('COM_HECMAILING_EMAIL_NOT_SENT', $email, $elmt[1]);
        			JError::raiseNotice( 500, $error);
       				$errors++;
		        }
		        if ($backup_mail)
		        {
		        	// Send the email copy
		        	$MailFrom 	= $mainframe->getCfg('mailfrom');
      				$FromName 	= $mainframe->getCfg('fromname');
	        		if ( JUtility::sendMail($MailFrom,$FromName,  $from, 'COPY:'.$subject, $body,true) !== true )
	        		{
	        		  	$error	= JText::sprintf('COM_HECMAILING_EMAIL_BACKUP_NOT_SENT', $from);
	        			JError::raiseNotice( 500, $error);
	       				$errors++;
	        		}
	       			else
	       			{
	       				$nb++;
	       			}
		        }
	      }
	      else
	      {
	       $error	= JText::_('COM_HECMAILING_INVALID GROUP')." ".$groupe." for contact ".$idcontact;
		    			   JError::raiseWarning(0, $error );
		    			   $errors++;
        }
	    }
      if ($errors>0)
      {
      	return false;
      }
      else
      {
      	return true;
      }
					

	}
	
	function saveGroup()
	{
		echo '<script language="javascript" type="text/javascript">alert("Groupe enregistre");</script>';
		echo '<script language="javascript" type="text/javascript">window.close();</script>';
	
	}	
}
?>
