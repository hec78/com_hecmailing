<?php
/**
* @version 3.4.3
* @package hecMailing for Joomla
* @subpackage : View Send (Sending mail form)
* @module views.form.tmpl.view.html.php
* @copyright Copyright (C) 2008-2017 Hecsoft All rights reserved.
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
		$siteValue = $MailFrom.';'.$FromName;
		
		if (!$user->guest)
			$userValue= $user->email.";".$user->name;
		else 
			$userValue="";
		
		$default_sender_mode = $this->default;
		if ($this->value==$this->default) $this->value="";
		if (substr($default_sender_mode,0,1)=="$")
		{
			$default_sender_mode =intval($pparams->get(substr($default_sender_mode,1),'1'));
		}
		else 
		{
			try {
				$default_sender_mode =intval($default_sender_mode);
			}
			catch(Exception $e)
			{
				$default_sender_mode =1;
			}
				
		}
		if ($default_sender_mode==0 && $user->guest) $default_sender_mode=1;
		
		// No default sender email provided
		if ($this->value == '')
		{
			switch ($default_sender_mode)
			{
				case 0: // Connected user
					$value = $userValue;
					break;
				case 2: // Group (unused)
				case 1: // Default site
					$value = $siteValue;
					break;
			}
		}
		else
			$value=$this->value;
		$options = array();
		if (!$user->guest)
			$options[] = JHTML::_('select.option', $userValue,$user->name , 'email', 'name');
		$options[] = JHTML::_('select.option', $siteValue, JText::_('COM_HECMAILING_DEFAULT').'('.$FromName.')', 'email', 'name');
		// TODO : Add Ajax to Add dynamicaly group sender?
		
		
		$input_options = 'class="' . $this->getAttribute('class') . '"';
		// Initialize variables.
		$html = JHtml::_('select.genericlist', $options, $this->name, $input_options, 'email', 'name', $value, $this->id);
				

		

		return $html;
	}
	/**
	 * Wrapper method for getting attributes from the form element
	 *
	 * @param string $attr_name Attribute name
	 * @param mixed  $default   Optional value to return if attribute not found
	 *
	 * @return mixed The value of the attribute if it exists, null otherwise
	 */
	public function getAttribute($attr_name, $default = null)
	{
		if (!empty($this->element[$attr_name]))
		{
			return $this->element[$attr_name];
		}
		else
		{
			return $default;
		}
	}

}
