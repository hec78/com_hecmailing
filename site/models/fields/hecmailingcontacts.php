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
class JFormFieldHecmailingContacts extends JFormField
{

	/**
	 * The form field type.
	 *
	 * @var        string
	 * @since    1.6
	 */
	protected $type = 'hecmailingcontacts';
	
	function __construct()
	{
		parent::__construct();
		
		$this->contacts = $this->getContacts();
		$this->remove_label_ifone = $this->getAttribute('remove_label_if_only_one','1')=='1';
		
		if (count($this->contacts)==1)
			$this->hidden=$this->remove_label_ifone;
		
	}
	
	public function getLabel() {
		if (count($this->contacts)==1)
		{
			$this->hidden=$this->remove_label_ifone;
			return "";
		}
		else 
			return $this->label;
	}
	
	
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
		
		$input_options = 'class="' . $this->getAttribute('class') . ' '.$this->getAttribute('options').'"';
		//Type of input the field shows
		$input_type = $this->getAttribute('input_type','list');
		
		if ($this->hint=='') $this->hint= $this->getAttribute('label');
		$script="";		
		
		
		//Depends of the type of input, the field will show a type or another
		switch ($this->input_type)
		{
			case 'list':
			default:
				$options = array();
				//Iterate through all the groups
				if ($this->contacts)
				{
					if (count($this->contacts)==1)
					{
						$contact=$this->contacts[0];
						$contactName=$contact->name;
						$contactInfo=$contact->info;
						$html = "<input type=\"hidden\" name=\"".$this->name."\" id=\"".$this->id."\" value=\"".$contact->id."\" />";
						
						
					}
					else if(count($this->contacts)>1)
					{
						$scriptelmt = array();
						if ($this->default==-1)
							$options[] = JHtml::_('select.option', "0", JText::_($this->hint), 'id','name');
						else
						{
							if (intVal($this->value)<=0 && $this->default>0)
							{
								$this->value=$contacts[$this->default];
							}
						}
						$contactName="";
						$contactInfo="";
						foreach ($this->contacts as $contact)
						{
							$scriptelmt[] = $contact->id.": { info:'".$contact->info."' , name:'".$contact->name."'}";
							$options[] = JHtml::_('select.option', $contact->id, $contact->name, 'id','name');
							if ($this->value==$contact->id)
							{
								$contactName=$contact->name;
								$contactInfo=$contact->info;
							}
						}
						$input_options.=" onChange=\"selectContact(this)\" ";
						$html = JHtml::_('select.genericlist', $options, $this->name, $input_options, 'id', 'name', $this->value, $this->id);
						$script="var ".$this->id."_contactInfo =  {".join(",", $scriptelmt)."};\n";
						$script.="function selectContact(obj) {\n";
						$script.="    var selectedIndex = obj.selectedIndex;\n";
						$script.="    var id = obj.options[selectedIndex].value;\n";
						$script.="    if(id>0) {\n";
						$script.="        var current=".$this->id."_contactInfo[id];\n";
						$script.="        jQuery('#".$this->id."_contactInfoHeader').html(current['name']);\n";
						$script.="        jQuery('#".$this->id."_contactInfoContent').html(current['info']);\n";
						$script.="    } else {\n";
						$script.="        jQuery('#".$this->id."_contactInfoHeader').html('');\n";
						$script.="        jQuery('#".$this->id."_contactInfoContent').html('');\n";
						$script.="    }\n";
						$script.="}\n";
					}
					else
					{
						$contactName="";
						$contactInfo="";
						$html = "<input type=\"hidden\" name=\"".$this->name."\" id=\"".$this->id."\" value=\"0\" />";
					}
				}
				$html.= "<div class=\"contactInfoHeader\" id=\"".$this->id."_contactInfoHeader\">".$contactName."</div>";
				$html.= "<div class=\"contactInfoContent\" id=\"".$this->id."_contactInfoContent\">".$contactInfo."</div>";
				break;
		}
		
		
		JFactory::getDocument()->addScriptDeclaration($script);
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
	
	function getContacts()
	{
		$db=JFactory::getDbo();
		$query = "SELECT ct_id_contact as id,ct_nm_contact as name, ct_vl_info as info FROM #__hecmailing_contact WHERE published=1 order by ct_nm_contact";
		$db->setQuery($query);
		if (!$rows = $db->loadObjectList()) return false;
		
		return $rows;
	}

}
