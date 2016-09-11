<?php

/**
 * @version     1.0.1
 * @package     com_hecmailing
 * @copyright   Copyright (C) 2015. Kantar Worldpanel Tous droits réservés.
 * @license     GNU General Public License version 2 ou version ultérieure ; Voir LICENSE.txt
 * @author      Hervé CYR <herve.cyr@kantarworldpanel.com> - http://www.kantarworldpanel.com/fr
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
}
?>
