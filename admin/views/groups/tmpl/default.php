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
$document->addScript("components/com_hecmailing/admin.hecmailing.js");
$user		= JFactory::getUser();
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$sortFields = $this->getSortFields();

JText::script('COM_HECMAILING_GROUPS_CONFIRM_DELETE');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'groups.delete')
		{
			var f = document.adminForm;
			var cb='';
<?php foreach ($this->items as $i => $item):?>
<?php if ($item->grp_nb_item > 0):?>
			cb = f['cb'+<?php echo $i;?>];
			if (cb && cb.checked)
			{
				if (confirm(Joomla.JText._('COM_HECMAILING_GROUPS_CONFIRM_DELETE')))
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

<form action="<?php echo JRoute::_('index.php?option=com_hecmailing&view=groups'); ?>" method="post" name="adminForm"  id="adminForm">
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
					<?php echo JHtml::_('grid.sort', 'COM_HECMAILING_HEADING_GROUP_TITLE', 'g.grp_nm_groupe', $listDirn, $listOrder); ?>
				</th>
				<th class="left">
					<?php echo JHtml::_('grid.sort', 'COM_HECMAILING_HEADING_DESCR_TITLE', 'g.grp_cm_groupe', $listDirn, $listOrder); ?>
				</th>
				<th class="left">
					<?php echo JHtml::_('grid.sort', 'COM_HECMAILING_HEADING_PUBLISH_TITLE', 'g.published', $listDirn, $listOrder); ?>
				</th>
				<th width="20%" class="center">
					<?php echo JText::_('COM_HECMAILING_HEADING_ITEM_IN_GROUP'); ?>
				</th>
				<th width="1%">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'g.grp_id_groupe', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="4">
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
				$published = JHTML::_('jgrid.published', $row->published, $i, 'groups.' );
 				$link 		= JRoute::_( 'index.php?option=com_hecmailing&view=group&layout=edit&id='. $row->grp_id_groupe );
				$checked = JHTML::_('grid.id', $i, $row->grp_id_groupe ); 
				
 				//$checked 	= JHTML::_('grid.checkedout',   $row, $i );
			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center">
					<?php echo $checked; ?>
				
				</td>
      			<td width="40%">
					<?php
					if ($this->table->isCheckedOut($user->get('id'), $row->checked_out )) :
						echo $row->grp_nm_groupe;
					else :
						?>
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_HECMAILING_EDIT_GROUP' );?>::<?php echo $row->grp_nm_groupe."(".$row->grp_id_groupe.")"; ?>">
						<a href="<?php echo $link; ?>">
							<?php echo $row->grp_nm_groupe; ?></a> </span>
 						<?php
 					endif;
 					?>
 				</td>
				<td>
						<?php echo $row->grp_cm_groupe; ?>
				</td>
					<td width="30px" class="at_published" align="center">
						<?php echo $published ?>
				</td>
					<td width="30px">
						<?php echo $row->grp_nb_item; ?>
				</td>
				<td>
				<?php
				if ($this->table->isCheckedOut($user->get ('id'), $row->checked_out )) :
					echo $row->grp_id_groupe;
				else :
					?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'EDIT_GROUP' );?>::<?php echo $row->grp_nm_groupe."(".$row->grp_id_groupe.")"; ?>">
					<a href="<?php echo $link; ?>">
						<?php echo $row->grp_id_groupe; ?></a> </span>
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
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
	<div class="warning"><?php echo  JText::_( 'COM_HECMAILING_WARNING_CHANGE_GROUP' ); ?></div>
</form>
		
