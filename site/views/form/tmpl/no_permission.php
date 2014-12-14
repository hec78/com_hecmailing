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
$mainframe->addCustomHeadTag ('<link rel="stylesheet" href="components/com_hecmailing/css/toolbar.css" type="text/css" media="screen" />');
$mainframe->addCustomHeadTag ('<link rel="stylesheet" href="components/com_hecmailing/css/dialog.css" type="text/css" media="screen" />');
$mainframe->addCustomHeadTag ('<link rel="stylesheet" href="components/com_hecmailing/libraries/jt/jt_DialogBox.css" type="text/css" />');
$mainframe->addCustomHeadTag ('<script src="components/com_hecmailing/libraries/jt/dom-drag.js" type="text/javascript"></script>');
//echo '<script src="components/com_hecmailing/libraries/jt/dom-drag.js" type="text/javascript"></script>\n';
$mainframe->addCustomHeadTag ('<script src="components/com_hecmailing/libraries/jt/jt_.js" type="text/javascript"></script>');
//echo '<script src="components/com_hecmailing/libraries/jt/jt_.js" type="text/javascript"></script>';
$mainframe->addCustomHeadTag ('<script src="components/com_hecmailing/libraries/jt/jt_DialogBox.js" type="text/javascript"></script>');
//echo '<script src="components/com_hecmailing/libraries/jt/jt_DialogBox.js" type="text/javascript"></script>';
$mainframe->addCustomHeadTag ('<link rel="stylesheet" href="components/com_hecmailing/libraries/jt/veils.css" type="text/css" />');

//$mainframe->addCustomHeadTag ('<script src="components/com_hecmailing/libraries/jt/jt_AppDialogs.js" type="text/javascript"></script>');
//$mainframe->addCustomHeadTag ('<script src="components/com_hecmailing/libraries/jt/MyApp_dialogs.js" type="text/javascript"></script>');

?>

<style type="text/css">
.liste{
	
	text-align:left;
	border:1px solid #4FBAB3;
	white-space:nowrap;
	font:normal 12px verdana;
	background:#fff;
	padding:5px;
	overflow: auto;
	height:50px;
}
 
.liste a{
	display:block;
	cursor:default;
	color:#000;
	text-decoration:none;
	background:#fff;
}
 
.liste a:hover{
	color:white;
	background-color:#7370CB;
}

</style>

<div class="warning"><?php echo JText::_('NO PERMISSION'); ?></div>


