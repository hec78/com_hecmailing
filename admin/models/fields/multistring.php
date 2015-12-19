<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('text');

/**
 * Form Field class for the Joomla Framework.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_users
 * @since       1.6
 */
class JFormFieldMultiString extends JFormFieldText
{
	/**
	 * The form field type.
	 *
	 * @var        string
	 * @since   1.6
	 */
	protected $type = 'MultiString';
	
	/**
	 * Method to get the field input markup.
	 *
	 * @return  string	The field input markup.
	 *
	 * @since   1.6
	 */
	protected function getInput()
	{
		$script=array();
		$filter=strtolower($this->getAttribute("filter",""));
		$filter_cond="";
		$msg_error="";
		if ($filter=="domain")
		{
			$filter_cond="    if (/^[a-zA-Z0-9][a-zA-Z0-9-]{1,61}[a-zA-Z0-9](?:\.[a-zA-Z]{2,})+$/.test(val)){";
			$msg_error=JText::_("COM_HECMAILING_JFIELD_MULTISTRING_ERROR_DOMAIN");
		}
		elseif ($filter=="integer")
		{
			$filter_cond="    if (/^[0-9]$/.test(val)){";
			$msg_error=JText::_("COM_HECMAILING_JFIELD_MULTISTRING_ERROR_INTEGER");
		}
		elseif ($filter=="decimal")
		{
			$filter_cond="    if (/^\d+(\.\d{1,2})?$/.test(val)){";
			$msg_error=JText::_("COM_HECMAILING_JFIELD_MULTISTRING_ERROR_DECIMAL");
		}
		elseif ($filter!="")
		{
			$filter_cond="    if($filter.test(val)){";
			$msg_error=$this->getAttribute("filter_error_message",JText::_("COM_HECMAILING_JFIELD_MULTISTRING_ERROR_FORMAT"));
		}
		$script[]="";
		$script[]="// MultiString JFormField Script";
		$script[]="function getData_".$this->id."() {";
		$script[]="    var container=document.getElementById('".$this->id."_container');";
		$script[]="    var valueelmt=document.getElementById('".$this->id."');";
		$script[]="    var value='';";
		$script[]="    for (var i=0; i<container.rows.length; i++) {";
		$script[]="        var row=container.rows[i];";
		$script[]="        var col=row.cells[0];";
		$script[]="        value=value+';'+col.innerHTML;";
		$script[]="    }";    
 		$script[]="    valueelmt.value=value;";				
		$script[]="}";
		$script[]="function removeRow_".$this->id."(item) {";
		$script[]="    var container=document.getElementById('".$this->id."_container');";
		$script[]="    var row=document.getElementById(item+'_row');";
		$script[]="    container.removeChild(row);";
		$script[]="    getData_".$this->id."();";
		$script[]="}";
		$script[]="function removeAllRows_".$this->id."() {";
		$script[]="    if(confirm('".JText::_("COM_HECMAILING_REMOVE_ALL")."')) {";
		$script[]="        var container=document.getElementById('".$this->id."_container');";
		$script[]="        for (var i=container.rows.length-1; i>=0; i--) {";
		$script[]="            var row=container.rows[i];";
		$script[]="            container.removeChild(row);";
		$script[]="        }";    
		$script[]="        getData_".$this->id."();";
		$script[]="    }";
		$script[]="}";
		$script[]="function addRow_".$this->id."() {";
		$script[]="    var container=document.getElementById('".$this->id."_container');";
		$script[]="    var nbel=document.getElementById('".$this->id."_count');";
		$script[]="    var nb=parseInt('0'+nbel.value)+1;";
		$script[]="    nbel.value=nb;";
		$script[]="    var row = document.createElement(\"tr\");";
		$script[]="    var id='". $this->id ."'+nb";
		$script[]="    var item=id;";
		$script[]="    var val='';";
		$script[]="    row.id=id+'_row';";
		$script[]="    container.appendChild(row);";
		$script[]="    var col1 = document.createElement(\"td\");";
		$script[]="    row.appendChild(col1);";
		$script[]="    var col2 = document.createElement(\"td\");";
		$script[]="    row.appendChild(col2);";
		$script[]="    row.cells[0].innerHTML='<input type=\"text\" id=\"'+item+'\" class=\"input-large\" value=\"'+val+'\" onblur=\"validate_".$this->id."(\''+item+'\')\" onkeypress=\"if (event.keyCode==13){validate_".$this->id."(\''+item+'\');return false;}\">';";
		$script[]="    row.cells[1].innerHTML='<a onclick=\"validate_".$this->id."(\''+item+'\')\"><span class=\"icon-ok\"></span></a><a onclick=\"removeRow_".$this->id."(\''+item+'\')\"><span class=\"icon-delete\"></span></a>';";
		$script[]="}";
		$script[]="function validate_".$this->id."(item) {";
		$script[]="    var container=document.getElementById(\"".$this->id."_container\");";
		$script[]="    var row=document.getElementById(item+'_row');";
		$script[]="    var input=document.getElementById(item);";
		$script[]="    var val=input.value;";
		if ($filter_cond!="")
			$script[]=$filter_cond;
		$script[]="        row.cells[0].innerHTML=input.value;";
		$script[]="        row.cells[1].innerHTML='<a onclick=\"editRow_".$this->id."(\''+item+'\')\"><span class=\"icon-edit\"></span></a><a onclick=\"removeRow_".$this->id."(\"'+item+'\")\"><span class=\"icon-delete\"></span></a>';";
		$script[]="        getData_".$this->id."();";
		if ($filter_cond!="") {
			$script[]="    } else {";
			$script[]="        if(!document.getElementById(item+'_msg')){";
			$script[]="            var msg = document.createElement(\"div\");";
			$script[]="            msg.id=item+'_msg';";
			$script[]="            msg.innerHTML='".$msg_error."';";
			$script[]="            msg.style.color='red';";
			$script[]="            row.cells[0].appendChild(msg);";
			$script[]="        }";
			$script[]="        input.style.backgroundColor='red';";
			$script[]="        input.style.color='white';";
			$script[]="        input.focus();";
			$script[]="    }";
		}
		$script[]="}";
		$script[]="function editRow_".$this->id."(item) {";
		$script[]="    var container=document.getElementById(\"".$this->id."_container\");";
		$script[]="    var row=document.getElementById(item+'_row');";
		$script[]="    var val=row.cells[0].innerHTML;";
		$script[]="    row.cells[0].innerHTML='<input type=\"text\" id=\"'+item+'\" class=\"input-large\" value=\"'+val+'\" onblur=\"validate_".$this->id."(\''+item+'\')\" onkeypress=\"if (event.keyCode==13){validate_".$this->id."(\''+item+'\');return false;}\">';";
		$script[]="    row.cells[1].innerHTML='<a onclick=\"validate_".$this->id."(\''+item+'\')\"><span class=\"icon-ok\"></span></a><a onclick=\"removeRow_".$this->id."(\''+item+'\')\"><span class=\"icon-delete\"></span></a>';";
		$script[]="}";
		$script[]="// END OF MultiString JFormField Script";
		// Add to document head
		JFactory::getDocument()->addScriptDeclaration(implode("\n    ", $script));
		
		$css=array();
		$css[]="#".$this->id."_table { border-collapse: collapse;}";
		$css[]="#".$this->id."_table tr {";
   		$css[]="   border-bottom: 1px solid lightgrey;";
   		$css[]="   padding: 15px;";
		$css[]="}";
		$css[]="table#".$this->id."_table tr:nth-child(even) {background-color: #eee;}";
		$css[]="table#".$this->id."_table tr:nth-child(odd) { background-color: #fff;}";
		JFactory::getDocument()->addStyleDeclaration(implode("\n", $css));
		$width=$this->getAttribute("width","");
		$libnew=JText::_($this->getAttribute("label_new",JText::_("COM_HECMAILING_JFIELD_MULTISTRING_ADD_TEXT")));
		$libdelall=JText::_($this->getAttribute("label_delall",JText::_("COM_HECMAILING_JFIELD_MULTISTRING_REMOVE_ALL")));
		if ($width!="") $width="width:".$width;
		$vals=explode(';',$this->value);
		$html=array();
		$html[]="<input type='hidden' name='".$this->name."' id='". $this->id."' value='".$this->value."' />";
		$html[]="<input type='hidden' id='".$this->id . "_count' value='".count($vals)."' />";
		$html[]="<table style='$width' id='".$this->id."_table'><thead><tr><th class='align-right' colspan='2' >";
		$html[]="<a  onclick='removeAllRows_".$this->id."()' ><span class='icon-delete'></span> ".$libdelall."</a>";
		$html[]="<a  onclick='addRow_".$this->id."()' ><span class='icon-new'></span> ".$libnew."</a></th></tr>";
		$html[]="<tbody id='". $this->id."_container'>";
		$n=1;
		foreach ($vals as $val)
		{
			if ($val!='')
			{
				$html[]="<tr id='".$this->id.$n."_row' ><td>".$val."</td>";
				$html[]="<td width='50px'>";
				$html[]="<a onclick='editRow_".$this->id."(\"".$this->id.$n."\")'><span class='icon-edit'></span></a>";
				$html[]="<a onclick='removeRow_".$this->id."(\"".$this->id.$n."\")'><span class='icon-delete'></span></a>";
				$html[]="</td></tr>";
				$n++;
			}
		}
		
		$html[]='</table>';
		return implode("\n", $html);
	}
}
