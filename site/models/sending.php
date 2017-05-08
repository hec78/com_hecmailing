<?php 
use Joomla\Input\Files;
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
JLoader::register('HecMailingMailFrontendHelper',JPATH_COMPONENT.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mail.php');
JLoader::register('HecMailingGroupsHelper',JPATH_COMPONENT.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'groups.php');
/**
 * hecMailing Component Form Model
 *
 * @package		Joomla
 */
class hecMailingModelSending extends JModelLegacy 
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

	function updateRecipient($messageId, $recipientId, $status, $error="")
	{
		$db=$this->getDBO();
		$query = "UPDATE #__hecmailing_message_recipient SET status=".$status.",error=".$db->quote($error).",timestamp=now()
      			  WHERE message_id=".$messageId." AND id=".$recipientId." AND status!=".$status;
		
		$db->setQuery($query);
		return $db->execute();
	}

  /**
	 * Method to get message information
	 *
	 * @access	public
	 * @param	int idMessage : Message ID
	 * @return message object:
	 * 		message_subject
	 * 		message_body
	 * 		message_from
	 * 		message_fromname
	 * 		recipients
	 * 			id
	 * 			userid
	 * 			email
	 * 			name
	 * 			status
	 * 		attachments
	 * 			files
	 * 			filename
	 * 	     	cid 
	 */
   function getMessage($idMessage, $getsent=false)
   {
      $db=$this->getDBO();
      $query = "SELECT id,message_subject,message_body,message_from,IFNULL(message_fromname,'') as message_fromname, message_read_notification , group_id
      			FROM #__hecmailing_message WHERE id=".$idMessage;
      $db->setQuery($query);
      if (!$message = $db->loadObject())
      {
          return false;
      }
      $query = "SELECT mr.id,mr.userid,IFNULL(u.email,mr.email) as email,IFNULL(u.name,mr.name) as name,status  
      			FROM #__hecmailing_message_recipient mr LEFT JOIN #__users u ON mr.userid=u.id 
      			WHERE message_id=".$idMessage;
      if (!$getsent) $query.=" AND status=0";
      $db->setQuery($query);
      if (!$recip = $db->loadObjectList()) {  	$message->recipients = false;  }
      else { $message->recipients = $recip; }
      $query = "SELECT file,IFNULL(cid,'') as cid,filename
      			FROM #__hecmailing_message_attachment
      			WHERE message_id=".$idMessage;
      $db->setQuery($query);
      if (!$attlist = $db->loadObjectList()) { 	$message->attachments=null;  }
      else  
      {  	
      	$message->attachments = $attlist;  
      }
      if ($message->recipients)
      {
	      foreach($message->recipients as $recipient){
		      $query = "SELECT 	`message_id`, `recipient_id`,`question_code`,	`token`,	`question_title`,	`answer_list`
			 		FROM `#__hecmailing_answers` 
		      		WHERE message_id=".$idMessage." AND recipient_id=".$recipient->id;
		      $db->setQuery($query);
		      if (!$answerlist = $db->loadObjectList()) { 	$recipient->answers=null;  }
		      else
		      {
		      	$recipient->answers=  $answerlist;
		      }
	      }
      }
      $query = "SELECT tosend.nb as tosendcount, sent.nb as sentcount,readx.nb as readcount, error.nb as errorcount , excluded.nb as excludedcount
      			FROM (SELECT count(*) as nb FROM #__hecmailing_message_recipient WHERE status=0 AND message_id=".$idMessage.") as tosend,
      			(SELECT count(*) as nb FROM #__hecmailing_message_recipient WHERE status in (1,2) AND message_id=".$idMessage.") as sent,
      			(SELECT count(*) as nb FROM #__hecmailing_message_recipient WHERE status=2 AND message_id=".$idMessage.") as readx,
      			(SELECT count(*) as nb FROM #__hecmailing_message_recipient WHERE status=9 AND message_id=".$idMessage.") as error,
      			(SELECT count(*) as nb FROM #__hecmailing_message_recipient WHERE status=8 AND message_id=".$idMessage.") as excluded";
      $db->setQuery($query);
      $r = $db->loadObject();
      $message->tosend_count=$r->tosendcount;
      $message->sent_count=$r->sentcount;
      $message->read_count=$r->readcount;
      $message->error_count=$r->errorcount;
      $message->excluded_count=$r->excludedcount;
      $message->total_count=$message->tosend_count+$message->sent_count+$message->error_count+$message->excluded_count+$message->read_count;
      return $message;
   }
    
   /**
    * Method to send current mail to selected group
    *
    * @access	public
    */
   function send($messageId, $count)
   {
   	   	$db	=JFactory::getDBO();
   		$message = $this->getMessage($messageId);
   		$groupe = HecMailingGroupsHelper::getHecmailingGroupsIAdmin($message->group_id);
   		if ($groupe && $groupe[$message->group_id]->read)
   		{
   		
	   		jimport( 'joomla.mail.helper' );
	   		$params 	= JComponentHelper::getParams( 'com_hecmailing' );
	   		$from 		= $message->message_from;
	   		$fromname 	= $message->message_fromname;
	   		$subject 	= $message->message_subject;
	   		$body 		= $message->message_body;
	   		$attach		= $message->attachments;
	   		$readnotif  = $message->message_read_notification;
	   		
	   		
	   		
	   		$excluded_domains = $params->get('excluded_domains','');
	   		$excluded_domains = explode(";", $excluded_domains);
	   		
	   		if ($readnotif) $count=1;
	   		$nberrors=0;
	   		$nbexcluded=0;
	   		$nbsent=0;
	   		$nlu=0;
	   		// Check for a valid to address
	   		$error	= false;
	   	
	   
	   		// Check for a valid from address
	   		if ( ! $from || ! JMailHelper::isEmailAddress($from) )
	   		{
	   			$error	= JText::sprintf('COM_HECMAILING_EMAIL_INVALID', $fromvalue ."/".$from."/".$sender);
	   			raise (new Exception($error ));
	   		}
	   		
	   		
	   
		   	// Clean the email data
		   	$subject = JMailHelper::cleanSubject($subject);
		   	$body	 = JMailHelper::cleanBody($body);
		   	$sender	 = JMailHelper::cleanAddress($from);
	   		$list=array();
		   	if ($message->recipients)
	   		foreach($message->recipients as $recipient)	// send to each email
	   		{
	   			
	   			
	   			$email = $recipient->email;
	   			$name=$recipient->name;
	   			
	   			// check email
	   			if ( ! $email  || ! JMailHelper::isEmailAddress($email) )
	   			{
	   				// Bad email --> Add warning and add error counter, but don't send email
	   				$error	= JText::sprintf('COM_HECMAILING_EMAIL_INVALID', $email, $name);
	   				$this->updateRecipient($messageId, $recipient->id, 9, $error);
	   				$nberrors++;
	   				$nlu++;
	   			}
	   			else
	   			{
	   				$mailparts=explode("@", $email);
	   				if (!in_array($mailparts[1], $excluded_domains))
	   				{
	   					$nlu++;
	   					$list[]=$recipient;
	   				}
	   				else 
	   				{
	   					// Excluded domain email --> Add warning and add excluded counter, but don't send email
	   					$error	= JText::sprintf('COM_HECMAILING_EMAIL_DOMAINE_EXCLUDED', $email);
	   					$this->updateRecipient($messageId, $recipient->id, 8, $error);
	   					$nbexcluded++;
	   				}
	   			}
	   			if($nlu>=$count) break;
	   		}
		   	if (count($list)>0)
		   	{
		   		if($count==1) 
		   		{
		   			
		   			$recip=$list[0];
		   			
		   			// Ajout read notification TAG
		   			if ($readnotif>0)
		   			{
		   				$tagurl = JUri::base(false)."index.php?option=com_hecmailing&task=sending.read&idmessage=".$messageId."&idrecipient={idrecipient}";
		   				if (strchr("{readtag}", $body)<0 && $readnotif==3) $readnotif=2;
		   				$tagtmpldefault="<img src='{readtag_image_url}' alt=\"".JText::_("COM_HECMAILING_SENDING_ALT_READTAG")."\" />";
		   				$tagtmpl = $params->get("readtag_template", $tagtmpldefault);
		   				$tagtmpl=str_replace("{readtag_default_text}",JText::_("COM_HECMAILING_SENDING_ALT_READTAG") , $tagtmpl);
		   				$tagtmpl=str_replace("{readtag_image_url}",$tagurl , $tagtmpl);
		   				switch ($readnotif)
		   				{
		   					case 1:
		   						$body=str_replace("<body>","<body>".$tagtmpl,$body );
		   						break;
		   					case 2:
		   						$body=str_replace("</body>",$tagtmpl."</body>",$body );
		   						break;
		   					case 3:
		   						$body=str_replace("{readtag}",$tagtmpl,$body );
		   						break;
		   				}
		   			}
		   			 
		   			// Replace body variables
		   			$body=str_replace("{idrecipient}",$recip->id,$body );
		   			$body=str_replace("{email}",$recip->email,$body );
		   			$body=str_replace("{name}",$recip->name,$body );
		   			$body=str_replace("{message_id}",$message->id,$body );
		   			$body=str_replace("{recipient_id}",$recip->id,$body );
		   			// Replace HashCode for answers
		   			if (isset($recip->answer))
			   			foreach($recip->answers as $question)
			   			{
			   				$i=$question->answer_index;
			   				$answer_list=explode(";", $question->answer_list);
			   				foreach($answer_list as $answer_code)
			   				{
				   				$hashcode = crypt($answer->token, $answer_code);
				   				$var="answer_hashcode_".$i;
				   				$body=str_replace($var,$hashcode,$body );
				   				$i++;
			   				}
			   			}
		   			
		   			
		   		}
		   		// Send the email
		   		if ( HecMailingMailFrontendHelper::sendMail($from, $sender, null, $subject, $body,true,null,$list,$attach,null,null) !== true )
		   		{
		   			// Error while sending email --> Add Error ...
		   			$error	= HecMailingMailFrontendHelper::$lastError;
		   			$nberrors+=count($list);
		   			foreach($list as $r )
		   				$this->updateRecipient($messageId, $r->id, 9, $error);
		   		}
		   		else
		   		{
		   			// email ok ...
		   			$nbsent+=count($list);
		   			foreach($list as $r )
		   				$this->updateRecipient($messageId, $r->id, 1, "");
		   		}
		   		
		   	}
		   	if ($nberrors==0)
	   			return array("errorcount"=>$message->error_count+$nberrors,"excludedcount"=>$message->excluded_count+$nbexcluded, "sentcount"=>$message->sent_count+$nbsent,"totalcount"=>$message->total_count, "tosendcount"=>$message->tosend_count-$nberrors-$nbsent, "partial_errorcount"=>$nberrors, "partial_sentcount"=>$nbsent, "readcount"=>$message->read_count, "message"=>"","status"=>"OK");	
		   	else
		   		return array("errorcount"=>$message->error_count+$nberrors,"excludedcount"=>$message->excluded_count+$nbexcluded, "sentcount"=>$message->sent_count+$nbsent,"totalcount"=>$message->total_count, "tosendcount"=>$message->tosend_count-$nberrors-$nbsent, "partial_errorcount"=>$nberrors, "partial_sentcount"=>$nbsent, "readcount"=>$message->read_count, "message"=>"","status"=>"WARNING");
   		}
   		else
   			return array("errorcount"=>0,"excludedcount"=>0, "sentcount"=>0,"totalcount"=>0, "tosendcount"=>0, "partial_errorcount"=>0, "partial_sentcount"=>0, "readcount"=>0, "message"=>JText::_("COM_HECMAILING_SENDING_NORIGHT"), "status"=>"NORIGHT");
   } 
    
 
} 

?> 

