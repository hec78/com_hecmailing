<?php
/**
 * @version 0.0.1 
 * @package hecmailing
 * @copyright 2009 Hecsoft.info
 * @license http://www.gnu.org/licenses/gpl-3.0.html
 * @link http://joomlacode.org/gf/project/userport/
 * @author H Cyr
 **/
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );


/**
 * @package		hecMailing
 * 
 * JTable object for manage groups
 *    
 **/
class TableGroupe extends JTable
{
	/** @var int Primary key */
	var $grp_id_groupe			= null;
	/** @var string */
	var $grp_nm_groupe 				= null;
	/** @var string */
	var $grp_cm_groupe				= null;
	var $published				= null;
	var $ordering			=null;
	
	/**
	* @param database A database connector object
	*/
	function __construct(&$db)
	{
		parent::__construct( '#__hecmailing_groups', 'grp_id_groupe', $db );
	}

	/**
	 * Overloaded check function
	 *
	 * @access public
	 * @return boolean
	 * @see JTable::check
	 * @since 1.5
	 */
	function check()
	{
	  if (strlen($this->grp_nm_groupe)==0 ) return false;
		return true;
	}
}
?>