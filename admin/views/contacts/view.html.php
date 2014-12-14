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
		$canDo = JHelperContent::getActions('com_users');

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
