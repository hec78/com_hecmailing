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
 * View to edit a user group.
 *
 * @package     com_hecmailing
 * @subpackage  -
 * @since       1.6
 */
class HecMailingViewParam extends JViewLegacy
{
	protected $form;

	/**
	 * The item data.
	 *
	 * @var   object
	 * @since 1.6
	 */
	protected $item;

	/**
	 * The model state.
	 *
	 * @var   JObject
	 * @since 1.6
	 */
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		//$this->state = $this->get('State');
		HECMailingHelper::addSubmenu('param');
		$this->form  = $this->get('Form');
		$model = $this->getModel();
		
		$app=JFactory::getApplication();
		
		$this->baseurl = 'http://joomla.hecsoft.net/media/updater/';
		
		$this->addToolbar();
		$this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		//JFactory::getApplication()->input->set('hidemainmenu', true);

		$canDo = JHelperContent::getActions('com_hecmailing');

		JToolbarHelper::title(JText::_('COM_HECMAILING_PARAMETERS_TITLE'));

		if ($canDo->get('core.admin'))
		{
			JToolbarHelper::preferences('com_hecmailing');
			JToolbarHelper::divider();
		}
		
		JToolbarHelper::divider();
		JToolbarHelper::help('JHELP_HECMAILING_CONTACT_EDIT');
	}
}
