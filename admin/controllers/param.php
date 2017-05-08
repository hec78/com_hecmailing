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
