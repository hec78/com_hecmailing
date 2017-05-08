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
	protected $default_view = 'contact';
	protected $defalt_layout = 'default';
	/**
	 * The URL view list variable.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $view_list = null;
	
	
	function display($cachable=false, $urlparams=false)
	{
		$app = JFactory::getApplication();
		$model = $this->getModel('contact', 'hecMailingModel');
		//get the view name from the query string
		$viewName = $app->input->get('view', 'contact');
		//get our view
		$viewType= $document->getType();
		
		$this->view = $this->getView($viewName, $viewType);
		
		$this->view->setModel($model,true);
		$this->view->display();
	}
	
	/**
	 * Method to send current mail to selected group
	 *
	 * @access	public
	 */
	function send()
	{
		$app = JFactory::getApplication();
		// Check for request forgeries
		JSession::checkToken();
		
		$data = $app->input->get('jform', '', 'array');
		$formData = new JInput($app->input->get('jform', '', 'array'));
		//get a refrence of the page instance in joomla
		$document=JFactory::getDocument();
		//get the view name from the query string
		$viewName = $app->input->get('view', 'contact');
		//get our view
		$viewType= $document->getType();
	
		$this->view = $this->getView($viewName, $viewType);
		$errors=0;
		//get the model
		$model = $this->getModel('send', 'hecMailingModel');
		$contactmodel = $this->getModel('contact', 'hecMailingModel');
		//$res = $contactinfo->form->validate($_POST);
		
		$post = JFactory::getApplication()->input->post;
		$dispatcher = JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('captcha');
		
		$res = $dispatcher->trigger('onCheckAnswer',$post->get('g-recaptcha_response',''));
		if (isset($res))
		{
			if(count($res)>0)
			{
				if(!$res[0]){
					$app->enqueueMessage(JText::_('COM_HECMAILING_CONTACT_CAPTCHA_ERR') ,'error');
					$this->view->setModel($contactmodel,true);
					$this->view->display();
					return false ;
				}
			}
		}
		
		
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
				if (array_key_exists ( $field , $data ))
				{
					if (strpos($data[$field], $header) !== false)
					{
						throw new Exception('',403);
					}
				}
			}
		}
		/* Free up memory */
	  	unset ($headers, $fields);
	  		
	   
				
  		// set mail fields from POST form
  		$from = $formData->getString('email','');
  		$name  = $formData->getString('name','');
  		$idcontact = $formData->getInt('contact', 0);
  		$contactinfo = $contactmodel->getContactInfo($idcontact)  ;
  		$subject = $formData->getString('subject', '');
  		
  		$msg 	= $formData->getString('body', '');
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
  		$backup_mail = $app->input->getInt('backup_mail','0','post');
	   	$backup_mail = ($backup_mail==1);
			
		$groupe = $contactinfo->grp_id_groupe;
	
  		// Check for a valid from address
  		if ( ! $from || ! JMailHelper::isEmailAddress($from) )
  		{
  			
  			$app->enqueueMessage(JText::sprintf('COM_HECMAILING_EMAIL_INVALID', $from) ,'error');
  			$this->view->setModel($contactmodel,true);
  			$this->view->display();
  			
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
		          $email = $elmt->email;
		          // Check email
		          if ( ! $email  || ! JMailHelper::isEmailAddress($email) )
		    	  {
		    			   $error	= JText::sprintf('COM_HECMAILING_EMAIL_INVALID', $email, $elmt->name);
		    			   $app->enqueueMessage($error ,'error');
		    			   $errors++;
		          }
		          else
		          {
		        	$recipient[] = [$email, $elmt->name];	
		        	$this->addLogEntry("Send contact to ".$email);
		          }
		        }
		        if 	(count($recipient)>0)	// if there is at least one email ok ...
		        {
		        		// Send the email
		        	
		        		if ( HecMailingHelper::sendMail($from, $name, $recipient, $subject, $body) !== true )
		        		{
		        		  	$error	= JText::sprintf('COM_HECMAILING_EMAIL_NOT_SENT', $email, $elmt->name);
		        		  	$app->enqueueMessage($error ,'error');
		        			
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
        		  	$error	= JText::sprintf('COM_HECMAILING_EMAIL_NOT_SENT', $email, $elmt->name);
        		  	$app->enqueueMessage($error ,'error');
        			
       				$errors++;
		        }
		        if ($backup_mail)
		        {
		        	// Send the email copy
		        	$MailFrom 	= $app->get('mailfrom','');
      				$FromName 	= $app->get('fromname','');
	        		if ( HecMailingHelper::sendMail($MailFrom,$FromName,  array($from), 'COPY:'.$subject, $body) !== true )
	        		{
	        		  	$error	= JText::sprintf('COM_HECMAILING_EMAIL_BACKUP_NOT_SENT', $from);
	        		  	$app->enqueueMessage($error ,'error');
	        			
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
		       $app->enqueueMessage($error ,'error');
		       $errors++;
	        }
	    }
	      if ($errors>0)
	      {
	      	$this->view->setModel($contactmodel,true);
	      	$this->view->display();
	      	return false;
	      }
	      else
	      {
	      	$msg=JText::_("COM_HECMAILING_CONTACT_SENT");
	      	$this->setRedirect(JRoute::_('index.php'),$msg,'success');
	      	$this->redirect();
	      	return true;
	      }
						
	
		}

	
}