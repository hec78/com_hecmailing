<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_hecmailing
 *
 * @copyright   Copyright (C) 2005 - 2014 HECSoft, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * View to edit a user group.
 *
 * @package     com_hecmailing
 * @subpackage  -
 * @since       1.6
 */
class HecMailingViewTemplate extends JViewLegacy
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
				$msg = JText::_("COM_HECMAILING_TEMPLATE_NOID");
				$link=  JRoute::_('index.php?option=com_hecmailing&view=templates',FALSE);
				JFactory::getApplication()->redirect ($link, $msg);
				die;
			}
		}	
		$this->id=$id;
		$this->item  = $model->getItem($id);
		if ($this->item==null)
		{
			$msg = JText::_("COM_HECMAILING_TEMPLATE_NOID");
			$link=  JRoute::_('index.php?option=com_hecmailing&view=templates',FALSE);
			JFactory::getApplication()->redirect ($link, $msg);
			die;
		}
		$grp=$model->getGroups($id);
		$heclist = array();
		if ($grp) 
		foreach($grp as $g)
		{
			$heclist[] = JHTML::_('select.option', $g[0], $g[1], 'id', 'name');
		}
	  
		$this->hecgroups = JHTML::_('select.genericlist',  $heclist, 'grp_id_groupe', 'class="inputbox" size="1"', 'id', 'name', $this->item->grp_id_groupe);
	
  
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
		$this->editor= JFactory::getEditor();
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

		$isNew = ($this->item->msg_id_message == 0);
		$canDo = JHelperContent::getActions('com_hecmailing');

		JToolbarHelper::title(JText::_($isNew ? 'COM_HECMAILING_VIEW_NEW_TEMPLATE_TITLE' : 'COM_HECMAILING_VIEW_EDIT_TEMPLATE_TITLE'), '');

		if ($canDo->get('core.edit') || $canDo->get('core.create'))
		{
			JToolbarHelper::apply('template.apply');
			JToolbarHelper::save('template.save');
		}

		if ($canDo->get('core.create'))
		{
			JToolbarHelper::save2new('template.save2new');
		}

		// If an existing item, can save to a copy.
		if (!$isNew && $canDo->get('core.create'))
		{
			JToolbarHelper::save2copy('template.save2copy');
		}

		if (empty($this->item->id))
		{
			JToolbarHelper::cancel('template.cancel');
		}
		else
		{
			JToolbarHelper::cancel('template.cancel', 'JTOOLBAR_CLOSE');
		}

		JToolbarHelper::divider();
		JToolbarHelper::help('JHELP_HECMAILING_TEMPLATE_EDIT');
	}
}
