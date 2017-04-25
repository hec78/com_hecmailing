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
JHTML::_('behavior.tooltip');
$doc = JFactory::getDocument();
$doc->addStyleSheet(JUri::base() . '/components/com_hecmailing/assets/css/item.css');
$doc->addScript(JUri::base(true).'/components/com_hecmailing/assets/js/jquery.dataTables.min.js');
$doc->addStyleSheet(JUri::base(true).'/components/com_hecmailing/assets/js/jquery.dataTables.min.css');
$options = array(
		'onActive' => 'function(title, description){
        description.setStyle("display", "block");
        title.addClass("open").removeClass("closed");
    }',
		'onBackground' => 'function(title, description){
        description.setStyle("display", "none");
        title.addClass("closed").removeClass("open");
    }',
		'startOffset' => 0,  // 0 starts on the first tab, 1 starts the second, etc...
		'useCookie' => true, // this must not be a string. Don't use quotes.
);

//Note that the options argument is optional so JHtmlTabs::start() can be called without it
?>
<form action="<?php echo JRoute::_('index.php?option=com_hecmailing&view=message'); ?>" method="post"
	name="adminForm" id="adminForm">
<div><button class="return" onClick="Joomla.submitform('messages');return false;"><?php echo JText::_('COM_HECMAILING_FORM_LBL_MESSAGE_BUTTON_MESSAGES'); ?></button>
<button class="send" onClick="Joomla.submitform('send');return false;"><?php echo JText::_('COM_HECMAILING_FORM_LBL_MESSAGE_BUTTON_SENDAGAIN'); ?></button></div>
<?php 
echo JHtmlTabs::start('tabs_id',$options);
echo JHtmlTabs::panel(JText::_('COM_HECMAILING_FORM_LBL_MESSAGE_PANEL_MESSAGE'),'panel-id-1');

?>
<?php if ($this->item) : 
	$id=$this->item->id;
?>

    <div class="item_fields">
        <table class="table">
            <tr>
				<th><?php echo JText::_('COM_HECMAILING_FORM_LBL_MESSAGE_ID'); ?></th>
				<td><?php echo $this->item->id; ?></td>
			</tr>
			<tr>
				<th><?php echo JText::_('COM_HECMAILING_FORM_LBL_MESSAGE_DATE'); ?></th>
				<td><?php echo $this->item->message_date; ?></td>
			</tr>
			<tr>
				<th><?php echo JText::_('COM_HECMAILING_FORM_LBL_MESSAGE_USER'); ?></th>
				<td><?php echo $this->item->user->name; ?></td>
			</tr>
			<tr>
				<th><?php echo JText::_('COM_HECMAILING_FORM_LBL_MESSAGE_STATUS'); ?></th>
				<td><table width="100%" style="text-align:center"><tr><th><?php echo JText::_('COM_HECMAILING_FORM_LBL_MESSAGE_STATUS_0'); ?></th>
				<th><?php echo JText::_('COM_HECMAILING_FORM_LBL_MESSAGE_STATUS_1'); ?></th>
				<th><?php echo JText::_('COM_HECMAILING_FORM_LBL_MESSAGE_STATUS_2'); ?></th>
				<th><?php echo JText::_('COM_HECMAILING_FORM_LBL_MESSAGE_STATUS_8'); ?></th>
				<th><?php echo JText::_('COM_HECMAILING_FORM_LBL_MESSAGE_STATUS_9'); ?></th>
				</tr>
				<tr>
					<td><?php echo $this->item->nbtosend; ?></td>
					<td><?php echo $this->item->nbsent; ?></td>
					<td><?php echo $this->item->nbread; ?></td>
					<td><?php echo $this->item->nbexcluded; ?></td>
					
					<td><?php echo $this->item->nberr; ?></td>
				</tr>
				</table></td>
			</tr>
			<tr>
				<th><?php echo JText::_('COM_HECMAILING_FORM_LBL_MESSAGE_GROUPE'); ?></th>
				<td><?php echo $this->item->group->group_name; ?></td>
			</tr>
			<tr>
				<th><?php echo JText::_('COM_HECMAILING_FORM_LBL_MESSAGE_FROM'); ?></th>
				<td><?php echo $this->item->message_from; ?></td>
			</tr>
			<tr>
				<th><?php echo JText::_('COM_HECMAILING_FORM_LBL_MESSAGE_ATTACHMENT'); ?></th>
				<td><ul>
				<?php foreach($this->item->attachments as $att)
				{
					if ($att->cid=="")
						echo "<li><a href=\"".$att->file."\" target=\"blank\">".$att->filename."</a></li>";
					else 
						$this->item->message_body = str_replace("cid:".$att->cid, $att->file, $this->item->message_body);
				}?>
				</ul>
				
				
				</td>
			</tr>
			<tr>
				<th><?php echo JText::_('COM_HECMAILING_FORM_LBL_MESSAGE_SUBJECT'); ?></th>
				<td><?php echo $this->item->message_subject; ?></td>
			</tr>
			<tr>
				<th><?php echo JText::_('COM_HECMAILING_FORM_LBL_MESSAGE_BODY'); ?></th>
				<td><?php echo $this->item->message_body; ?></td>
			</tr>
			

			

        </table>
    </div>
    
    <?php
    echo JHtmlTabs::panel(JText::_('COM_HECMAILING_FORM_LBL_MESSAGE_PANEL_RECIPIENTS'),'panel-id-2'); //You can use any custom text
    
    ?>
     <div class="item_fields">
        <table id="recipient_table" class="table front-end-list" ><thead>
        	<tr>
	        	<th><?php echo JText::_('COM_HECMAILING_FORM_LBL_RECIPIENT_EMAIL'); ?></th>
	        	<th><?php echo JText::_('COM_HECMAILING_FORM_LBL_RECIPIENT_NAME'); ?></th>
	        	<th><?php echo JText::_('COM_HECMAILING_FORM_LBL_RECIPIENT_STATUS'); ?></th>
	        	<th><?php echo JText::_('COM_HECMAILING_FORM_LBL_RECIPIENT_DATE'); ?></th>
        	</tr></thead>
        	<tfoot>
			<tr>
				<td colspan="4">
					<?php //echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot><tbody>
        	<?php 
        	foreach($this->item->recipients as $recipient)
        	{
        		
        		$status=JText::_('COM_HECMAILING_FORM_LBL_RECIPIENT_STATUS_'.$recipient->status);
        		$statusimages = array(0=>"tosend_24x18.png",1=>"sent_24x18.png", 2=>"read_24x18.png", 8=>"excluded_24x18.png", 9=>"error_24x18.png");
        		$img = "<img src='".JUri::base(true)."/components/com_hecmailing/assets/images/".$statusimages[$recipient->status]."' alt='".$status."' title='".$status."' />";
        		
        		echo "<tr><td>".$recipient->email."</td><td>".$recipient->name."</td><td>".$img."</td><td>".$recipient->timestamp."</td></tr>";
        	}
        	
        	?>
        		</tbody>
        		
        </table>
        </div>
    <?php 
    echo JHtmlTabs::end();
else:
    echo JText::_('COM_HECMAILING_ITEM_NOT_LOADED');
endif;
?>
	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="idmessage" value="<?php echo $this->item->id; ?>" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php //echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php// echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
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
		jQuery('.return').click(function () {window.location.href = '<?php echo JRoute::_('index.php?option=com_hecmailing&view=messages'); ?>';});
		jQuery('.send').click(function () {window.location.href = '<?php echo JRoute::_('index.php?option=com_hecmailing&view=send&idmessage='.$this->item->id); ?>';});
	}
	jQuery(document).ready(function() {
	    jQuery('#recipient_table').dataTable( {
	        "scrollY":        400,
	        "scrollCollapse": true,
	        "jQueryUI":       true
	    } );
	} );
</script>