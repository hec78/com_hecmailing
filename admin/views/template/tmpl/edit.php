<?php
/**
* @version   3.4.0
* @package   HEC Mailing for Joomla
* @copyright Copyright (C) 1999-2017 Hecsoft All rights reserved.
* @author    Herve CYR
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

defined('_JEXEC') or die;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHTML::_('behavior.tooltip');
//jimport('joomla.html.pane');
//$pane = &JPane::getInstance('sliders', array('allowAllClose' => true));
JFilterOutput::objectHTMLSafe( $row, ENT_QUOTES, 'misc' );
$cparams = JComponentHelper::getParams ('com_hecmailing');
$document = JFactory::getDocument();
$burl = "../";
$document->addStyleSheet($burl."components/com_hecmailing/css/hecmailing.css");
?>
<?php $data=$this->item; ?>
	<form action="<?php echo JRoute::_('index.php?option=com_hecmailing&view=template&layout=edit&id='.(int) $this->item->msg_id_message); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
		<div class="col">
		<table class="admintable">
			<tr><td class="key"><label for="name"><?php echo JText::_( 'COM_HECMAILING_TEMPLATE_ID' ); ?>:</label></td>
				<td><?php echo $data->msg_id_message; ?><input type="hidden" name="msg_id_message" id="msg_id_message" value="<?php echo $this->id; ?>" ></td></tr>
			<tr><td class="key"><label for="name"><?php echo JText::_( 'COM_HECMAILING_TEMPLATE_NAME' ); ?>:</label></td>
				<td><input class="inputbox" type="text" name="msg_lb_message" id="msg_lb_message" size="30" maxlength="30" value="<?php echo $data->msg_lb_message; ?>" /></td></tr>
			<tr><td class="key"><label for="name"><?php echo JText::_( 'COM_HECMAILING_CONTACT_PUBLISH' ); ?>:</label></td>
				<td><?php echo $this->published; ?></td></tr>
			<tr><td class="key"><label for="name"><?php echo JText::_( 'COM_HECMAILING_GROUP' ); ?>:</label></td>
				<td ><?php  echo $this->hecgroups; ?></td></tr>
		
		</table>
		<hr>
		<table>
			<tr><td class="key"><label for="name"><?php echo JText::_( 'COM_HECMAILING_TEMPLATE_SUBJECT' ); ?>:</label></td>
				<td><input class="inputbox" type="text" name="msg_vl_subject" id="msg_vl_subject" size="30" maxlength="30" value="<?php echo $data->msg_vl_subject; ?>" /></td></tr>
			<tr><td class="key"><label for="name"><?php echo JText::_( 'COM_HECMAILING_TEMPLATE_BODY' ); ?>:</label></td>
				<td><?php echo $this->editor->display('msg_vl_body', $data->msg_vl_body, 400, 200, '60', '20', true); ?></td></tr>
			<tr><td class="key"><label for="name"><?php echo JText::_( 'COM_HECMAILING_TEMPLATE_FROM' ); ?>:</label></td>
				<td><input class="inputbox" type="text" name="msg_vl_from" id="msg_vl_from" size="30" maxlength="30" value="<?php echo $data->msg_vl_from; ?>" /></td></tr>
		</table>
		</div>
		<div class="clr"></div>
		<input type="hidden" name="option" value="com_hecmailing" />
		<input type="hidden" name="id" value="<?php echo $data->msg_id_message; ?>" />
		<input type="hidden" name="cid[]" value="<?php echo $data->msg_id_message; ?>" />
		<input type="hidden" name="task" value="" />
		<?php echo JHTML::_( 'form.token' ); ?>
</form>


