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