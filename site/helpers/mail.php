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
defined('_JEXEC') or die;

class HecMailingMailFrontendHelper {
    
	public static $lastError="";
	
	public static function canAcces(){
		$user=JFactory::getUser();
		$db = JFactory::getDbo();
		$app = JFactory::getApplication();
		$query = $db->getQuery(true);
		$query->select("");
		return true;
		
	}
	
	
	
	
	public static function startsWith($haystack, $needle)
	{
		$length = strlen($needle);
		return (substr($haystack, 0, $length) === $needle);
	}
	
	public static function endsWith($haystack, $needle)
	{
		$length = strlen($needle);
		if ($length == 0) {
			return true;
		}
	
		return (substr($haystack, -$length) === $needle);
	}
	
	public static function extractEmbeddedImagesFromHTML($html)
	{
		$types=array("jpg"=>"image/jpeg", "php"=>"", ""=>"image/jpeg");
		$image_list=array();
		$doc=new DOMDocument();
		//$doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
		$doc->loadHTML($html);
		$xml=simplexml_import_dom($doc); // just to make xpath more simple
		$images=$xml->xpath('//img');
		$num=1;
		foreach ($images as $img) {
			$cidname='image_'.$num;
			$src=$img["src"];
			if (isset($src)) $src=$src->__toString();
			if (!HecMailingMailFrontendHelper::startsWith($src,"http://") && !HecMailingMailFrontendHelper::startsWith($src,"https://"))
			{
				//$file = JPATH_BASE.DIRECTORY_SEPARATOR.$src;
				$file = $src;
				$path_parts = pathinfo($file);
				$filename=$path_parts['basename'];
				if (isset($path_parts['extension']))
					$ext = $path_parts['extension'];
				else 
				{
					$extx = explode(".", $filename);
					$c = count($extx);
					if ($c>1)
						$ext = $extx[$c-1];
					else 
						$ext="";
				}
				if (isset($types[$ext]))
					$mime = $types[$ext];
				else 
					$mime = "image/".$ext;
				if ($mime!="")
				{
					$image_list[] = array ('cid'=>$cidname,'file'=>$file, 'filename'=>$filename, 'mime'=>$mime,'type'=>2);
					$img['src']="cid:".$cidname;
				}
			}
		}
		$obj=new stdClass();
		$obj->html = $doc->saveHTML();
		$obj->images = $image_list;
		return array("html"=>$doc->saveHTML(), "files"=>$image_list);
	}
	
	public static function AddSitePrefixForImagesFromHTML($html)
	{
		$doc=new DOMDocument();
		//$doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
		$doc->loadHTML($html);
		$xml=simplexml_import_dom($doc); // just to make xpath more simple
		$images=$xml->xpath('//img');
		$num=1;
		foreach ($images as $img) {
			$src=$img["src"];
			if (isset($src)) $src=$src->__toString();
			
			if (!HecMailingMailFrontendHelper::startsWith($src,"http://") && !HecMailingMailFrontendHelper::startsWith($src,"https://"))
			{
				$img['src']=JURI::base().$src;
			}
		}
		return $doc->saveHTML();
	}	
	
	public static function AddSitePrefixForLinksFromHTML($html)
	{
		$doc=new DOMDocument();
		$doc->loadHTML($html);
		$xml=simplexml_import_dom($doc); // just to make xpath more simple
		$links=$xml->xpath('//a');
		$num=1;
		foreach ($links as $link) {
			$href=$link["href"];
			if (isset($href)) $href=$href->__toString();
			
			if (!HecMailingMailFrontendHelper::startsWith($href,"http://") && !HecMailingMailFrontendHelper::startsWith($href, "https://"))
			{
				$link['href']=JURI::base().$href;
			}
		}
		return $doc->saveHTML();
	}
	
	public static function sendMail($from, $fromname, $recipient, $subject, $body, $html=true, $cc=null, $bcc=null, $attachment=null, $replyto=null, $replytoname=null )
	{
		// Get a JMail instance
		HecMailingMailFrontendHelper::$lastError="";
		$mail =JFactory::getMailer();
		$mail->setSender(array($from, $fromname));
		$mail->setSubject($subject);
		$mail->setBody($body);
	
		// Are we sending the email as HTML?
		$mail->IsHTML($html);
		if (isset($recipient))
		{
			if (is_array($recipient)){	foreach($recipient as $adr)	{$mail->AddAddress($adr->email,$adr->name);	}}
			else {	$mail->AddAddress($recipient->email,$recipient->name);	}
		}
	
		if (isset($cc))
		{
			if (is_array($cc))	{foreach($cc as $adr){$mail->addBCC($adr->email,$adr->name);}	}
			else{$mail->addBCC($cc->email,$cc->name);	}
		}
		if (isset($bcc))
		{
			if (is_array($bcc))	{foreach($bcc as $adr)	{$mail->addBCC($adr->email,$adr->name);}}
			else {$mail->addBCC($bcc->email,$bcc->name);}
		}
			
		// Add attachments
		if ($attachment!=null)
		foreach($attachment as $att) 
		{	
			if ($att->cid!='') $mail->AddEmbeddedImage(JPATH_BASE.DIRECTORY_SEPARATOR.$att->file, $att->cid, $name = $att->filename);	
			else $mail->addAttachment(JPATH_BASE.DIRECTORY_SEPARATOR.$att->file,$name = $att->filename);
		}
			
		// Take care of reply email addresses
		if( is_array( $replyto ) )
		{
			$numReplyTo = count($replyto);
			for ( $i=0; $i < $numReplyTo; $i++)	{	$mail->addReplyTo( array($replyto[$i], $replytoname[$i]) );	}
		}
		elseif( isset( $replyto ) )	{$mail->addReplyTo( array( $replyto, $replytoname ) );	}
		// Send email and return Send function return code
		if ($mail->Send())
		{
			return true;
		}
		else 
		{
			HecMailingMailFrontendHelper::$lastError=$mail->ErrorInfo;
			return false;
		}
	
	}
	public static function checkWebServiceOrigine()
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
	 * Generate a random string, using a cryptographically secure
	 * pseudorandom number generator (random_int)
	 *
	 * For PHP 7, random_int is a PHP core function
	 * For PHP 5.x, depends on https://github.com/paragonie/random_compat
	 *
	 * @param int $length      How many characters do we want?
	 * @param string $keyspace A string of all possible characters
	 *                         to select from
	 * @return string
	 */
	function random_str($length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
	{
		$str = '';
		$max = mb_strlen($keyspace, '8bit') - 1;
		for ($i = 0; $i < $length; ++$i) {
			$str .= $keyspace[random_int(0, $max)];
		}
		return $str;
	}
	
	public static function extractQuestionsFromHTML($html)
	{
		// Exemple de contenu :
		/* 
		 
		Bonjour,
		
		
		
		La réunion de préparation aura lieu le Jeudi 5 Janv. 2017 :
		
		{answer question="ReunionMeetingIndoor2017" code="Oui" }Je participerai{/answer}
		
		{answer question="ReunionMeetingIndoor2017" code="Non" }Non je participerai pas{/answer}
		
		
		
		Le meeting indoor aura lieu le 5 Fev. 2017 :
		
		{answer question="MeetingIndoor2017" code="Oui" }Je participerai{/answer} ou {answer question="MeetingIndoor2017" code="Non" }Non je participerai pas{/answer}
		*/
		$answer_list=array();
		$question_list=array();
		$baselink=JURI::base()."index.php?option=com_hecmailing&task=message.answer";
		preg_match_all("({answer.+}(.+){\/answer.*})Us",$html, $answers);
		$answer_index=0;
		if ($answers != null)
		{
			
			foreach ($answers[0] as $answer )
			{
				
				preg_match('(question.*=.*"(.+)")U',$answer, $question);
				if ($question!=null) $question=$question[1]; else $question='';
				preg_match('(code.*=.*"(.+)")U',$answer, $code);
				if ($code!=null) $code=$code[1]; else $code='';
				$answerlib=$answers[1][$answer_index];
				
				$link = '<a href="'.$baselink.'&message_id={message_id}&recipient_id={recipient_id}&question='.$question.'&answer='.$code.'&hashcode={answer_hashcode_'.$answer_index.'}" target="_blank" >'.$answerlib.'</a>';
				$html=str_replace($answer,$link,$html);
				$aobj=new stdClass();
				$aobj->question_code=$question;
				$aobj->answer_code=$code;
				$aobj->answer_title=$answerlib;
				$aobj->answer_index=$answer_index;
				$answer_list[]= $aobj;
				
				if (!isset($question_list[$question]))
				{
					$qobj=new stdClass();
					$qobj->question_code=$question;
					$qobj->question_title="";
					$qobj->answers=array();
					$qobj->answers[]=$code;
					$qobj->answer_index=$answer_index; // 1st answer link index 
					$question_list[$question]= $qobj;
				}
				else 
				{
					$question_list[$question]->answers[]=$answer;
				}
				
				
				$answer_index++;
			}
		}
		
		$obj=new stdClass();
		$obj->html = $html;
		$obj->answers = $answer_list;
		$obj->questions = $question_list;
		return $obj;
	}
}
?>
