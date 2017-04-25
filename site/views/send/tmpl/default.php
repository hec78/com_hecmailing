<?php 
/**
* @version 3.4.0
* @package hecMailing for Joomla
* @module views.send.tmpl.default.php
* @subpackage : View Send (Sending mail form)
* @author : Hervé CYR
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
*
*******************************************************************************/
defined ('_JEXEC') or die ('restricted access'); 
jimport('joomla.html.html');
// Modif Joomla 1.6+
$app = JFactory::getApplication();
$document = JFactory::getDocument();
// Modif pour J1.6+ : change $app->addCustomHeadTag en   $document->addCustomTag
$document->addCustomTag('<link rel="stylesheet" href="components/com_hecmailing/assets/css/toolbar.css" type="text/css" media="screen" />');
$document->addCustomTag('<link rel="stylesheet" href="components/com_hecmailing/assets/css/send.css" type="text/css" media="screen" />');
$document->addCustomTag ('<link rel="stylesheet" href="administrator/components/com_hecmailing/libraries/jquery-ui.min.css" type="text/css" />');
$document->addCustomTag ('<link rel="stylesheet" href="administrator/components/com_hecmailing/libraries/jquery-ui.theme.min.css" type="text/css" />');
// load JQuery if Joomla < 3.0.0
if(version_compare(JVERSION,'3.0.0','<')){
	$document->addScript("administrator/components/com_hecmailing/libraries/jquery.min.js");
}
$document->addCustomTag ('<script src="administrator/components/com_hecmailing/libraries/jquery-ui.min.js" type="text/javascript"></script>');
$document->addCustomTag ('<script src="components/com_hecmailing/views/send/send.js" type="text/javascript"></script>');
if ( version_compare( JVERSION, '3.4', '<' ) == 1) {
	JHTML::_('behavior.formvalidation');
}
else {
	JHTML::_('behavior.formvalidator');
}
?>

<style type="text/css">


</style>

<div id="loadtmpl" style="display:none" title="<?php echo JText::_('COM_HECMAILING_LOAD_TEMPLATE'); ?>" >
  <div class="image"><img src="components/com_hecmailing/assets/images/disk.gif" width="64px"></div>
  <div class="content"><br/>
    <?php echo JText::_('COM_HECMAILING_SELECT_TEMPLATE_BELOW'); ?><br/><br/>
    <?php echo JText::_('COM_HECMAILING_TEMPLATE')."&nbsp;:&nbsp;".$this->saved;?><br/><br/>
  </div>
  <div class="buttons">
    <button onclick="javascript:loadTemplate();return false;">
      <img src="components/com_hecmailing/assets/images/ok16.png" ><?php echo JText::_('COM_HECMAILING_LOAD'); ?>
    </button>
    <button onclick="javascript:cancel();return false;">
      <img src="components/com_hecmailing/assets/images/cancel16.png" ><?php echo JText::_('COM_HECMAILING_CANCEL'); ?>
    </button>
  </div>
</div>
<div id="savetmpl" style="display:none"  title="<?php echo JText::_('COM_HECMAILING_SAVE_TEMPLATE'); ?>">
  <div class="image"><img src="components/com_hecmailing/assets/images/disk.gif" width="64px"></div>
  <div class="content"><br/>
    <?php echo JText::_('COM_HECMAILING_SAVE_TEMPLATE_INFO'); ?><br/><br/>
    <?php echo JText::_('COM_HECMAILING_SAVE_TEMPLATE_LABEL') ?><input type="text" id="tmplName" Name="tmplName" /><br/><br/>
  </div>
  <div class="buttons">
    <button onclick="javascript:saveTemplate();return false;">
      <img src="components/com_hecmailing/assets/images/ok16.png" ><?php echo JText::_('COM_HECMAILING_SAVE_TEMPLATE_BUTTON'); ?>
    </button>
    <button onclick="javascript:cancelSaveTmpl();return false;">
      <img src="components/com_hecmailing/assets/images/cancel16.png" ><?php echo JText::_('COM_HECMAILING_CANCEL'); ?>
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
      		<img src="components/com_hecmailing/assets/images/ok16.png" ><?php echo JText::_('COM_HECMAILING_LOAD'); ?>
    	</button>
    	<button onclick="javascript:hideBrowse();return false;">
    	  	<img src="components/com_hecmailing/assets/images/cancel16.png" ><?php echo JText::_('COM_HECMAILING_CANCEL'); ?>
    	</button>
  	</div>
</div>
<div id="ErrorMessage" style="display:none" title="<?php echo JText::_('COM_HECMAILING_MSG_ERROR_TITLE') ?>" >
	<div style="float:left"><img src="components/com_hecmailing/assets/images/messagebox_warning.png" /></div><div class="content" style="margin-left:70px"></div><br/>
	<div class="buttons center">
		<button onclick="javascript:jQuery('#ErrorMessage').dialog('close');return false;"><span class="icon-close"></span><?php echo JText::_('COM_HECMAILING_MSG_ERROR_CLOSE')?></button>
	</div>
</div>

<script type="text/javascript">
var base_dir='<?php echo $this->browse_path; ?>';
var text_msg_select_group = '<?php echo JText::_('COM_HECMAILING_MSG_SELECT_GROUP'); ?>';
var text_msg_empty_subject = '<?php echo JText::_('COM_HECMAILING_MSG_EMPTY_SUBJECT'); ?>';
var text_manage = '<?php JText::_('MANAGE_GROUP') ?>';
var text_msg_tmplname_empty= '<?php echo JText::_('COM_HECMAILING_MSG_EMPTY_TEMPLATE_NAME'); ?>';
var current_group=0;
var currentURI = '<?php echo JURI::base(false); ?>';
var baseURI = '<?php echo JURI::base( true ); ?>';
<?php echo $this->rights; ?>
</script>


<form action="<?php echo JURI::current(); ?>" method="post" name="adminForm" id="adminForm" ENCTYPE="multipart/form-data" class="form-validate">
<input type="hidden" name="option" id="option" value="com_hecmailing">
<input type="hidden" name="view" id="view" value="">
<input type="hidden" name="task" id="task" value="">
<input type="hidden" name="idTemplate" id="idTemplate" value="0">
<input type="hidden" name="saveTemplate" id="saveTemplate" value=""><?php echo JHTML::_( 'form.token' ); ?>
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
<div>
	<?php
	// Iterate through the fields and display them.
	foreach($this->form->getFieldset('basic') as $field):
    // If the field is hidden, only use the input.
	    if ($field->hidden):
	        echo $field->input;
	    else:
	    ?>
	    <dl class="dl-horizontal">
	    <dt>
	        <?php echo $field->label; ?>
	    </dt>
	    <dd<?php echo ($field->type == 'Editor' || $field->type == 'Textarea') ? ' style="clear: both; margin: 0;"' : ''?>>
	        <?php echo $field->input ?>
	    </dd>
	    </dl>
	    <?php
	    endif;
	endforeach;
?>
</div>


</form>



