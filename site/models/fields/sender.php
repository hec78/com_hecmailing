<?php

/**
 * @version     1.0.0
 * @package     com_hecmailing
 * @copyright   Copyright (C) 2014. Tous droits réservés.
 * @license     GNU General Public License version 2 ou version ultérieure ; Voir LICENSE.txt
 * @author      Hervé CYR <herve.cyr@kantarworldpanel.com> - 
 */
defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');

/**
 * Supports a value from an external table
 */
class JFormFieldSender extends JFormField
{

	/**
	 * The form field type.
	 *
	 * @var        string
	 * @since    1.6
	 */
	protected $type = 'sender';
	
	/**
	 * Method to get the field input markup.
	 *
	 * @return    string    The field input markup.
	 * @since    1.6
	 */
	protected function getInput()
	{
		// Modif Joomla 1.6+
		$app = JFactory::getApplication();
		$user =JFactory::getUser();
		$MailFrom 	= $app->getCfg('mailfrom');
		$FromName 	= $app->getCfg('fromname');
		
		$options = array();
		$options[] = JHTML::_('select.option', $user->email.';'.$user->name, $user->name, 'email', 'name');
		$options[] = JHTML::_('select.option', $MailFrom.';'.$FromName, JText::_('COM_HECMAILING_DEFAULT').'('.$FromName.')', 'email', 'name');
		
		$input_options = 'class="' . $this->getAttribute('class') . '"';
		// Initialize variables.
		$html = JHtml::_('select.genericlist', $options, $this->name, $input_options, 'email', 'name', $this->value, $this->id);
				

		

		return $html;
	}


}
