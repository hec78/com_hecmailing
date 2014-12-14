<?php
/**
 * @version 1.8.2
 * @package hecmailing
 * @copyright 2009-2013 Hecsoft.net
 * @license http://www.gnu.org/licenses/gpl-3.0.html
 * @link http://joomla.hecsoft.net
 * @author H Cyr
 **/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
  jimport('joomla.error.log');
/*
 * Make sure the user is authorized to view this page
 */
$user = & JFactory::getUser();
$document = &JFactory::getDocument();
$task	= JRequest::getCmd('task');
$id 	= JRequest::getVar('id', 0, 'get', 'int');
$cid 	= JRequest::getVar('cid', array(0), 'post', 'array');
$option = JRequest::getCmd('option');
// Modif Joomla 1.6+
$mainframe = JFactory::getApplication();
JArrayHelper::toInteger($cid, array(0));
require_once( JApplicationHelper::getPath( 'admin_html' ) );
// Set the table directory
JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_hecmailing'.DS.'tables');
if ($task != 'ws-group')
{
	$document->addStyleSheet( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_hecmailing'.DS.'hecmailing.css', 
                          'text/css', null, array() );
}
                          
switch ($task)
{
   
	case 'add' :
		editObject(false );
		break;
	case 'edit':
		editObject(true);
		break;

	case 'apply':
	case 'save':
	case 'save2new':
	case 'save2copy':
		saveObject( $task );
		break;

	case 'remove':
	
		removeObject( $cid );
		break;

	case 'publish':
		changeObject( $cid, 1 );
		break;

	case 'unpublish':
		changeObject( $cid, 0 );
		break;

	case 'orderup':
		//orderObject( $cid[0], -1 );
		break;

	case 'orderdown':
		//orderObject( $cid[0], 1 );
		break;

	case 'accesspublic':
		changeAccess( $cid[0], 0 );
		break;

	case 'accessregistered':
		changeAccess( $cid[0], 1 );
		break;

	case 'accessspecial':
		changeAccess( $cid[0], 2 );
		break;

	case 'saveorder':
		saveOrder( $cid );
		break;

	case 'cancel':
		cancelObject();
		break;

  case 'templates':
    showTemplates();
    break;
  
  case 'delTemplate':
    delTemplates($cid);
    break;
    case 'contact':
    case 'cancelContact':
    	showContact();
    break;
    case 'edit_contact':
    case 'editContact':
    	$idContact 	= JRequest::getVar('contactid', 0, 'get', 'int');
    	editContact($idContact);
		break;
    case 'new_contact':
    case 'newContact':
    	
    	editContact(0);
		break;
    case 'delContact':
		delContact($cid);
		break;
    case 'saveContact':
    	saveContact();
    break;
     case 'groups':
	
		showObjects( $option );
		break;
	case 'upgrade':
       updateComponent(false);

	    break;
	case 'upgradetest':
       updateComponent(true);

	    break;
	case 'param':
       showPanel();
		break;
	case 'ws-group':
		$groupType = JRequest::getVar('grouptype', 0, 'get', 'int');
		$groupId = JRequest::getVar('groupid', 0, 'get', 'int');
		GroupListWebService($groupType, $groupId);
		exit;
		break;
	default:
           showObjects( $option );
		break;
}

/**
* List the records
* @param string The current GET/POST option
*/
function showObjects( $option )
{
	// Modif Joomla 1.6/1.7+
    $mainframe = JFactory::getApplication();


	$db					=& JFactory::getDBO();
	$filter_order		= $mainframe->getUserStateFromRequest( $option.'filter_order', 		'filter_order', 	'cd.ordering',	'cmd' );
	$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option.'filter_order_Dir',	'filter_order_Dir',	'',				'word' );
	$filter_state 		= $mainframe->getUserStateFromRequest( $option.'filter_state', 		'filter_state', 	'',				'word' );
	$filter_catid 		= $mainframe->getUserStateFromRequest( $option.'filter_catid', 		'filter_catid',		0,				'int' );
	$search 			= $mainframe->getUserStateFromRequest( $option.'search', 			'search', 			'',				'string' );
	$search 			= JString::strtolower( $search );

	$limit		= $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
	$limitstart	= $mainframe->getUserStateFromRequest($option.'.limitstart', 'limitstart', 0, 'int');

	$where = array();

/*	if ( $search ) {
		$where[] = 'cd.name LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
	}
	if ( $filter_catid ) {
		$where[] = 'cd.catid = '.(int) $filter_catid;
	}
	if ( $filter_state ) {
		if ( $filter_state == 'P' ) {
			$where[] = 'cd.published = 1';
		} else if ($filter_state == 'U' ) {
			$where[] = 'cd.published = 0';
		}
	}
*/
  if ($filter_order != 'g.grp_nm_groupe' && $filter_order != 'g.grp_cm_groupe' && $filter_order != 'g.published' && $filter_order != 'grp_nb_item' && $filter_order != 'g.grp_id_groupe')
    $filter_order= 'cd.ordering';
	$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
	if ($filter_order == 'cd.ordering'){
		$orderby 	= ' ORDER BY grp_id_groupe';
	} else {
		$orderby 	= ' ORDER BY '. $filter_order .' '. $filter_order_Dir ;
	}

	// get the total number of records
	$query = 'SELECT COUNT(*)'
	. ' FROM #__hecmailing_groups AS g '
	. $where
	;
	$db->setQuery( $query );
	$total = $db->loadResult();

	jimport('joomla.html.pagination');
	$pageNav = new JPagination( $total, $limitstart, $limit );

	// get the subset (based on limits) of required records
	$query = 'SELECT g.*, count(gd.grp_id_groupe) as grp_nb_item '
	. ' FROM #__hecmailing_groups AS g Left Join #__hecmailing_groupdetail gd on g.grp_id_groupe=gd.grp_id_groupe '
	. $where
	. 'GROUP BY grp_id_groupe'
	. $orderby
	;
	$db->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
	$rows = $db->loadObjectList();
	foreach ($rows as $r)
	{
		$r->checked_out=false;
	}
	// build list of categories
	$javascript = 'onchange="document.adminForm.submit();"';
	$lists['catid'] = JHTML::_('list.category',  'filter_catid', 'com_contact_details', intval( $filter_catid ), $javascript );

	// state filter
	$lists['state']	= JHTML::_('grid.state',  $filter_state );

	// table ordering
	$lists['order_Dir']	= $filter_order_Dir;
	$lists['order']		= $filter_order;

	// search filter
	$lists['search']= $search;

	HTML_hecmailing::showObjects( $rows, $pageNav, $option, $lists );
}

/**
* Creates a new or edits and existing user record
* @param int The id of the record, 0 if a new entry
* @param string The current GET/POST option
*/
function editObject($edit )
{
	$db		=& JFactory::getDBO();
	$user 	=& JFactory::getUser();

	$cid 	= JRequest::getVar('cid', array(0), '', 'array');
	$option = JRequest::getCmd('option');
	
	JArrayHelper::toInteger($cid, array(0));

	$row =& JTable::getInstance('groupe', 'Table');
	// load the row from the db table
	if($edit)
	$row->load( $cid[0] );
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
       $query = "Select gd.gdet_cd_type, gd.gdet_id_value, gd.gdet_vl_value,gd.gdet_id_detail, u.name, g.name ,hg.grp_nm_groupe 
       		From #__hecmailing_groupdetail gd 
  			left join #__users u on gd.gdet_id_value=u.id left join #__core_acl_aro_groups g on g.id=gd.gdet_id_value and gd.gdet_cd_type=3
			left join #__hecmailing_groups hg on hg.grp_id_groupe = gd.gdet_id_value and gd.gdet_cd_type=5
   			Where gd.grp_id_groupe=".$cid[0];
  	}else{
      //Code pour Joomla >= 1.6.0
      $query = "SELECT gd.gdet_cd_type, gd.gdet_id_value, gd.gdet_vl_value,gd.gdet_id_detail, u.NAME, gn.title,hg.grp_nm_groupe 
				FROM #__hecmailing_groupdetail gd LEFT JOIN #__users u ON gd.gdet_id_value=u.id  AND gd.gdet_cd_type=2 
				LEFT JOIN #__usergroups gn ON gd.gdet_id_value=gn.id AND gd.gdet_cd_type=3 
				left join #__hecmailing_groups hg on hg.grp_id_groupe = gd.gdet_id_value and gd.gdet_cd_type=5
				WHERE gd.grp_id_groupe=".$cid[0];  
  	}
  
  $db->setQuery($query);
  
  $detail = $db->loadRowList();
  
   if(version_compare(JVERSION,'1.6.0','<')){
       //Code pour Joomla! 1.5  
		    $query = "Select ifnull(ug.userid,0) as userid, ifnull(ug.groupid,0) as groupid,ug.grp_id_groupe, u.name, g.name  , ug.flag
		    From #__hecmailing_rights ug 
		  left join #__users u on ug.userid=u.id left join #__core_acl_aro_groups g on g.id=ug.groupid
		  
		   Where ug.grp_id_groupe=".$cid[0];
	}else{
      //Code pour Joomla >= 1.6.0
       $query = "Select ifnull(ug.userid,0) as userid, ifnull(ug.groupid,0) as groupid,ug.grp_id_groupe, u.name,  gn.title AS NAME  , ug.flag
       	  From #__hecmailing_rights ug 
		  left join #__users u on ug.userid=u.id 
		  LEFT JOIN #__usergroups gn ON ug.groupid=gn.id
		   Where ug.grp_id_groupe=".$cid[0];
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
	$usersperm = JHTML::_('select.genericlist',  $ulist, 'newuserperm', 'class="inputbox" size="1"', 'id', 'name', 0);
	if($edit)
		$lists['ordering'] 			= JHTML::_('list.specificordering',  $row, $cid[0], $query );
	else
		$lists['ordering'] 			= JHTML::_('list.specificordering',  $row, '', $query );
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
  
  $groups = JHTML::_('select.genericlist',  $glist, 'newgroupej', 'class="inputbox" size="1"', 'id', 'name', 0);
  $groupsperm = JHTML::_('select.genericlist',  $glist, 'newgroupperm', 'class="inputbox" size="1"', 'id', 'name', 0);
	
	
  $query = "SELECT grp_id_groupe, grp_nm_groupe FROM  #__hecmailing_groups WHERE grp_id_groupe!=".$cid[0]." ORDER BY grp_nm_groupe"; 

  $db->setQuery($query);
  $grp = $db->loadRowList();
  $heclist = array();
  if ($grp) 
    foreach($grp as $g)
    {
    	$heclist[] = JHTML::_('select.option', $g[0], $g[1], 'id', 'name');
    }
  
  $hecgroups = JHTML::_('select.genericlist',  $glist, 'newgroupe', 'class="inputbox" size="1"', 'id', 'name', 0);
	
  $typesgroupselmt=array();
  $typesgroupselmt[] = JHTML::_('select.option', 3, "Joomla", 'id', 'name');
  $typesgroupselmt[] = JHTML::_('select.option', 5, "HEC MAiling", 'id', 'name');
  $typesgroups = JHTML::_('select.genericlist',  $typesgroupselmt, 'typegroupe', 'class="inputbox" size="1" onChange="changeType(webservice,this.options[this.selectedIndex].value, '.$cid[0].');"', 'id', 'name', 0);
	// build the html radio buttons for published
	$lists['published'] 		= JHTML::_('select.booleanlist',  'published', '', $row->published );
	
	// get params definitions
	$file 	= JPATH_ADMINISTRATOR .'/components/com_hecmailing/config.xml';
	$paramstxt="";
	$params = new JParameter( $paramstxt, $file, 'component' );

	HTML_hecmailing::editObject( $row, $lists, $detail, $params, $users, $groups, $perms, $hecgroups, $typesgroups,$usersperm, $groupsperm  );
}

/**
* Saves the record from an edit form submit
* @param string The current GET/POST option
*/
function saveObject( $task )
{
	// Modif Joomla 1.6/1.7+
    $mainframe = JFactory::getApplication();
	$error=false;
	// Check for request forgeries
	JRequest::checkToken() or jexit( 'Invalid Token' );
    $log = &JLog::getInstance('com_hecmailing.log.php');
    $log->addEntry(array('comment' => '======= saveObject Group ========='));
	// Initialize variables
	$db		=& JFactory::getDBO();
	$row	=& JTable::getInstance('groupe', 'Table');
	$tmppost = JRequest::get( 'post' );
	$tmpfile = JRequest::get( 'FILES' );
	// $tmpfile = JRequest::getVar('jform', null, 'files', 'array');
	$post=array();
	$post['grp_id_groupe'] = $tmppost['grp_id_groupe'];
	$post['grp_nm_groupe'] = $tmppost['grp_nm_groupe'];
	$post['grp_cm_groupe'] = $tmppost['grp_cm_groupe'];
	$post['published'] = $tmppost['published'];
	if (!$row->bind( $post )) {
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
	
	
	$nbold = $tmppost['nbold'];
	$nbnew= $tmppost['nbnew'];
	
	// Traite les suppression de detail
	$todel = $tmppost['todel'];
    if (isset($todel))
    {
      $listToDel = split(';',$todel);
      
      foreach ($listToDel as $item)
      {
      	if ($item)
      	{
	        $query = "delete from #__hecmailing_groupdetail Where gdet_id_detail=".$item;
	        $db->setQuery($query);
	        if (!($result=$db->query()))
	        {
	        	$log->addEntry(array('comment'=>'query to delete detail='.$query));
	        	$log->addEntry(array('comment'=>"Error deleting detail =".$db->stderr()));
	      		$error = "Error Deleting group detail";
	        }
	        else
	        {
	        	$log->addEntry(array('comment'=>'query='.$query));
	        }
      	}
      }
    }
    else 
    	$log->addEntry(array('comment'=>'No detail to delete '.$todel));
    
    /*if ($tmpfile)
      foreach ($tmpfile as $k=>$e)
      {
          $log->addEntry(array('comment'=>'FILE:'.$k."=".$e));
          if ($e)
            foreach($e as $kk=>$i)
            {
                $log->addEntry(array('comment'=>'FILE:'.$kk."=+".$i));
            }
      }*/
  /* Ajoute les detail au groupe */
  for ($i=1;$i<=$nbnew;$i++)
  {
    $v = $tmppost['new'.$i];
    
    if (isset($v))
    {
      
      $tv = split(";",$v);
      $t=$tv[0];
      $n=$tv[1];
      $l='';
      if ($t==4)
      {
        $l = $n;
        $n=0;
      }
      $log->addEntry(array('comment'=>'Adding new detail #'.$i."= type ".$t." code ".$n." value ".$l));
      
      $detail = new stdClass();
	  $detail->grp_id_groupe = $row->grp_id_groupe;
	  $detail->gdet_cd_type = $t;
	  $detail->gdet_id_value = $n;
	  $detail->gdet_vl_value =$l; 
	  if (!$db->insertObject( '#__hecmailing_groupdetail', $detail, '' )) {
    	$log->addEntry(array('comment'=>"Error=".$db->stderr()));
      	$error = "Error Adding group detail";
    	
  	 }
    }
  }
  // Traite import fichier
  $msgimport='';
  $f =   $tmpfile['import_file'];
  if (isset($f) && strlen($f['name'])>0)
  {
	
      
    
	     $log->addEntry(array('comment'=>"Import File=".$f['name']));
      if (!$f['error'] )
      {
		  $log->addEntry(array('comment'=>"File Ok=".$f['tmp_name']));
          $ndelim = $tmppost['import_delimiter'];
		  $ldelim = $tmppost['import_linedelimiter'];
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
          $col = (int)$tmppost['import_column'];
          $len =$tmppost['import_len'];
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
					$log->addEntry(array('comment'=>'stream_get_line('.$ldelim.','.$delim.'):'.$buffer));
				}
				else
				{
					
					$buffer = fgets($handle, 4096);
					$log->addEntry(array('comment'=>'fgets (Col='.$delim.'):'.$buffer));
				}
              $adr=false;
			  
              if (strlen($buffer)>0 && $buffer)
              {
				$buffer=rtrim($buffer,"\r\n");
                if ($delim=="*")
                {
                  if ($col+$len<strlen($buffer))
                  {
                    $adr = substr($buffer,$col,$len);
                  }
                }
                else
                {
                  $cols = split($delim,$buffer);
                  if ($col<count($cols))
                  {
                    $adr=$cols[$col];
                  }
                }
              }
              if ($adr)
              {
                $log->addEntry(array('comment'=>'import '.$buffer."=".$col.".".$adr.".".$delim));
                $query = "insert into #__hecmailing_groupdetail (grp_id_groupe,gdet_cd_type,gdet_id_value,gdet_vl_value)
                      values (".$row->grp_id_groupe.",4,0,".$db->Quote( $adr, true ).")";
				$db->setQuery($query);
				if (!$db->query())
				{
					$log->addEntry(array('comment'=>"Error=".$db->stderr()));
					$error = "Error Adding email ".adr." from file :".$db->stderr();
				}
                else
				{
					$log->addEntry(array('comment'=>"import=".$adr." OK(".$query.")"));
                }
				$nimport++;
			}
            $msgimport = " - ".$nimport." adresses import&eacute;e(s)";
          }
          fclose($handle);
        }
		else
		{
			$error = "Probleme ouverture fichier ".$f['tmp_name']."(".$f['size'].")";
		}
  
      }
	  else
	  {
		switch ($f['error']){     
                   case 1: // UPLOAD_ERR_INI_SIZE  
					
                   $error="Le fichier dépasse la limite autorisée par le serveur (fichier php.ini) !";     
                   break;     
                   case 2: // UPLOAD_ERR_FORM_SIZE     
                   $error="Le fichier dépasse la limite autorisée dans le formulaire HTML !"; 
                   break;     
                   case 3: // UPLOAD_ERR_PARTIAL     
                   $error="L'envoi du fichier a été interrompu pendant le transfert !";     
                   break;     
                   case 4: // UPLOAD_ERR_NO_FILE     
                   $error="Le fichier que vous avez envoyé a une taille nulle !"; 
                   break;     
          }     
		  $log->addEntry(array('error'=>$error));
	  }
    }
    
  // Traite permissions
    $nboldperm = $tmppost['nboldperm'];
	$nbnewperm= $tmppost['nbnewperm'];
	$todelperm = $tmppost['todelperm'];

    if (isset($todelperm))
    {
      $listToDel = split(';',$todelperm);
      if ($listToDel)
      foreach ($listToDel as $item)
      {
      	$k = split('-', $item);
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
				$log->addEntry(array('comment'=>"Error=".$db->stderr()));
				$error = "Error Deletin group perm ".$i;
			}
		}
     
        }
    }
 
  for ($i=1;$i<=$nbnewperm;$i++)
  {
    $v = $tmppost['newperm'.$i];
    $right_send=$tmppost['newperm_send'.$i];
	$right_manage=$tmppost['newperm_manage'.$i];
	$right_grant=$tmppost['newperm_grant'.$i];
    if (isset($v))
    {
      
      $tv = split(";",$v);
      $t=$tv[0];
      $n=$tv[1];
      $l='';
      if ($t==4)
      {
        $l = $n;
        $n=0;
      }
      
      if ($t==2)
      { 
      	$u=$n;
      	$g=null;
      }
      else
      {
      	$u=null;
      	$g=$n;
      }
	  
	  $rights=0;
	  if ($right_send==1 || $right_send=="on") $rights+=1;
	  if ($right_manage==1 || $right_manage=="on") $rights+=2;
	  if ($right_grant==1 || $right_grant=="on") $rights+=4;
	  $log->addEntry(array('comment'=>'newperm'.$i."=".$t.".".$n." => ".$rights. "{".$right_send.",".$right_manage.",".$right_grant."}"));
      /*$query = "insert into #__hecmailing_rights (grp_id_groupe,userid,groupid)
          values (".$row->grp_id_groupe.",".$u.",".$g.")";
      
      $db->execute($query);
      $log->addEntry(array('comment'=>"Query=".$query));*/
      $perm = new stdClass();
	  $perm->grp_id_groupe = $row->grp_id_groupe;
	  $perm->userid = $u;
	  $perm->groupid = $g;
	  $perm->flag = $rights;
	   
	  if (!$db->insertObject( '#__hecmailing_rights', $perm, '' )) {
    	$log->addEntry(array('comment'=>"Error=".$db->stderr()));
      	$error = "Error Adding group perm";
    	
  	 }
    }
  }
	$log->addEntry(array('comment'=>'nboldperm'.$nboldperm));
   for ($i=1;$i<=$nboldperm;$i++)
  {
    $v = $tmppost['oldperm'.$i];
	$log->addEntry(array('comment'=>'oldperm'.$i."=".$tmppost['oldperm'.$i]));
	if (isset($v))
    {
      	$tv = split(";",$v);
		$right_send=$tmppost['perm_send'.$i];
		$right_manage=$tmppost['perm_manage'.$i];
		$right_grant=$tmppost['perm_grant'.$i];
		$rights=0;
		if ($right_send==1) $rights+=1;
		if ($right_manage==1) $rights+=2;
		if ($right_grant==1) $rights+=4;
		$query="update  #__hecmailing_rights set flag=".$rights." Where grp_id_groupe=".$row->grp_id_groupe." ";
		
		if ($tv[1]!="0") {	$query.=" AND userid=".$tv[1]; }
		else {	$query.=" AND groupid=".$tv[2]; }
		$log->addEntry(array('comment'=>'update right query'.$query." => ".$rights. "{".$right_send.",".$right_manage.",".$right_grant."}"));
		$db->setQuery($query);
		if (!$db->query())
		{
			$log->addEntry(array('comment'=>"Error=".$db->stderr()));
			$error = "Error Updating group perm";
    	
		}
	}
	}
	$row->checkin();
	

	switch ($task)
	{
		case 'apply':
		case 'save2copy':
			if (!$error)
				$msg	= JText::_( 'GROUPE_SAVED' );
			else 
				$msg=$error;
			$link	= 'index.php?option=com_hecmailing&task=edit&cid[]='. $row->grp_id_groupe .'';
			break;

		case 'save2new':
			if (!$error)
				$msg	= JText::sprintf( 'CHANGES_TO_X_SAVED', 'Group' );
			else 
				$msg=$error;
			$link	= 'index.php?option=com_hecmailing&task=edit';
			break;

		case 'save':
		default:
			if (!$error)
				$msg	= JText::_( 'GROUPE_SAVED') .$msgimport;
			else 
				$msg=$error;
			$link	= 'index.php?option=com_hecmailing';
			break;
	}

	$mainframe->redirect( $link, $msg );
}

/**
* Removes records
* @param array An array of id keys to remove
* @param string The current GET/POST option
*/
function removeObject( &$cid )
{
	// Modif Joomla 1.6/1.7+
    $mainframe = JFactory::getApplication();

	// Check for request forgeries
	JRequest::checkToken() or jexit( 'Invalid Token' );
  echo "<script>alert('".JText::_("REMOVE_ITEM")."');</script>";
	// Initialize variables
	$db =& JFactory::getDBO();
	JArrayHelper::toInteger($cid);

	if (count( $cid )) {
		$cids = implode( ',', $cid );
		$query = 'DELETE FROM #__hecmailing_groups'
		. ' WHERE grp_id_groupe IN ( '. $cids .' )'
		;
		$db->setQuery( $query );
		if (!$db->query()) {
			echo "<script> alert('".$db->getErrorMsg(true)."'); window.history.go(-1); </script>\n";
		}
	}
  $msg = count($cid)." ".JText::_("GROUP_DELETED");
	$mainframe->redirect( "index.php?option=com_hecmailing" ,$msg);
}

/**
* Changes the state of one or more content pages
* @param array An array of unique category id numbers
* @param integer 0 if unpublishing, 1 if publishing
* @param string The current option
*/
function changeObject( $cid=null, $state=0 )
{
    // Modif Joomla 1.6/1.7+
    $mainframe = JFactory::getApplication();
    
    if ($state==1) {
         $publish = 1;
         $msg=JText::_("GROUP_PUBLISHED");
            }
        else {
            $publish = 0;
            $msg=JText::_("GROUP_UNPUBLISHED");
            }
    $mailTable =& JTable::getInstance('groupe', 'Table');
    
    $mailTable->publish($cid, $publish);
    $msg = count($cid)." ".$msg;
    	$mainframe->redirect( "index.php?option=com_hecmailing", $msg );
}

/** JJC
* Moves the order of a record
* @param integer The increment to reorder by
*/
function orderContacts( $uid, $inc )
{
	// Modif Joomla 1.6/1.7+
    $mainframe = JFactory::getApplication();

	// Check for request forgeries
	JRequest::checkToken() or jexit( 'Invalid Token' );

	// Initialize variables
	$db =& JFactory::getDBO();

	$row =& JTable::getInstance('object', 'Table');
	$row->load( $uid );
	$row->move( $inc, 'catid = '. (int) $row->catid .' AND published != 0' );

	$mainframe->redirect( 'index.php?option=com_hecmailing' );
}

/** PT
* Cancels editing and checks in the record
*/
function cancelObject()
{
	// Modif Joomla 1.6/1.7+
    $mainframe = JFactory::getApplication();

	// Check for request forgeries
	JRequest::checkToken() or jexit( 'Invalid Token' );

	// Initialize variables
	$db =& JFactory::getDBO();
	$row =& JTable::getInstance('groupe', 'Table');
	$row->bind( JRequest::get( 'post' ));
	$row->checkin();

	$mainframe->redirect('index.php?option=com_hecmailing');
}

/**
* changes the access level of a record
* @param integer The increment to reorder by
*/
function changeAccess( $id, $access  )
{
	// Modif Joomla 1.6/1.7+
    $mainframe = JFactory::getApplication();

	// Check for request forgeries
	JRequest::checkToken() or jexit( 'Invalid Token' );

	// Initialize variables
	$db =& JFactory::getDBO();

	$row =& JTable::getInstance('hecmailing', 'Table');
	$row->load( $id );
	$row->access = $access;

	if ( !$row->check() ) {
		return $row->getError();
	}
	if ( !$row->store() ) {
		return $row->getError();
	}

	$mainframe->redirect( 'index.php?option=com_hecmailing' );
}

function saveOrder( &$cid )
{
	// Modif Joomla 1.6/1.7+
    $mainframe = JFactory::getApplication();

	// Check for request forgeries
	JRequest::checkToken() or jexit( 'Invalid Token' );

	// Initialize variables
	$db			=& JFactory::getDBO();
	$total		= count( $cid );
	$order 		= JRequest::getVar( 'order', array(0), 'post', 'array' );
	JArrayHelper::toInteger($order, array(0));

	$row =& JTable::getInstance('object', 'Table');
	$groupings = array();

	// update ordering values
	for( $i=0; $i < $total; $i++ ) {
		$row->load( (int) $cid[$i] );
		// track categories
		$groupings[] = $row->catid;

		if ($row->ordering != $order[$i]) {
			$row->ordering = $order[$i];
			if (!$row->store()) {
				JError::raiseError(500, $db->getErrorMsg() );
			}
		}
	}

	// execute updateOrder for each parent group
	$groupings = array_unique( $groupings );
	if ($groupings)
	 foreach ($groupings as $group){
	   	$row->reorder('catid = '.(int) $group);
	}

	$msg 	= JText::_('MSG_NEW_ORDERING_SAVED');
	$mainframe->redirect( 'index.php?option=com_hecmailing', $msg );
}

function showTemplates()
{
 	// Modif Joomla 1.6/1.7+
    $mainframe = JFactory::getApplication();
	$option = JRequest::getCmd('option');

	$db					=& JFactory::getDBO();
	$filter_order		= $mainframe->getUserStateFromRequest( $option.'filter_order', 		'filter_order', 	'cd.ordering',	'cmd' );
	$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option.'filter_order_Dir',	'filter_order_Dir',	'',				'word' );
	$search 			= $mainframe->getUserStateFromRequest( $option.'search', 			'search', 			'',				'string' );
	$search 			= JString::strtolower( $search );

	$limit		= $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
	$limitstart	= $mainframe->getUserStateFromRequest($option.'.limitstart', 'limitstart', 0, 'int');

	$where = array();
  if ($filter_order != 'msg_lb_message' && $filter_order != 'msg_id_message')
    $filter_order= 'cd.ordering';
	$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
	if ($filter_order == 'cd.ordering'){
		$orderby 	= ' ORDER BY msg_id_message';
	} else {
		$orderby 	= ' ORDER BY '. $filter_order .' '. $filter_order_Dir ;
	}

	// get the total number of records
	$query = 'SELECT COUNT(*)'
	. ' FROM #__hecmailing_save AS s '
	. $where
	;
	$db->setQuery( $query );
	$total = $db->loadResult();

	jimport('joomla.html.pagination');
	$pageNav = new JPagination( $total, $limitstart, $limit );

	// get the subset (based on limits) of required records
	$query = 'SELECT * '
	. ' FROM #__hecmailing_save '
	. $where
	. $orderby
	;
	
	$db->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
	$rows = $db->loadObjectList();

	// build list of categories
	//$javascript = 'onchange="document.adminForm.submit();"';
	//$lists['catid'] = JHTML::_('list.category',  'filter_catid', 'com_contact_details', intval( $filter_catid ), $javascript );

	// state filter
	//$lists['state']	= JHTML::_('grid.state',  $filter_state );

	// table ordering
	$lists['order_Dir']	= $filter_order_Dir;
	$lists['order']		= $filter_order;

	// search filter
	$lists['search']= $search;

	HTML_hecmailing::showTemplates( $rows, $pageNav, $option, $lists );
}

/**
* Removes records
* @param array An array of id keys to remove
* @param string The current GET/POST option
*/
function delTemplates( &$cid )
{
	// Modif Joomla 1.6/1.7+
    $mainframe = JFactory::getApplication();

	// Check for request forgeries
	JRequest::checkToken() or jexit( 'Invalid Token' );
  echo "<script>alert('".JText::_("REMOVE_ITEM")."');</script>";
	// Initialize variables
	$db =& JFactory::getDBO();
	JArrayHelper::toInteger($cid);

	if (count( $cid )) {
		$cids = implode( ',', $cid );
		$query = 'DELETE FROM #__hecmailing_save'
		. ' WHERE msg_id_message IN ( '. $cids .' )'
		;
		$db->setQuery( $query );
		if (!$db->query()) {
			echo "<script> alert('".$db->getErrorMsg(true)."'); window.history.go(-1); </script>\n";
		}
	}
  $msg = count($cid).JText::_('MSG_TEMPLATE_DELETED');
	$mainframe->redirect( "index.php?option=com_hecmailing&task=templates" ,$msg);
}

function showContact()
{
    // Modif Joomla 1.6/1.7+
    $mainframe = JFactory::getApplication();
	$option = JRequest::getCmd('option');
	$db					=& JFactory::getDBO();
	$filter_order		= $mainframe->getUserStateFromRequest( $option.'filter_order', 		'filter_order', 	'cd.ordering',	'cmd' );
	$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option.'filter_order_Dir',	'filter_order_Dir',	'',				'word' );
	$search 			= $mainframe->getUserStateFromRequest( $option.'search', 			'search', 			'',				'string' );
	$search 			= JString::strtolower( $search );

	$limit		= $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
	$limitstart	= $mainframe->getUserStateFromRequest($option.'.limitstart', 'limitstart', 0, 'int');

	$where = array();
  if ($filter_order != 'ct_nm_contact' && $filter_order != 'ct_id_contact')
    $filter_order= 'cd.ordering';
	$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
	if ($filter_order == 'cd.ordering'){
		$orderby 	= ' ORDER BY ct_id_contact';
	} else {
		$orderby 	= ' ORDER BY '. $filter_order .' '. $filter_order_Dir ;
	}

	// get the total number of records
	$query = 'SELECT COUNT(*)'
	. ' FROM #__hecmailing_contact AS s '
	. $where
	;
	$db->setQuery( $query );
	$total = $db->loadResult();

	jimport('joomla.html.pagination');
	$pageNav = new JPagination( $total, $limitstart, $limit );

	// get the subset (based on limits) of required records
	$query = 'SELECT * '
	. ' FROM #__hecmailing_contact left join #__hecmailing_groups on #__hecmailing_contact.grp_id_groupe=#__hecmailing_groups.grp_id_groupe '
	. $where
	. $orderby
	;
	
	$db->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
	$rows = $db->loadObjectList();

	// build list of categories
	//$javascript = 'onchange="document.adminForm.submit();"';
	//$lists['catid'] = JHTML::_('list.category',  'filter_catid', 'com_contact_details', intval( $filter_catid ), $javascript );

	// state filter
	//$lists['state']	= JHTML::_('grid.state',  $filter_state );

	// table ordering
	$lists['order_Dir']	= $filter_order_Dir;
	$lists['order']		= $filter_order;

	// search filter
	$lists['search']= $search;

	HTML_hecmailing::showContact( $rows, $pageNav, $option, $lists );
}

/**
* Edit a contact info
* @param array An array of unique category id numbers
* @param integer 0 if unpublishing, 1 if publishing
* @param string The current option
*/
function editContact(  $edit )
{
	global $cid;
    $db		=& JFactory::getDBO();
	$user 	=& JFactory::getUser();

	$id 	= JRequest::getVar('contactid', 0, 'get', 'int');
	if ($id==0) $id=$cid[0];
	$option = JRequest::getCmd('option');


	$lists = array();
  	$query = "Select ct_id_contact ,grp_id_groupe,ct_nm_contact ,ct_cm_contact,ct_vl_info , ct_vl_template ,ct_vl_prefixsujet 
  		From #__hecmailing_contact  
        Where ct_id_contact=".$id;
  $db->setQuery($query);
  $detail = $db->loadObject();
  if ($detail)
  {
  	$default_group_id = $detail->grp_id_groupe;
  }    
  else
  {
  	$default_group_id = 0;
  }
  $query = "Select grp_id_groupe, grp_nm_groupe From #__hecmailing_groups order by grp_nm_groupe";
  $db->setQuery($query);
  $groups = $db->loadRowList();
  $glist=array();
  if ($groups)
  	foreach($groups as $g)
  	{
      $glist[] = JHTML::_('select.option', $g[0], $g[1], 'grp_id_groupe', 'grp_nm_groupe');
    }
	$groups = JHTML::_('select.genericlist',  $glist, 'grp_id_groupe', 'class="inputbox" size="1"', 'grp_id_groupe', 'grp_nm_groupe', $default_group_id);
	
	// get params definitions
	$file 	= JPATH_ADMINISTRATOR .'/components/com_hecmailing/config.xml';
	$mesparams = '';
	$params = new JParameter( $mesparams, $file);

	HTML_hecmailing::editContact( $id,$groups,$detail, $params );
}

/**
* Removes records
* @param array An array of id keys to remove
* @param string The current GET/POST option
*/
function delContact( &$cid )
{
	// Modif Joomla 1.6/1.7+
    $mainframe = JFactory::getApplication();

	// Check for request forgeries
	JRequest::checkToken() or jexit( 'Invalid Token' );
  echo "<script>alert('".JText::_("REMOVE_ITEM")."');</script>";
	// Initialize variables
	$db =& JFactory::getDBO();
	JArrayHelper::toInteger($cid);

	if (count( $cid )) {
		$cids = implode( ',', $cid );
		$query = 'DELETE FROM #__hecmailing_contact'
		. ' WHERE ct_id_contact IN ( '. $cids .' )'
		;
		$db->setQuery( $query );
		if (!$db->query()) {
			echo "<script> alert('".$db->getErrorMsg(true)."'); window.history.go(-1); </script>\n";
		}
	}
  $msg = count($cid).JText::_('MSG_CONTACT_DELETED');
	$mainframe->redirect( "index.php?option=com_hecmailing&task=contact" ,$msg);
}


/**
* Removes records
* @param array An array of id keys to remove
* @param string The current GET/POST option
*/
function saveContact( )
{
	// Modif Joomla 1.6/1.7+
    $mainframe = JFactory::getApplication();

	// Check for request forgeries
	//JRequest::checkToken() or jexit( 'Invalid Token' );
 
 	$post = JRequest::get( 'post' );
 	$ct_id_contact = $post['ct_id_contact'];
	$grp_id_groupe = $post['grp_id_groupe'];
	$ct_nm_contact = $post['ct_nm_contact'];
	$ct_vl_prefixsujet = $post['ct_vl_prefixsujet'];
	//$ct_cm_contact = $post['ct_cm_contact'];
	$ct_vl_info = JRequest::getVar('ct_vl_info', '', 'post', 'string', JREQUEST_ALLOWRAW); //$post['ct_vl_info'];
	//$ct_vl_info=htmlspecialchars($ct_vl_info);
	$ct_vl_template = JRequest::getVar('ct_vl_template', '', 'post', 'string', JREQUEST_ALLOWRAW);
	$ct_vl_info = html_entity_decode($ct_vl_info);
	//$ct_vl_info = nl2br($ct_vl_info);
	//$ct_vl_template = nl2br($ct_vl_template);
	// Initialize variables
	$db =& JFactory::getDBO();
	$msg = JText::_('MSG_CONTACT_SAVED');
  	if ($ct_id_contact>0)
	{
		$query = 'UPDATE #__hecmailing_contact set grp_id_groupe='.$grp_id_groupe.',ct_nm_contact='.$db->Quote($ct_nm_contact).
		',ct_vl_info='.$db->Quote($ct_vl_info).',ct_vl_template='.$db->Quote($ct_vl_template).',ct_vl_prefixsujet='.$db->Quote($ct_vl_prefixsujet).
		' WHERE ct_id_contact ='.$ct_id_contact	;
		$db->setQuery( $query );
		if (!$db->query()) {
			$msg = JText::_('MSG_ERROR_SAVE_CONTACT').':'.$query.'/'.$db->getErrorMsg(true);
		}
		
	}
	else
	{
	
		$query = 'INSERT  #__hecmailing_contact (grp_id_groupe,ct_nm_contact,ct_cm_contact,ct_vl_info,ct_vl_template,ct_vl_prefixsujet ) 
			values ('.$db->Quote($grp_id_groupe).','.$db->Quote($ct_nm_contact).','.$db->Quote('').','.$db->Quote($ct_vl_info).','.$db->Quote($ct_vl_template).','.$db->Quote($ct_vl_prefixsujet).')';
		$db->setQuery( $query );
		if (!$db->query()) {
			$msg = JText::_('MSG_ERROR_SAVE_CONTACT').':'.$db->getErrorMsg(true);
		}
		
	}
  	
	$mainframe->redirect( "index.php?option=com_hecmailing&task=contact" ,$msg);
}

function loadGroupType($group)
{
	$path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_hecmailing'.DS.'groups';
	$files = JFolder::files($path, $filter = '.', false, true , null);
	foreach($files as $f)
	{
		// read and write a document
		$xml = new JSimpleXML;
		$xml->loadFile($f);
		

	}
}

function GroupListWebService($groupType, $currentGroup)
{
	$type = JRequest::getCmd('group-type');
	$db =& JFactory::getDBO();
	switch($groupType)
	{
		case 1:
			if(version_compare(JVERSION,'1.6.0','<')){
				//Code pour Joomla! 1.5  
				$query = "Select id, name From #__core_acl_aro_groups order by id";
			}
			else 
			{
				//Code pour Joomla >= 1.6.0
				$query = "SELECT id, title FROM  #__usergroups  ORDER BY id";
			}
			break;
		case 5:
			$query = "Select grp_id_groupe, grp_nm_groupe FROM #__hecmailing_groups WHERE grp_id_groupe!=".$currentGroup." ORDER BY grp_nm_groupe";
			break;
	}
	$db->setQuery( $query );
	if (!$db->query()) {
		$data = JText::_('MSG_ERROR_SAVE_CONTACT').':'.$query.'/'.$db->getErrorMsg(true);
	}
	else
		$data = $db->loadRowList();
	 
	// Get the document object.
	$document =& JFactory::getDocument();
 
	// Set the MIME type for JSON output.
	$document->setMimeEncoding('application/json');
 
	// Change the suggested filename.
	JResponse::setHeader('Content-Disposition','attachment;filename="group'.$groupType.'.json"');
 
	// Output the JSON data.
	echo json_encode($data);
}

function showPanel()
{
    global $baseurl;
    $baseurl = 'http://joomla.hecsoft.net/media/updater/';
    //$baseurl= "http://hecsoft.planethoster.org/joomla/media/updater/" ;
    HTML_hecmailing::showPanel($baseurl );
}


function updateComponent($test)
{
    global $baseurl;
   	// Modif Joomla 1.6/1.7+
    $mainframe = JFactory::getApplication();
	
    JLoader::import ( 'helper',JPATH_COMPONENT_ADMINISTRATOR);
    $baseurl = 'http://joomla.hecsoft.net/media/updater/';
    //$baseurl= "http://hecsoft.planethoster.org/joomla/media/updater/"; 
    $ver =   getComponentVersion();
	if ($test)
	{
		$latest =    getLatestComponentVersion($baseurl."hecmailing_test.xml");  
	}
	else
	{
		$latest =    getLatestComponentVersion($baseurl."hecmailing.xml");  
	}
    $msg =  JText::sprintf( 'UPDATED COMPONENT',$ver,$latest ).'<br>';
    $url = $baseurl.'com_hecmailing.'.$latest.'.zip';
    jimport('joomla.installer.helper');
    jimport('joomla.installer.installer');
    $dest=JInstallerHelper::downloadPackage ($url)  ;
	
    if ($dest)
    {
	  // Modif 1.8.2
	  $config = JFactory::getConfig();
      $package = JInstallerHelper::unpack($config->get('tmp_path').DS.$dest);
      if ($package)
      {
         $dir= $package['dir'];
         $type=$package['type'];
        // Get an installer instance
         $installer =& JInstaller::getInstance();
     		// Install the package
     		if (!$installer->install($package['dir'])) {
       			// There was an error installing the package
       			$msg = JText::sprintf('INSTALLEXT', JText::_($package['type']), JText::_('Error'));
       			$result = false;
     		} else {
      			// Package installed sucessfully
       			$msg = JText::sprintf('INSTALLEXT', JText::_($package['type']), JText::_('Success'));
       			$result = true;
     		}
     		echo $msg;
   			// Cleanup the install files
     		if (!is_file($package['packagefile'])) {
        			$config =& JFactory::getConfig();
         			$package['packagefile'] = $config->getValue('config.tmp_path').DS.$package['packagefile'];
     		}
    		JInstallerHelper::cleanupInstall($package['packagefile'], $package['extractdir']);
     }
     else
     {
        $msg = "Probleme unpack ".$dest;
        $result=false;
     }
   }
   else
   {
        $msg= "Probleme download";
        $result=false;
   }
   $mainframe->redirect( "index.php?option=com_hecmailing&task=param" ,$msg);
}

           
