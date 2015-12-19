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
$document->addCustomTag('<link rel="stylesheet" href="components/com_hecmailing/assets/css/toolbar.css" type="text/css" media="screen" />');
$document->addCustomTag ('<link rel="stylesheet" href="administrator/components/com_hecmailing/libraries/jquery-ui.min.css" type="text/css" />');
// load JQuery if Joomla < 3.0.0
if(version_compare(JVERSION,'3.0.0','<')){
	$document->addScript("administrator/components/com_hecmailing/libraries/jquery.min.js");
}
$document->addCustomTag ('<script src="administrator/components/com_hecmailing/libraries/jquery-ui.min.js" type="text/javascript"></script>');
//$document->addCustomTag ('<script src="components/com_hecmailing/views/sending/sending.js" type="text/javascript"></script>');
$menu = JFactory::getApplication()->getMenu()->getDefault();
$homelink=$menu->link;
if (!isset($this->message)) $idmessage=false; else $idmessage=$this->message->id;
?>

<style type="text/css">
#component-hecmailing-sending { text-align:center;}
</style>
<?php if ($idmessage) { ?>
<script language="javascript" type="text/javascript">
function send()
{
	var formData = {idMessage:"<?php echo $idmessage; ?>" , count:"<?php echo $this->count; ?>"}; //Array 
    jQuery.ajax({
        type: "POST",
        url: "<?php echo JURI::base(false); ?>/index.php?option=com_hecmailing&task=sending.wssend",
        data: formData,
    	dataType: "json",
    	success: function (response, textStatus, jqXHR){
    		var result = response;
    		if (result!=null) 
	    	{
    	    	if(result.status=="OK" || result.status=="TERMINATED")
    	    	{
		    		if (result.tosendcount>0)
		    		{
			    		pct = parseInt((result.totalcount-result.tosendcount) / result.totalcount * 100);
			    		jQuery( "#progressbar" ).progressbar({ value: pct,	});
			    		var message="<?php echo JText::_('COM_HECMAILING_SENDING_COUNT'); ?>";
			    		message=message.replace("{sent}", result.sentcount);
			    		message=message.replace("{error}", result.errorcount);
			    		message=message.replace("{excluded}", result.excludedcount);
			    		message=message.replace("{total}", result.totalcount);			    		
			    		jQuery('#progresslabel').text(message);
			    		jQuery('#percent').text(pct+" %");
			    		progressTimer = setTimeout( send, 100 );
		    		}
		    		else
		    		{
		        		pct=100;
		    			jQuery( "#progressbar" ).progressbar({	value: pct,	});
		    			jQuery("#label").text("<?php echo JText::_('COM_HECMAILING_SENDING_TERMINATED'); ?>");
		    			var message="<?php echo JText::_('COM_HECMAILING_SENDING_COUNT_TERMINATED'); ?>";
			    		message=message.replace("{sent}", result.sentcount);
			    		message=message.replace("{error}", result.errorcount);
			    		message=message.replace("{read}", result.readcount);
			    		message=message.replace("{excluded}", result.excludedcount);
		    			jQuery('#progresslabel').text(message);
		    			jQuery('#percent').text(pct+" %");
		    			jQuery( "#buttonCancelContainer" ).hide();
	    	    		jQuery( "#buttonCloseContainer" ).show();
			    		clearTimeout();
		    		}
    	    	}
    	    	else if (result.status=="NORIGHT")
    	    	{
    	    		jQuery( "#progressbar" ).hide();
    	    		jQuery("#label").text("<?php echo JText::_('COM_HECMAILING_SENDING_NORIGHT'); ?>");
    	    		jQuery('#percent').hide();
    	    		jQuery( "#buttonCancelContainer" ).hide();
    	    		jQuery( "#buttonCloseContainer" ).show();
    	    		
    	    	}
	    	}
    	   },
    	error: function (msg,status) {
    		jQuery('#label').text('<?php echo JText::_('COM_HECMAILING_SENDING_WEBSERVICE_ERROR'); ?> :'+status+ ' - '+msg);
	    		clearTimeout();
	    	}
    });
    	
	
}

jQuery(function() {
	pct=0;
	jQuery( "#progressbar" ).progressbar({	value: pct,max:100	});
	jQuery( "#label" ).text('<?php echo JText::_('COM_HECMAILING_SENDING_EMAIL_RUNNING'); ?>');
	
	send();
});


</script>


<form action="<?php echo JURI::current(); ?>" method="post" name="adminForm" id="adminForm" ENCTYPE="multipart/form-data">
<div class="componentheading"><?php echo JText::_('COM_HECMAILING_SENDING_EMAIL'); ?></div>
<div id="component-hecmailing-sending">
	<div id="label"></div>
	<div id="progresslabel"></div>
	<div id="percent"></div>
	<div id="progressbar"></div>
	<br/>
	<div id="buttonCancelContainer"><button id="buttoncancel" onclick="clearTimeout();"><?php echo JText::_('COM_HECMAILING_SENDING_CANCEL'); ?></button></div>
	<div id="buttonCloseContainer" style="display:none"><a class="btn" id="buttonclose" href="<?php echo JUri::base().$menu->link; ?>" ><?php echo JText::_('COM_HECMAILING_SENDING_CLOSE'); ?></a>
	<a class="btn" id="buttonclose" href="<?php echo JUri::base().'index.php?option=com_hecmailing&view=message&id='.$idmessage; ?>" ><?php echo JText::_('COM_HECMAILING_SENDING_MESSAGE'); ?></a></div>
</div>

<?php } else {?>
	<div class="componentheading"><?php echo JText::_('COM_HECMAILING_SENDING_EMAIL'); ?></div>
	<div>Le message n'existe pas</div>
	<div id="buttonCloseContainer" style="display:none"><a class="btn" id="buttonclose" href="<?php echo JUri::base().$menu->link; ?>" ><?php echo JText::_('COM_HECMAILING_SENDING_CLOSE'); ?></a></div>
<?php } ?>
<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="option" id="option" value="com_hecmailing">
<!--<input type="hidden" name="view" id="view" value="form">-->
<input type="hidden" name="task" id="task" value="">
<input type="hidden" name="idMessage" id="idMessage" value="<?php if (isset($this->message)) echo $this->message->id; ?>">
</form>



