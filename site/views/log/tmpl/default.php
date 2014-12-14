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
	function tableOrdering( order, dir, task )
	{
		var form = document.adminForm;
		form.filter_order.value = order;
		form.filter_order_Dir.value = dir;
		document.adminForm.submit( task );
	}
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

<!--
	function setgood() {
		// TODO: Put setGood back
		return true;
	}

	function dellog() {
		var theform = document.getElementById("adminForm");
		var thetask = document.getElementById("task");
		if (confirm ('<?php echo JText::_("COM_HECMAILING_CONFIRM_DEL_LOG") ?>'))
		{
			thetask.value = "dellog";
			theform.submit();
			return;
		}
	}
//-->
</script>
<table width="100%">
	<tr><td>
		<div class="componentheading"><?php echo JText::_('COM_HECMAILING_MAILING_LOG'); ?></div>
		<div id="component-hecmailing"></div>

		<table class="toolbar"><tr>
			<?php if ($this->show_mail_sent) { ?>
			<td class="button" id="User Toolbar-log">
				<a href="index.php?option=com_hecmailing" class="toolbar">
				<span class="icon-32-back" title="<?php echo JText::_('COM_HECMAILING_BACK')?>"></span><?php echo JText::_('COM_HECMAILING_BACK'); ?></a>
			</td>
			<?php } ?>
			<td class="button" id="User Toolbar-log">
				<a onclick="javascript:dellog();return false;" class="toolbar">
				<span class="icon-32-delete" title="<?php echo JText::_('COM_HECMAILING_DELETE')?>"></span><?php echo JText::_('COM_HECMAILING_DELETE'); ?></a>
			</td>	
		</tr></table>
<hr>
</td></tr>
<tr><td>
<form Method="POST" Action="<?php echo JURI::current(); ?>" name="adminForm" id="adminForm" onSubmit="setgood();" enctype="multipart/form-data">
	<table id="filter" width="100%" ><tr>
		<td align="left"><?php echo JText::_("COM_HECMAILING_GROUP"); ?> : <?php echo $this->groupes; ?></td>
		<td><?php echo JText::_( 'COM_HECMAILING_FILTER' ); ?>: <input type="text" height="1" name="search2" id="search2" value="<?php echo $this->lists['search2']; ?>"  onchange="document.adminForm.submit();" /></td>
		<td><input type="submit" name="filter" id="filter" value="<?php echo JText::_("COM_HECMAILING_FILTER"); ?>"></td>
	</tr></table>
<table id="list" width="100%">
	<thead><tr>
		<th width="10" class="title"><?php echo JText::_( 'COM_HECMAILING_NUM' ); ?>&nbsp;</th><th width="10" class="title">
			<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->logs); ?>);" />&nbsp;</th>
		<th width="10"><?php echo JText::_("COM_HECMAILING_ID_LOG"); ?>&nbsp;</th>
		<th class="title"><a href="javascript:tableOrdering('u.username','desc','');" title="<?php echo JText::_('COM_HECMAILING_SORT_MSG'); ?>"><?php echo JText::_("COM_HECMAILING_USERNAME"); ?></a>&nbsp;</th>
		<th class="title"><a href="javascript:tableOrdering('l.log_dt_sent','desc','');" title="<?php echo JText::_('COM_HECMAILING_SORT_MSG'); ?> "><?php echo JText::_("COM_HECMAILING_DATE_SEND"); ?></a>&nbsp;</th>
		<th class="title"><a href="javascript:tableOrdering('l.log_vl_subject','desc','');" title="<?php echo JText::_('COM_HECMAILING_SORT_MSG'); ?> "><?php echo JText::_("COM_HECMAILING_SUBJECT"); ?></a>&nbsp;</th>
		<th class="title"><a href="javascript:tableOrdering('l.log_vl_mailok','desc','');" title="<?php echo JText::_('COM_HECMAILING_SORT_MSG'); ?> "><?php echo JText::_("COM_HECMAILING_SEND_OK"); ?></a>&nbsp;</th>
		<th class="title"><a href="javascript:tableOrdering('l.log_vl_mailerr','desc','');" title="<?php echo JText::_('COM_HECMAILING_SORT_MSG'); ?> "><?php echo JText::_("COM_HECMAILING_SEND_ERR"); ?></a>&nbsp;</th>
	</tr></thead>
	<tfoot><tr><td colspan="7"><?php echo $this->pagination->getListFooter(); ?></td></tr></tfoot>
	<tbody>
	<?php
		$classrow='row1';
		$no=$this->pagination->limitstart; 
		for ($i=0, $n=count($this->logs); $i < $n; $i++) {
			$obj=$this->logs[$i];
			$no++;
			if ($classrow=='row1')
				$classrow = 'row2';
			else
			    $classrow= 'row1';
		$detail = 'index.php?option=com_hecmailing&idlog='.$obj->log_id_message.'&task=viewlog';
		$checked = JHTML::_('grid.id', $i, $obj->log_id_message );
	?>

		<tr class="<?php echo $classrow; ?>" >
			<td width="5px"><?php echo $this->pagination->getRowOffset( $i ); ?></td>
			<td width="5px"><?php echo $checked; ?></td>
			<td><a href="<?php echo $detail; ?>" ><?php echo $obj->log_id_message; ?></a></td>
			<td><?php echo $obj->username; ?></td><td><?php echo $obj->log_dt_sent; ?></td><td><a href="<?php echo $detail; ?>" ><?php echo $obj->log_vl_subject; ?></a></td>
			<td><?php echo $obj->log_nb_ok; ?></td><td><?php echo $obj->log_nb_errors; ?></td></tr>
	<?php  } ?>
	</tbody>
 </table>
<input type="hidden" name="task" id="task" value="log" >
<input type="hidden" name="option" value="com_hecmailing" >
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />	
<?php echo JHTML::_( 'form.token' ); ?>	
</form>
</td></tr></table>
