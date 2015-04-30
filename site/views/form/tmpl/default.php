<?php 
/**
* @version 3.1.0
* @package hecMailing for Joomla
* @module views.form.tmpl.default.php
* @subpackage : View Form (Sending mail form)
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
defined ('_JEXEC') or die ('restricted access'); 
jimport('joomla.html.html');
// Modif Joomla 1.6+
$mainframe = JFactory::getApplication();
$document = JFactory::getDocument();
// Modif pour J1.6+ : change $mainframe->addCustomHeadTag en   $document->addCustomTag
$document->addCustomTag('<link rel="stylesheet" href="components/com_hecmailing/css/toolbar.css" type="text/css" media="screen" />');
$document->addCustomTag ('<link rel="stylesheet" href="administrator/components/com_hecmailing/libraries/jquery-ui.css" type="text/css" />');
if(version_compare(JVERSION,'3.0.0','<')){
	$document->addScript("administrator/components/com_hecmailing/libraries/jquery-1.11.1.min.js");
}
$document->addCustomTag ('<script src="administrator/components/com_hecmailing/libraries/jquery-ui.min.js" type="text/javascript"></script>');
$document->addCustomTag ('<script src="components/com_hecmailing/views/form/form.js" type="text/javascript"></script>');

?>

<style type="text/css">
.liste{
	text-align:left;
	border:1px solid #4FBAB3;
	white-space:nowrap;
	font:normal 12px verdana;
	background:#fff;
  	padding:5px;
  	overflow: auto;
	margin-bottom:auto;
	width:100%;	
	margin-bottom: 10px;
	height : 450px;
}

.liste a{
	display:block;
	cursor:default;
	color:#000;
	text-decoration:none;
	background:#fff;
}

.liste a:hover{
	color:white;
	background-color:#7370CB;
}

#browseDlg .buttons {
	margin-bottom:0px;
	height:30px;
}

#savetmpl {
	font-size : 10pt;
}
#loadtmpl {
	font-size : 10pt;
}

</style>
<div id="dialogManageGroup" title="<?php echo JText::_('COM_HECMAILING_MANAGE_GROUP_TITLE'); ?>" style="display:none" ></div>
<div id="loadtmpl" style="display:none" title="<?php echo JText::_('COM_HECMAILING_LOAD_TEMPLATE'); ?>" >
  <div class="image"><img src="components/com_hecmailing/images/disk.gif" width="64px"></div>
  <div class="content"><br/>
    <?php echo JText::_('COM_HECMAILING_SELECT_TEMPLATE_BELOW'); ?><br/><br/>
    <?php echo JText::_('COM_HECMAILING_TEMPLATE')."&nbsp;:&nbsp;".$this->saved;?><br/><br/>
  </div>
  <div class="buttons">
    <button onclick="javascript:loadTemplate();return false;">
      <img src="components/com_hecmailing/images/ok16.png" ><?php echo JText::_('COM_HECMAILING_LOAD'); ?>
    </button>
    <button onclick="javascript:cancel();return false;">
      <img src="components/com_hecmailing/images/cancel16.png" ><?php echo JText::_('COM_HECMAILING_CANCEL'); ?>
    </button>
  </div>
</div>
<div id="savetmpl" style="display:none"  title="<?php echo JText::_('COM_HECMAILING_SAVE_TEMPLATE'); ?>">
  <div class="image"><img src="components/com_hecmailing/images/disk.gif" width="64px"></div>
  <div class="content"><br/>
    <?php echo JText::_('COM_HECMAILING_SAVE_TEMPLATE_INFO'); ?><br/><br/>
    <?php echo JText::_('COM_HECMAILING_SAVE_TEMPLATE_LABEL') ?><input type="text" id="tmplName" Name="tmplName" /><br/><br/>
  </div>
  <div class="buttons">
    <button onclick="javascript:saveTemplate();return false;">
      <img src="components/com_hecmailing/images/ok16.png" ><?php echo JText::_('COM_HECMAILING_SAVE_TEMPLATE_BUTTON'); ?>
    </button>
    <button onclick="javascript:cancelSaveTmpl();return false;">
      <img src="components/com_hecmailing/images/cancel16.png" ><?php echo JText::_('COM_HECMAILING_CANCEL'); ?>
    </button>
  </div>
</div>

<div id="browseDlg" style="display:none" title="<?php echo JText::_('COM_HECMAILING_BROWSE_FILES'); ?>">
   	<div class="content" >
   		<div id="browsePath" style="height:20px;background-color:grey;">..</div>
   		<div id="browseCurrentDir" style="display:none">..</div>
 		<div id="browseListe" class="liste" >.<br>..</div>
  	</div>
  	<div id="browseButtons" class="buttons">
    	<button onclick="javascript:selectFile();return false;">
      		<img src="components/com_hecmailing/images/ok16.png" ><?php echo JText::_('COM_HECMAILING_LOAD'); ?>
    	</button>
    	<button onclick="javascript:hideBrowse();return false;">
    	  	<img src="components/com_hecmailing/images/cancel16.png" ><?php echo JText::_('COM_HECMAILING_CANCEL'); ?>
    	</button>
  	</div>
</div>


<script language="javascript" type="text/javascript">
var base_dir='<?php echo $this->browse_path; ?>';
var text_msg_select_group = '<?php echo JText::_('COM_HECMAILING_MSG_SELECT_GROUP'); ?>';
var text_msg_empty_subject = '<?php echo JText::_('COM_HECMAILING_MSG_EMPTY_SUBJECT'); ?>';
var text_manage = '<?php JText::_('MANAGE_GROUP') ?>';
var text_msg_tmplname_empty= '<?php echo JText::_('COM_HECMAILING_MSG_EMPTY_TEMPLATE_NAME'); ?>';
var current_group=0;
var currentURI = '<?php echo JURI::base(false); ?>';
var baseURI = '<?php echo JURI::base( true ); ?>';


function submitbutton2(pressbutton) {
    myform = document.getElementById("adminForm");;
    mytask = document.getElementById("task");
    if (pressbutton) {
      mytask.value=pressbutton;
    }
    if (typeof myform.onsubmit == "function") {
        myform.onsubmit();
    }

    if (pressbutton == 'cancel') {
        myform.submit();
        return;
    }
  <?php
    $editor =JFactory::getEditor();
    echo $editor->save( 'body' );
  ?>
  myform.submit();
}

<?php echo $this->rights; ?>
</script>


<form action="<?php echo JURI::current(); ?>" method="post" name="adminForm" id="adminForm" ENCTYPE="multipart/form-data">
<div class="componentheading"><?php echo JText::_('COM_HECMAILING_MAILING'); ?></div>
<div id="component-hecmailing">

</div>
<table class="toolbar"><tr>
<td class="button" id="User Toolbar-send">
  <a href="#" onclick="javascript: checksend();return false;"  class="toolbar">
    <span class="icon-32-send" title="<?php echo JText::_('COM_HECMAILING_SEND_INFO'); ?>"></span><?php echo JText::_('COM_HECMAILING_SEND'); ?></a></td>
<td class="button" id="User Toolbar-cancel">
  <a href="#" onclick="javascript: submitbutton('cancel');return false;"  class="toolbar">
    <span class="icon-32-cancel" title="<?php echo JText::_('COM_HECMAILING_CANCEL_MSG'); ?>"></span><?php echo JText::_('COM_HECMAILING_CANCEL'); ?></a></td>
<td class="spacer"></td><td class="spacer"></td><td class="spacer"></td><td class="spacer"></td>            
<td class="button" id="User Toolbar-save">
  <a href="#" onclick="javascript: showSaveTemplate();return false;"  class="toolbar">
    <span class="icon-32-save" title="<?php echo JText::_('COM_HECMAILING_SAVE_INFO'); ?>"></span><?php echo JText::_('COM_HECMAILING_SAVE'); ?></a></td>
<td class="button" id="User Toolbar-archive">
  <a href="#" onclick="javascript: showLoadTemplate();return false;"  class="toolbar">
    <span class="icon-32-archive" title="<?php echo JText::_('COM_HECMAILING_LOAD_TEMPLATE')?>"></span><?php echo JText::_('COM_HECMAILING_LOAD_TEMPLATE'); ?></a>
</td>
<td>&nbsp;</td>
<?php  if ($this->show_mail_sent) { ?>
<td class="button" id="User Toolbar-log">
  <a href="index.php?option=com_hecmailing&task=log" class="toolbar">
    <span class="icon-32-log" title="<?php echo JText::_('COM_HECMAILING_SHOW LOG')?>"></span><?php echo JText::_('COM_HECMAILING_SHOW_LOG'); ?></a>
</td>
<?php  } ?>
</tr></table> 
<?php 
  echo $this->msg;

?>
<hr><br>
<table width="100%" class="admintable" style="valign:top">

  <tr valign="top"><td class="key"><?php echo JText::_('COM_HECMAILING_SENDER'); ?> :</td><td><?php echo $this->from; ?></td></tr>
  
  <tr valign="top"><td class="key"><?php echo JText::_('COM_HECMAILING_GROUP'); ?> :</td><td><?php echo $this->groupes; ?><button id="manage_button" style="visibility:hidden;" onClick="manage_group();return false;"><?php echo JText::_('COM_HECMAILING_MANAGE_GROUP_BUTTON'); ?></button></td></tr>
  
  <tr valign="top"><td class="key"><?php echo JText::_('COM_HECMAILING_USE_PROFILE'); ?> :</td><td><input type="checkbox" name="useprofil" value="1" id="useprofil" <?php echo $this->default_use_profil; ?>> <small><?php echo JText::_('COM_HECMAILING_USE_PROFILE_TEXT'); ?></small></td></tr>
  
<!--   <tr valign="top"><td class="key"><?php echo JText::_('COM_HECMAILING_BACKUP_MAIL'); ?> :</td><td><input type="checkbox" name="backup_mail" value="1" id="backup_mail" <?php echo $this->backup_mail; ?>> <small><?php echo JText::_('COM_HECMAILING_BACKUP_MAIL_TEXT'); ?></small></td></tr>-->  
  <tr valign="top"><td class="key"><?php echo JText::_('COM_HECMAILING_CONTENT_IMAGE'); ?> :</td><td><input type="checkbox" name="incorpore" value="1" id="incorpore" <?php echo $this->image_incorpore; ?>> <small><?php echo JText::_('COM_HECMAILING_CONTENT_IMAGE_TEXT'); ?></small></td></tr>
  
  <tr valign="top"><td class="key"><?php echo JText::_('COM_HECMAILING_SUBJECT'); ?> :</td><td><input style="width:100%"  type="text" id="subject" name="subject" value="<?php echo $this->subject; ?>"></td></tr>
  
  <tr valign="top"><td class="key"><?php echo JText::_('COM_HECMAILING_ATTACHMENT'); ?> :</td><td>
  <table id="attachment" name="attachment">
    <tbody id="attachmentbody" name="attachmentbody">
    	<tr><td>
    		<button onclick="javascript:addAttachment();return false;"><?php echo JText::_('COM_HECMAILING_ADD_ATTACHMENT'); ?></button>
    		
    		<input type="button" value="<?php echo JText::_('COM_HECMAILING_BROWSE_SERVER'); ?>" onclick="javascript: showBrowse(); return false;" />
    	</td></tr>
  
  
  <?php
    $ilocal=0;
    foreach($this->attachment as $file)
    {
    	$ilocal++;
    	echo"<tr><td><input type=\"hidden\" name=\"local".$ilocal."\" id=\"local".$ilocal."\" value=\"".$file."\"><input type=\"checkbox\" name=\"chklocal".$ilocal."\" id=\"chklocal".$ilocal."\" checked=\"checked\">".$file."</td></tr>";
    }
    $iattach=0 ;
    for ($i=0;$i<$this->upload_input_count;$i++)
    {
    	$iattach++;
    	echo"<tr><td><input type=\"file\" name=\"attach".$iattach."\" id=\"attach".$iattach."\" ></td></tr>";
    
    }
  ?>
  </tbody></table>
  </td></tr>
  <tr valign="top"><td class="key"><?php echo JText::_('COM_HECMAILING_BODY'); ?> :</td><td>
  <?php
  $editor =JFactory::getEditor();
  echo $editor->display('body', $this->body, $this->width, $this->height, '60', '20', true);
  ?>
  <input type="hidden" name="localcount" id="localcount" value="<?php echo $ilocal; ?>"><input type="hidden" name="attachcount" id="attachcount" value="<?php echo $iattach; ?>"></td></tr>
</table>
<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="option" id="option" value="com_hecmailing">
<!--<input type="hidden" name="view" id="view" value="form">-->
<input type="hidden" name="task" id="task" value="">
<input type="hidden" name="idTemplate" id="idTemplate" value="0">
<input type="hidden" name="saveTemplate" id="saveTemplate" value="">
</form>



