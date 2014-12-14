<?php
/**
 * @version 0.0.1 
 * @package hecmailing
 * @copyright 2009 Hecsoft.info
 * @license http://www.gnu.org/licenses/gpl-3.0.html
 * @link http://joomlacode.org/gf/project/userport/
 * @author H Cyr
 **/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

require_once( JApplicationHelper::getPath( 'toolbar_html' ) );

switch ($task)
{
	case 'add'  :
		TOOLBAR_hecmailing::_EDIT(false);
		break;
	case 'edit' :
	case 'editA':
		TOOLBAR_hecmailing::_EDIT(true);
		break;
  case 'templates':
    TOOLBAR_hecmailing::_TEMPLATES();
		break;
	case 'contact':
    TOOLBAR_hecmailing::_CONTACT();
		break;
	case 'editContact':
    TOOLBAR_hecmailing::_EDITCONTACT(true);
		break;
	case 'newContact':
    TOOLBAR_hecmailing::_EDITCONTACT(false);
		break;
  
	case 'param':
	
		TOOLBAR_hecmailing::_PARAM();
		break;
	case 'groups':
    TOOLBAR_hecmailing::_GROUPS();
		break;
	default:
    TOOLBAR_hecmailing::_DEFAULT();
		break;
}