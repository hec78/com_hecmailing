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
 * View to edit a user group.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_users
 * @since       1.6
 */
class HecMailingViewGroup extends JViewLegacy
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
		
		$this->form  = $this->get('Form');
		$model = $this->getModel();
		
		$app=JFactory::getApplication();
		$id 	= $app->input->getInt('id',0);
		if (!isset($id))
		{
			$cid 	= $app->input->get('cid',array(),'array');
			if (count($cid)>=1) $id=$cid[0];
			else
			{
				$msg = JText::_("COM_HECMAILING_GROUP_NOID");
				$link=  JRoute::_('index.php?option=com_hecmailing&view=groups',FALSE);
				JFactory::getApplication()->redirect ($link, $msg);
				die;
			}
		}	
		$this->id=$id;
		$this->item  = $model->getItem($id);
		$users = $model->getUsers();
		$ulist = array();
		if ($users)
		foreach($users as $u)
		{
		  $ulist[] = JHTML::_('select.option', $u[0], $u[2], 'id', 'name');
		}
		$this->users = JHTML::_('select.genericlist',  $ulist, 'newuser', 'class="inputbox" size="1"', 'id', 'name', 0);
		$this->usersperm = JHTML::_('select.genericlist',  $ulist, 'newuserperm', 'class="inputbox" size="1"', 'id', 'name', 0);
		
		$jgrp = $model->getJoomlaGroups();
		$glist = array();
		if ($jgrp) 
		foreach($jgrp as $g)
		{
			$glist[] = JHTML::_('select.option', $g[0], $g[1], 'id', 'name');
		}
		$grp=$model->getGroups($id);
		$heclist = array();
		if ($grp) 
		foreach($grp as $g)
		{
			$heclist[] = JHTML::_('select.option', $g[0], $g[1], 'id', 'name');
		}
	  
		$this->hecgroups = JHTML::_('select.genericlist',  $glist, 'newgroupe', 'class="inputbox" size="1"', 'id', 'name', 0);
	
  
		$this->groups = JHTML::_('select.genericlist',  $glist, 'newgroupej', 'class="inputbox" size="1"', 'id', 'name', 0);
		$this->groupsperm = JHTML::_('select.genericlist',  $glist, 'newgroupperm', 'class="inputbox" size="1"', 'id', 'name', 0);
		
		$typesgroupselmt=array();
		$typesgroupselmt[] = JHTML::_('select.option', 3, "Joomla", 'id', 'name');
		//$typesgroupselmt[] = JHTML::_('select.option', 5, "HEC Mailing", 'id', 'name');
		$this->typesgroups = JHTML::_('select.genericlist',  $typesgroupselmt, 'typegroupe', 'class="inputbox" size="1" onChange="changeType(webservice,this.options[this.selectedIndex].value, '.$id.');"', 'id', 'name', 3);

		// build the html radio buttons for published
		$this->published 		= JHTML::_('select.booleanlist',  'published', '', $this->item->published );
		
		// get params definitions
		$file 	= JPATH_ADMINISTRATOR .'/components/com_hecmailing/config.xml';
		$paramstxt="";
		$this->params = new JRegistry( $paramstxt, $file, 'component' );
		
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);

		$isNew = ($this->item->grp_id_groupe == 0);
		$canDo = JHelperContent::getActions('com_hecmailing');

		JToolbarHelper::title(JText::_($isNew ? 'COM_HECMAILING_VIEW_NEW_GROUP_TITLE' : 'COM_HECMAILING_VIEW_EDIT_GROUP_TITLE'), '');

		if ($canDo->get('core.edit') || $canDo->get('core.create'))
		{
			JToolbarHelper::apply('group.apply');
			JToolbarHelper::save('group.save');
		}

		if ($canDo->get('core.create'))
		{
			JToolbarHelper::save2new('group.save2new');
		}

		// If an existing item, can save to a copy.
		if (!$isNew && $canDo->get('core.create'))
		{
			JToolbarHelper::save2copy('group.save2copy');
		}

		if (empty($this->item->id))
		{
			JToolbarHelper::cancel('group.cancel');
		}
		else
		{
			JToolbarHelper::cancel('group.cancel', 'JTOOLBAR_CLOSE');
		}

		JToolbarHelper::divider();
		JToolbarHelper::help('JHELP_HECMAILING_GROUPS_EDIT');
	}
}
