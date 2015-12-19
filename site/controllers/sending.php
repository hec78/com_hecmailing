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
JLoader::register('HecMailingMailFrontendHelper',JPATH_COMPONENT.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mail.php');
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 */
class HecMailingControllerSending extends HecMailingController
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
		
	
		parent::__construct($config);
			
		
	}
	
	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function &getModel($name = 'Sending', $prefix = 'HecMailingModel', $config = Array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
	
	
	/**
	 * Method to save current mail as template
	 *
	 * @access	public
	 */
	function wssend()
	{
		$result=array("errorcount"=>0, "excludedcount"=>0,"sentcount"=>0,"totalcount"=>0, "tosendcount"=>0, "partial_errorcount"=>0, "partial_sentcount"=>0, "status"=>"NONE", "message"=>"");
		$model=$this->getModel();
		if (HecMailingMailFrontendHelper::checkWebServiceOrigine())
		{
			if (array_key_exists("idMessage", $_POST))
			{
				$idMessage = $_POST["idMessage"];
				if (array_key_exists("count", $_POST))
					$count = $_POST["count"];
				else
					$count=1;
				$result=$model->send($idMessage,$count );
				$status=$result["status"];
				if ($status=="OK")
				{
					if ($result["tosendcount"]==0)
						$result["status"]="TERMINATED";
					else
						$result["status"]="OK";
				}
				
				
			}
			else 
			{
				$result["status"]="ERR";
				$result["message"]="Message ID is missing";
			}
		}
		else
		{
			$result["status"]="ERR";
			$result["message"]="You can't call this service outside the origine website";
		}
		// Get the document object.
		$document =JFactory::getDocument();
		
		// Set the MIME type for JSON output.
		$document->setMimeEncoding('application/json');
		
		// Change the suggested filename.
		JResponse::setHeader('Content-Disposition','attachment;filename="sendreport.json"');
		
		// Output the JSON data.
		echo json_encode($result);
		
		exit;
	}
	
	
	/**
	 * Method to save current mail as template
	 *
	 * @access	public
	 */
	function read()
	{
		$model=$this->getModel();
		$params 	= JComponentHelper::getParams( 'com_hecmailing' );
		$idMessage = JRequest::getInt("idmessage",0);
		$idRecipient =JRequest::getInt("idrecipient",0);
		$model->updateRecipient($idMessage,$idRecipient,2 );
		header('P3P: policyref="kwptg.kantarworldpanel.fr/w3c/p3p.xml",CP="NOI ADM DEV PSAi COM NAV OUR OTRo STP IND DEM"');
		header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Some time in the past
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header('Content-Disposition','attachment;filename="hecmailing.png"');
		$tagimage   =JPATH_BASE.DIRECTORY_SEPARATOR.$params->get('readtag_image','components/com_hecmailing/images/pix.png');
		$ext = JFile::getExt($tagimage);
		switch (strtolower($ext))
		{
			case 'png':
				header("Content-Type: image/png" );
				$image= imagecreatefrompng($tagimage);
				if (!imagepng($image)) echo "Image not found";
				break;
			case 'jpg':
			case 'jpeg':
				header("Content-Type: image/jpg" );
				$image= imagecreatefromjpeg($tagimage);
				if (!imagejpeg($image)) echo "Image not found";
				break;
			case 'gif':
				header("Content-Type: image/gif" );
				$image= imagecreatefromgif($tagimage);
				if (!imagegif($image)) echo "Image not found";
				break;
			default:
				header("Content-Type: image/png" );
				$image= imagecreatefrompng(JPATH_COMPONENT.DIRECTORY_SEPARATOR."components/com_hecmailing/images/pix.png");
				if (!imagepng($image)) echo "Image not found";
				break;
		}
		exit;
	}
}

?>