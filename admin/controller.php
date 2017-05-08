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
 * Users master display controller.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_users
 * @since       1.6
 */
class HecMailingController extends JControllerLegacy
{
	/**
	 * Checks whether a user can see this view.
	 *
	 * @param   string   $view  The view name.
	 *
	 * @return  boolean
	 * @since   1.6
	 */
	protected function canView($view)
	{
		$canDo = JHelperContent::getActions('com_hecmailing');
		
		switch ($view)
		{
			// Special permissions.
			case 'groups':
			case 'group':
			case 'contacts':
			case 'contact':
				return $canDo->get('core.manage');
				break;

			// Default permissions.
			default:
				return true;
		}
	}

	/**
	 * Method to display a view.
	 *
	 * @param   boolean      If true, the view output will be cached
	 * @param   array        An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JController	 This object to support chaining.
	 * @since   1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$view   = $this->input->get('view', '');
		$layout = $this->input->get('layout', 'default');
		$id     = $this->input->getInt('id');
		$task   = $this->input->get('task','');
		
		if ($view=='' && $task!='') $view = $task;
		else if ($view=='') $view = "groups";
		
		if (!$this->canView($view))
		{
			JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));

			return;
		}

		// Check for edit form.
		/*if ($view == 'group' && $layout == 'edit' && !$this->checkEditId('core.edit', $id))
		{
			// Somehow the person just went to the form - we don't allow that.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_hecmailing&view=groups', false));

			return false;
		}*/
		
		
		$this->input->set('view', $view);
		$this->input->set('layout', $layout);
		return parent::display();
	}
}
