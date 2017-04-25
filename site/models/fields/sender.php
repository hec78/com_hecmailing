<?php
/**
* @version 3.4.0
* @package hecMailing for Joomla
* @subpackage : View Form (Sending mail form)
* @module views.form.tmpl.view.html.php
* @copyright Copyright (C) 2008-2011 Hecsoft All rights reserved.
* @license GNU/GPL
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
	 * @since    3.0
	 */
	protected $type = 'sender';
	
	/**
	 * Method to get the field input markup.
	 *
	 * @return    string    The field input markup.
	 * @since    3.0
	 */
	protected function getInput()
	{
		
		$app = JFactory::getApplication();
		$pparams =$app->getParams();
		$user =JFactory::getUser();
		$MailFrom 	= $app->get('mailfrom');
		$FromName 	= $app->get('fromname');
		
		$default_sender =intval($pparams->get('default_sender','0'));
		if ($this->value == 0)
			$value = $default_sender;
		else
			$value=$this->value;
		$options = array();
		$options[] = JHTML::_('select.option', $user->email.';'.$user->name, $user->name, 'email', 'name');
		$options[] = JHTML::_('select.option', $MailFrom.';'.$FromName, JText::_('COM_HECMAILING_DEFAULT').'('.$FromName.')', 'email', 'name');
		
		$input_options = 'class="' . $this->getAttribute('class') . '"';
		// Initialize variables.
		$html = JHtml::_('select.genericlist', $options, $this->name, $input_options, 'email', 'name', $value, $this->id);
				

		

		return $html;
	}


}
