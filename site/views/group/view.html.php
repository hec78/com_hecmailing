<?php 
/**
* @version 3.0.0
* @package hecMailing for Joomla
* @subpackage : View Form (Sending mail form)
* @module views.form.tmpl.view.html.php
* @copyright Copyright (C) 2008-2015 Hecsoft All rights reserved.
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
defined('_JEXEC') or die ('restricted access'); 
jimport('joomla.application.component.view'); 
jimport('joomla.html.toolbar');
jimport('joomla.html.parameter');

class hecMailingViewGroup extends JViewLegacy 
{ 
	function display ($tpl=null) 
	{ 
      
		// Modif Joomla 1.6+
		$mainframe = JFactory::getApplication();
        $db		=JFactory::getDBO();
		$user 	=JFactory::getUser();

		$idgroup 	= JRequest::getInt('idgroup',0);
		$option = JRequest::getCmd('option');
		//$model_form = & $this->getModel("form"); 
		//$groupelist = $model_form->getGroupes($send_all,false, $askselect);
		
		
		
		$row =JTable::getInstance('groupe', 'Table');
		// load the row from the db table
		$edit=true;
		if($edit)
			$row->load( $idgroup );
		$row->text = "";
		if ($edit) {
			// do stuff for existing records
			$row->checkout($user->get('id'));
		} else {
			// do stuff for new records
			$row->imagepos 	= 'top';
			$row->ordering 	= 0;
			$row->published = 1;
		}
			$lists = array();
			if(version_compare(JVERSION,'1.6.0','<')){
		       //Code pour Joomla! 1.5  
		       $query = "Select gd.gdet_cd_type, gd.gdet_id_value, gd.gdet_vl_value,gd.gdet_id_detail, u.name, g.name From #__hecmailing_groupdetail gd 
		  			left join #__users u on gd.gdet_id_value=u.id left join #__core_acl_aro_groups g on g.id=gd.gdet_id_value and gd.gdet_cd_type=3
		   			Where grp_id_groupe=".$idgroup;
		  	}else{
		      //Code pour Joomla >= 1.6.0
		      $query = "SELECT gd.gdet_cd_type, gd.gdet_id_value, gd.gdet_vl_value,gd.gdet_id_detail, u.NAME, gn.title 
						FROM #__hecmailing_groupdetail gd LEFT JOIN #__users u ON gd.gdet_id_value=u.id 
						LEFT JOIN #__user_usergroup_map g ON g.user_id=gd.gdet_id_value AND gd.gdet_cd_type=3 
						LEFT JOIN #__usergroups gn ON g.group_id=gn.id
						WHERE grp_id_groupe=".$idgroup;  
		  	}
		  
		  $db->setQuery($query);
		  
		  $detail = $db->loadRowList();
  
		   if(version_compare(JVERSION,'1.6.0','<')){
		       //Code pour Joomla! 1.5  
				    $query = "Select ifnull(ug.userid,0) as userid, ifnull(ug.groupid,0) as groupid,ug.grp_id_groupe, u.name, g.name  
				    From #__hecmailing_rights ug 
				  left join #__users u on ug.userid=u.id left join #__core_acl_aro_groups g on g.id=ug.groupid
				   Where ug.grp_id_groupe=".$cid[0];
			}else{
		      //Code pour Joomla >= 1.6.0
		       $query = "Select ifnull(ug.userid,0) as userid, ifnull(ug.groupid,0) as groupid,ug.grp_id_groupe, u.name,  gn.title AS NAME  , ug.flag
		       	  From #__hecmailing_rights ug 
				  left join #__users u on ug.userid=u.id 
				  LEFT JOIN #__usergroups gn ON ug.groupid=gn.id
				   Where ug.grp_id_groupe=".$idgroup;
			}	    
		  $db->setQuery($query);
		  $perms = $db->loadRowList();
		  
      
		$query = "Select id as value, username as text,name From #__users order by name";
	  $db->setQuery($query);
	  $users = $db->loadRowList();
	  $ulist=array();
	  if ($users)
	  	foreach($users as $u)
	  	{
	      $ulist[] = JHTML::_('select.option', $u[0], $u[2], 'id', 'name');
	    }
		$users = JHTML::_('select.genericlist',  $ulist, 'newuser', 'class="inputbox" size="1"', 'id', 'name', 0);
		/*if($edit)
			$lists['ordering'] 			= JHTML::_('list.specificordering',  $row, $idgroup, $query );
		else
			$lists['ordering'] 			= JHTML::_('list.specificordering',  $row, '', $query );*/
	if(version_compare(JVERSION,'1.6.0','<')){
	       //Code pour Joomla! 1.5  
	  		$query = "Select id, name From #__core_acl_aro_groups order by id";
	}
	else 
	{
		//Code pour Joomla >= 1.6.0
		$query = "SELECT id, title FROM  #__usergroups  ORDER BY id";
	}
	  $db->setQuery($query);
	  $grp = $db->loadRowList();
	  $glist = array();
	  if ($grp) 
	    foreach($grp as $g)
	    {
	    	$glist[] = JHTML::_('select.option', $g[0], $g[1], 'id', 'name');
	    }
	  
	  $groups = JHTML::_('select.genericlist',  $glist, 'newgroupe', 'class="inputbox" size="1"', 'id', 'name', 0);
	
		// build the html radio buttons for published
		$lists['published'] 		= JHTML::_('select.booleanlist',  'published', '', $row->published );
		
		// get params definitions
		$file 	= JPATH_ADMINISTRATOR .'/components/com_hecmailing/config.xml';
		$paramstxt="";
		$params = new JRegistry( $paramstxt, $file, 'component' );

	
	
		$this->assignRef('row', $row);
		$this->assignRef('lists', $lists);
		$this->assignRef('detail', $detail);
		$this->assignRef('params', $params);
		$this->assignRef('users', $users);
		$this->assignRef('groups', $groups);
		$this->assignRef('perms', $perms);
		$viewLayout = JRequest::getVar( 'layout', 'default' );
		$this->_layout = $viewLayout;

        parent::display($tpl); 
	} 

} 

?> 

