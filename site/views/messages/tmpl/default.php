<?php
/**
 * @version     1.0.0
 * @package     com_kwpmessage
 * @copyright   Copyright (C) 2014. Tous droits réservés.
 * @license     GNU General Public License version 2 ou version ultérieure ; Voir LICENSE.txt
 * @author      Hervé CYR <herve.cyr@kantarworldpanel.com> - 
 */
// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
JHTML::_('script', 'system/multiselect.js', false, true);
// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_hecmailing/assets/css/list.css');


$user       = JFactory::getUser();
$userId     = $user->get('id');
$listOrder  = $this->state->get('list.ordering');
$listDirn   = $this->state->get('list.direction');
$ordering   = ($listOrder == 'a.ordering');
$canCreate  = $user->authorise('core.create', 'com_hecmailing');
$canEdit    = $user->authorise('core.edit', 'com_hecmailing');
$canCheckin = $user->authorise('core.manage', 'com_hecmailing');
$canChange  = $user->authorise('core.edit.state', 'com_hecmailing');
$canDelete  = $user->authorise('core.delete', 'com_hecmailing');
?>
<div id="hecmailing_messages">
<form action="<?php echo JRoute::_('index.php?option=com_hecmailing&view=messages'); ?>" method="post"
	name="adminForm" id="adminForm">

	<?php echo $this->loadTemplate('filter'); ?>
	<?php if ($canCreate): ?>
		<button type="button"
			onclick="window.location.href = '<?php echo JRoute::_('index.php?option=com_hecmailing&view=send', false, 2); ?>';">
			<img title="<?php echo JText::_("COM_HECMAILING_MESSAGES_ACTIONS_NEW"); ?>" src="<?php echo JUri::base(true); ?>/components/com_hecmailing/assets/images/new32x24.png" ><?php echo JText::_('COM_HECMAILING_NEW_MESSAGE'); ?></button>
	<?php endif; ?>
	<table class="front-end-list">
		<thead>
			<tr>
				<th class="align-left">
					<?php echo JHtml::_('grid.sort',  'COM_HECMAILING_MESSAGES_DATE', 'a.message_date', $listDirn, $listOrder); ?>
				</th>
				<th class="align-left">
					<?php echo JHtml::_('grid.sort',  'COM_HECMAILING_MESSAGES_SUBJECT', 'a.message_subject', $listDirn, $listOrder); ?>
				</th>
				<th class="align-left">
					<?php echo JHtml::_('grid.sort',  'COM_HECMAILING_MESSAGES_USER', 'u.name', $listDirn, $listOrder); ?>
				</th>

				<th class="align-left">
					<?php echo JHtml::_('grid.sort',  'COM_HECMAILING_MESSAGES_GROUP', 'g.grp_nm_groupe', $listDirn, $listOrder); ?>
				</th>

				<th class="align-center">
					<?php 
					$tooltip = '<img src="'.JUri::base(true).'/components/com_hecmailing/assets/images/tosend 32x24.png"/>'.JText::_("COM_HECMAILING_MESSAGES_HEADER_TOSEND_TOOLTIP");
					echo JHtml::_('grid.sort',  '<img src="'.JUri::base(true).'/components/com_hecmailing/assets/images/tosend 32x24.png"/>', 'nbtosend', $listDirn, $listOrder,null, 'asc', $tooltip); ?>
				</th>
				
				<th class="align-center">
					<?php
						$tooltip = '<img src="'.JUri::base(true).'/components/com_hecmailing/assets/images/sent 32x24.png"/>'.JText::_("COM_HECMAILING_MESSAGES_HEADER_SENT_TOOLTIP");
						echo JHtml::_('grid.sort',  '<img src="'.JUri::base(true).'/components/com_hecmailing/assets/images/sent 32x24.png"/>', 'nbok', $listDirn, $listOrder,null, 'asc', $tooltip); ?>
				</th>
				<th class="align-center">
					<?php
					$tooltip = '<img src="'.JUri::base(true).'/components/com_hecmailing/assets/images/read 32x24.png"/>'.JText::_("COM_HECMAILING_MESSAGES_HEADER_EXCLUDED_TOOLTIP");
					echo JHtml::_('grid.sort',  '<img src="'.JUri::base(true).'/components/com_hecmailing/assets/images/read 32x24.png"/>', 'nbread', $listDirn, $listOrder,null, 'asc', $tooltip); ?>
				</th>
				<th class="align-center">
					<?php
					$tooltip = '<img src="'.JUri::base(true).'/components/com_hecmailing/assets/images/excluded 32x24.png"/>'.JText::_("COM_HECMAILING_MESSAGES_HEADER_ERROR_TOOLTIP");
					echo JHtml::_('grid.sort',  '<img src="'.JUri::base(true).'/components/com_hecmailing/assets/images/excluded 32x24.png"/>', 'nberr', $listDirn, $listOrder,null, 'asc', $tooltip); ?>
				</th>
				<th class="align-center">
					<?php
					$tooltip = '<img src="'.JUri::base(true).'/components/com_hecmailing/assets/images/error 32x24.png"/>'.JText::_("COM_HECMAILING_MESSAGES_HEADER_ERROR_TOOLTIP");
					echo JHtml::_('grid.sort',  '<img src="'.JUri::base(true).'/components/com_hecmailing/assets/images/error 32x24.png"/>', 'nberr', $listDirn, $listOrder,null, 'asc', $tooltip); ?>
				</th>
			

			

				<?php if ($canEdit || $canDelete): ?>
					<th class="align-center">
						<?php echo JText::_('COM_HECMAILING_MESSAGES_ACTIONS'); ?>
					</th>
				<?php endif; ?>

		</tr>
		</thead>
		<tfoot>
		<tr>
			<td colspan="<?php echo isset($this->items[0]) ? count(get_object_vars($this->items[0])) : 10; ?>">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
		</tr>
		</tfoot>
		<tbody>
		<?php foreach ($this->items as $i => $item) : ?>
			<?php $canEdit = $user->authorise('core.edit', 'com_hecmailing'); ?>

			

			<tr class="row<?php echo $i % 2; ?> ">
				
					
					<td class="align-left"><a href="<?php echo JRoute::_('index.php?option=com_hecmailing&view=message&id=' . (int) $item->id); ?>">
						<?php echo $item->message_date; ?></a>
					</td>
					<td class="align-left">
					<a href="<?php echo JRoute::_('index.php?option=com_hecmailing&view=message&id=' . (int) $item->id); ?>">

						<?php echo $this->escape($item->message_subject); ?>
					</a>
					</td>

					

					<td class="align-left">
						<?php echo $item->name; ?>
					</td>

					<td class="align-left">
						<?php echo $item->grp_nm_groupe; ?>
					</td>

					<td class="align-center <?php if ($item->nbtosend>0) echo "message_tosend"; ?>" title="<?php echo $item->nbtosend." ".JText::_("COM_HECMAILING_MESSAGES_VALUE_TOSEND_TOOLTIP"); ?>">
						<?php echo $item->nbtosend; ?>
					</td>
					<td class="align-center <?php if ($item->nbok>0) echo "message_ok"; ?>" title="<?php echo $item->nbok." ".JText::_("COM_HECMAILING_MESSAGES_VALUE_SENT_TOOLTIP"); ?>">
						<?php echo $item->nbok; ?>
					</td>
					<td class="align-center <?php if ($item->nbread>0) echo "message_read"; ?>" title="<?php echo $item->nbread." ".JText::_("COM_HECMAILING_MESSAGES_VALUE_READ_TOOLTIP"); ?>">
						<?php echo $item->nbread; ?>
					</td>
					<td class="align-center <?php if ($item->nbexcluded>0) echo "message_excluded"; ?>" title="<?php echo $item->nbexcluded." ".JText::_("COM_HECMAILING_MESSAGES_VALUE_EXCLUDED_TOOLTIP"); ?>">
						<?php echo $item->nbexcluded; ?>
					</td>
					<td class="align-center <?php if ($item->nberr>0) echo "message_error"; ?>" title="<?php echo $item->nberr." ".JText::_("COM_HECMAILING_MESSAGES_VALUE_ERROR_TOOLTIP"); ?>">
						<?php echo $item->nberr; ?>
					</td>
				

					<?php if ($canEdit || $canDelete): ?>
					<td class="align-center">
						<?php if ($item->nbtosend>0) { ?>
						<a href="<?php echo JRoute::_('index.php?option=com_hecmailing&view=sending&idmessage=' . (int) $item->id); ?>" >
						<img title="<?php echo JText::_("COM_HECMAILING_MESSAGES_ACTIONS_CONTINUE"); ?>" src="<?php echo JUri::base(true); ?>/components/com_hecmailing/assets/images/continue32x24.png"" ></a>
						<?php } ?>
					</td>
					<?php endif; ?>

			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>

	<?php if ($canCreate): ?>
		<button type="button"
			onclick="window.location.href = '<?php echo JRoute::_('index.php?option=com_hecmailing&view=send', false, 2); ?>';">
			<img title="<?php echo JText::_("COM_HECMAILING_MESSAGES_ACTIONS_NEW"); ?>" src="<?php echo JUri::base(true); ?>/components/com_hecmailing/assets/images/new32x24.png" ><?php echo JText::_('COM_HECMAILING_NEW_MESSAGE'); ?></button>
	<?php endif; ?>

	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
</div>
<script type="text/javascript">
	if (typeof jQuery == 'undefined') {
		var headTag = document.getElementsByTagName("head")[0];
		var jqTag = document.createElement('script');
		jqTag.type = 'text/javascript';
		jqTag.src = '//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js';
		jqTag.onload = jQueryCode;
		headTag.appendChild(jqTag);
	} else {
		jQueryCode();
	}

	function jQueryCode() {
		jQuery('.delete-button').click(function () {
			var item_id = jQuery(this).attr('data-item-id');
			<?php if($canDelete): ?>
			if (confirm("<?php echo JText::_('COM_HECMAILING_DELETE_MESSAGE'); ?>")) {
				window.location.href = '<?php echo JRoute::_('index.php?option=com_hecmailing&task=message.remove&id=', false, 2) ?>' + item_id;
			}
			<?php endif; ?>

		});
	}

</script>