<?php
/**
 * @version 1.7.0 
 * @package hecmailing
 * @copyright 2009-2011 Hecsoft.net
 * @license http://www.gnu.org/licenses/gpl-3.0.html
 * @link http://joomlacode.org/gf/project/userport/
 * @author H Cyr
 **/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
* @package		HEC Mailing
* @subpackage	
*/
class TOOLBAR_hecmailing
{
	/**
	* Draws the menu for a New Contact
	*/
	function _EDIT($edit) {
		$cid = JRequest::getVar( 'cid', array(0), '', 'array' );

		$text = ( $edit ? JText::_( 'Edit' ) : JText::_( 'New' ) );

		JToolBarHelper::title( JText::_( 'HEC_MAILING' ) .': <small><small>[ '. $text .' ]</small></small>', 'hecmailing.png' );

		//JToolBarHelper::custom( 'save2new', 'new.png', 'new_f2.png', 'Save & New', false,  false );
		//JToolBarHelper::custom( 'save2copy', 'copy.png', 'copy_f2.png', 'Save To Copy', false,  false );
		JToolBarHelper::save();
		JToolBarHelper::apply();
		if ( $edit ) {
			// for existing items the button is renamed `close`
			JToolBarHelper::cancel( 'cancel', JText::_('CLOSE') );
		} else {
			JToolBarHelper::cancel();
		}
		
		JToolBarHelper::help( 'screen.hecmailing.edit' );
		
	}

  function _TEMPLATES()
  {
    $text = JText::_( 'TEMPLATES' );
		JToolBarHelper::title( JText::_( 'HEC_MAILING' ) .': <small><small>[ '. $text .' ]</small></small>', 'hecmailing.png' );
		JToolBarHelper::custom('delTemplate','delete.png','delete.png', JText::_('DELETE'));
    	JToolBarHelper::cancel();
    	TOOLBAR_hecmailing::addSubmenu("templates");
  }
  
  function _CONTACT()
  {
    	$text = JText::_( 'CONTACT' );
		JToolBarHelper::title( JText::_( 'HEC MAILING' ) .': <small><small>[ '. $text .' ]</small></small>', 'hecmailing.png' );
		JToolBarHelper::custom('newContact','new.png','new.png', JText::_('NEW_CONTACT'),false,false);
		JToolBarHelper::custom('editContact','edit.png','edit.png', JText::_('EDIT_CONTACT'),false,false);
		JToolBarHelper::custom('delContact','delete.png','delete.png', JText::_('DELETE'));
    	JToolBarHelper::cancel();
    	TOOLBAR_hecmailing::addSubmenu("contact");
  }

 function _EDITCONTACT($edit) {
		$cid = JRequest::getVar( 'cid', array(0), '', 'array' );

		$text = ( $edit ? JText::_( 'Edit' ) : JText::_( 'New' ) );

		JToolBarHelper::title( JText::_( 'HEC_MAILING' ) .': <small><small>[ '. $text .' ]</small></small>', 'hecmailing.png' );

		//JToolBarHelper::custom( 'save2new', 'new.png', 'new_f2.png', 'Save & New', false,  false );
		//JToolBarHelper::custom( 'save2copy', 'copy.png', 'copy_f2.png', 'Save To Copy', false,  false );
		JToolBarHelper::custom('saveContact','save.png','save.png', JText::_('SAVE'),false,false);
		JToolBarHelper::custom('cancelContact','cancel.png','cancel.png', JText::_('CANCEL'),false,false);
		
		JToolBarHelper::help( 'screen.hecmailing.edit' );
		
	}
	
	function _GROUPS() {
		JHTML::stylesheet('icons.css', 'administrator/components/com_hecmailing/hecmailing.css');
		JToolBarHelper::title( JText::_( 'HEC_MAILING' ).': <small><small>[ '. JText::_('ADMIN_GROUPS') .' ]</small></small>', 'hecmailing.png' );
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::deleteList();
		JToolBarHelper::editListX();
		JToolBarHelper::custom('add', 'new.png','new_f2.png',JText::_('NEW_GROUP'),false,false);
		TOOLBAR_hecmailing::addSubmenu("groups");
	}
		function _DEFAULT() {
	JHTML::stylesheet('icons.css', 'administrator/components/com_hecmailing/hecmailing.css');
		JToolBarHelper::title( JText::_( 'HEC_MAILING' ).': <small><small>[ '. JText::_('ADMIN_GROUPS') .' ]</small></small>', 'hecmailing.png' );
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::deleteList();
		JToolBarHelper::editListX();
		JToolBarHelper::custom('add', 'new.png','new_f2.png',JText::_('NEW_GROUP'),false,false);
		TOOLBAR_hecmailing::addSubmenu("groups");

	}
  function _PARAM() {
		JHTML::stylesheet('icons.css', 'administrator/components/com_hecmailing/hecmailing.css');
		JToolBarHelper::title( JText::_( 'HEC_MAILING' ).': <small><small>[ '. JText::_('PARAMETERS') .' ]</small></small>', 'hecmailing.png' );
			//JToolBarHelper::preferences('com_hecmailing', '500');

		JToolBarHelper::help( 'screen.hecmailing' );
		TOOLBAR_hecmailing::addSubmenu("param");
	}
	
/**
	 * Configure the Linkbar.
	 *
	 * @param	string	$vName	The name of the active view.
	 *
	 * @return	void
	 * @since	1.6
	 */
	public static function addSubmenu($vName)
	{
		


		JSubMenuHelper::addEntry(
			JText::_('COM_HECMAILING_GROUPLIST'),
			'index.php?option=com_hecmailing&task=groups',
			$vName == 'groups'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_HECMAILING_CONTACTS'),
			'index.php?option=com_hecmailing&task=contact',
			$vName == 'contact'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_HECMAILING_MENUTEMPLATE'),
			'index.php?option=com_hecmailing&task=templates',
			$vName == 'templates'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_HECMAILING_PARAMETERS'),
			'index.php?option=com_hecmailing&task=param',
			$vName == 'param'
		);
		
	}
}