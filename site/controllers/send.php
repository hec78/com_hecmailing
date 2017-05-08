<?php
/**
* @version   3.4.0
* @package   HEC Mailing for Joomla
* @copyright Copyright (C) 1999-2017 Hecsoft All rights reserved.
* @author    Herv� CYR
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
defined('_JEXEC') or die;
require_once JPATH_COMPONENT.'/controller.php';
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 */
class HecMailingControllerSend extends HecMailingController
{
	/**
	 * The URL view item variable.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $view_item = 'send';
	
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
		
		$modelbase = $this->getModel("Send","HecMailingModel");
		
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
	public function &getModel($name = 'Send', $prefix = 'HecMailingModel', $config = Array())
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
	
	function send()
	{
		// Check for request forgeries
		//JRequest::checkToken() or jexit( 'Invalid Token' );
		JSession::checkToken() or jexit( 'Invalid Token' );
		// get the model
		$model = $this->getModel();
		// get POST data
		$data=$_POST;
		$files=$_FILES;
		
		//$model= $this->getModel();
		if ($idmessage=$model->prepare_send($data, $files))
		{
			$return = JRoute::_(JURI::current().'?option=com_hecmailing&idmessage='.$idmessage.'&view=sending');
		}
		else
		{
			$error	= JText::_('COM_HECMAILING_EMAIL_NOT_SENT');
			JError::raiseNotice( 500, $error);
			$return = JRoute::_(JURI::base());
		}
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
		$app=JFactory::getApplication();
		//get the view name from the query string
		$viewName = $app->input->get('view', 'form');
	
		$viewType= $document->getType();
	
		$viewName	= 'form';
		$layout		= 'default';
		$app->input->set('view' , $viewName);
		$app->input->set('layout', $layout);
	
		//get our view
		$view = $this->getView($viewName, $viewType);
		try {
			//get the model
			$model = $this->getModel();
	
			$view->setModel ($model, true);
		}
		catch (Exception $err)
		{
			$app->enqueueMessage($err->getMessage());
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
		$app=JFactory::getApplication();
		// Show alert message
		echo "<script>alert('".JText::_("COM_HECMAILING_CANCEL_ALERT")."');</script>";
		$userid = $app->input->get( 'id', 0, 'post', 'int' );
		//$this->display();
		$msg=JText::_('COM_HECMAILING_CANCEL_MSG');
	
		$app->input->set( 'Itemid', 0 );
		$return = JURI::current();
		$this->setRedirect( $return, $msg );
	}
	/**
	 * Method to check if source page is this website page
	 *
	 * @access	public
	 * @return true if page belong this website false else
	 */
	
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
	/**
	 * Method to return directory content for Browse WebService
	 *
	 * @access	public
	 */
	
	function getdir()
	{
		$app=JFactory::getApplication();
		if (JFactory::getUser()->guest)
			$conf=array('cancreatedir'=>false, 'canupload'=>false);
		else 
			$conf=array('cancreatedir'=>true, 'canupload'=>true);
		$message="";
		$status='ok';
		if ($this->checkWebServiceOrigine())
		{
			if (array_key_exists("dir", $_POST))
			{
				$dir = $_POST["dir"];
			}
			else
			{
				$dir= $app->input->get('dir','');;
			}
			$list=array();
			$params = JComponentHelper::getParams( 'com_hecmailing' );
			$root = realpath(JPATH_ROOT).DS.$params->get('browse_path','images');
			if ($dir != '')
			{
				$list[] = array('type'=>'dir','path'=>"..");
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
							{
								$list[] = array('type'=>'dir','path'=>$file);
							}
						}
						else 
						{
							if ($file !='.' && $file!='..')
							{
								$mtime = date ("d/m/Y H:i",filemtime($dir .'/'. $file));
								$fsize= filesize($dir .'/'. $file);
								$list[]= array('type'=>'file','path'=>$file, 'mtime'=>$mtime, 'size'=>$fsize);
								
							}
						}
					}
					closedir($dh);
				}
			} else {
				$list[] = array();
				$status='error';
				$message=JText::sprintf("COM_HECMAILING_WEBSERVICE_GETLIST_FOLDERDOESNTEXIST",$dir);
			}
			
		}
		else
		{
			$list[] = array();
			$status='error';
			$message=JText::_("COM_HECMAILING_WEBSERVICE_NOTALLOWED");
		}
		
		sort($list);
		$data = array('dir'=> $relatdir, 'list' => $list, 'status'=>$status, 'params'=>$conf, 'message'=>$message);
		// Get the document object.
		$document =JFactory::getDocument();
		
		// Set the MIME type for JSON output.
		$document->setMimeEncoding('application/json');
		
		// Change the suggested filename.
		$app->setHeader('Content-Disposition','attachment;filename="getdirlist.json"');
		
		// Output the JSON data.
		echo json_encode($data);
		
		exit;
	}
	
	/**
	 * Method to return directory content for Browse WebService
	 *
	 * @access	public
	 */
	
	function upload()
	{
		$params = JComponentHelper::getParams( 'com_hecmailing' );
		$conf=array();
		$message="";
		$status='ok';
		$rootdir = realpath(JPATH_ROOT).DS.$params->get('browse_path','images');
		$list= array();
		$app=JFactory::getApplication();
		if ($this->checkWebServiceOrigine())
		{
			$dir = $app->input->getString("directory","");
			//$files = $app->input->files->get("uploadedfiles");
			$files=$_FILES["uploadedfiles"];
			if($files)
			//foreach ($files as $file) {
			for ($i=0;$i<count($files['name']);$i++) {
				
				// Get uploaded files
				$filename = JFile::makeSafe($files['name'][$i]);
				$src = $files['tmp_name'][$i];
				if ($src!='')
				{
					//Set up the source and destination of the file
					if ($dir!="")
						$dest = $rootdir.DIRECTORY_SEPARATOR.$dir.DIRECTORY_SEPARATOR.$filename;
					else 
						$dest = $rootdir.DIRECTORY_SEPARATOR.$filename;
					// Upload uploaded file to attchment directory (temp or saved dir)
					if (JFile::upload($src, $dest, false,true))
					{
						$list[$filename]=$filename;
						$message=JText::_("COM_HECMAILING_WEBSERVICE_UPLOADED");
					}
					else 
					{
						$status='error';
						$message=JText::_("COM_HECMAILING_WEBSERVICE_UPLOAD_ERROR").":".JFile::get;
					}
				} else {
					$status='error';
					$message=JText::_("COM_HECMAILING_WEBSERVICE_UPLOAD_EMPTYSOURCE").":".JFile::get;
				}
				
			}
			
			
		}
		else
		{
			$list[] = array();
			$status='error';
			$message=JText::_("COM_HECMAILING_WEBSERVICE_NOTALLOWED");
		}
		sort($list);
		$data = array('dir'=> $dir, 'selected' => $list, 'status'=>$status, 'params'=>$conf, 'message'=>$message);
		// Get the document object.
		$document =JFactory::getDocument();
	
		// Set the MIME type for JSON output.
		$document->setMimeEncoding('application/json');
	
		// Change the suggested filename.
		$app->setHeader('Content-Disposition','attachment;filename="uploaded.json"');
	
		// Output the JSON data.
		echo json_encode($data);
	
		exit;
	}
	
	
	function createdir()
	{
		$params = JComponentHelper::getParams( 'com_hecmailing' );
		$conf=array();
		$message="";
		$status='ok';
		$rootdir = realpath(JPATH_ROOT).DS.$params->get('browse_path','images');
		$list= array();
		$app=JFactory::getApplication();
		if ($this->checkWebServiceOrigine())
		{
			$dir = $app->input->getString("directory","");
			$newfolder = $app->input->getString("newfolder","");
			if ($newfolder!='')
			{
				//Set up the source and destination of the file
				if ($dir!="")
					$dest = $rootdir.DIRECTORY_SEPARATOR.$dir.DIRECTORY_SEPARATOR.$newfolder;
				else
					$dest = $rootdir.DIRECTORY_SEPARATOR.$newfolder;
				if (mkdir($dest))
				{				
					
					$message=JText::_("COM_HECMAILING_WEBSERVICE_NEWFOLDER_CREATED");
				}
				else 
				{
					$status='error';
					$message=JText::sprintf("COM_HECMAILING_WEBSERVICE_NEWFOLDER_ERROR",$dest);
				}
			}
			else {
				$status='error';
				$message=JText::_("COM_HECMAILING_WEBSERVICE_NEWFOLDER_MISSING");
			}
		}
		else
		{
			$newfolder="";
			$status='error';
			$message=JText::_("COM_HECMAILING_WEBSERVICE_NOTALLOWED");
		}
		
		$data = array('dir'=> $dir, 'selected' => $newfolder, 'status'=>$status, 'params'=>$conf, 'message'=>$message);
		// Get the document object.
		$document =JFactory::getDocument();
		
		// Set the MIME type for JSON output.
		$document->setMimeEncoding('application/json');
		
		// Change the suggested filename.
		$app->setHeader('Content-Disposition','attachment;filename="createdfolder.json"');
		
		// Output the JSON data.
		echo json_encode($data);
		
		exit;
		
	}
	
	function getGroupContent()
	{
		$app=JFactory::getApplication();
		if (checkWebServiceOrigine())
		{
			$currentGroup = $app->input->get('groupid', 0, 'get', 'int');
			$groupType = $app->input->get('grouptype', 0, 'get', 'int');
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
		$app->setHeader('Content-Disposition','attachment;filename="group'.$groupType.'.json"');
		
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