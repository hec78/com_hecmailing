<?php
/**
* @version   3.4.0
* @package   HEC Mailing for Joomla
* @copyright Copyright (C) 1999-2017 Hecsoft All rights reserved.
* @author    Hervé CYR
* @license   GNU/GPL
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
*
*/

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');

/**
 * Supports an HTML select list of categories
 */
class JFormFieldAttachments extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'attachments';
	/**
	 * The initialised state of the document object.
	 *
	 * @var    boolean
	 * @since  11.1
	 */
	protected static $initialised = false;
	
	function __construct()
	{
		parent::__construct();
		
		$this->app=JFactory::getApplication();
		
		$this->option=$this->app->input->get('option', '');
		$this->component_path = Juri::base(true)."/components/".$this->option;
		$this->fieldpath=$this->component_path."/models/fields/attachments";
		
		// Componets parameters
		$this->params = JComponentHelper::getParams( $this->option );
	}
	
	
	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{
		$doc = JFactory::getDocument();
		
		
		$lib_add_attachment = JText::_($this->getAttribute("label_attachment"),"JFORMFIELD_ATTACHMENTS_ADD_ATTACHMENT");
		$lib_browse = JText::_($this->getAttribute("label_browse"), "JFORMFIELD_ATTACHMENTS_ADD_ATTACHMENT");
		$class=$this->getAttribute("class","jformfield_attachment");
		$height=$this->getAttribute("browse_height","600");
		$width=$this->getAttribute("browse_width","600");
		$can_create_folder=$this->getAttribute("can_create_folder","param:can_create_folder:1");
		
		// Images
		$cancelimg=$this->fieldpath."/images/cancel16.png";
		$folderimg=$this->fieldpath."/images/folder16.png";
		$fileimg=$this->fieldpath."/images/file16.png";
		$loaderimg=$this->fieldpath."/images/ajax-loader.gif";
		$messageboxquestionimg=$this->fieldpath."/images/messagebox_question.png";
		$messageboxwarningimg=$this->fieldpath."/images/messagebox_warning.png";
		
		
		// Ajax
		$webservicecontroller=(string)$this->getAttribute("controller","webservice");
		$basewebserviceurl = Juri::base(true)."/index.php?option=".$this->option."&task=".$webservicecontroller;
		$uploadurl=$basewebserviceurl.".upload";
		$browseurl=$basewebserviceurl.".getdir";
		$createdirurl=$basewebserviceurl.".createdir";
		
		
		
		
		if (!self::$initialised)
		{
			// Load the modal behavior script.
			//JHtml::_('behavior.modal');
			// Build the script : Based on JFormFieldMedia object of Joomla Core.
			$script = array();
			$script[]="";
			$script[]="function jformfieldattachments_defaultAttr(arg, def) {";
			$script[]="    return (typeof arg == 'undefined' ? def : arg);";
			$script[]="}";
			$script[]="function jformfieldattachments_showBrowse(id) {";
			$script[]="    jQuery( \"#\"+id+\"_browseDlg\" ).dialog({resizable: true, height:".$height.",width : ".$width.",modal: true, draggable: true});";
			$script[]="    jformfieldattachments_fillList(id);";
			$script[]="    return false; }";
			$script[]="function jformfieldattachments_showUpload(id) {";
			$script[]="    jQuery( \"#\"+id+\"_uploadDlg\" ).dialog({resizable: false, height:300,width : 400,modal: true, draggable: true});";
			$script[]="    return false; }";
			$script[]="function jformfieldattachments_removeAttachment(id, idatt) {";
			$script[]="    jQuery('#'+id+'_confirmeRemoveAttachment_id').val(idatt);";
			$script[]="    jQuery( \"#\"+id+\"_confirmeRemoveAttachment\" ).dialog({resizable: false, height:150,width : 300,modal: true, draggable: true});";
			$script[]="    return false; }";
			$script[]="function jformfieldattachments_confirmRemoveAttachment(id) {";
			$script[]="    var idatt=jQuery('#'+id+'_confirmeRemoveAttachment_id').val();";
			$script[]="    jQuery( \"#\"+idatt+\"_row\" ).remove();";
			$script[]="    jQuery( \"#\"+id+\"_confirmeRemoveAttachment\" ).dialog('close');";
			$script[]="    return false; }";
			$script[]="function jformfieldattachments_cancelRemoveAttachment(id) {";
			$script[]="    jQuery( \"#\"+id+\"_confirmeRemoveAttachment\" ).dialog('close');";
			$script[]="}";
			$script[]="function jformfieldattachments_showCreateFolder(id) {";
			$script[]="    jQuery( \"#\"+id+\"_createFolderDlg\" ).dialog({resizable: false, height:120,width : 400,modal: true, draggable: true});";
			$script[]="    return false;";
			$script[]="}";
			$script[]="function jformfieldattachments_errorBox(id,text) {";
			$script[]="    jQuery('#'+id+'_ErrorMessage .content').html(text);";
			$script[]="    jQuery('#'+id+'_ErrorMessage').dialog({resizable: false, height:150,width : 300,modal: true, draggable: true});";
			$script[]="}";
			$script[]="function jformfieldattachments_displayMessage(id,message, status) {";
			$script[]="    jQuery('#'+id+'_message span.message_text').html(message);";
			$script[]="    jQuery('#'+id+'_message span.message_text').removeClass('error warning ok');";
			$script[]="    jQuery('#'+id+'_message span.message_text').addClass(status);";
			$script[]="    jQuery('#'+id+'_message span.message_icon').removeClass('icon-info icon-warning-2');";
			$script[]="    if(message!='') {";
			$script[]="        jQuery('#'+id+'_message').show();";
			$script[]="    } else {";
			$script[]="        jQuery('#'+id+'_message').hide();";
			$script[]="    }";
			$script[]="    if (status=='error') {";
			$script[]="        jQuery('#'+id+'_message span.message_icon').addClass('icon-warning-2');";
			
			$script[]="    } else {";
			$script[]="        jQuery('#'+id+'_message span.message_icon').addClass('icon-info');";
			$script[]="    }";
			$script[]="    if(status!='ok') {";
			$script[]="        jformfieldattachments_errorBox(id,message);";
			$script[]="    }";
			$script[]="}";
			$script[]="function jformfieldattachments_confirmCreateFolder(id) {";
			$script[]="    var newfolderobj=jQuery('#'+id+'_createFolderText');";
			$script[]="    var newfolder=newfolderobj.val();";
			$script[]="    jQuery( \"#\"+id+\"_createFolderDlg\" ).dialog('close');";
			$script[]="    var lurl ='".$createdirurl."';";
			$script[]="    var dir=document.getElementById(id+'_browseCurrentDir').innerHTML;";
			$script[]="    formData= { directory : dir, newfolder: newfolder};";
			$script[]="    try {";
			$script[]="        jQuery.ajax({";
			$script[]="        type: 'POST',";
			$script[]="        url: lurl,";
			$script[]="        data: formData,";
			$script[]="        dataType: 'json',";
			$script[]="        success: function (data, textStatus, jqXHR){";
			$script[]="            selected=data.selected;";
			$script[]="            var newdir=dir;";
			$script[]="            if (selected!='') { newdir+='/'+selected; }";
			$script[]="            jformfieldattachments_fillList(id,newdir, {},data.message,data.status );";
			//$script[]="            jformfieldattachments_displayMessage(id,data.message,data.status);";
			$script[]="        },";
			$script[]="        error: function (msg,status) {";
			//$script[]="            jformfieldattachments_errorBox(id,'".JText::_('JFORMFIELD_ATTACHMENTS_WEBSERVICE_ERROR')." :'+status);";
			$script[]="            jformfieldattachments_displayMessage(id,'".JText::_('JFORMFIELD_ATTACHMENTS_WEBSERVICE_ERROR')." :'+status,'error');";
			$script[]="	       }});";
			$script[]="    } catch(ex) {";
			//$script[]="        jformfieldattachments_errorBox(id,'".JText::_('JFORMFIELD_ATTACHMENTS_WEBSERVICE_ERROR')." :'+ex);";
			$script[]="        jformfieldattachments_displayMessage(id,'".JText::_('JFORMFIELD_ATTACHMENTS_WEBSERVICE_ERROR')." :'+ex,'error');";
			$script[]="    }";
			$script[]="    return false; }";
			$script[]="function jformfieldattachments_cancelCreateFolder(id) {";
			$script[]="    jQuery( \"#\"+id+\"_createFolderDlg\" ).dialog('close');";
			$script[]="}";
			$script[]="function jformfieldattachments_hideBrowse(id) {jQuery( \"#\"+id+\"_browseDlg\" ).dialog('close'); return false;}";
			$script[]="// Add attachment row (file input)";
			$script[]="function jformfieldattachments_addAttachment(id) {";
			$script[]="    var count = document.getElementById(id+'_attachmentcount');";
			$script[]="    var tab=document.getElementById(id+'_attachmentbody');";
			$script[]="    var row=document.createElement('tr');";
			$script[]="    var num=parseInt(count.value)+1;";
			$script[]="    var newid=id+'_'+num;";
			$script[]="    var name=id+'[]';";
			$script[]="    row.id=newid+'_row';";
			$script[]="    row.innerHTML='<td><input type=\"file\" id=\"'+newid+'\" name=\"'+name+'\" style=\"width:100%\"></td><td><a class=\"btn\" onclick=\"jformfieldattachments_removeAttachment(\'".$this->id."\',\''+newid+'\');\"><img src=\"".$cancelimg."\" ></a></td>';";
			$script[]="    count.value=num;";
			$script[]="    tab.appendChild(row);";
			$script[]="}";
			$script[]="function jformfieldattachments_selectFile(id) {";
			$script[]="    var count = document.getElementById(id+'_attachmentcount');";
			$script[]="    var tab=document.getElementById(id+'_attachmentbody');";
			$script[]="    var dlgBrowse = document.getElementById(id+'_browseDlg');";
			$script[]="    var dlgBrowseList = document.getElementById(id+'_browseListe');";
			$script[]="    var sel = jformfieldattachments_getSelected(id,dlgBrowseList);";
			$script[]="    if (jformfieldattachments_curdir.length>0) ledir=jformfieldattachments_curdir+'/';";
			$script[]="    else ledir='/';";
			$script[]="        var num=parseInt(count.value);";
			$script[]="    for (i=0;i<sel.length;i++) {";
			$script[]="        num++;";
			$script[]="        var newid=id+'_'+num;";
			$script[]="        var name=id+'[]';";
			$script[]="        row=document.createElement('TR');";
			$script[]="        row.id=newid+'_row'";
			$script[]="        row.innerHTML='<td><input type=\"text\" readonly=\"readonly\" id=\"'+newid+'\" name=\"'+name+'\" style=\"width:90%\" value=\"'+base_dir+ledir+sel[i]+'\" ></td><td><a class=\"btn\" onclick=\"jformfieldattachments_removeAttachment(\'".$this->id."\',\''+newid+'\');\"><img src=\"".$cancelimg."\" ></a></td>';";
			$script[]="        tab.appendChild(row);";
			$script[]="        count.value=num;";
			$script[]="    }";
			$script[]="    jformfieldattachments_hideBrowse(id);";
			$script[]="}";
			$script[]="function jformfieldattachments_calcSize(size) {";
			$script[]="    var units=['o','K','M','G'];";
			$script[]="    var i=0;var v=size;";
			$script[]="    while (v>1024 && i<4) {";
			$script[]="        i++;v=Math.round(v/1024);";
			$script[]="    }";
			$script[]="    return v+' '+units[i];";
			$script[]="}";
			$script[]="function jformfieldattachments_addElement(id,divListe,path, isfolder, isSelected,fsize,mtime) {";
			$script[]="    var a = document.createElement('a');";
			$script[]="    if(isSelected) { selText='checked=\"checked\"';} else {selText='';}";
			$script[]="    num=divListe.childNodes.length;";
			$script[]="    if (isfolder) { "; //dir
			$script[]="        a.id = 'folder'+num;";
			$script[]="        a.className = 'jformfieldattachments_directory';";
			$script[]="        a.ondblclick= function() { jformfieldattachments_dbclick(id,this);return false; }";
			$script[]="        a.innerHTML='<img src=\"".$folderimg."\" width=\"16\" width=\"16\" > '+path;";
			$script[]="    } else {";
			$script[]="        a.id = 'file'+num;";
			$script[]="        a.onclick = function() { jformfieldattachments_select(this);return false; }";
			$script[]="        a.ondblclick= function() { jformfieldattachments_selectandclose(this);return false; }";
			$script[]="        a.innerHTML='<input type=\"checkbox\" id=\"chk'+a.id+'\" name=\"'+path+'\" '+selText+' ><img src=\"".$fileimg."\" width=\"16\" width=\"16\" ><span class=\"col-path\">'+path+'</span><span class=\"col-time\">'+mtime+'</span><span class=\"col-size\">'+jformfieldattachments_calcSize(fsize)+'</span>';";
			$script[]="        a.className = 'jformfieldattachments_file';";
			$script[]="    }";
			$script[]="    a.name=path;";
			$script[]="    divListe.appendChild(a);";
			$script[]="}";
			$script[]="function jformfieldattachments_resetList(divListe) {";
			$script[]="    obj=divListe;";
			$script[]="    try {";
			$script[]="        if(obj.hasChildNodes() && obj.childNodes) { while(obj.firstChild) { obj.removeChild(obj.firstChild);	}}";
			$script[]="    } catch(e) { //  do nothing, or uncomment the error message - alert(e.message);	";
			$script[]="    }";
			$script[]="}";
			$script[]="function jformfieldattachments_getSelected(divListe){";
			$script[]="    var ret=new Array();";
			$script[]="    jQuery( '#'+divListe+'_browseListe input[type=checkbox]').each( function( i, item ) {";
			$script[]="        if (item.type=='checkbox'){ if (item.checked) ret.push(item.name);}";            
			$script[]="    });";
			$script[]="    return ret;";
			$script[]="}";
			$script[]="function jformfieldattachments_dbclick(id,param) {";
			$script[]="    if (param.name=='..'){";
			$script[]="        k=-1;";
			$script[]="        for(i=jformfieldattachments_curdir.length-1;i>0;i--){";
			$script[]="            if (jformfieldattachments_curdir.substr(i,1)=='/')	{k=i;break;	}";
			$script[]="        }";
			$script[]="        newdir=jformfieldattachments_curdir.substr(0,k);";
			$script[]="    } else { newdir=jformfieldattachments_curdir+'/'+ param.name; }";
			$script[]="    jformfieldattachments_fillList(id, newdir);";
			$script[]="}";
			$script[]="function jformfieldattachments_select(elmt) {";
			$script[]="    var chk = elmt.getElementsByTagName('input');";
			$script[]="    chk=chk[0];";
			$script[]="    chk.checked=!chk.checked;";
			$script[]="}";
			$script[]="function jformfieldattachments_selectandclose(elmt){";
			$script[]="    var chk = elmt.getElementsByTagName('input');";
			$script[]="    chk=chk[0];";
			$script[]="    chk.checked=!chk.checked;";
			$script[]="    jformfieldattachments_selectFile();";
			$script[]="}";
			$script[]="var jformfieldattachments_curdir='';";
			
			$script[]="function jformfieldattachments_showHide(id,show) {";
			$script[]="    if(show) {";
			$script[]="        jQuery(id).show();";
			$script[]="    } else {";
			$script[]="        jQuery(id).hide();";
			$script[]="    };";
			$script[]="}";
				
			$script[]="function jformfieldattachments_fillList(id,dirname, selected, originalmessage, originalstatus) {";
			$script[]="    if(selected==undefined) selected={};";
			$script[]="    var dlg = document.getElementById(id+'_browseDlg');";
			$script[]="    var lst = document.getElementById(id+'_browseListe');";
			$script[]="    var btn = document.getElementById(id+'_browseButtons');";
			$script[]="    var originalmessage=jformfieldattachments_defaultAttr(originalmessage, '');";
			$script[]="    var originalstatus=jformfieldattachments_defaultAttr(originalstatus, false);";
			$script[]="    jformfieldattachments_resetList(lst);";
			$script[]="    lst.style.width='100%';";
			$script[]="    lst.style.height=dlg.clientHeight - 50;";
			$script[]="    btn.style.top = dlg.clientHeight - 50;";
			$script[]="    img = document.createElement('img');";
			$script[]="    img.src='".$loaderimg."';";
			$script[]="    l = (dlg.clientWidth-100)/2;";
			$script[]="    h = (dlg.clientHeight-100)/2;";
			$script[]="    img.style.marginLeft= l+'px';";
			$script[]="    img.style.marginTop= h+'px';";
			$script[]="    lst.appendChild(img);";
			$script[]="    if (dirname==undefined)	{ dirname=''; }";
			$script[]="    var lurl ='".$browseurl."';";
			$script[]="    formData={'dir': dirname};";
			$script[]="    try {";
			$script[]="        jQuery.ajax({";
        	$script[]="        type: 'POST',";
            $script[]="        url: lurl,";
        	$script[]="        data: formData,";
    		$script[]="        dataType: 'json',";
    		$script[]="        success: function (data, textStatus, jqXHR){";
			$script[]="            jformfieldattachments_resetList(lst);";
			$script[]="            jformfieldattachments_curdir = data.dir;";
			$script[]="            items= data.list;";
			$script[]="            if(data.status=='ok') {";
			$script[]="                jQuery.each( items, function( i, item ) {";
			$script[]="                    jformfieldattachments_addElement(id,lst,item.path, item.type == 'dir', (item.path in selected),item.size,item.mtime);";
			$script[]="                });";
			$script[]="                if (originalmessage!='') {";
			$script[]="                    jformfieldattachments_displayMessage(id,originalmessage,originalstatus);";
			$script[]="                } else {";
			$script[]="                    jformfieldattachments_displayMessage(id,data.message,'ok');";
			$script[]="                }";
			$script[]="            } else {";
			$script[]="                jformfieldattachments_displayMessage(id,data.message,'error');";
			$script[]="            }";
			$script[]="            jformfieldattachments_showHide('#'+id+'_createFolderButton',data.params.cancreatedir);";
			$script[]="            jformfieldattachments_showHide('#'+id+'_uploadFieldset',data.params.canupload);";
			
			$script[]="        },";
			$script[]="        error: function (msg,status) {";
    		//$script[]="            jformfieldattachments_errorBox(id,'".JText::_('JFORMFIELD_ATTACHMENTS_WEBSERVICE_ERROR')." :'+status);";
    		$script[]="            jformfieldattachments_displayMessage(id,'".JText::_('JFORMFIELD_ATTACHMENTS_WEBSERVICE_ERROR')." :'+status,'error');";
	    	$script[]="	       }});";
			$script[]="    } catch(ex) {";
			//$script[]="        jformfieldattachments_errorBox(id,'".JText::_('JFORMFIELD_ATTACHMENTS_WEBSERVICE_ERROR')." :'+ex);";
			$script[]="        jformfieldattachments_displayMessage(id,'".JText::_('JFORMFIELD_ATTACHMENTS_WEBSERVICE_ERROR')." :'+ex,'error');";
			$script[]="    }";
			$script[]="    if (dirname=='')	{";
			$script[]="        document.getElementById(id+'_browseCurrentDir').innerHTML=dirname;";
			$script[]="        document.getElementById(id+'_browsePath').innerHTML = '/';";
			$script[]="    } else {";
			$script[]="        document.getElementById(id+'_browseCurrentDir').innerHTML=dirname;";
			$script[]="        document.getElementById(id+'_browsePath').innerHTML = dirname;";
			$script[]="    }";
			$script[]="}";
			$script[]="function jformfieldattachments_uploadFiles(id, dir) {";
			$script[]="    var dlg = document.getElementById(id+'_browseDlg');";
			$script[]="    uploadinput = document.getElementById(id+'_uploadfileselect');";
			$script[]="    jquploadinput = jQuery('#'+id+'_uploadfileselect');";
			$script[]="    lst = document.getElementById(id+'_browseListe');";
			$script[]="    var files = uploadinput.files;";
			$script[]="    var formData = new FormData();";
			$script[]="    for (var i = 0; i < files.length; i++) {";
  			$script[]="        var file = files[i];";
			$script[]="        formData.append('uploadedfiles[]', file, file.name);";
			$script[]="    }";
			$script[]="    var dir=document.getElementById(id+'_browseCurrentDir').innerHTML;";
			$script[]="    formData.append('directory', dir);";
			$script[]="    img = document.createElement('img');";
			$script[]="    img.src='".$loaderimg."';";
			$script[]="    l = (dlg.clientWidth-100)/2;";
			$script[]="    h = (dlg.clientHeight-100)/2;";
			$script[]="    img.style.marginLeft= l+'px';";
			$script[]="    img.style.marginTop= h+'px';";
			$script[]="    lst.appendChild(img);";
			$script[]="    uploadinput.innerHTML='...'";
			$script[]="    try {";
			$script[]="        jQuery.ajax({";
			$script[]="        type: 'POST',";
			$script[]="        url: '".$uploadurl."',";
			$script[]="        data: formData,";
			$script[]="        dataType: 'json',";
			$script[]="        processData: false, // Don't process the files";
        	$script[]="        contentType: false,";
			$script[]="        success: function (data, textStatus, jqXHR){";
			$script[]="            sellist=data.selected;";
			$script[]="            selected={};";
			$script[]="            for(var item in sellist) { selected[sellist[item]]=sellist[item]; }";
			$script[]="            jquploadinput.replaceWith(jquploadinput.clone())";
			$script[]="    		   lst.removeChild(img);";
			$script[]="            jformfieldattachments_fillList(id,jformfieldattachments_curdir, selected);";
			$script[]="            jformfieldattachments_displayMessage(id,data.message,data.status);";
			$script[]="        },";
			$script[]="        error: function (msg,status) {";
			//$script[]="            jformfieldattachments_errorBox(id,'".JText::_('JFORMFIELD_ATTACHMENTS_WEBSERVICE_ERROR')." :'+status);";
			$script[]="            jformfieldattachments_displayMessage(id,'".JText::_('JFORMFIELD_ATTACHMENTS_WEBSERVICE_ERROR')." :'+status,'error');";
			$script[]="	       }});";
			$script[]="    } catch(ex) {";
			//$script[]="        jformfieldattachments_errorBox(id,'".JText::_('JFORMFIELD_ATTACHMENTS_WEBSERVICE_ERROR')." :'+ex);";
			$script[]="        jformfieldattachments_displayMessage(id,'".JText::_('JFORMFIELD_ATTACHMENTS_WEBSERVICE_ERROR')." :'+ex,'error');";
			$script[]="    }";
			$script[]="}";
			
			// Add the script to the document head.
			JFactory::getDocument()->addScriptDeclaration(implode("\n    ", $script));
			
			$css=array();
			$css[]=".jformfieldattachments_browseliste{ text-align:left; border:1px solid #4FBAB3; white-space:nowrap; font:normal 12px verdana; background:#fff;";
			$css[]="              padding:5px; overflow: auto;	margin-bottom:auto;	width:100%;	margin-bottom: 10px; height : 350px; z-index:1001;}";
			$css[]=".jformfieldattachments_browseliste a{	display:block;	cursor:default;	color:#000;	text-decoration:none;	background:#fff; vertical-align:center; width:100%}";
			$css[]=".jformfieldattachments_browseliste a:hover{ color:white; background-color:blue;}";
			$css[]=".jformfieldattachments_browsedlg .buttons {	margin-bottom:0px;	height:30px;}";
			$css[]=".jformfieldattachments_browsedlg .buttons .right {	float:right;}";
			$css[]=".jformfieldattachments_directory {	padding-left:16px;height:20px;}";
			$css[]=".jformfieldattachments_file { height:20px; }";
			$css[]=".jformfieldattachments_browsepath { width:100%;height:20px;background-color:white;border:1px solid #4FBAB3;padding-left:10px;margin-bottom:1px;}";
			$css[]=".jformfieldattachments_browseliste a .col-size,.jformfieldattachments_browseliste a .col-time { float:right; font-size:80%;}";
			$css[]=".jformfieldattachments_browseliste a .col-time { width:120px;   }";
			$css[]=".jformfieldattachments_browseliste a .col-size { width:70px;  }";
			$css[]=".jformfieldattachments_createFolderDlg input { width:90%;  }";
			$css[]=".jformfieldattachments_message { display:none; }";
			$css[]=".jformfieldattachments_message .ok { color:blue; }";
			$css[]=".jformfieldattachments_message .error { color:red; }";
			$css[]=".jformfieldattachments_message .warning { color:orange; }";
			$doc->addStyleDeclaration(implode("", $css));	
			
			self::$initialised = true;
		}
		
		// Initialize variables.
		
		$html = array();
		$html[]="<div id=\"".$this->id."_confirmeRemoveAttachment\" style=\"display:none\" title=\"".JText::_('JFORMFIELD_ATTACHMENTS_REMOVE_ATTACHMENT_TITLE')."\">";
		$html[]="  <div><div style=\"float:left\"><img src=\"".$messageboxquestionimg."\" /></div><div id=\"".$this->id."_content\" style=\"margin-left:70px;\">";
		$html[]="    ".JText::_('JFORMFIELD_ATTACHMENTS_REMOVE_ATTACMENT_DESC');
		$html[]="  </div><input type=\"hidden\" id=\"".$this->id."_confirmeRemoveAttachment_id\" value=\"\"></div>";
		$html[]="  <div id=\"".$this->id."_browseButtons\" class=\"buttons center\" style=\"margin-top:30px;\">";
		$html[]="    <button onclick=\"javascript:jformfieldattachments_confirmRemoveAttachment('".$this->id."');return false;\"><span class=\"icon-ok\"></span>".JText::_('JFORMFIELD_ATTACHMENTS_REMOVE')."</button>";
		$html[]="    <span class=\"icon-close\"></span><button onclick=\"javascript:jformfieldattachments_cancelRemoveAttachment('".$this->id."');return false;\"><img src=\"".JUri::base(true)."/components/com_hecmailing/assets/images/cancel16.png\" >".JText::_('JFORMFIELD_ATTACHMENTS_CANCEL')."</button>";
		$html[]="  </div>";
		$html[]="</div>";
		$html[]="<div id=\"".$this->id."_ErrorMessage\" style=\"display:none\" title=\"".JText::_('JFORMFIELD_ATTACHMENTS_ERROR_TITLE')."\">";
		$html[]="  <div style=\"min-height:70px\"><div style=\"float:left\"><img src=\"".$messageboxwarningimg."\" /></div>";
		$html[]="  <div id=\"".$this->id."_ErrorMessageContent\" class=\"content\"  style=\"margin-left:70px;\" ></div></div>";
		$html[]="  <div id=\"".$this->id."_browseButtons\" class=\"buttons align-center\">";
		$html[]="    <button onclick=\"javascript:jQuery('#".$this->id."_ErrorMessage').dialog('close');return false;\"><span class=\"icon-close\"></span>".JText::_('JFORMFIELD_ATTACHMENTS_OK')."</button>";
		$html[]="  </div>";
		$html[]="</div>";
		$html[]="<div id=\"".$this->id."_browseDlg\" style=\"display:none\" title=\"".JText::_('JFORMFIELD_ATTACHMENTS_BROWSE_FILES_TITLE')."\" class=\"jformfieldattachments_browsedlg\" >";
		$html[]="  <div class=\"content\" >";
   		$html[]="    <div id=\"".$this->id."_browsePath\" class=\"jformfieldattachments_browsepath\" >..</div>";
   		$html[]="    <div id=\"".$this->id."_browseCurrentDir\" style=\"display:none\">..</div>";
 		$html[]="    <div id=\"".$this->id."_browseListe\" class=\"jformfieldattachments_browseliste\" >.<br>..</div>";
  		$html[]="  </div>";
  		$html[]="<div id=\"".$this->id."_createFolderDlg\" style=\"display:none\" title=\"".JText::_('JFORMFIELD_ATTACHMENTS_CREATE_FOLDER_TITLE')."\" class=\"jformfieldattachments_createFolderDlg\" >";
  		$html[]="  <div style=\"float:left\"><img src=\"".$messageboxquestionimg."\" /></div>";
  		$html[]="  <div class=\"content\"  style=\"margin-left:70px;\" >";
  		$html[]="    <input type=\"text\" id=\"".$this->id."_createFolderText\" value=\"\" >";
  		$html[]="  </div>";
  		$html[]="  <div id=\"".$this->id."_browseButtons\" class=\"buttons align-center\">";
  		$html[]="    <button onclick=\"javascript:jformfieldattachments_confirmCreateFolder('".$this->id."');return false;\"><span class=\"icon-ok\"></span>".JText::_('JFORMFIELD_ATTACHMENTS_CREATEFOLDER')."</button>";
		$html[]="    <span class=\"icon-close\"></span><button onclick=\"javascript:jformfieldattachments_cancelCreateFolder('".$this->id."');return false;\"><img src=\"".JUri::base(true)."/components/com_hecmailing/assets/images/cancel16.png\" >".JText::_('JFORMFIELD_ATTACHMENTS_CANCEL')."</button>";
  		$html[]="  </div>";
  		$html[]="</div>";
  		$html[]="  <div id=\"".$this->id."_message\" class=\"jformfieldattachments_message\"><span class=\"message_icon\"></span><span class=\"message_text\"></span></div>";
  		$html[]="  <div  class=\"buttons\">";
    	$html[]="    <button onclick=\"javascript:jformfieldattachments_selectFile('".$this->id."');return false;\"><img src=\"".JUri::base(true)."/components/com_hecmailing/assets/images/ok16.png\" >".JText::_('JFORMFIELD_ATTACHMENTS_SELECT')."</button>";
    	$html[]="    <button onclick=\"javascript:jformfieldattachments_hideBrowse('".$this->id."');return false;\"><img src=\"".JUri::base(true)."/components/com_hecmailing/assets/images/cancel16.png\" >".JText::_('JFORMFIELD_ATTACHMENTS_CANCEL')."</button>";
    	$html[]="    <button id=\"".$this->id."_createFolderButton\" class=\"right\" onclick=\"javascript:jformfieldattachments_showCreateFolder('".$this->id."');return false;\"><span class=\"icon-new\"></span> ".JText::_('JFORMFIELD_ATTACHMENTS_CREATEFOLDER')."</button>";
    	$html[]="  <br/></div>";
    	$html[]="  <fieldset id=\"".$this->id."_uploadFieldset\"><legend>".JText::_('JFORMFIELD_ATTACHMENTS_UPLOAD_LEGEND')."</legend>";
    	$html[]="    <form id=\"".$this->id."_fileform\" action=\"".$uploadurl."\" method=\"POST\" ENCTYPE=\"multipart/form-data\">";
    	$html[]="      <input type=\"hidden\"  id=\"".$this->id."_directory\" name=\"".$this->id."_directory\" value=\"\" >";
    	$html[]="      <input type=\"file\" id=\"".$this->id."_uploadfileselect\" name=\"".$this->id."_uploadfileselect[]\" multiple/>";
    	$html[]="      <button onclick=\"javascript:jformfieldattachments_uploadFiles('".$this->id."');return false;\"><span class=\"icon-upload\"></span> ".JText::_('JFORMFIELD_ATTACHMENTS_UPLOAD_BUTTON')."</button>";
    	$html[]="    </form>";
    	$html[]="  </fieldset>";
		$html[]="</div>";
				
        $html[]="<div id='".$this->id."_container' class='".$class."'>";
        $html[]="<div id='".$this->id."_header' class='".$class."_header'>";
    	//$html[]="<button onclick='javascript:jformfieldattachments_addAttachment(\"".$this->id."\");return false;'><span class=\"icon-upload\"></span> ".$lib_add_attachment."</button>";
		$html[]="<button onclick='javascript: jformfieldattachments_showBrowse(\"".$this->id."\"); return false;' /><span class=\"icon-search\"></span> ".$lib_browse."</button>";
		//$html[]=$media->renderField();
    	$html[]="</div><div id='".$this->id."_list' class=''".$class."_list'>";
    	$html[]="<table id='".$this->id."_table' class='".$class."_table' style=\"width:100%\" >";
    	$html[]="<tbody id='".$this->id."_attachmentbody' >";
    	$ilocal=0;
    	$iattach=0;
    	if ($this->value)
    	foreach($this->value as $attachment)
    	{
    		$ilocal++;
    		$html[]="<tr><td><input type=\"hidden\" name=\"".$this->name.$ilocal."\" id=\"".$this->id.$ilocal."\" value=\"".$attachment."\"><input type=\"checkbox\" name=\"chklocal".$ilocal."\" id=\"chklocal".$ilocal."\" checked=\"checked\">".$attachment."</td></tr>";
    		
    	}
    	/*$iattach=0 ;
    	for ($i=0;$i<$this->upload_input_count;$i++)
    	{
    		$iattach++;
    		echo"<tr><td><input type=\"file\" name=\"attach".$iattach."\" id=\"attach".$iattach."\" ></td></tr>";
    
	    }*/
  		$html[]="</tbody></table></div></div>";
  		$html[]=" <input type='hidden' name='".$this->id."_localcount' id='".$this->id."_localcount' value='".$ilocal."'><input type='hidden' name='".$this->id."_attachmentcount' id='".$this->id."_attachmentcount' value='".$ilocal."'>";
  		 
		return implode("\n",$html);
	}
	
	private function startsWith($haystack, $needle) {
		// search backwards starting from haystack length characters from the end
		return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
	}
	
	private function endsWith($haystack, $needle) {
		// search forward starting from end minus needle length characters
		return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
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
			$value= $this->element[$attr_name];
		}
		else
		{
			$value= $default;
		}
		
		if ($this->startsWith($value, "param:"))
		{
			$tmp = explode(":", $value);
			$paramname=$tmp[1];
			if (count($tmp)==3)
				$default=$tmp[2];
			$value=$this->params->get($paramname, $default);
		}
		return $value;
	}
}