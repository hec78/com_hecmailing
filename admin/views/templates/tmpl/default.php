<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
$document =JFactory::getDocument();
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');
//$document->addScript("components/com_hecmailing/assets/css/hecmailing.js");
$user		= JFactory::getUser();
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$sortFields = $this->getSortFields();

JText::script('COM_HECMAILING_CONTACTS_CONFIRM_DELETE');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'templates.delete')
		{
			var f = document.adminForm;
			var cb='';
<?php foreach ($this->items as $i => $item):?>
<?php if ($item->grp_nb_item > 0):?>
			cb = f['cb'+<?php echo $i;?>];
			if (cb && cb.checked)
			{
				if (confirm(Joomla.JText._('COM_HECMAILING_TEMPLATES_CONFIRM_DELETE')))
				{
					Joomla.submitform(task);
				}
				return;
			}
<?php endif;?>
<?php endforeach;?>
		}
		Joomla.submitform(task);
	}
</script>
<script type="text/javascript">
	Joomla.orderTable = function()
	{
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $listOrder; ?>')
		{
			dirn = 'asc';
		}
		else
		{
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, '');
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_hecmailing&view=templates'); ?>" method="post" name="adminForm"  id="adminForm">
<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif;?>
	<div id="filter-bar" class="btn-toolbar">
		<div class="filter-search btn-group pull-left">
			<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" class="hasTooltip" title="<?php echo JHtml::tooltipText('COM_USERS_SEARCH_IN_GROUPS'); ?>" />
		</div>
		<div class="btn-group pull-left">
			<button type="submit" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
			<button type="button" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.id('filter_search').value='';this.form.submit();">
			<i class="icon-remove"></i></button>
			<?php
 				//echo $this->catid;
 				echo $this->stateList;
 			?>
		</div>
		<div class="btn-group pull-right">
			<label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY'); ?></label>
			<select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
				<option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option>
				<?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder); ?>
			</select>
		</div>
	</div>
	<div class="clearfix"> </div>
	<table class="table table-striped">
		<thead>
			<tr>
				<th width="1%">
					<?php echo JHtml::_('grid.checkall'); ?>
				</th>
				<th class="left">
					<?php echo JHtml::_('grid.sort', 'COM_HECMAILING_HEADING_TEMPLATE_TITLE', 't.msg_lb_message', $listDirn, $listOrder); ?>
				</th>
				<th class="left">
					<?php echo JHtml::_('grid.sort', 'COM_HECMAILING_HEADING_TEMPLATE_SUBJECT', 't.msg_vl_subject', $listDirn, $listOrder); ?>
				</th>
				<th class="left">
					<?php echo JHtml::_('grid.sort', 'COM_HECMAILING_HEADING_TEMPLATE_PUBLISH', 't.published', $listDirn, $listOrder); ?>
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 't.msg_id_message', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="5">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
			
			
 	
 			<?php
 			$k = 0;
 			if ($this->items)
			foreach ($this->items as $i => $item) {
 				$row = $item;
				$published = JHTML::_('jgrid.published', $row->published, $i, 'templates.' );
 				$link 		= JRoute::_( 'index.php?option=com_hecmailing&view=template&layout=edit&id='. $row->msg_id_message );
				$checked = JHTML::_('grid.id', $i, $row->msg_id_message ); 
				
			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center">
					<?php echo $checked; ?>
				
				</td>
      			<td width="70%">
					<?php
					if ($this->table->isCheckedOut($user->get('id'), $row->checked_out )) :
						echo $row->msg_lb_message;
					else :
						?>
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_HECMAILING_EDIT_TEMPLATE' );?>::<?php echo $row->msg_lb_message."(".$row->msg_id_message.")"; ?>">
						<a href="<?php echo $link; ?>">
							<?php echo $row->msg_lb_message; ?></a> </span>
 						<?php
 					endif;
 					?>
 				</td>
 				<td >
					<?php echo $row->msg_vl_subject; ?>
				</td>
				<td width="30px" class="at_published" align="center">
					<?php echo $published ?>
				</td>
					
				<td>
				<?php
				if ($this->table->isCheckedOut($user->get ('id'), $row->checked_out )) :
					echo $row->msg_id_message;
				else :
					?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_HECMAILING_EDIT_TEMPLATE' );?>::<?php echo $row->msg_lb_message."(".$row->msg_id_message.")"; ?>">
					<a href="<?php echo $link; ?>">
						<?php echo $row->msg_id_message; ?></a> </span>
					<?php
				endif;
				?>
				</td>
			</tr>
		<?php
			$k = 1 - $k;
		}
		?>
		</tbody>
	</table>
	<input type="hidden" name="option" value="com_hecmailing" />
	<input type="hidden" name="view" value="templates" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
	
</form>
		
