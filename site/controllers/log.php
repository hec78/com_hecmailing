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
/**
 * @package     HECMailing
 * @subpackage  Main
 */
class HecMailingControllerLog extends HecMailingController
{
	/**
	 * The URL view item variable.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $view_item = 'logdetail';

	/**
	 * The URL view list variable.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $view_list = 'log';
	
	
	/**
	 * Method to deleted sent mail from de list
	 *
	 * @access	public
	 */
	function dellog(&$cid)
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
			
		// Initialize variables
		$db =JFactory::getDBO();
		$cid 	= JRequest::getVar('cid', array(0), 'post', 'array');
	
		JArrayHelper::toInteger($cid);
	
	
		if (count( $cid )>0) // if there is email to delete
		{
			$cids = implode( ',', $cid );	// get email list from cid
			// execute log attchments delete query
			$query = 'DELETE FROM #__hecmailing_log_attachment WHERE log_id_message IN('.$cids.')';
			$db->setQuery( $query );
			if (!$db->query())
			{	// Query error!!!
				echo "<script> alert('".$db->getErrorMsg(true)."'); window.history.go(-1); </script>\n";
			}
			// execute log delete query
			$query = 'DELETE FROM #__hecmailing_log WHERE log_id_message IN('.$cids.')';
			$db->setQuery( $query );
			if (!$db->query())
			{
				echo "<script> alert('".$db->getErrorMsg(true)."'); window.history.go(-1); </script>\n";
			}
			$msg=JText::_('COM_HECMAILING_LOG_DELETED');
		}
		else
		{
			$msg=JText::_('COM_HECMAILING_NO_LOG_DELETED');
		}
		$return = JRoute::_(JURI::current().'?task=log');
		$this->setRedirect( $return, $msg );
	}
	

	
}