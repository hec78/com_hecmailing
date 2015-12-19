// @version 3.2.0
// @package hecMailing for Joomla
// @module views.send.tmpl.default.php (associated javascript module)
// @subpackage : View Send (Send mail form)
// @copyright Copyright (C) 2008-2016 Hecsoft All rights reserved.
// @license GNU/GPL
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
// 
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//////////////////////////////////////////////////////////////////////////////
function loadTemplate()
{
	iIdTemplate=document.getElementById("idTemplate");
	var s=document.getElementById("saved");
	selected = s.options[s.selectedIndex].value;
	if (selected>0)
	{
		jQuery( "#loadtmpl" ).dialog('close');
  		iIdTemplate.value=selected;
  		Joomla.submitbutton('form.load');
	}
}
function saveTemplate()
{
	iSaveTemplate=document.getElementById("saveTemplate");
	val=document.getElementById("tmplName").value;
	if (val=='')
	{
		alert (text_msg_tmplname_empty);
	}
	else
	{
		jQuery( "#savetmpl" ).dialog('close');
		iSaveTemplate.value=val;
		Joomla.submitbutton('form.save');
	}
}
function showLoadTemplate()
{
   	jQuery( "#loadtmpl" ).dialog({
		resizable: false,
		height:250,
		width : 300,
		modal: true,
		draggable: true 
		});
  return false;  
}

function showSaveTemplate()
{
	jQuery( "#savetmpl" ).dialog({
		resizable: false,
		height:250,
		width : 330,
		modal: true,
		draggable: true 
		});
   return false;  
}

function cancelSaveTmpl() {	jQuery( "#savetmpl" ).dialog("close"); }
function cancel() {	jQuery( "#loadtmpl" ).dialog("close");}
function showErrorBox(text,title) {
	jQuery('#ErrorMessage .content').html(text);
	if (title!='')	jQuery('#ErrorMessage .content').prop('title',title);
	jQuery('#ErrorMessage').dialog({resizable: false, height:150,width : 300,modal: true, draggable: true});
}
function checksend()
{
	var grp = jQuery("#jform_groupe");
	if (grp.val()<=0)
	{
		showErrorBox(text_msg_select_group,'');
		grp.css('color','red');
		grp.focus();
		return false;
	}
	else
		grp.css('color','');
	var subject = jQuery("#jform_message_subject");
	if (subject.val()=='')
	{
		showErrorBox(text_msg_empty_subject,'');
		subject.css('color','red');
		subject.focus();
		return false;
	}
	else
		subject.css('color','');
	Joomla.submitform('send.send');
}
var current_group=0;
function showManageButton(selectBox)
{
	var btn = document.getElementById("manage_button");
	current_group=selectBox.options[selectBox.options.selectedIndex].value;
	flag=rights[current_group];
	if ((flag & 6)>0)
	{
		btn.style.visibility = 'visible';
	}
	else
	{
		btn.style.visibility = 'hidden';
	}
}
function manage_group()
{
	window.open("index2.php?option=com_hecmailing&task=manage_group&tmpl=component&idgroup='; ?>"+current_group,text_manage,"directories=no,location=no,menubar=no,resizable=yes,scrollbars=yes,status=yes,toolbar=no,width=800,height=600");
}
