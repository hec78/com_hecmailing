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

defined('_JEXEC') or die;

/**
 * User groups list controller class.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_users
 * @since       1.6
 */
class HecMailingControllerGroups extends JControllerAdmin
{
	/**
	 * @var     string  The prefix to use with controller messages.
	 * @since   1.6
	 */
	protected $text_prefix = 'COM_HECMAILING_GROUPS';

	/**
	 * Proxy for getModel.
	 *
	 * @since   1.6
	 */
	public function getModel($name = 'Groups', $prefix = 'HecMailingModel', $config = array())
	{
		return parent::getModel($name, $prefix, array('ignore_request' => true));
	}

	/**
	 * Removes an item.
	 *
	 * Overrides JControllerAdmin::delete to check the core.admin permission.
	 *
	 * @since   1.6
	 */
	public function delete()
	{
		return parent::delete();
	}

	/**
	 * Method to publish a list of records.
	 *
	 * Overrides JControllerAdmin::publish to check the core.admin permission.
	 *
	 * @since   1.6
	 */
	public function publish()
	{
		$app=JFactory::getApplication();
		$jinput = $app->input;
		$task = $jinput->get('task', "", 'STR' );
		$cid = $jinput->get("cid",array(),"array");
		if ($task=="publish") $published=1;
		else $published=0;
		if ( $this->getModel()->publish($published,$cid))
		{
			if ($published)
			{
				if (count($cid)==1)
					$msg = JText::_("COM_HECMAILING_GROUP_PUBLISHED");
				else 
					$msg = JText::_("COM_HECMAILING_GROUPS_PUBLISHED");
			}
			else 
			{
				if (count($cid)==1)
					$msg = JText::_("COM_HECMAILING_GROUP_UNPUBLISHED");
				else
					$msg = JText::_("COM_HECMAILING_GROUPS_UNPUBLISHED");
			}
			$link=  JRoute::_('index.php?option=com_hecmailing&view=groups',FALSE);
			$app->redirect ($link, $msg);
		}
		else
		{
			$msg = JText::sprintf("COM_HECMAILING_GROUP_NOTPUBLISHED", $model->error);
			$app->enqueueMessage($msg,'error');
			$link=  JRoute::_('index.php?option=com_hecmailing&view=groups',FALSE);
			$app->redirect ($link, $msg);
		}
	}

	/**
	 * Changes the order of one or more records.
	 *
	 * Overrides JControllerAdmin::reorder to check the core.admin permission.
	 *
	 * @since   1.6
	 */
	public function reorder()
	{
		return parent::reorder();
	}

	/**
	 * Method to save the submitted ordering values for records.
	 *
	 * Overrides JControllerAdmin::saveorder to check the core.admin permission.
	 *
	 * @since   1.6
	 */
	public function saveorder()
	{
		if (!JFactory::getUser()->authorise('core.admin', $this->option))
		{
			JError::raiseError(500, JText::_('JERROR_ALERTNOAUTHOR'));
			jexit();
		}

		return parent::saveorder();
	}

	/**
	 * Check in of one or more records.
	 *
	 * Overrides JControllerAdmin::checkin to check the core.admin permission.
	 *
	 * @since   1.6
	 */
	public function checkin()
	{
		if (!JFactory::getUser()->authorise('core.admin', $this->option))
		{
			JError::raiseError(500, JText::_('JERROR_ALERTNOAUTHOR'));
			jexit();
		}

		return parent::checkin();
	}
	
	public function getGroupContent()
	{
		$app=JFactory::getApplication();
		
		$groupType = $app->input->get('grouptype', 0, 'get', 'int');
		$currentGroup = $app->input->get('groupid', 0, 'get', 'int');
		$type = JRequest::getCmd('group-type');
		$db =JFactory::getDBO();
		switch($groupType)
		{
			case 1:
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
			$data = JText::_('MSG_ERROR_SAVE_CONTACT').':'.$query.'/'.$db->getErrorMsg(true);
		}
		else
			$data = $db->loadRowList();
		 
		// Get the document object.
		$document =JFactory::getDocument();
	 
		// Set the MIME type for JSON output.
		$document->setMimeEncoding('application/json');
	 
		// Change the suggested filename.
		JResponse::setHeader('Content-Disposition','attachment;filename="group'.$groupType.'.json"');
	 
		// Output the JSON data.
		echo json_encode($data);
		die;
	}
	
}
