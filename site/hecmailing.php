<?php
/**
* @version 1.8.0
* @package hecMailing for Joomla
* @copyright Copyright (C) 2005-2013 Hecsoft All rights reserved.
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
*/

/* No direct access */

defined ('_JEXEC') or die ('restricted access');
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
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

$task = JRequest::getVar('task','');

// Browse server webservice ...
if ($task=="getdir")
{
   
	if (checkWebServiceOrigine())
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
		$params = &JComponentHelper::getParams( 'com_hecmailing' );
		$root = realpath(JPATH_ROOT).DS.$params->get('browse_path','images/stories');
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
	$document =& JFactory::getDocument();
	
	// Set the MIME type for JSON output.
	$document->setMimeEncoding('application/json');
	
	// Change the suggested filename.
	JResponse::setHeader('Content-Disposition','attachment;filename="getdirlist.json"');
	
	// Output the JSON data.
	echo json_encode($data);
		
	exit;
}
else if ($task=="getgroupcontent")
{
	if (checkWebServiceOrigine())
	{
		$currentGroup = JRequest::getVar('groupid', 0, 'get', 'int');
		$groupType = JRequest::getVar('grouptype', 0, 'get', 'int');
		$db =& JFactory::getDBO();
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
// END Browse server webservice ...

require_once (JPATH_COMPONENT.DS.'controller.php');
$controller = new hecMailingController();
$controller->execute(JRequest::getVar('task'));
$controller->redirect(); 

?> 