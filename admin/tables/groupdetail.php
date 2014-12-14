<?php
/**
 * @version 1.7.0 
 * @package hecmailing
 * @copyright 2009-2011 Hecsoft.info
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
class TableGroupDetail extends JTable
{
	/** @var int Primary key */
	var $gdet_id_value     = null;
	var $grp_id_groupe			= null;
	/** @var string */
	var $gdet_cd_type       = null;
  var $gdet_vl_value  =null;
	
  /**
	* @param database A database connector object
	*/
	function __construct(&$db)
	{
		parent::__construct( '#__hecmailing_groupdetail', 'gdet_id_value', $db );
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
	  if ($this->grp_id_groupe==null ) return false;
		return true;
	}
}
?>