<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
require_once JPATH_COMPONENT.'/controller.php';
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 */
class HecMailingControllerForm extends HecMailingController
{
	/**
	 * The URL view item variable.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $view_item = 'form';
	
	/**
	 * The URL view list variable.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $view_list = null;
	
	public function __construct($config = array())
	{
		$this->input = JFactory::getApplication()->input;
	
		// Check acces right (for send email)
		$params = JComponentHelper::getParams( 'com_hecmailing' );
		$usr =JFactory::getUser();
		parent::__construct($config);
		// Check if current user is in authorized joomla groups
		$adminType = $params->get('usertype','ADMINISTRATOR;SUPER ADMINISTRATOR');
		
		$modelbase = $this->getModel("Form","HecMailingModel");
		
		// Check if current user is in admin hec Mailing group (groupaccess hec Mailing parameter)
		$usrgrp = $params->get('groupaccess','MailingGroup');
			
		if (!$modelbase->isAdminUserType($adminType) && !$modelbase->isInGroupe($usrgrp) && !$modelbase->hasGroupe())
		{
			$msg=JText::sprintf("COM_HECMAILING_NO_RIGHT",$usrgrp);
			$return = JURI::root();		// redirect to home if no right
			$this->setRedirect( $return, $msg );
		}
	
		
		
	}
	
	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function &getModel($name = 'Form', $prefix = 'HecMailingModel', $config = Array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
	
	
	/**
	 * Method to save current mail as template
	 *
	 * @access	public
	 */
	function save()
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
		$view = $this->getView('form', $viewType);
		$session =JFactory::getSession();
		$db	=JFactory::getDBO();
		 
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
		$mail =JFactory::getMailer();
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
	$mainframe = JFactory::getApplication();
	// Check for request forgeries
	JRequest::checkToken() or jexit( 'Invalid Token' );
	//get a refrence of the page instance in joomla
	$document=JFactory::getDocument();
			//get the view name from the query string
			$viewName = JRequest::getVar('view', 'form');
			//get our view
			$viewType= $document->getType();
			$view = $this->getView('form', $viewType);
					$viewName = "form";
			//get the model
			$model = $this->getModel($viewName, 'HecMailingModel');
	
			$attach_path='';
			$session =JFactory::getSession();
			$db	=JFactory::getDBO();
	
			jimport( 'joomla.mail.helper' );
			 
			$SiteName 	= $mainframe->getCfg('sitename');
			$MailFrom 	= $mainframe->getCfg('mailfrom');
			$FromName 	= $mainframe->getCfg('fromname');
	
  		$link 		= base64_decode( JRequest::getVar( 'link', '', 'post', 'base64' ) );
			 
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
			$attach_path=$params->get('attach_path', $attach_path);
  			$path = realpath(JPATH_ROOT).DS;	// Get path root
			
  			$body .= "<img=\"".JURI::base(false)."index.php?option=com_hecmailing&task=form.mail_read&email={EMAIL}&mail_id={MAIL_ID}\" alt=\"READ TAG\" />";
	
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
								$listElmt=array();
								$user =JFactory::getUser();
								 
								if ($groupe>=0)	// if group selected
								{
								// Get email list from groupe
								$detail = $model->getMailAdrFromGroupe($groupe,$useprofil);
								 
								 
						 		// Insert email info
						 		//Create data object
						 		$rowdetail = new JObject();
						 		/*if(version_compare(JVERSION,'1.6.0','<')){ $rowdetail->log_dt_sent = JFactory::getDate()->toFormat(); }
						 		else { 	$rowdetail->log_dt_sent = JFactory::getDate()->format("%Y-%M-%d %H:%M:%S"); }*/
						 		$rowdetail->log_dt_sent = JFactory::getDate();
						 		if(version_compare(JVERSION,'1.6.0','<')){ $rowdetail->log_dt_sent = $rowdetail->log_dt_sent->toFormat(); }
						 		else { $rowdetail->log_dt_sent = $rowdetail->log_dt_sent->format("Y-m-d H:i:s"); }
						 		$rowdetail->log_vl_subject = $subject ;
						 		$rowdetail->log_vl_body = $bodytolog  ;
						 		$rowdetail->log_vl_from = $from   ;
						 		$rowdetail->grp_id_groupe =  $groupe    ;
						 		$rowdetail->usr_id_user =  $user->id  ;
						 		$rowdetail->log_bl_useprofil =  $useprofil ;
						 		$rowdetail->log_nb_ok = 0  ;
						 		$rowdetail->log_nb_errors  =  0 ;
						 		$rowdetail->log_vl_mailok = ""; //join(";", array_map($func,$lstmailok))  ;
								$rowdetail->log_vl_mailerr =""; //join(";",array_map($func,$lstmailerr)) ;
								 
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
								 
								 
								$nb=0;
								foreach($detail as $elmt)	// send to each email
								{
								$email = $elmt[1];
									// check email
			  				if ( ! $email  || ! JMailHelper::isEmailAddress($email) )
			  				{
			  				// Bad email --> Add warning and add error counter, but don't send email
			  				$error	= JText::sprintf('COM_HECMAILING_EMAIL_INVALID', $email, $elmt[2]);
			  				JError::raiseWarning(0, $error );
			    			$errors++;
			  				}
			  				else
			  				{
			  				// Correction pour J2.5 et envoi par bloc
			  				$emailNamed = array($email,$elmt[2]);
			  				$list[] = $emailNamed;
			  				$listElmt[] = $elmt;
	
			  				// Send the email
			  				if (count($list)>= $sendcount)
			  				{
			  				if ( $this->sendMail($from, $sender, null, $subject, $body,true,null,$list,$attach,null,null, $inline) !== true )
			  					{
			  					// Error while sending email --> Add Error ...
			  					$error	= JText::sprintf('COM_HECMAILING_EMAIL_NOT_SENT', "", count($list));
			  					JError::raiseNotice( 500, $error);
			  					$errors+=count($list);
			  					$lstmailerr = array(); // array_merge($lstmailerr,$list);
			  					$status=9;
			  					$info = JText::sprintf('COM_HECMAILING_EMAIL_NOT_SENT',"","");
			  				}
			  				else
			  				{
			  				// email ok ...
			  					$nb+=count($list);
			  					$lstmailok = array(); // array_merge($lstmailok,$list);
			  					$status=1;
			  					$info="";
			  				}
			  				 
			  				// Insert emails
			  					$query = $db->getQuery(true);
			  					$query->insert('#__hecmailing_log_user');
			  					$columns = array( "log_id_message",  "userid",  "email",  "status",  "info");
			  					$query->columns($db->quoteName($columns));
			  					foreach($listElmt as $email_sent)
			  					{
			  					$query->values(implode(',', array($logid,$db->quote($email_sent[0]), $db->quote($email_sent[1]), $status,$db->quote($info))));
			  					}
			  					$db->setQuery($query);
			  					$db->query();
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
			      $error	= JText::sprintf('COM_HECMAILING_EMAIL_NOT_SENT', "", count($list));
			      	JError::raiseNotice( 500, $error);
		      		$errors+=count($list);
			      		$lstmailerr = array(); //array_merge($lstmailerr,$list);
			      		$status=9;
			      	$info = JText::sprintf('COM_HECMAILING_EMAIL_NOT_SENT',"","");
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
			      	 
			      	 
			      	 
			      	$msg=JText::_("COM_HECMAILING_EMAIL_SENT"). "(".$nb.JText::_("COM_HECMAILING_SEND_OK")."/".$errors.JText::_("COM_HECMAILING_SEND_ERR").")";
  		if ($logid>0)
	  		$return = JRoute::_(JURI::current().'?idlog='.$logid.'&view=logdetail');
	  		else
			      		$return = JRoute::_(JURI::base());
  		$this->setRedirect( $return, $msg );
	}
	/**
	 * Method to load a saved template mail
	 *
	 * @access	public
	 */
	function load()
	{
		//get a reference of the page instance in joomla
		$document=JFactory::getDocument();
		//get the view name from the query string
		$viewName = JRequest::getVar('view', 'form');
	
		$viewType= $document->getType();
	
		$viewName	= 'form';
		$layout		= 'default';
		JRequest::setVar('view' , $viewName);
		JRequest::setVar('layout', $layout);
	
		//get our view
		$view = $this->getView($viewName, $viewType);
	
		//get the model
		$model = $this->getModel();
	
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
	function checkWebServiceOrigine()
	{
		return true;
		$user = JFactory::getUser();
		$user->guest==0 or die("|NOT ALLOWED|");
		if (isset($_SERVER['HTTP_REFERER']))
			$ref = $_SERVER['HTTP_REFERER'];
		else
			$ref="";
		$uri = $_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"];
		$ref_tab = explode('/', $ref);
		$ser_tab = explode('/', $uri);
		$uri_serveur='';
		$j=2;
		$ok=true;
	
		for ($i=0;$i<count($ser_tab)-4;$i++)
		{
			if ($ref_tab[$j]!=$ser_tab[$i])
			{
				$ok=false;
				break;
			}
			$j++;
		}
		return $ok;
	}
	
	function getdir()
	{
		if ($this->checkWebServiceOrigine())
		{
			if (array_key_exists("dir", $_POST))
			{
				$dir = $_POST["dir"];
			}
			else
			{
				$dir= JRequest::getVar('dir','');;
			}
			$list=array();
			$params = JComponentHelper::getParams( 'com_hecmailing' );
			$root = realpath(JPATH_ROOT).DS.$params->get('browse_path','images');
			if ($dir != '')
			{
				$list[] ="@..";
				$relatdir = $dir;
				$dir = $root . $dir;
			}
			else
			{
				$relatdir="";
				$dir=$root;
			}
		
			// Open a known directory, and proceed to read its contents
			if (is_dir($dir))
			{
				if ($dh = opendir($dir))
				{
					while (($file = readdir($dh)) !== false)
					{
						if (is_dir($dir .'/'. $file))
						{
							if ($file !='.' && $file!='..')
								$list[] = '@'.$file;
						}
					}
					closedir($dh);
				}
				if ($dh = opendir($dir))
				{
					while (($file = readdir($dh)) !== false)
					{
						if (!is_dir($dir .'/'. $file))
						{
							if ($file !='.' && $file!='..')
								$list[]=  $file;
						}
					}
					closedir($dh);
				}
			}
		}
		else
		{
			$list[] = "$NOT ALLOWED";
		}
		$data = array('dir'=> $relatdir, 'list' => $list);
		// Get the document object.
		$document =JFactory::getDocument();
		
		// Set the MIME type for JSON output.
		$document->setMimeEncoding('application/json');
		
		// Change the suggested filename.
		JResponse::setHeader('Content-Disposition','attachment;filename="getdirlist.json"');
		
		// Output the JSON data.
		echo json_encode($data);
		
		exit;
	}
	function getGroupContent()
	{
		if (checkWebServiceOrigine())
		{
			$currentGroup = JRequest::getVar('groupid', 0, 'get', 'int');
			$groupType = JRequest::getVar('grouptype', 0, 'get', 'int');
			$db =JFactory::getDBO();
			switch($groupType)
			{
				case 3:
					if(version_compare(JVERSION,'1.6.0','<')){
						//Code pour Joomla! 1.5
						$query = "Select id, name From #__core_acl_aro_groups order by id";
					}
					else
					{
						//Code pour Joomla >= 1.6.0
						$query = "SELECT id, title FROM  #__usergroups  ORDER BY id";
					}
					break;
				case 5:
					$query = "Select grp_id_groupe, grp_nm_groupe FROM #__hecmailing_groups WHERE grp_id_groupe!=".$currentGroup." ORDER BY grp_nm_groupe";
					break;
			}
			$db->setQuery( $query );
			if (!$db->query()) {
				$data = array(array('-1', JText::_('MSG_ERROR_SAVE_CONTACT').':'.$query.'/'.$db->getErrorMsg(true)));
			}
			else
				$data = $db->loadRowList();
		}
		else
		{
			$data = array(array('0','NOT ALLOWED'));
		}
			
		// Get the document object.
		$document =& JFactory::getDocument();
		
		// Set the MIME type for JSON output.
		$document->setMimeEncoding('application/json');
		
		// Change the suggested filename.
		JResponse::setHeader('Content-Disposition','attachment;filename="group'.$groupType.'.json"');
		
		// Output the JSON data.
		echo json_encode($data);
		exit;
		
		
	}
	
	function mail_read()
	{
		$app = JFactory::getApplication();
		$mail_id=$app->input->getInt("mail_id", 0 );
		$email=$app->input->getInt("email", 0 );
		
		$db = JFactory::getDbo();
		$query = "UPDATE #__hecmailing_log_user SET status=2, timestamp=sysdate() WHERE log_id_message=" . $mail_id . " AND email=".$db->quote($email);
		$db->setQuery($query);
		$db->query();
		$file=JPATH_COMPONENT."/images/pix.png";
		$image= imagecreatefrompng($file);
		header("Content-Type: image/png" );
		imagepng($image);
		exit;
		
	}
}

?>