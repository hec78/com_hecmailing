<?php
/**
* @version   3.4.2
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

defined('_JEXEC') or die;
jimport('joomla.error.log');
jimport('joomla.log.log');
/**
 * User group model.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_users
 * @since       1.6
 */
class HecMailingModelGroup extends JModelAdmin
{
	var $log=false;
	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param   type	The table type to instantiate
	 * @param   string	A prefix for the table class name. Optional.
	 * @param   array  Configuration array for model. Optional.
	 * @return  JTable	A database object
	 * @since   1.6
	*/
	public function getTable($type = 'Groupe', $prefix = 'JTable', $config = array())
	{
		$return = JTable::getInstance($type, $prefix, $config);
		return $return;
	}

	
	
	public function getItem ($pk=null)
	{
		$row = JTable::getInstance('groupe', 'Table');
		// load the row from the db table
		if($pk!=0)
		{
			if ($pk>0)
				$row->load( $pk );
			else 
			{
				$row->load( -$pk );
				$row->id=0;
			}
		}
		else
		{
			$row = new StdClass;
    		$row->image='blank.png';
    		$row->id=0;
    		$row->grp_id_groupe=0;
    		$row->grp_nm_groupe='';
    		$row->grp_cm_groupe='';
			$row->text="";
			$row->detail=array();
			$row->perms=array();
			$row->published=1;
			$row->checked_out=0;
			return $row;
		}
		$row->text = "";
		$user 	= JFactory::getUser();
		$row->checkout($user->get('id'));
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('gd.gdet_cd_type, gd.gdet_id_value, gd.gdet_vl_value,gd.gdet_id_detail');
		$query->from('#__hecmailing_groupdetail gd');
		
		/* Table Users */
		$query->select('u.NAME as name');
		$query->join("LEFT", "#__users u ON gd.gdet_id_value=u.id");
		
		/* Table HECMailing.groups */
		$query->select("hg.grp_nm_groupe");
		$query->join("LEFT","#__hecmailing_groups hg on hg.grp_id_groupe = gd.gdet_id_value and gd.gdet_cd_type=5");
		
		if(version_compare(JVERSION,'1.6.0','<')){
			//left join #__core_acl_aro_groups g on g.id=gd.gdet_id_value and gd.gdet_cd_type=3
			$query->select ("g.name AS jgroup_name");
			$query->join("LEFT","#__core_acl_aro_groups g on g.id=gd.gdet_id_value and gd.gdet_cd_type=3");
		}
		else
		{
			//LEFT JOIN #__usergroups gn ON gd.gdet_id_value=gn.id AND gd.gdet_cd_type=3 
			$query->select("gn.title AS jgroup_name");
			$query->join("LEFT","#__usergroups gn ON gd.gdet_id_value=gn.id AND gd.gdet_cd_type=3");
		}
		$query->where ("gd.grp_id_groupe=".$pk);
		$db->setQuery($query);

		try
		{
			$detail = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JError::raiseNotice(500, $e->getMessage());
			return null;
		}
		$row->detail=$detail;
		$query = $db->getQuery(true);
		$query->select("ifnull(ug.userid,0) as userid, ifnull(ug.groupid,0) as groupid,ug.grp_id_groupe,ug.flag");
		$query->from("#__hecmailing_rights ug ");
		$query->select("u.name");
		$query->join("LEFT","#__users u on ug.userid=u.id");
		if(version_compare(JVERSION,'1.6.0','<')){
			$query->select("g.name AS jgroup_name");
			$query->join("LEFT","#__core_acl_aro_groups g on g.id=ug.groupid");
		} else {
			$query->select("gn.title AS jgroup_name");
			$query->join("LEFT","#__usergroups gn ON ug.groupid=gn.id");
		}
		$query->where ("ug.grp_id_groupe=".$pk);
		$db->setQuery($query);
		try
		{
			$perms = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JError::raiseNotice(500, $e->getMessage());
			return null;
		}
		$row->perms=$perms;
		return $row;
	}
	
	public function getUsers()
	{
		$db = JFactory::getDbo();
		$query = "Select id , username,name From #__users order by name";
		$db->setQuery($query);
		$users = $db->loadRowList();
		return $users;
	}
	
	public function getJoomlaGroups()
	{
		$db = JFactory::getDbo();
		if(version_compare(JVERSION,'1.6.0','<')){
			//Code pour Joomla! 1.5  
			$query = "Select id, name From #__core_acl_aro_groups order by id";
		}
		else 
		{
			//Code pour Joomla >= 1.6.0
			$query = "SELECT id, title as name FROM  #__usergroups  ORDER BY id";
		}
		$db->setQuery($query);
		$grp = $db->loadRowList();
		return $grp;
	}
	
	public function getGroups($current_groupe)
	{
		$db = JFactory::getDbo();
		$query = "SELECT grp_id_groupe, grp_nm_groupe FROM  #__hecmailing_groups WHERE grp_id_groupe!=".$current_groupe." ORDER BY grp_nm_groupe"; 
		$db->setQuery($query);
		$grp = $db->loadRowList();
		return $grp;
	}
	
	/**
	 * Method to get the record form.
	 *
	 * @param   array  $data		An optional array of data for the form to interogate.
	 * @param   boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return  JForm	A JForm object on success, false on failure
	 * @since   1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_hecmailing.group', 'group', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 * @since   1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_hecmailing.edit.group.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}

		$this->preprocessData('com_hecmailing.group', $data);

		return $data;
	}

	
	
	/**
	 * Override preprocessForm to load the user plugin group instead of content.
	 *
	 * @param   object	A form object.
	 * @param   mixed	The data expected for the form.
	 * @throws	Exception if there is an error in the form event.
	 * @since   1.6
	 */
	protected function preprocessForm(JForm $form, $data, $groups = '')
	{
		parent::preprocessForm($form, $data, 'hecmailing');
	}

	
	protected function AddLog($type,$info)
	{
		$version = new JVersion();
		if ( (real)$version->RELEASE < 3.0 )
		{
			if (!$this->log) $this->log = &JLog::getInstance('com_hecmailing.log.php');
			$log->addEntry(array($type => $text));
		}
		else
		{
			if (!$this->log) JLog::addLogger(array('text_file' => 'com_hecmailing.log.php', 'text_entry_format' => '{DATETIME} {PRIORITY} {MESSAGE}'));
			$this->log=true;
			if ($type=="error") $type='JLog::ERROR';
			else if ($type=="error") $type='JLog::WARNING';
			JLog::add($info, $type, "com_hecmailing"); 
		}
		
	
	}
	
	/**
	 * Method to save the form data.
	 *
	 * @param   array  The form data.
	 * @return  boolean  True on success.
	 * @since   1.6
	 */
	public function save($data)
	{
		// Modif Joomla 1.6/1.7+
		$error=false;
			
		
		$this->addLog('comment' ,'======= saveObject Group =========');

		// Initialize variables
		$db		=JFactory::getDBO();
		$row	=JTable::getInstance('groupe', 'Table');
		
		$post=array();
		if (!$row->bind( $data )) {
			JError::raiseError(500, $row->getError() );
		}
			
		// pre-save checks
		if (!$row->check()) {
			JError::raiseError(500, $row->getError() );
		}

		// save the changes
		if (!$row->store()) {
			JError::raiseError(500, $row->getError() );
		}
			
		$nbold = $data['nbold'];
		$nbnew= $data['nbnew'];
			
		// Traite les suppression de detail
		$todel = $data['todel'];
		if (isset($todel))
		{
			$listToDel = explode(';',$todel);
			foreach ($listToDel as $item)
			{
				if ($item)
				{
					$query = "delete from #__hecmailing_groupdetail Where gdet_id_detail=".$item;
					$db->setQuery($query);
					if (!($result=$db->query()))
					{
						$this->addLog('comment','query to delete detail='.$query);
						$this->addLog('comment',"Error deleting detail =".$db->stderr());
						$error = "Error Deleting group detail";
					}
					else
					{
						$this->addLog('comment','query='.$query);
					}
				}
			}
		}
		else 
			$this->addLog('comment','No detail to delete '.$todel);
			
		/* Ajoute les detail au groupe */
		for ($i=1;$i<=$nbnew;$i++)
		{
			$v = $data['new'.$i];
			
			if (isset($v))
			{
			  
				$tv = explode(";",$v);
				$t=$tv[0];
				$n=$tv[1];
				$l='';
				if ($t==4)
				{
					$l = $n;
					$n=0;
				}
				$this->addLog('comment','Adding new detail #'.$i."= type ".$t." code ".$n." value ".$l);
			  
				$detail = new stdClass();
				$detail->grp_id_groupe = $row->grp_id_groupe;
				$detail->gdet_cd_type = $t;
				$detail->gdet_id_value = $n;
				$detail->gdet_vl_value =$l; 
				if (!$db->insertObject( '#__hecmailing_groupdetail', $detail, '' )) {
					$this->addLog('comment',"Error=".$db->stderr());
					$error = "Error Adding group detail";
				}
			}
		}
		// Traite import fichier
		$msgimport='';
		$f =   $data['import_file'];
		if (isset($f) && strlen($f['name'])>0)
		{
			$this->addLog('comment',"Import File=".$f['name']);
			if (!$f['error'] )
			{
				$this->addLog('comment',"File Ok=".$f['tmp_name']);
				$ndelim = $data['import_delimiter'];
				$ldelim = $data['import_linedelimiter'];
				switch($ndelim)
				{
					case '1':
						$delim="\t";
						break;
					case '2':
						$delim=";";
						break;
					case '3':
						$delim=",";
						break;
					case '4':
						$delim=" ";
						break;
					default:
						$delim="*";
				}
				switch($ldelim)
				{
					case '1':
						$ldelim="\r\n";
						break;
					case '2':
						$ldelim="\n";
						break;
					case '3':
						$ldelim="\r";
						break;
					default:
						$ldelim="*";
				}
				$col = (int)$data['import_column'];
				$len =$data['import_len'];
				if (isset($len ))
				{
					$len=(int)$len;
				} 
				if (defined('PHP_MAJOR_VERSION') && PHP_MAJOR_VERSION >= 5) 
				{
					$php5=true;
				} 
				else 
				{
					$php5=false;
				}
				$nimport=0;	 
				ini_set("auto_detect_line_endings", true);
				$handle = @fopen($f['tmp_name'], "rb");
				if ($handle) {
					while (!feof($handle)) {
						if ($php5 && $ldelim!="*")
						{
							$buffer = stream_get_line($handle, 4096, "\r"); 
							$this->addLog('comment','stream_get_line('.$ldelim.','.$delim.'):'.$buffer);
						}
						else
						{
							
							$buffer = fgets($handle, 4096);
							$this->addLog('comment','fgets (Col='.$delim.'):'.$buffer);
						}
						$adr=false;
					    if (strlen($buffer)>0 && $buffer)
						{
							$buffer=rtrim($buffer,"\r\n");
							if ($delim=="*"){if ($col+$len<strlen($buffer)) { $adr = substr($buffer,$col,$len); }	
						}
						else
						{
						  $cols = explode($delim,$buffer);
						  if ($col<count($cols)) { $adr=$cols[$col];  }
						}
					}
					if ($adr)
					{
						$this->addLog('comment','import '.$buffer."=".$col.".".$adr.".".$delim);
						$query = "insert into #__hecmailing_groupdetail (grp_id_groupe,gdet_cd_type,gdet_id_value,gdet_vl_value)
							  values (".$row->grp_id_groupe.",4,0,".$db->Quote( $adr, true ).")";
						$db->setQuery($query);
						if (!$db->query())
						{
							$this->addLog('comment',"Error=".$db->stderr());
							$error = JText::sprintf("COM_HECMAILING_GROUP_ERROR_ADD_MAIL",$adr,$db->stderr());
						}
						else
						{
							$this->addLog('comment',"import=".$adr." OK(".$query.")");
						}
						$nimport++;
					}
					$msgimport = JText::sprintf("COM_HECMAILING_GROUP_MSG_IMPORTED",$nimport);
				  }
				  fclose($handle);
				}
				else
				{
					$error = JText::sprintf("COM_HECMAILING_GROUP_ERROR_FILE_OPEN",$f['tmp_name'],$f['size']);
				}
		  
			}
			else
			{
				switch ($f['error']){     
					case 1: // UPLOAD_ERR_INI_SIZE  
						$error=JText::_("COM_HECMAILING_GROUP_ERROR_FILESIZE_LIMIT");     
						break;     
					case 2: // UPLOAD_ERR_FORM_SIZE     
					    $error=JText::_("COM_HECMAILING_GROUP_ERROR_FILESIZE_LIMITFORM"); 
						break;     
					case 3: // UPLOAD_ERR_PARTIAL     
						$error=JText::_("COM_HECMAILING_GROUP_ERROR_TRANSFERT_FILE");     
						break;     
					case 4: // UPLOAD_ERR_NO_FILE     
						$error=JText::_("COM_HECMAILING_GROUP_ERROR_FILE_NULLSIZE"); 
						break;     
				  }     
				  $this->addLog('error',$error);
			  }
			}
			
			// Traite permissions
			$nboldperm = $data['nboldperm'];
			$nbnewperm= $data['nbnewperm'];
			$todelperm = $data['todelperm'];

			if (isset($todelperm) && $todelperm!="")
			{
				$listToDel = explode(';',$todelperm);
				if ($listToDel)
				foreach ($listToDel as $item)
				{
					$k = explode('-', $item);
					$g = $k[0];
					$u=$k[1];
					$jg=$k[2];
					if ($u!="0")
					{
						$cond = "userid=".$u;
						$jg="null";
					}
					else
					{
						$cond = "groupid=".$jg;
						$u="null";
					}
					if ($g!="")
					{
						$query = "delete from #__hecmailing_rights Where grp_id_groupe=".$g." and ".$cond;
						$db->setQuery($query);
						if (!$db->query())
						{
							$this->addLog('comment',"Error=".$db->stderr());
							$error = JText::sprintf("COM_HECMAILING_GROUP_ERROR_DELETE_PERM",$i);
						}
					}
				}
			}
		 
			for ($i=1;$i<=$nbnewperm;$i++)
			{
				$v = $data['newperm'.$i];
				$right_send=$data['newperm_send'.$i];
				$right_manage=$data['newperm_manage'.$i];
				$right_grant=$data['newperm_grant'.$i];
				if (isset($v))
				{
					$tv = explode(";",$v);
					$t=$tv[0];
					$n=$tv[1];
					$l='';
					if ($t==4)	{ $l = $n;	$n=0;}
					if ($t==2)  { $u = $n;	$g=null; }  else  {	$u=null;	$g=$n;  }
					$rights=0;
					if ($right_send==1 || $right_send=="on") $rights+=1;
					if ($right_manage==1 || $right_manage=="on") $rights+=2;
					if ($right_grant==1 || $right_grant=="on") $rights+=4;
					$this->addLog('comment','newperm'.$i."=".$t.".".$n." => ".$rights. "{".$right_send.",".$right_manage.",".$right_grant."}");
					$perm = new stdClass();
					$perm->grp_id_groupe = $row->grp_id_groupe;
					$perm->userid = $u;
					$perm->groupid = $g;
					$perm->flag = $rights;
					if (!$db->insertObject( '#__hecmailing_rights', $perm, '' )) {
						$this->addLog('comment',"Error=".$db->stderr());
						$error = JText::_("COM_HECMAILING_GROUP_ERROR_ADD_PERM");
					}
				}
			}
			$this->addLog('comment','nboldperm'.$nboldperm);
			for ($i=1;$i<=$nboldperm;$i++)
			{
				$v = $data['oldperm'.$i];
				$this->addLog('comment','oldperm'.$i."=".$data['oldperm'.$i]);
				if (isset($v))
				{
					$tv = explode("-",$v);
					$right_send=$data['perm_send'.$i];
					$right_manage=$data['perm_manage'.$i];
					$right_grant=$data['perm_grant'.$i];
					$rights=0;
					if ($right_send==1) $rights+=1;
					if ($right_manage==1) $rights+=2;
					if ($right_grant==1) $rights+=4;
					$query="update #__hecmailing_rights set flag=".$rights." Where grp_id_groupe=".$row->grp_id_groupe." ";
					
					if ($tv[1]!="0") {	$query.=" AND userid=".$tv[1]; }
					else {	$query.=" AND groupid=".$tv[2]; }
					$this->addLog('comment','update right query'.$query." => ".$rights. "{".$right_send.",".$right_manage.",".$right_grant."}");
					$db->setQuery($query);
					if (!$db->query())
					{
						$this->addLog('comment',"Error=".$db->stderr());
						$error = JText::_("COM_HECMAILING_GROUP_ERROR_UPDATE_PERM");
					}
				}
			}
			$row->checkin();
			$this->error=$error;
			return !$error;
		
	}

	/**
	 * Method to delete rows.
	 *
	 * @param   array  An array of item ids.
	 * @return  boolean  Returns true on success, false on failure.
	 * @since   1.6
	 */
	public function delete(&$cid)
	{
		$app = JFactory::getApplication();
		$db		= JFactory::getDBO();
		JArrayHelper::toInteger($cid);

		if (count( $cid )) {
			$cids = implode( ',', $cid );
			$query = 'DELETE FROM #__hecmailing_groups'
				. ' WHERE grp_id_groupe IN ( '. $cids .' )';
			$db->setQuery( $query );
			if (!$db->query()) {
				$this->error=$db->getErrorMsg(true);
				return false;
			}
		}
		else
		{
			$error = JText::_("COM_HECMAILING_GROUP_ERROR_NOGROUP");
			return false;
		}
  		return true;
	}
	
	
}
