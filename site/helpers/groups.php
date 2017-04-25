<?php
/**
* @version 3.4.0
* @package hecMailing for Joomla
* @subpackage : Helper
* @module helpers.groups.php
* @copyright Copyright (C) 2008-2011 Hecsoft All rights reserved.
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
 
defined('_JEXEC') or die;
/**
 *  Can send mail to this group 
 */
define ("HECMAILING_GROUP_RIGHT_CANSEND",1);
/**
 *  Can manage (edit content and recipient list) to this group
 */
define ("HECMAILING_GROUP_RIGHT_CANMANAGE",2);
/**
 *  Can add or remove permission to this group
 */
define ("HECMAILING_GROUP_RIGHT_CANGRANT",3);
/*
* Group Helper
*/
class HecMailingGroupsHelper {
    
	public static $lastError="";
	
	/**
	 * @method getRightFromFlagObject : Return object corresponding 
	 *                              to the authorisation binary flag
	 * @param int $flag : Binary Flag (1=Can Send, 2=Can Manage, 3=Can Grant)
	 * @return object { cansend, canmanage, cangrant }
	 */
	public static function getRightFromFlagObject($flag, $object=null)
	{
		if ($object==null) $object=new stdClass();
		$object->cansend = ($object->flag & 1)!=0;
		$object->canmanage = ($object->flag & 2)!=0;
		$object->cangrant = ($object->flag & 4)!=0;
		return $object;
	}
	/**
	 * @method getRightFromFlagAssoc : Return assoc array corresponding 
	 *                              to the authorisation binary flag
	 * @param int $flag : Binary Flag (1=Can Send, 2=Can Manage, 3=Can Grant)
	 * @return array { 'cansend' =>, 'canmanage'=>, 'cangrant'=> }
	 */
	public static function getRightFromFlagAssoc($flag)
	{
		return array(	'cansend' => ($row->flag & 1)!=0, 
						'canmanage' => ($row->flag & 2)!=0,
						'cangrant' => ($row->flag & 4)!=0);
	}
	/**
	 * @method getGroupeQuery : Return query for a group
	 * @param int $groupe : HECMailing Group Id
	 * @param string $blockcond1 : First Block condition
	 * @param string $blockcond2 : 2nd Block condition
	 * @return string
	 */
  public static function getGroups($published=true, $all=false, $flags=array(HECMAILING_GROUP_RIGHT_CANSEND))
   {
      $db=JFactory::getDBO();
      $user =JFactory::getUser();
      $params 	= JComponentHelper::getParams( 'com_hecmailing' );
      $admintype = $params->get('usertype');
      $admingroup = $params->get('groupaccess');
      $query=$db->getQuery(true);
      
      if (HecMailingGroupsHelper::isInJoomlaGroupe($admingroup) || HecMailingGroupsHelper::isAdminUserType($admintype))
      {
      	$query->select("grp_id_groupe as id, grp_nm_groupe as name, 3 as flag")
      		  ->from("#__hecmailing_groups");
      }
      else
      {
      	$query->select("DISTINCT g.grp_id_groupe as id, grp_nm_groupe as name, r.flag")
      		->from("#__hecmailing_groups g")
      		->join("INNER", "#__hecmailing_rights r ON r.grp_id_groupe=g.grp_id_groupe")
      		->join("LEFT","#__users u ON u.id=r.userid")
      		->join("LEFT","#__user_usergroup_map m ON  m.group_id=r.groupid AND m.user_id=".$user->id);
      	foreach ($flags as $flag)	
			$query->where("( (r.flag AND $flag)=$flag)");	
      	if (!$all)
      		$query->where("((r.userid=".$user->id." AND ifnull(r.groupid,0)=0) OR (r.groupid=m.group_id AND ifnull(r.userid,0)=0))");
      	
      }      
      if ($published) $query->where("published=1");
      $query->order("grp_nm_groupe");
      $db->setQuery($query);
      if (!$rows = $db->loadObjectList('id'))
      {
          return false;
      }
      else 
      {
      	foreach ($rows as $row){
      		$row = HecMailingGroupsHelper::getRightFromFlagObject($row->flag, $row);
      	}
      }
      return $rows;
     
   }
   /**
    * Method to know if current logged user is in a mailing group
    *
    * @access	public
    * @param	int Groupe identifier
    * @return true is current user is in the group and false else
    */
    public static function isInJoomlaGroupe($group)
    {
    	$db=JFactory::getDBO();
    	$user =JFactory::getUser();
    	if (is_int($group))
    	{
    		$query = "SELECT *
                FROM #__hecmailing_groupdetail gd inner join  #__hecmailing_groups g on gd.grp_id_groupe=g.grp_id_groupe
                WHERE g.grp_id_groupe=".$db->Quote($group)." AND gdet_id_value=".$user->id." AND gdet_cd_type=2";
    		 
    	}
    	else 
    	{
    		$query = "SELECT *
                FROM #__hecmailing_groupdetail gd inner join  #__hecmailing_groups g on gd.grp_id_groupe=g.grp_id_groupe
                WHERE g.grp_nm_groupe=".$db->Quote($group)." AND gdet_id_value=".$user->id." AND gdet_cd_type=2";
    	}
    	$db->setQuery($query);
    	if (!$rows = $db->loadRow()) {	return false; 	}
       	return true;
    }
	  /**
    * Method to know if current logged user is admin
    *
    * @access	public static
    * @param	string List of admin group name (group1,group2)
    * @return true is current user is in admin group and false else
    */
    public static function isAdminUserType($admintype)
    {
    	if(version_compare(JVERSION,'1.6.0','<')){
    		//Code pour Joomla! 1.5
    		return strpos($admintype, $user->usertype);
    	}else{
    		//Code pour Joomla >= 1.6.0
    		$db=JFactory::getDBO();
    		$user =JFactory::getUser();
    		$userid = $user->get( 'id' );
    		$listUserTypeAllowed = explode(";",$admintype);
    		$query = "select count(*) FROM #__usergroups g LEFT JOIN #__user_usergroup_map AS map ON map.group_id = g.id ";
    		$query.= "WHERE map.user_id=".(int) $userid." AND g.title IN ('".join("','",$listUserTypeAllowed)."')";
    		$db->setQuery($query);
    		$rows=$db->loadRow();
    		if (!$rows)
    		{
    			return false;
    		}
    		if ($rows[0]==0)
    		{
    			return false;
    		}
    		return true;
    	}
    }
   public static function getHecmailingGroupsIAdmin($groupid=false)
   {
   	$db=JFactory::getDBO();
   	$user =JFactory::getUser();
   	if ($user->guest) return false;
    $params 	= JComponentHelper::getParams( 'com_hecmailing' );
   	$admintype = $params->get('usertype');
   	$admingroup = $params->get('groupaccess');
   	if ($groupid) $groupcond=" AND r.grp_id_groupe=".$groupid;
   	if (HecMailingGroupsHelper::isInJoomlaGroupe($admingroup) || HecMailingGroupsHelper::isAdminUserType($admintype))
   	{
   		$query = "SELECT r.grp_id_groupe,7 as flag FROM #__hecmailing_groups r Where published=1 $groupcond order by r.grp_nm_groupe";
   	}
   	else
   	{
   		if(version_compare(JVERSION,'1.6.0','<')){
   			$query = "SELECT g.grp_id_groupe,r.flag FROM #__hecmailing_groups g
           			INNER JOIN #__hecmailing_rights r ON r.grp_id_groupe=g.grp_id_groupe
           			LEFT JOIN #__users u ON u.id=r.userid
          			WHERE published=1 AND (r.userid=".$user->id." OR r.groupid=".$user->gid.") $groupcond ORDER BY grp_nm_groupe";
   		}
   		else
   		{
   			$query = "SELECT g.grp_id_groupe,r.flag FROM #__hecmailing_groups g
           			INNER JOIN #__hecmailing_rights r ON r.grp_id_groupe=g.grp_id_groupe
           			WHERE published=1 AND r.userid=".$user->id." $groupcond
                UNION SELECT r.grp_id_groupe,r.flag FROM #__hecmailing_rights r INNER JOIN #__user_usergroup_map map
                ON r.groupid=map.group_id WHERE map.user_id=".$user->id." $groupcond";
   
   		}
   	}
   	$db->setQuery($query);
   	$rows = $db->loadObjectList('grp_id_groupe');
   	if (!$rows) {	return false; 	}
   	foreach($rows as $row)
   	{
   		$row->read = (($row->flag && 1)!=0);
   		$row->write = (($row->flag && 2)!=0);
   		$row->grant = (($row->flag && 4)!=0);
   	}
   	 
   	return $rows;
   }
   public static function getGroupe($groupid)
   {
   		$db=JFactory::getDBO();
   		$user =JFactory::getUser();
   		$params 	= JComponentHelper::getParams( 'com_hecmailing' );
   		$admintype = $params->get('usertype');
   		$admingroup = $params->get('groupaccess');
   	if (HecMailingGroupeHelper::isInJoomlaGroupe($admingroup) || HecMailingGroupeHelper::isAdminUserType($admintype))
   	{
   		$query = "SELECT grp_id_groupe,7 FROM #__hecmailing_groups Where published=1 order by grp_nm_groupe";
   	}
   	else
   	{
   		if(version_compare(JVERSION,'1.6.0','<')){
   			$query = "SELECT g.grp_id_groupe,g.group_nm_groupe,flag as right FROM #__hecmailing_groups g
           			INNER JOIN #__hecmailing_rights r ON r.grp_id_groupe=g.grp_id_groupe
           			LEFT JOIN #__users u ON u.id=r.userid
          			WHERE published=1 AND (r.userid=".$user->id." OR r.groupid=".$user->gid.") ORDER BY grp_nm_groupe";
   		}
   		else
   		{
   			$query = "SELECT g.grp_id_groupe,flag as right FROM #__hecmailing_groups g
           			INNER JOIN #__hecmailing_rights r ON r.grp_id_groupe=g.grp_id_groupe
           			WHERE published=1 AND r.userid=".$user->id."
                UNION SELECT r.grp_id_groupe FROM #__hecmailing_rights r INNER JOIN #__user_usergroup_map map
                ON r.groupid=map.group_id WHERE map.user_id=".$user->id;
   			 
   		}
   	}
   	$db->setQuery($query);
   	$rows = $db->loadObjectList('grp_id_groupe');
   	if (!$rows) {	return false; 	}
   	foreach($rows as $row)
   	{
   		$row->read = (($row->right && 1)!=0);
   		$row->write = (($row->right && 2)!=0);
   		$row->grant = (($row->right && 4)!=0);
   	}
   	 
   	return $rows;
   }
}
?>
