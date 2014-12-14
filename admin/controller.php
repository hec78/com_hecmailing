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
				return $canDo->get('core.admin');
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
		$view   = $this->input->get('view', 'groups');
		$layout = $this->input->get('layout', 'default');
		$id     = $this->input->getInt('id');

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
