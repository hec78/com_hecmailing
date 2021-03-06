<?php
/**
* @version   3.4.0
* @package   HEC Mailing for Joomla
* @copyright Copyright (C) 1999-2017 Hecsoft All rights reserved.
* @author    Herve CYR
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
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
class HecMailingHelper
{
	/**
	 * @var    JObject  A cache for the available actions.
	 * @since  1.6
	 */
	protected static $actions;

	/**
	 * Configure the Linkbar.
	 *
	 * @param   string  $vName  The name of the active view.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public static function addSubmenu($vName)
	{
		
		// Groups and Levels are restricted to core.admin
		$canDo = JHelperContent::getActions('com_hecmailing');

		if ($canDo->get('core.manage'))
		{
			JHtmlSidebar::addEntry(
				JText::_('COM_HECMAILING_SUBMENU_GROUPS'),
				'index.php?option=com_hecmailing&view=groups',
				$vName == 'groups'
			);
			JHtmlSidebar::addEntry(
				JText::_('COM_HECMAILING_SUBMENU_CONTACT'),
				'index.php?option=com_hecmailing&view=contacts',
				$vName == 'contacts'
			);
			JHtmlSidebar::addEntry(
			JText::_('COM_HECMAILING_SUBMENU_TEMPLATE'),
			'index.php?option=com_hecmailing&view=templates',
			$vName == 'templates'
					);
			JHtmlSidebar::addEntry(
				JText::_('COM_HECMAILING_SUBMENU_PANEL'),
				'index.php?option=com_hecmailing&view=param',
				$vName == 'param'
			);

			
		}
	}
	public static function  getComponentVersion()
	{
		if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
		jimport('joomla.application.helper');
		$path=JPATH_COMPONENT_ADMINISTRATOR . DS . 'manifest.xml';
		if (DS=="\\") $path = str_replace("/", DS, $path);
		$file=false;
		if(version_compare(JVERSION,'3.1.0','<')){
			$file = JApplicationHelper::parseXMLInstallFile($path);
			if($file) {
				return $file['version'];
			}
		} else {
			$content = file_get_contents($path);
			if ($doc = new SimpleXMLElement($content))
				$ver= (string)($doc->version);
				return $ver;
		}
		
		static $warned = false;
		if(!$warned)
		{
			$app =JFactory::getApplication();
			$app->enqueueMessage('Fallback component version used!');
			$warned = true;
		}
		return '1.5.1'; // fallback call
		
	}

	public static function   getRemoteFile($url)
	{
	   // get the host name and url path
	   $parsedUrl = parse_url($url);
	   $host = $parsedUrl['host'];
	   if (isset($parsedUrl['path'])) {
		  $path = $parsedUrl['path'];
	   } else {
		  // the url is pointing to the host like http://www.mysite.com
		  $path = '/';
	   }

	   if (isset($parsedUrl['query'])) {
		  $path .= '?' . $parsedUrl['query'];
	   } 

	   if (isset($parsedUrl['port'])) {
		  $port = $parsedUrl['port'];
	   } else {
		  // most sites use port 80
		  $port = '80';
	   }

	   $timeout = 10;
	   $response = ''; 
	   // connect to the remote server 
	   $fp = @fsockopen($host, '80', $errno, $errstr, $timeout ); 

	   if( !$fp ) { 
		  echo "Cannot retrieve $url";
	   } else {
		  // send the necessary headers to get the file 
		  fputs($fp, "GET $path HTTP/1.0\r\n" .
					 "Host: $host\r\n" .
					 "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.0.3) Gecko/20060426 Firefox/1.5.0.3\r\n" .
					 "Accept: */*\r\n" .
					 "Accept-Language: en-us,en;q=0.5\r\n" .
					 "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7\r\n" .
					 "Keep-Alive: 300\r\n" .
					 "Connection: keep-alive\r\n" .
					 "Referer: http://$host\r\n\r\n");

		  // retrieve the response from the remote server 
		  while ( $line = fread( $fp, 4096 ) ) { 
			 $response .= $line;
		  } 

		  fclose( $fp );

		  // strip the headers
		  $pos      = strpos($response, "\r\n\r\n");
		  $response = substr($response, $pos + 4);
	   }

	   // return the file content 
	   return $response;
	}



	public static function  getLatestComponentVersion($url)
	{
		$content=HecMailingHelper::getRemoteFile($url);
		if ($content)
	  	{
	  	  	if(version_compare(JVERSION,'3.1.0','<'))
	  	  	{
		  		jimport('joomla.utilities.simplexml');
				$doc = new JSimpleXML();
			    $ver = '';
			  	if( $doc->loadString($content))
				{
			    	foreach( $doc->document->children() as $child ) {
						if ($child->name()=='version')
						{
							  $ver = $child->data();
							  break;
						}
				  	}
				  	if ($ver=='')
					{
						$app =JFactory::getApplication();
						$app->enqueueMessage("No version found");
						return "";
				  	}
				  	else
						return $ver;
				  	}
				  	else
				  	{
				  		$app =& JFactory::getApplication();
						$app->enqueueMessage("Can't load xml content");
						return "";
			  		}
	  	  } 
	  	  else 
	  	  {
	  	  	if ($doc = new SimpleXMLElement($content))
	  	  	{
	  	  		$ver= $doc->version;
	  	  		if (!$ver)
	  	  		{
	  	  			$app =JFactory::getApplication();
	  	  			$app->enqueueMessage("No version found");
	  	  			return "";
	  	  		}
	  	  		else
	  	  			return (string)($ver);
	  	  	}
	  	  }					  
				  
		}
		else
		{
			 $app =& JFactory::getApplication();
				 $app->enqueueMessage("Can't get xml content from ".$url);
				 return "";
		  
	  }
		
		return ''; // fallback call
		
	}

	public static function  download($src,$dest)
	{
		$content = file($src);
		$fd = fopen($dest,"w");
		foreach ($content as $line)
		{
		  fwrite($fd,$line);
		  }
		fclose($fd);
	}
	
	/**
	 * Method to send a mail (override of joomla sendMail with attachments and embedded images)
	 *
	 * @access	public
	 */
	public static function sendMail($from, $fromname, $recipient, $subject, $body, $mode=0, $cc=null, $bcc=null, $attachment=null, $replyto=null, $replytoname=null, $inline=null )
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
		if (isset($inline))
		{
			foreach ($inline as $att)
			{
				$mail->AddEmbeddedImage($att[0], $att[1], $name = $att[2]);
			}
		}
		if (isset($attachment))
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
	
	
}
?>