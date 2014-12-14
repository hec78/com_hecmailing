<?php 
/**
 * @version 1.8.2
 * @package hecmailing
 * @copyright 2009-2013 Hecsoft.net
 * @license http://www.gnu.org/licenses/gpl-3.0.html
 * @link http://joomla.hecsoft.net
 * @author H Cyr
 **/
 
defined ('_JEXEC') or die ('restricted access'); 
jimport('joomla.html.html');
// Modif Joomla 1.6+
$mainframe = JFactory::getApplication();
$document = JFactory::getDocument();
// Modif pour J1.6+ : change $mainframe->addCustomHeadTag en   $document->addCustomTag
$document->addCustomTag ('<link rel="stylesheet" href="components/com_hecmailing/css/toolbar.css" type="text/css" media="screen" />');
$document->addCustomTag ('<link rel="stylesheet" href="components/com_hecmailing/css/dialog.css" type="text/css" media="screen" />');
?>

<script language="javascript" type="text/javascript">
<!--
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
			$editor =& JFactory::getEditor();
			echo $editor->save( 'body' );
		?>
		alert('submit task='+document.getElementById("task").value);
		myform.submit();
	}

	function showLoadTemplate()
	{
        document.getElementById("loadtmpl").style.visibility = "visible";
        return false;  
    }
	function cancel()
    {
		document.getElementById("loadtmpl").style.visibility = "hidden";
    }
//-->
</script>
<div>
<form action="index.php" method="post" name="adminForm" id="adminForm" ENCTYPE="multipart/form-data">
<div class="componentheading"><?php echo JText::_('COM_HECMAILING_MAILING_LOG_DETAIL'); ?></div>
<div id="component-hecmailing">
</div>
<table class="toolbar"><tr>
<td class="button" id="User Toolbar-log">
  <a href="index.php?option=com_hecmailing&task=log" class="toolbar">
    <span class="icon-32-log" title="<?php echo JText::_('COM_HECMAILING_SHOW LOG')?>"></span><?php echo JText::_('COM_HECMAILING_SHOW_LOG'); ?></a>
</td>
<td>&nbsp;</td>

<td class="button" id="User Toolbar-log">
  <a href="index.php?option=com_hecmailing&idlog=<?php echo $this->idlog; ?>" class="toolbar">
    <span class="icon-32-send" title="<?php echo JText::_('COM_HECMAILING_SEND_AGAIN')?>"></span><?php echo JText::_('COM_HECMAILING_SEND_AGAIN'); ?></a>
</td>
</tr></table> 

<?php echo $this->msg;
	$data=$this->data; 
	if ($data)
	{
		$mailok =$data->log_vl_mailok; 
		$okformatte = str_replace(";","; ",$mailok);
		$mailerr =$data->log_vl_mailerr; 
		$errformatte = str_replace(";","; ",$mailerr);
	}
	if ($data)
	{
?>
<hr><br>
<table width="100%" class="admintable" style="valign:top">
<tr valign="top"><td nowrap class="key"><?php echo JText::_('COM_HECMAILING_DATE_SEND'); ?>:</td><td><?php echo $data->log_dt_sent; ?></td></tr>
<tr valign="top"><td nowrap class="key"><?php echo JText::_('COM_HECMAILING_SENDER'); ?>:</td><td><?php echo $data->log_vl_from; ?></td></tr>
<tr valign="top"><td nowrap class="key"><?php echo JText::_('COM_HECMAILING_GROUP'); ?>:</td><td><?php echo $data->grp_nm_groupe; ?></td></tr>
<tr valign="top"><td nowrap class="key"><?php echo JText::_('COM_HECMAILING_SEND_OK'); ?>:</td><td><?php echo $okformatte; ?></td></tr>
<tr valign="top"><td nowrap class="key"><?php echo JText::_('COM_HECMAILING_SEND_ERR'); ?>:</td><td><?php echo $errformatte; ?></td></tr>
<tr valign="top"><td nowrap class="key"><?php echo JText::_('COM_HECMAILING_SUBJECT'); ?>:</td><td><?php echo $data->log_vl_subject; ?></td></tr>
<tr valign="top"><td nowrap class="key"><?php echo JText::_('COM_HECMAILING_ATTACHMENT'); ?>:</td><td><?php
	echo "<i>";
	foreach ($data->attachment as $file)
	{
		echo $file . "<br>";
	}
	echo "</i>";
	?></td></tr>
<tr valign="top"><td class="key"><?php echo JText::_('COM_HECMAILING_BODY'); ?> :</td><td>
<?php
echo $data->log_vl_body;
?>
</td></tr>
</td></tr></table>
<?php 
}
else
{
	echo "<span class=\"error\">Le message n'existe pas</span>";
}
echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="option" id="option" value="com_hecmailing">
<input type="hidden" name="task" id="task" value="">
</form>
</div>

