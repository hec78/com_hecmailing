<?php
/**
* @package     HECMailing
* @subpackage  Main
*
* @copyright   Copyright (C) 2005 - 2014 HecSoft All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;
require_once JPATH_COMPONENT.'/controller.php';
require_once JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR."helpers/hecmailing.php";
/**
 * @package     HECMailing
 * @subpackage  Main
 */
class HecMailingControllerContact extends HecMailingController
{
	/**
	 * The URL view item variable.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $view_item = 'contact';

	/**
	 * The URL view list variable.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $view_list = null;
	
	
	/**
	 * Method to send current mail to selected group
	 *
	 * @access	public
	 */
	function send()
	{
		$mainframe = JFactory::getApplication();
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		//get a refrence of the page instance in joomla
		$document=JFactory::getDocument();
		//get the view name from the query string
		$viewName = JRequest::getVar('view', 'form');
		//get our view
		$viewType= $document->getType();
	
		$view = $this->getView($viewName, $viewType);
		$errors=0;
		//get the model
		$model = $this->getModel('form', 'hecMailingModel');
		$contactmodel = $this->getModel('contact', 'hecMailingModel');
		 
		// get DB connection
		$session = JFactory::getSession();
		$db	=JFactory::getDBO();
	
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
			          $email = $elmt[1];
			          // Check email
			          if ( ! $email  || ! JMailHelper::isEmailAddress($email) )
			    	  {
			    			   $error	= JText::sprintf('COM_HECMAILING_EMAIL_INVALID', $email, $elmt[2]);
			    			   JError::raiseWarning(0, $error );
			    			   $errors++;
			          }
			          else
			          {
			        	$recipient[] = $email;	
			        	$this->addLogEntry("Send contact to ".$email);
			          }
			        }
			        if 	(count($recipient)>0)	// if there is at least one email ok ...
			        {
			        		// Send the email
			        	
			        		if ( HecMailingHelper::sendMail($from, $name, $recipient, $subject, $body) !== true )
			        		{
			        		  	$error	= JText::sprintf('COM_HECMAILING_EMAIL_NOT_SENT', $email, $elmt[2]);
			        			JError::raiseNotice( 500, $error);
			       				$errors++;
			        		}
			       			else
			       			{
			       				$nb+=count($recipient);
			       				$this->addLogEntry("Send contact to ".";".join($recipient));
			       			}
			      	}
			        else
			        {
	        		  	$error	= JText::sprintf('COM_HECMAILING_EMAIL_NOT_SENT', $email, $elmt[2]);
	        			JError::raiseNotice( 500, $error);
	       				$errors++;
			        }
			        if ($backup_mail)
			        {
			        	// Send the email copy
			        	$MailFrom 	= $mainframe->getCfg('mailfrom');
	      				$FromName 	= $mainframe->getCfg('fromname');
		        		if ( HecMailingHelper::sendMail($MailFrom,$FromName,  array($from), 'COPY:'.$subject, $body) !== true )
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

	
}