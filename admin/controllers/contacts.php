<?php
/**
 * @package     HecMailing
 * @subpackage  com_hecmailing
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * User groups list controller class.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_users
 * @since       1.6
 */
class HecMailingControllerContacts extends JControllerAdmin
{
	/**
	 * @var     string  The prefix to use with controller messages.
	 * @since   1.6
	 */
	protected $text_prefix = 'COM_HECMAILING_CONTACTS';

	/**
	 * Proxy for getModel.
	 *
	 * @since   1.6
	 */
	public function getModel($name = 'Contacts', $prefix = 'HecMailingModel', $config = array())
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
					$msg = JText::_("COM_HECMAILING_CONTACT_PUBLISHED");
				else 
					$msg = JText::_("COM_HECMAILING_CONTACTS_PUBLISHED");
			}
			else 
			{
				if (count($cid)==1)
					$msg = JText::_("COM_HECMAILING_CONTACT_UNPUBLISHED");
				else
					$msg = JText::_("COM_HECMAILING_CONTACTS_UNPUBLISHED");
			}
			$link=  JRoute::_('index.php?option=com_hecmailing&view=contacts',FALSE);
			$app->redirect ($link, $msg);
		}
		else
		{
			$msg = JText::sprintf("COM_HECMAILING_CONTACT_NOTPUBLISHED", $model->error);
			$app->enqueueMessage($msg,'error');
			$link=  JRoute::_('index.php?option=com_hecmailing&view=contacts',FALSE);
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
}
