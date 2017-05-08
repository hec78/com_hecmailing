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
 * View class for a list of user groups.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_users
 * @since       1.6
 */
class HecMailingViewContacts extends JViewLegacy
{
	/**
	 * The item data.
	 *
	 * @var   object
	 * @since 1.6
	 */
	protected $items;

	/**
	 * The pagination object.
	 *
	 * @var   JPagination
	 * @since 1.6
	 */
	protected $pagination;

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
		$this->items      = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state      = $this->get('State');
		$app = JFactory::getApplication();
		//$filter_catid = $app->input->get("filter_catid");
		HECMailingHelper::addSubmenu('contacts');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$javascript = 'onchange="Joomla.submitform();"';
		//$this->catid = JHTML::_('list.category',  'filter_catid', 'com_contact_details', intval( $filter_catid ), $javascript );
		$this->catid="";
		// state filter
		$this->stateList	= JHTML::_('grid.state',  $this->state );
		$this->table = JTable::getInstance('contact', 'Table');
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
		$canDo = JHelperContent::getActions('com_hecmailing');

		JToolbarHelper::title(JText::_('COM_HECMAILING_VIEW_CONTACTS_TITLE'), 'HECMailing contacts');

		if ($canDo->get('core.create'))
		{
			JToolbarHelper::addNew('contact.add');
		}

		if ($canDo->get('core.edit'))
		{
			JToolbarHelper::editList('contact.edit');
			JToolbarHelper::divider();
		}

		if ($canDo->get('core.delete'))
		{
			JToolbarHelper::deleteList('', 'contact.delete');
			JToolbarHelper::divider();
		}

		if ($canDo->get('core.admin'))
		{
			JToolbarHelper::preferences('com_hecmailings');
			JToolbarHelper::divider();
		}

		JToolbarHelper::help('JHELP_HECMAILING_CONTACTS');
	}

	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 *
	 * @since   3.0
	 */
	protected function getSortFields()
	{
		return array(
				'a.grp_id_groupe' => JText::_('COM_HECMAILING_HEADING_CONTACT_TITLE')
		);
	}
}
