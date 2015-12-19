<?php

/**
 * @version     3.4.0
 * @package     com_hecmailing
 * @copyright   Copyright (C) 2014. Tous droits réservés.
 * @license     GNU General Public License version 2 ou version ultérieure ; Voir LICENSE.txt
 * @author      Hervé CYR <herve.cyr@kantarworldpanel.com> - 
 */
defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');
JLoader::register('HecMailingGRoupsHelper',JPATH_COMPONENT.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'groups.php');
/**
 * Supports a value from an external table
 */
class JFormFieldHecmailingGroups extends JFormField
{

	/**
	 * The form field type.
	 *
	 * @var        string
	 * @since    1.6
	 */
	protected $type = 'hecmailinggroups';
	
	/**
	 * Method to get the field input markup.
	 *
	 * @return    string    The field input markup.
	 * @since    1.6
	 */
	protected function getInput()
	{

		//Assign field properties.
				// Initialize variables.
		$html = '';
		$groups = HecMailingGroupsHelper::getGroups();
		$input_options = 'class="' . $this->getAttribute('class') . ' '.$this->getAttribute('options').'"';
		//Type of input the field shows
		$input_type = $this->getAttribute('input_type','list');
		$none_value = $this->getAttribute('none_value','');
		
		//Depends of the type of input, the field will show a type or another
		switch ($this->input_type)
		{
			case 'list':
			default:
				$options = array();
				if ($none_value!='')
					$options[] = JHtml::_('select.option', 0, JText::_($none_value), 'id','name');
				//Iterate through all the groups
				if ($groups)
				foreach ($groups as $group)
				{
					$options[] = JHtml::_('select.option', $group->id, $group->name, 'id','name');
				}
				$html = JHtml::_('select.genericlist', $options, $this->name, $input_options, 'id', 'name', $this->value, $this->id);
				break;
		}

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
