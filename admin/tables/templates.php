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
class TableTemplates extends JTable
{
	/** @var int Primary key */
	var $msg_id_message			= null;
	/** @var int Primary key */
	var $msg_lb_message			= null;
	/** @var string */
	var $msg_vl_subject 				= null;
	/** @var string */
	var $msg_vl_body				= null;
	var $msg_vl_from = null;
	var $grp_id_groupe = null;
	
	
	/**
	* @param database A database connector object
	*/
	function __construct(&$db)
	{
		parent::__construct( '#__hecmailing_save', 'msg_id_message', $db );
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
	  if (strlen($this->msg_lb_message)==0 ) return false;
		return true;
	}
}
?>