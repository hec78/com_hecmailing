<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * User view level controller class.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_users
 * @since       1.6
 */
class HecMailingControllerParam extends JController
{
	public function upgradetest()
	{
		
		
	}
	
	public function upgrade()
	{
		
		
	}
	public function cancel($key=NULL)
	{
		$msg = JText::_("COM_HECMAILING_GROUPE_CANCELED");
		$app=JFactory::getApplication();
		$link=  JRoute::_('index.php?option=com_hecmailing&view=groups&layout=default',FALSE);
		$app->redirect ($link, $msg);
	}
	
}
