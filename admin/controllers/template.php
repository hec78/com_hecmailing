<?php
/**
 * @package     HecMailing
 * @subpackage  com_hecmailing
 *
 * @copyright   Copyright (C) 2005 - 2014 HECSoft All rights reserved.
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
class HecMailingControllerTemplate extends JControllerForm
{
	/**
	 * @var	    string  The prefix to use with controller messages.
	 * @since   1.6
	 */
	protected $text_prefix = 'COM_HECMAILING_TEMPLATE';

	/**
	 * Method to check if you can save a new or existing record.
	 *
	 * Overrides JControllerForm::allowSave to check the core.admin permission.
	 *
	 * @param   array   An array of input data.
	 * @param   string  The name of the key for the primary key.
	 *
	 * @return  boolean
	 * @since   1.6
	 */
	protected function allowSave($data, $key = 'id')
	{
		return (JFactory::getUser()->authorise('core.admin', $this->option) && parent::allowSave($data, $key));
	}

	/**
	 * Overrides JControllerForm::allowEdit
	 *
	 * Checks that non-Super Admins are not editing Super Admins.
	 *
	 * @param   array   An array of input data.
	 * @param   string  The name of the key for the primary key.
	 *
	 * @return  boolean
	 * @since   1.6
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		// Check if this group is a Super Admin
		if (JAccess::checkGroup($data[$key], 'core.admin'))
		{
			// If I'm not a Super Admin, then disallow the edit.
			if (!JFactory::getUser()->authorise('core.admin'))
			{
				return false;
			}
		}

		return parent::allowEdit($data, $key);
	}

	public function save($key = NULL, $urlVar = NULL)
	{
		$this->_save("save");
	}
	public function apply($key = NULL, $urlVar = NULL)
	{
		$this->_save("apply");
	}
	public function save2new($key = NULL, $urlVar = NULL)
	{
		$this->_save("save2new");
	}
	public function save2copy($key = NULL, $urlVar = NULL)
	{
		$this->_save("save2copy");
	}
	
	protected function _save($task)
	{
		$model = $this->getModel();
		$app=JFactory::getApplication();
		$input = $app->input;
		$data=$_POST;
		
		if ($model->save($data))
		{
			$msg = JText::_("COM_HECMAILING_MSG_TEMPLATE_SAVED");
			if ($task=="apply")
			{
				$app->enqueueMessage($msg);
				$view = $this->getView( 'contact', 'html' );
				$view->setLayout('edit');
				$view->setModel($model);
				$view->display();
			}
			else if ($task=="save2new") 
			{
				$link=  JRoute::_('index.php?option=com_hecmailing&view=template&layout=edit&id=0',FALSE);
				$app->redirect ($link, $msg);
			}
			else if ($task=="save2copy")
			{
				$link=  JRoute::_('index.php?option=com_hecmailing&view=template&layout=edit&id=-'.$data['id'],FALSE);
				$app->redirect ($link, $msg);
				
			}
			else 
			{
				$link=  JRoute::_('index.php?option=com_hecmailing&view=templates',FALSE);
				$app->redirect ($link, $msg);
			}
		}
		else
		{
			$msg = JText::sprintf("COM_HECMAILING_MSG_ERROR_SAVE_TEMPLATE", $model->error);
			$app->enqueueMessage($msg,'error');
			$view = $this->getView( 'template', 'html' );
			$view->setLayout('edit');
			$view->display();
		}
		
	}
	
	public function edit($key = NULL, $urlVar = NULL)
	{
		$msg="";
		$app=JFactory::getApplication();
		$id=$app->input->get("cid",array(),"array")[0];
		$link=  JRoute::_('index.php?option=com_hecmailing&view=template&layout=edit&id='.$id,FALSE);
		$app->redirect ($link, $msg);
	}
	
	public function cancel($key=NULL)
	{
		$msg = JText::_("COM_HECMAILING_GROUPE_CANCELED");
		$app=JFactory::getApplication();
		$link=  JRoute::_('index.php?option=com_hecmailing&view=templates&layout=default',FALSE);
		$app->redirect ($link, $msg);
	}
	
	public function delete()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		$model = $this->getModel();
		$app=JFactory::getApplication();
		$input = $app->input;
		$cid = $input->get("cid",array(),"array");
		if ($model->delete($cid))
		{
			$msg = JText::_("COM_HECMAILING_TEMPLATE_DELETED");
			$link=  JRoute::_('index.php?option=com_hecmailing&view=templates',FALSE);
			$app->redirect ($link, $msg);
		}
		else
		{
			$msg = JText::sprintf("COM_HECMAILING_TEMPLATE_NOTDELETED", $model->error);
			$app->enqueueMessage($msg,'error');
			$view = $this->getView( 'templates', 'html' );
			$view->setLayout('default');
			$view->display();
		}

	}
}
