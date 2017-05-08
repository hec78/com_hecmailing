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

$document->addStyleSheet(JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR."assets/css/hecmailing.css");
//$ver =   HecMailingHelper::getComponentVersion();
//$latestProd =    HecMailingHelper::getLatestComponentVersion($this->baseurl.'hecmailing.xml');  
//$latestTest =    HecMailingHelper::getLatestComponentVersion($this->baseurl.'hecmailing_test.xml');  
if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif;?>
<table width="100%">
	<tr valign="top"><td>
	     
	       <fieldset>

			<legend><?php echo JText::_( 'COM_HECMAILING_DONATION' ); ?></legend>

			<?php echo JText::_( 'COM_HECMAILING_DONATION_TEXT' ); ?><br><br>

			<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
	        	<input type="hidden" name="cmd" value="_s-xclick">
	        	<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----">
	        	<input type="image" src="https://www.paypal.com/<?php echo JText::_( 'en_US/GB'); ?>/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online.">
	        	<img alt="" border="0" src="https://www.paypal.com/fr_FR/i/scr/pixel.gif" width="1" height="1">
	        </form>
	      </fieldset>    </td></tr></table>
