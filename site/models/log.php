<?php 
/**
* @version 1.7.0
* @package hecMailing for Joomla
* @copyright Copyright (C) 2009 Hecsoft All rights reserved.
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
*
* Change Log
*		1.7.0 : Joomla 1.7 compliance
*		0.3.0 : Prise en charge groupe 0 (tous membres) dans getMailAdrFromGroupe
*		0.1.0 : Version d'origine
*/
defined('_JEXEC') or die ('restricted access'); 

jimport('joomla.application.component.model'); 

/**
 * hecMailing Component Form Model
 *
 * @package		Joomla
 */
class ModelhecMailingLog extends JModel 
{ 
    var $_id = 0; 
	var $_list=null;

	function __construct() 
	{ 
		parent::__construct(); 
		// Modif Joomla 1.6+
		$mainframe = JFactory::getApplication();


		// Get pagination request variables
        $limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
        $limitstart = JRequest::getVar('limitstart', 0, '', 'int');

        // In case limit has been changed, adjust it
        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
        $this->setState('limit', $limit);
        $this->setState('limitstart', $limitstart);
        $this->setState('option', 'com_hecmailing');
		// identifiant joomla
        $this->_id = JRequest::getVar('uid', 0, 'default', 'int'); 

   } 

	/* Obtient les objets */
	function getLog($id=0, $start='0001-01-01', $end='9999-12-31')
	{
		if(!$this->_list)
		{
			$query = $this->_buildQuery();
			$this->_list = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));  
			$user=&JFactory::getUser();
			if ($user->usertype == 'Super Administrator' || $user->usertype == 'Administrator') { $admin=true; } 
			else { $admin=false;}
		}
		return $this->_list;
	} 

    function _buildQuery()
	{
		
		// Modif Joomla 1.6+
		$mainframe = JFactory::getApplication();
		$where = array();
		$db =& $this->getDBO();
		$user=&JFactory::getUser();
		if ($user->usertype == 'Super Administrator' || $user->usertype == 'Administrator')  { $admin=true; } 
		else { $admin=false;}
		$option=JRequest::getVar( 'option', 'com_hecmailing' );
		$group = $mainframe->getUserStateFromRequest( $option.'group',        'group',        0,    'int' );
		$owner = $mainframe->getUserStateFromRequest( $option.'owner',        'owner',        0,    'int' );
		$filter_order        = $mainframe->getUserStateFromRequest( $option.'filter_order',        'filter_order',  'name', 'cmd' );
		$filter_order_Dir    = $mainframe->getUserStateFromRequest( $option.'filter_order_Dir',    'filter_order_Dir', 'desc', 'word' );
		$search				= $mainframe->getUserStateFromRequest( $option."search2",			'search2', 			'',			'string' );
		$search				= JString::strtolower( $search );
		
		if (isset( $search ) && $search!= '')
		{
			$searchEscaped = $db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$where[] = 'l.log_vl_subject LIKE '.$searchEscaped.' OR u.username LIKE '.$searchEscaped.' OR u.name LIKE '.$searchEscaped;
		}
		if ($group>0) {  $where[] = "l.grp_id_groupe =".$group; }
		if (!$admin)	{ $where[] = "l.usr_id_user=".$user->id; }
		if (isset($filter_order) && $filter_order!='') { $orderby = ' ORDER BY '. $filter_order .' '. $filter_order_Dir;  }
		else  {  $orderby ='';   }
		$where = ( count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '' );
		$query = "SELECT l.log_id_message, l.log_dt_sent, l.log_vl_subject,l.grp_id_groupe,	l.usr_id_user ,
  						l.log_nb_ok,l.log_nb_errors, u.name,u.username, g.grp_nm_groupe
						FROM #__hecmailing_log l LEFT JOIN #__hecmailing_groups g ON l.grp_id_groupe=g.grp_id_groupe 
						LEFT JOIN #__users u on l.usr_id_user=u.id ";
		$query.= $where;
		$query .= $orderby;
		return $query;
	}

	function getData($limitstart,$limit) 
	{
        // if data hasn't already been obtained, load it
        if (empty($this->_data)) 
		{
            $query = $this->_buildQuery();
            $this->_data = $this->_getList($query,$this->getState('limitstart'), $this->getState('limit'));
        }
        return $this->_data;
	}

	function getTotal()
	{
        // Load the content if it doesn't already exist
        if (empty($this->_total)) {
            $query = $this->_buildQuery();
            $this->_total = $this->_getListCount($query);    
        }
        return $this->_total;
	}

   function getPagination()
	{
        // Load the content if it doesn't already exist
        if (empty($this->_pagination)) {
            jimport('joomla.html.pagination');
            $this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
        }
        return $this->_pagination;
	}

    function getGroups()
	{
		$db=$this->getDBO();
		$query = "SELECT grp_id_groupe,grp_nm_groupe
				FROM #__hecmailing_groups ORDER BY grp_nm_groupe";
		$db->setQuery($query);
		if (!$rows = $db->loadRowList()) {  return false;  }
		$groupes=array();
		$groupes_html=array();
		$groupes_html["0"] = JHTML::_('select.option', '0', "{".JText::_( 'COM_HECMAILING_ALL' )."}", 'grp_id_groupe', 'grp_nm_groupe');
		foreach ($rows as $r)
		{
		$groupes_html[$r[0]] = JHTML::_('select.option', $r[0], $r[1], 'grp_id_groupe', 'grp_nm_groupe');
		$groupe[$r[0]] = $r[1];
		}
		return $groupes_html;
	}
} 
?> 



