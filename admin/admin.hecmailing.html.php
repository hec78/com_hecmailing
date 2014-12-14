<?php
/**
 * @version 1.8.0 
 * @package hecmailing
 * @copyright 2009-2013 Hecsoft.net
 * @license http://www.gnu.org/licenses/gpl-3.0.html
 * @link http://joomlacode.org/gf/project/userport/
 * @author H Cyr
 **/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
* @package		hecMailing
* 
*/
class HTML_hecmailing
{
  /**
   * Methode to display group list
   *
   * @param group list
   * @param Page navigation
   * @param Options
   * @param List of parameters (order,search...)
   *                 
   **/        
	function showObjects( &$rows, &$pageNav, $option, &$lists )
	{
		$user =& JFactory::getUser();
		//Ordering allowed ?
		$ordering = ($lists['order'] == 'cd.ordering');
 
		JHTML::_('behavior.tooltip');
 		?>
 		<form action="index.php?option=com_hecmailing" method="post" name="adminForm">
 		<table>
 		<tr>
 			<td align="left" width="100%">
 				<?php echo JText::_( 'FILTER' ); ?>:
 				<input type="text" name="search" id="search" value="<?php echo $lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
 				<button onclick="this.form.submit();"><?php echo JText::_( 'GO' ); ?></button>
 				<button onclick="document.getElementById('search').value='';this.form.getElementById('filter_catid').value='0';this.form.getElementById('filter_state').value='';this.form.submit();"><?php echo JText::_( 'RESET' ); ?></button>
 			</td>
 			<td nowrap="nowrap">
 				<?php
 				echo $lists['catid'];
 				echo $lists['state'];
 				?>
 			</td>
 		</tr>
 		</table>
 		<table class="adminlist">
 			<thead>
 				<tr>
 					<th width="10">
 						<?php echo JText::_( 'NUM' ); ?>
 					</th>
 					<th width="10" class="title">
 						<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" />
 					</th>
 					<th class="title" >
 						<?php echo JHTML::_('grid.sort',   JText::_('NAME'), 'g.grp_nm_groupe', @$lists['order_Dir'], @$lists['order'] ); ?>
 					</th>
 					<th  class="title">
 						<?php echo JHTML::_('grid.sort',   JText::_('DESCRIPTION'), 'g.grp_cm_groupe', @$lists['order_Dir'], @$lists['order'] ); ?>
 					</th>
 					<th class="title" >
 						<?php echo JHTML::_('grid.sort',   JText::_('PUBLISHED'), 'g.published', @$lists['order_Dir'], @$lists['order'] ); ?>
 					</th>
 					<th class="title" >
 						<?php echo JHTML::_('grid.sort',   JText::_('ITEM_COUNT'), 'grp_nb_item', @$lists['order_Dir'], @$lists['order'] ); ?>
 					</th>
 					<th  width="5px" class="title">
 						<?php echo JHTML::_('grid.sort',   JText::_('ID'), 'g.grp_id_groupe', @$lists['order_Dir'], @$lists['order'] ); ?>
 					</th>
 				<tr>
 			</thead>
 			<tfoot>
 				<tr>
 					<td colspan="7">
 						<?php echo $pageNav->getListFooter(); ?>
 					</td>
 				</tr>
 			</tfoot>
 			<tbody>
 			<?php
 			$k = 0;
 			if ($rows)
 			for ($i=0, $n=count($rows); $i < $n; $i++) {
 				$row = $rows[$i];
        $published = JHTML::_('grid.published', $row, $i );
 				$link 		= JRoute::_( 'index.php?option=com_hecmailing&task=edit&cid[]='. $row->grp_id_groupe );
         $checked = JHTML::_('grid.id', $i, $row->grp_id_groupe ); 
 				//$checked 	= JHTML::_('grid.checkedout',   $row, $i );
      ?>
      <tr class="<?php echo "row$k"; ?>">
      <td width="5px"><?php echo $pageNav->getRowOffset( $i ); ?></td>
			<td width="5px">
						<?php echo $checked; ?>
			</td>
				<td width="40%">
					<?php
					if (JTable::isCheckedOut($user->get ('id'), $row->checked_out )) :
						echo $row->grp_nm_groupe;
					else :
						?>
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'EDIT_GROUP' );?>::<?php echo $row->grp_nm_groupe."(".$row->grp_id_groupe.")"; ?>">
						<a href="<?php echo $link; ?>">
							<?php echo $row->grp_nm_groupe; ?></a> </span>
 						<?php
 					endif;
 					?>
 					</td>
					<td>
							<?php echo $row->grp_cm_groupe; ?>
					</td>
						<td width="30px" class="at_published" align="center">
							<?php echo $published ?>
					</td>
						<td width="30px">
							<?php echo $row->grp_nb_item; ?>
					</td>
					<td>
 					<?php
  					if (JTable::isCheckedOut($user->get ('id'), $row->checked_out )) :
  						echo $row->grp_id_groupe;
 					else :
 						?>
 						<span class="editlinktip hasTip" title="<?php echo JText::_( 'EDIT_GROUP' );?>::<?php echo $row->grp_nm_groupe."(".$row->grp_id_groupe.")"; ?>">
 						<a href="<?php echo $link; ?>">
  							<?php echo $row->grp_id_groupe; ?></a> </span>
  						<?php
 					endif;
 					?>
 					</td>
				</tr>
				<?php
				$k = 1 - $k;
			}
			?>
			</tbody>
 			</table>
		<input type="hidden" name="option" value="com_hecmailing" />
 		<input type="hidden" name="task" value="" />
 		<input type="hidden" name="boxchecked" value="0" />
 		<input type="hidden" name="filter_order" value="<?php echo $lists['order']; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $lists['order_Dir']; ?>" />
		<?php echo JHTML::_( 'form.token' ); ?>
		<div class="warning"><?php echo  JText::_( 'WARNING_CHANGE_GROUP' ); ?></div>
		</form>
		<?php
	}

/**
 * Methode for display group edit screen
 * 
 * @param Group list
 * @param Parameter list
 * @param Detail
 * @param Parameters
 * @param Joomla user list (like Html Select Option)
 * 
 **/        
	function editObject( &$row, &$lists, $detail, &$params, $users, $jgroups, $perms, $hecgroups, $grouptype, $usersperm, $jgroupsperm ) {
  		JRequest::setVar( 'hidemainmenu', 1 );
   		if (!isset($row))
   		{
    			$row = new StdClass;
    			$row->image='blank.png';
    			$row->id=0;
    			$row->grp_id_groupe=0;
    			$row->grp_nm_groupe='';
    			$row->grp_cm_groupe='';
    	}
    	JHTML::_('behavior.tooltip');
    	jimport('joomla.html.pane');
      // TODO: allowAllClose should default true in J!1.6, so remove the array when it does.
    	$pane = &JPane::getInstance('sliders', array('allowAllClose' => true));
    	JFilterOutput::objectHTMLSafe( $row, ENT_QUOTES, 'misc' );
    	$cparams = JComponentHelper::getParams ('com_hecmailing');
    	$document =& JFactory::getDocument();
    	$burl = "../";
    	$document->addScript($burl."components/com_hecmailing/libraries/jt/dom-drag.js");
    	$document->addScript($burl."components/com_hecmailing/libraries/jt/jt_.js");
    	$document->addScript($burl."components/com_hecmailing/libraries/jt/jt_DialogBox.js");
    	$document->addScript($burl."components/com_hecmailing/libraries/jt/jt_AppDialogs.js");
    	$document->addStyleSheet($burl."components/com_hecmailing/libraries/jt/jt_DialogBox.css");
    	$document->addStyleSheet($burl."components/com_hecmailing/libraries/jt/veils.css");
		$document->addStyleSheet("http://code.jquery.com/ui/1.10.2/themes/smoothness/jquery-ui.css");
    	$document->addScript("components/com_hecmailing/admin.hecmailing.js");
		$document->addScript("http://code.jquery.com/jquery-1.9.1.js");
		$document->addScript("http://code.jquery.com/ui/1.10.2/jquery-ui.js");
?>
	<script type="text/javascript">
  <!--
  		
		webservice = '<?php echo JURI::root().'index.php?option=com_hecmailing&task=getgroupcontent';?>';
  		submit_MustName = '<?php echo JText::_( 'YOU_MUST_PROVIDE_A_NAME', true ); ?>';
  		text_user ="<?php echo JText::_('USER', true); ?>";
  		text_mail="<?php echo JText::_('EMAIL', true); ?>";
  		text_joomlagroup="<?php echo JText::_('JOOMLA_GROUP', true); ?>";
		text_hecmailinggroup="<?php echo JText::_('HECMAILING_GROUP', true); ?>";
  		text_noitem='<?php echo JText::_("NO_SELECTED_ITEM"); ?>';
  		text_wantremove='<?php echo JText::_('WANT_REMOVE', true) ?>';
  		text_items='<?php echo JText::_('ITEMS', true) ?> ';
  		text_perms='<?php echo JText::_('PERMISSIONS', true) ?> ';
  		text_noperm='<?php echo JText::_("NO_SELECTED_PERM_ITEM"); ?>';
		text_group="<?php echo JText::_('GROUP', true); ?>";
		

  //-->
  </script>
  <LINK rel="stylesheet" type="text/css" href="../components/com_hecmailing/css/dialog.css">
  <div id="dialog_container"></div>
	<div id="dialogUser" style="display:none;" title="<?php echo JText::_( "NEW_USER" ); ?>" >
		<div class="image"><img src="../components/com_hecmailing/images/user64.png" ></div>
		<div class="content"><br/>
			<?php echo JText::_('SELECT_USER_BELOW'); ?><br/><br/>
			<?php echo JText::_('USER')." : ".$users ?></div><br/>
			<div class="buttons"><button onclick="javascript:addUser();return false;"><img src="../components/com_hecmailing/images/ok16.png" ><?php echo JText::_('ADD'); ?></button>
			<button onclick="javascript:cancelUser();return false;"><img src="../components/com_hecmailing/images/cancel16.png" ><?php echo JText::_('CANCEL'); ?></button>
		</div> 
	</div>
      
      <div id="dialogDelEntry"  style="display:none;" title="<?php echo JText::_( "DELETE" ); ?>">
      
         <div class="image" ><img width="64px" src="../components/com_hecmailing/images/poubelle64.png" ></div>
         <div id="dialogDelEntry_msg"  class="content"><?php echo JText::_('REMOVE_ALL_SELECTED'); ?></div><br/>
         <div class="buttons"><button onclick="javascript:deleteRows();return false;"><img src="../components/com_hecmailing/images/ok16.png" ><?php echo JText::_('REMOVE'); ?></button>
		 <button onclick="javascript:cancelDelEntry();return false;"><img src="../components/com_hecmailing/images/cancel16.png" ><?php echo JText::_('CANCEL'); ?></button></div> 
      </div>
      
	  <div id="dialogMail"  style="display:none;" title="<?php echo JText::_( "NEW_MAIL" ); ?>">
      	
         <div class="image" ><img width="64px" src="../components/com_hecmailing/images/email64.png" ></div>
         <div class="content">
           <?php echo JText::_('ENTER_EMAIL_BELOW') ?><br/><br/>
           <?php echo JText::_('EMAIL')." : "?><input type="text" name="newmail" id="newmail" value="" width="95%" />
         </div><br/>
         <div class="buttons"><button onclick="javascript:addMail();return false;"><img src="../components/com_hecmailing/images/ok16.png" ><?php echo JText::_('ADD'); ?></button>
		 <button onclick="javascript:cancelMail();return false;"><img src="../components/com_hecmailing/images/cancel16.png" ><?php echo JText::_('CANCEL'); ?></button></div> 
      </div>
      <div id="dialogUserPerm"  style="display:none;"  title="<?php echo JText::_( "NEW_USER_PERM" ); ?>">
      	 <div class="image"><img src="../components/com_hecmailing/images/user64.png" ></div>
         <div class="content"><br/>
			<table width="100%"><tr><td colspan="2">
           <?php echo JText::_('SELECT_USER_BELOW'); ?><br/></td></tr>
		   <tr><td><?php echo JText::_('USER')." : </td><td>".$usersperm ?></td></tr>
		   <tr><td colspan="2"><?php echo JText::_('RIGHTS'); ?></td></tr>
		   <tr><td><?php echo JText::_('RIGHT_SEND_MAIL'). ":"; ?></td><td><?php echo JText::_('RIGHT_MANAGE'). ":"; ?></td><td><?php echo JText::_('RIGHT_GRANT'). ":"; ?></td></tr>
		     <tr><td><input type="checkbox" name="right_send" id="right_send" checked="checked" value="1"></td><td><input type="checkbox" name="right_manage" id="right_manage" value="1"></td><td><input type="checkbox" name="right_grant" id="right_grant" value="1"></td></tr>
		   </table>
		   </div><br/>
        <div class="buttons"><button onclick="javascript:addUserPerm();return false;"><img src="../components/com_hecmailing/images/ok16.png" ><?php echo JText::_('ADD'); ?></button>
		<button onclick="javascript:cancelUserPerm();return false;"><img src="../components/com_hecmailing/images/cancel16.png" ><?php echo JText::_('CANCEL'); ?></button></div> 
   </div>
   <div id="dialogGroupPerm"  style="display:none;" title="<?php echo JText::_( "NEW_GROUP_PERM" ); ?>" >
        <div class="image" ><img width="64px" src="../components/com_hecmailing/images/group64.png" ></div>
        <div class="content">
		<table width="100%">
			<tr><td colspan="2"><?php echo JText::_('SELECT_GROUP_BELOW') ?><br/></td></tr>
			<tr><td><?php echo JText::_('GROUP')." : </td><td>".$jgroupsperm ?></td></tr>
		<tr><td colspan="2"><?php echo JText::_('RIGHTS'); ?></td></tr>
		   <tr><td><?php echo JText::_('RIGHT_SEND_MAIL'). ":"; ?></td><td><?php echo JText::_('RIGHT_MANAGE'). ":"; ?></td><td><?php echo JText::_('RIGHT_GRANT'). ":"; ?></td></tr>
		   <tr><td><input type="checkbox" name="rightg_send" id="rightg_send" checked="checked" value="1"></td><td><input type="checkbox" name="rightg_manage" id="rightg_manage" value="1"></td><td><input type="checkbox" name="rightg_grant" id="rightg_grant" value="1"></td></tr>
		</table>
		
        </div><br/>
        <div class="buttons"><button onclick="javascript:addGroupePerm();return false;"><img src="../components/com_hecmailing/images/ok16.png" ><?php echo JText::_('ADD'); ?></button>
		<button onclick="javascript:cancelGroupPerm();return false;"><img src="../components/com_hecmailing/images/cancel16.png" ><?php echo JText::_('CANCEL'); ?></button></div> 
   </div>
   <div id="dialogDelPerm"   style="display:none;" title="<?php echo JText::_( "DELETE_PERM" ); ?>">
   
		<div class="image" ><img width="64px" src="../components/com_hecmailing/images/poubelle64.png" ></div>
        <div id="dialogDelPerm_msg" class="content"><?php echo JText::_('REMOVE_ALL_SELECTED'); ?></div><br/>
        <div class="buttons"><button onclick="javascript:deleteRowsPerm();return false;"><img src="../components/com_hecmailing/images/ok16.png" ><?php echo JText::_('REMOVE'); ?></button>
		<button onclick="javascript:cancelDelPerm();return false;"><img src="../components/com_hecmailing/images/cancel16.png" ><?php echo JText::_('CANCEL'); ?></button></div> 
    </div>
	 <div id="dialogGroup"   title="<?php echo JText::_( "NEW_HECMAILING_GROUP" ); ?>" style="display:none;" >
         <div class="image" ><img width="64px" src="../components/com_hecmailing/images/group64.png" ></div>
         <div class="content">
			<?php echo JText::_('SELECT_TYPEGROUP_BELOW') ?><br/>
           <?php echo JText::_('GROUP')." : ".$grouptype ?><br/><br/>
           <?php echo JText::_('SELECT_GROUP_BELOW') ?><br/>
           <?php echo JText::_('GROUP')." : ".$hecgroups ?>
         </div><br/>
         <div class="buttons"><button onclick="javascript:addGroupe();return false;"><img src="../components/com_hecmailing/images/ok16.png" ><?php echo JText::_('ADD'); ?></button>
		 <button onclick="javascript:cancelGroup();return false;"><img src="../components/com_hecmailing/images/cancel16.png" ><?php echo JText::_('CANCEL'); ?></button></div> 
      </div>
	<form action="index.php" method="post" name="adminForm" enctype="multipart/form-data">
		<div class="col">
			<fieldset class="adminform"><legend><?php echo JText::_( 'GROUP' ); ?></legend>
			<table class="admintable">
  			  <tr>
    				<td class="key"><label for="name"><?php echo JText::_( 'ID' ); ?>:</label></td>
		    		<td><?php echo $row->grp_id_groupe; ?><input type="hidden" name="grp_id_groupe" id="grp_id_groupe" value="<?php echo $row->grp_id_groupe; ?>" ></td>
          </tr><tr>
        		<td class="key"><label for="name"><?php echo JText::_( 'NAME' ); ?>:</label></td>
        		<td ><input class="inputbox" type="text" name="grp_nm_groupe" id="grp_nm_groupe" size="60" maxlength="255" value="<?php echo $row->grp_nm_groupe; ?>" /></td>
          </tr><tr>
        		<td class="key"><label for="name"><?php echo JText::_( 'COMMENT' ); ?>:</label></td>
        		<td ><textarea name="grp_cm_groupe" id="grp_cm_groupe" rows="3" cols="45" class="inputbox"><?php echo $row->grp_cm_groupe; ?></textarea></td>
          </tr><tr>
        		<td class="key"><label for="name"><?php echo JText::_( 'PUBLISHED' ); ?>:</label></td>
        		<td ><fieldset class="radio"><?php echo $lists['published']; ?></fieldset></td>
        	</tr></table>	</fieldset>
        	<fieldset class="adminform">
        	<legend><?php echo JText::_( 'DETAIL' ); ?></legend>
				<div class="button">
					 <button id="btnnewuser" onclick="javascript:showAddNewUser();return false;" ><img src="../components/com_hecmailing/images/user16.png" ><?php echo JText::_( 'NEW_USER' ); ?></button>
					 <button id="btnnewmail" onclick="javascript:showAddNewMail();return false;" ><img src="../components/com_hecmailing/images/email16.png" ><?php echo JText::_( 'NEW_MAIL' ); ?></button>
					 <button id="btnnewgroupe" onclick="javascript:showAddNewGroupe();return false;" ><img src="../components/com_hecmailing/images/group16.png" ><?php echo JText::_( 'NEW_GROUP' ); ?></button>
					 <button id="delete" onclick="javascript:showDeleteEntry();return false;" ><img src="../components/com_hecmailing/images/poubelle16.png" ><?php echo JText::_( 'DELETE' ); ?></button>
					 <button id="btnimport" onclick="javascript:showImport();return false;" ><img src="../components/com_hecmailing/images/email16.png" ><?php echo JText::_( 'IMPORT' ); ?></button>
				</div>
				
				<div id="dialog_import" name="dialog_import" class="hecdialogx" style="display:none;" >
					<br/><hr>
					<div class="header" ><?php echo JText::_( 'IMPORT_EMAIL' ); ?></div>
					<div class="content">
						<table style="width:100%;align:center;">
						<tr><th><?php echo JText::_('CHOOSE_FILE') ?></th><th><?php echo JText::_('DELIMITER') ?></th><th><?php echo JText::_('LINE_DELIMITER') ?></th>
						<th><?php echo JText::_('EMAIL_COLUMN') ?></th><th><?php echo JText::_('LENGTH') ?></th><th><?php echo JText::_('MODE') ?></th></tr>
						<tr><td><input type="file" name="import_file" id="import_file" value="" width="95%" /></td>
						<td style="align:center"><select name="import_delimiter" id="import_delimiter"  >
							<option value="1"><?php echo JText::_('DELIMITER_TAB') ?></option>
							<option value="2"><?php echo JText::_('DELIMITER_SEMI_COLON') ?></option>
							<option value="3"><?php echo JText::_('DELIMITER_COLON') ?></option>
							<option value="4"><?php echo JText::_('DELIMITER_SPACE') ?></option>
							<option value="9"><?php echo JText::_('DELIMITER_FIXE') ?></option></select>   
						</td>
						<td style="align:center"><select name="import_linedelimiter" id="import_linedelimiter"  >
							<option value="*" ><?php echo JText::_('LINEDELIMITER_DEFAULT') ?></option>
							<option value="1"><?php echo JText::_('LINEDELIMITER_WINDOWS') ?></option>
							<option value="2"><?php echo JText::_('LINEDELIMITER_LINUX') ?></option>
							<option value="3"><?php echo JText::_('LINEDELIMITER_MAC') ?></option></select>
						</td>
						<td><input name="import_column" id="import_column" size="2" type="text" value="0"></td>
						<td><input name="import_len" id="import_len" size="2" type="text"></td>
						<td style="align:center"><select name="import_mode" id="import_mode"  >
							<option value="1"><?php echo JText::_('MODE_APPEND') ?></option>
							<option value="2"><?php echo JText::_('MODE_DELETE') ?></option>
							<option value="3"><?php echo JText::_('MODE_REPLACE') ?></option></select>
						</td>
						</tr></table>
					</div>
				</div>
				
				
					<table class="adminlist" id="detail">
						<thead><tr><th class="title"></th><th class="title"><?php echo JText::_('TYPE'); ?></th><th class="title"><?php echo JText::_('NAME'); ?></th></tr></thead>
						<tbody>
						<?php 
						  $i=0;$k=0;
						  if ($detail)
							foreach ($detail as $r)
							{
							   echo "<tr class=\"row".$k."\"><td><input type=\"checkbox\" name=\"suppress".$i."\" id=\"suppress".$i."\" value=\"".$r[3]."\"></td>";
							   $i++;
							   if ($k==1) $k=0;
							   else  $k=1;
								switch($r[0])
								{
								  case 1: 
									  echo "<td><img src=\"../components/com_hecmailing/images/user16.png\" >".JText::_("USER")."</td><td>".$r[2]."</td>";
									  break;
								  case 2: 
									  echo "<td><img src=\"../components/com_hecmailing/images/user16.png\" >".JText::_("USER")."</td><td>".$r[4]."</td>";
									  break;
								  case 3: 
									  echo "<td><img src=\"../components/com_hecmailing/images/group16.png\" >".JText::_("JOOMLA_GROUP")."</td><td>".$r[5]."</td>";
									  break;
								  case 4: 
									  echo "<td><img src=\"../components/com_hecmailing/images/email16.png\" >".JText::_("EMAIL")."</td><td>".$r[2]."</td>";
									  break;
								  case 5: 
									  echo "<td><img src=\"../components/com_hecmailing/images/group16.png\" >".JText::_("HECMAILING_GROUP")."</td><td>".$r[6]."</td>";
									  break;
								}
								echo "</tr>";
							}
						  echo "<input type=\"hidden\" name=\"nbold\" id=\"nbold\" value=\"".$i."\"/>";
						  echo "<input type=\"hidden\" name=\"nbnew\" id=\"nbnew\" value=\"0\"/>";
						  echo "<input type=\"hidden\" name=\"todel\" id=\"todel\" value=\"\"/>";
						  echo "<input type=\"hidden\" name=\"toimport\" id=\"toimport\" value=\"1\" />";
				?>				
						</tbody>
						</table>
			</fieldset>
			<fieldset class="adminform">
				<legend><?php echo JText::_( 'PERMISSIONS' ); ?></legend>
				<button id="btnnewuser" onclick="javascript:showAddNewUserPerm();return false;" ><img src="../components/com_hecmailing/images/user16.png" ><?php echo JText::_( 'NEW_USER' ); ?></button>		
				<button id="btnnewgroupe" onclick="javascript:showAddNewGroupePerm();return false;" ><img src="../components/com_hecmailing/images/group16.png" ><?php echo JText::_( 'NEW_GROUP' ); ?></button>
				<button id="delete" onclick="javascript:showDeletePermEntry();return false;" ><img src="../components/com_hecmailing/images/poubelle16.png" ><?php echo JText::_( 'DELETE' ); ?></button>
				<table class="adminlist" id="permissions">
				<thead>
				  <tr><th class="title"></th><th class="title"><?php echo JText::_('TYPE'); ?></th><th class="title"><?php echo JText::_('NAME'); ?></th><th class="title"><?php echo JText::_('RIGHT_SEND_MAIL'); ?></th>
				  <th class="title"><?php echo JText::_('RIGHT_MANAGE'); ?></th><th class="title"><?php echo JText::_('RIGHT_GRANT'); ?></th></tr>
				</thead>
				<tbody>
					<?php 
				  $i=0;$k=0;
          if ($perms)
				  foreach ($perms as $r)
				  {
						$i++;
            			echo "<tr class=\"row".$k."\"><td><input type=\"checkbox\" name=\"suppressperm".$i."\" id=\"suppressperm".$i."\" value=\"".$r[2]."-".$r[0]."-".$r[1]."\" >
							<input type=\"hidden\" name=\"oldperm".$i."\" id=\"oldperm".$i."\" value=\"".$r[2].";".$r[0].";".$r[1]."\"></td>";
			            
			            if ($k==1) $k=0;
			            else  $k=1;
            			if ($r[0]>0)
            			{
            				echo "<td><img src=\"../components/com_hecmailing/images/user16.png\" >".JText::_("USER")."</td><td>".$r[3]."</td>";
	           			}
            			else
            			{
            				echo "<td><img src=\"../components/com_hecmailing/images/group16.png\" >".JText::_("JOOMLA_GROUP")."</td><td>".$r[4]."</td>";
            			}
						$rights=$r[5];
												
						if (($rights & 1) == 1) $checked="checked=\"checked\"";
						else $checked="";
						echo "<td><input type=\"checkbox\" id=\"perm_send".$i."\" name=\"perm_send".$i."\" ".$checked." value=\"1\"></td>";
						if (($rights & 2) == 2) $checked="checked=\"checked\"";
						else $checked="";
						echo "<td><input type=\"checkbox\" id=\"perm_manage".$i."\" name=\"perm_manage".$i."\" ".$checked." value=\"1\"></td>";
						if (($rights & 4) == 4) $checked="checked=\"checked\"";
						else $checked="";
						echo "<td><input type=\"checkbox\" id=\"perm_grant".$i."\" name=\"perm_grant".$i."\" ".$checked." value=\"1\"></td>";
            			echo "</tr>";
          		  } 
	      echo "<input type=\"hidden\" name=\"nboldperm\" id=\"nboldperm\" value=\"".$i."\"/>";
          echo "<input type=\"hidden\" name=\"nbnewperm\" id=\"nbnewperm\" value=\"0\"/>";
          echo "<input type=\"hidden\" name=\"todelperm\" id=\"todelperm\" value=\"\"/>"; ?>      
          </tbody></table>
			</fieldset>
		</div>
		<div class="clr"></div>
		<input type="hidden" name="option" value="com_hecmailing" />
		<input type="hidden" name="id" value="<?php echo $row->grp_id_groupe; ?>" />
		<input type="hidden" name="cid[]" value="<?php echo $row->grp_id_groupe; ?>" />
		<input type="hidden" name="task" value="" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>
		<?php
	}


	/**
	 * Methode that show template list
	 * 
	 * @param array Template list
	 * @param PageNav
	 * @param array Options
	 * @param array Variable list (order...)
	 * 
	 **/                    	
	function showTemplates( $rows, $pageNav, $option, $lists )
	{
    ?>	
   <form action="index.php" method="post" name="adminForm">
      <table class="adminlist">
			<thead>
				<tr>
					<th width="10px">
						<?php echo JText::_( 'NUM' ); ?>
					</th>
					<th width="10px" class="title">
						<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" />
					</th>
					<th width="100%" class="title" >
						<?php echo JHTML::_('grid.sort',   JText::_('TEMPLATE'), 'msg_lb_message', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="50px" class="title">
						<?php echo JHTML::_('grid.sort',   JText::_('ID'), 'msg_id_message', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
				<tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="4">
						<?php echo $pageNav->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			<tbody>
			 	<?php
			       $k = 0;
			       for ($i=0, $n=count($rows); $i < $n; $i++) 
	               {
				        $row = $rows[$i];
      		          	$checked = JHTML::_('grid.id', $i, $row->msg_id_message ); 
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td width="5px">
						<?php echo $pageNav->getRowOffset( $i ); ?>
					</td>
					<td width="5px">
						<?php echo $checked; ?>
					</td>
					<td width="40%"><?php if ($row->msg_lb_message=='')
											echo $row->msg_vl_subject;
										else
											echo $row->msg_lb_message;
						?></td>
					<td width="40%"><?php echo $row->msg_id_message;	?></td>
				</tr>
				<?php
				$k = 1 - $k;
			}
			?>
			</tbody>
			</table>
			<input type="hidden" name="option" value="com_hecmailing" />
		    <input type="hidden" name="task" value="templates" />
		    <input type="hidden" name="boxchecked" value="0" />
		    <input type="hidden" name="filter_order" value="<?php echo $lists['order']; ?>" />
		    <input type="hidden" name="filter_order_Dir" value="<?php echo $lists['order_Dir']; ?>" />
		    <?php echo JHTML::_( 'form.token' ); ?>
   </form>
   <?php
  }
	
/**
 * Methode that show contact list
 * 
 * @param array Template list
 * @param PageNav
 * @param array Options
 * @param array Variable list (order...)
 * 
 **/                    	
	function showContact( $rows, $pageNav, $option, $lists )
	{
    ?>	
   <form action="index.php" method="post" name="adminForm">
      <table class="adminlist">
			<thead>
				<tr>
					<th width="10px">
						<?php echo JText::_( 'NUM' ); ?>
					</th>
					<th width="10px" class="title">
						<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" />
					</th>
					<th width="100%" class="title" >
						<?php echo JHTML::_('grid.sort',   JText::_('CONTACT_TITLE'), 'ct_nm_contact', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="50px" class="title">
						<?php echo JHTML::_('grid.sort',   JText::_('CONTACT_ID'), 'ct_id_contact', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
				<tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="4">
						<?php echo $pageNav->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			<tbody>
			 	<?php
			       $k = 0;
			       for ($i=0, $n=count($rows); $i < $n; $i++) 
	               {
				        $row = $rows[$i];
                		$checked = JHTML::_('grid.id', $i, $row->ct_id_contact ); 
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td width="5px"><?php echo $pageNav->getRowOffset( $i ); ?></td>
					<td width="5px"><?php echo $checked; ?></td>
					<td width="40%"><a href="index.php?option=com_hecmailing&task=editContact&contactid=<?php echo $row->ct_id_contact; ?>" ><?php echo $row->ct_nm_contact; ?></a></td>
					<td width="40%"><?php echo $row->ct_id_contact;	?></td>
				</tr>
				<?php
				$k = 1 - $k;
			}
			?>
			</tbody>
			</table>
			<input type="hidden" name="option" value="com_hecmailing" />
		    <input type="hidden" name="task" value="edit_contact" />
		    <input type="hidden" name="boxchecked" value="0" />
		    <input type="hidden" name="filter_order" value="<?php echo $lists['order']; ?>" />
		    <input type="hidden" name="filter_order_Dir" value="<?php echo $lists['order_Dir']; ?>" />
		    <?php echo JHTML::_( 'form.token' ); ?>
   </form>
   <?php
  }

  
/**
 * Methode that edit or create a contact
 * 
 * @param contact id
 * @param HEC Mailing group list
 * @param contact data (if edit)
 * @param param list
 * 
 **/ 
  function editContact($id,$groups,$data,$param)
  {
  		JRequest::setVar( 'hidemainmenu', 1 );
		JHTML::_('behavior.tooltip');
      	$editor =& JFactory::getEditor();
      	// Cas d'un nouveau contact
		if (!$data)
		{
			$data=new stdClass();
			$data->ct_id_contact=0;	
			$data->ct_nm_contact='';
			$data->ct_vl_info='';
			$data->ct_vl_template='{BODY}';
			$data->ct_vl_prefixsujet='';
		}
  ?>
  	<form action="index.php" method="post" name="adminForm">
    	<table class="admintable">
			<tr><td class="key"><label for="name"><?php echo JText::_( 'ID_CONTACT' ); ?>:</label></td>
				<td><?php echo $data->ct_id_contact; ?><input type="hidden" name="ct_id_contact" id="ct_id_contact" value="<?php echo $id; ?>" ></td></tr>
			<tr><td class="key"><label for="name"><?php echo JText::_( 'CONTACT_NAME' ); ?>:</label></td>
				<td><input class="inputbox" type="text" name="ct_nm_contact" id="ct_nm_contact" size="30" maxlength="30" value="<?php echo $data->ct_nm_contact; ?>" /></td></tr>
			<tr><td class="key"><label for="name"><?php echo JText::_( 'GROUP' ); ?>:</label></td>
				<td ><?php  echo $groups; ?></td></tr>
			<tr><td class="key"><label for="name"><?php echo JText::_( 'INFO' ); ?>:</label></td>
				<td><?php echo $editor->display('ct_vl_info', $data->ct_vl_info, 400, 200, '60', '20', true); ?></td></tr>
		</table>
		<hr>
		<table>
			<tr><td class="key"><label for="name"><?php echo JText::_( 'CONTACT_PREFIXSUJET' ); ?>:</label></td>
				<td><input class="inputbox" type="text" name="ct_vl_prefixsujet" id="ct_vl_prefixsujet" size="30" maxlength="30" value="<?php echo $data->ct_vl_prefixsujet; ?>" /></td></tr>
			
			<tr><td class="key"><label for="name"><?php echo JText::_( 'CONTACT_TEMPLATE' ); ?>:</label></td>
				<td><?php echo $editor->display('ct_vl_template', $data->ct_vl_template , 400, 200, '60', '20', true); ?></td></tr>
				<tr><td></td><td><?php echo JText::_( 'CONTACT_TEMPLATE_HELP' ); ?></td></tr>
		</table>
		<input type="hidden" name="option" value="com_hecmailing" />
		<input type="hidden" name="task" value="saveContact" />
	</form>
	<?php 
  }

  /**
 * Methode that show param and about screen
 * 
 * @param update base url
 * 
 **/ 
  function showPanel($baseurl)
	{
	  JLoader::import ( 'helper',JPATH_COMPONENT_ADMINISTRATOR);
	  $ver =   getComponentVersion();
	  $latestProd =    getLatestComponentVersion($baseurl.'hecmailing.xml');  
	  $latestTest =    getLatestComponentVersion($baseurl.'hecmailing_test.xml');  ?>
	  <table width="100%">
	  <tr valign="top"><td>
	     <fieldset>
	       <legend><?php echo JText::_( 'VERSION' ); ?></legend>
	       	<table class="admintable">
	        <tr><td class="key"><label for="name"><?php echo JText::_( 'VERSION' ); ?>:</label></td>
	            <td><?php echo $ver; ?></td></tr>
	       <?php
	          if ($latestProd > $ver)
	          {     ?>
	                 <tr><td class="key"><label for="name"><?php echo JText::_( 'LATEST_PROD_VERSION' ); ?>:</label></td>
	                       <td><?php echo $latestProd; ?>
	                        <a href="index.php?option=com_hecmailing&task=upgrade" style="color:red"><?php echo JText::_( 'UPGRADE_COMPONENT' ); ?></a></td></tr>
					
	       <?php
	          }
	          else {
	          	    ?>
	                 <tr><td class="key"><label for="name"><?php echo JText::_( 'LATEST_PROD_VERSION' ); ?>:</label></td>
	                       <td><?php echo $latestProd; ?> <?php echo JText::_( 'UPTODATE_VERSION' ); ?></td></tr>
	                <?php
	          }
			  if ($latestTest > $ver)
	          {     ?>
					<tr><td class="key"><label for="name"><?php echo JText::_( 'LATEST_TEST_VERSION' ); ?>:</label></td>
	                       <td><?php echo $latestTest; ?>
	                        <a href="index.php?option=com_hecmailing&task=upgradetest" style="color:red"><?php echo JText::_( 'UPGRADE_COMPONENT' ); ?></a></td></tr>
							<?php
	          }
	          else {
	          	    ?>
	                 <tr><td class="key"><label for="name"><?php echo JText::_( 'LATEST_TEST_VERSION' ); ?>:</label></td>
	                       <td><?php echo $latestTest; ?> <?php echo JText::_( 'UPTODATE_VERSION' ); ?></td></tr>
	                <?php
	          }
	       ?>
	        </table>

	       </fieldset>

	       <fieldset>

			<legend><?php echo JText::_( 'DONATION' ); ?></legend>

			<?php echo JText::_( 'DONATION_TEXT' ); ?><br><br>

			<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
	        	<input type="hidden" name="cmd" value="_s-xclick">
	        	<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----">
	        	<input type="image" src="https://www.paypal.com/<?php echo JText::_( 'en_US/GB'); ?>/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online.">
	        	<img alt="" border="0" src="https://www.paypal.com/fr_FR/i/scr/pixel.gif" width="1" height="1">
	        </form>
	      </fieldset>    </td><td>
	      <fieldset>
	        <legend>PARAMETRES</legend>
	        <?php
				if(version_compare(JVERSION,'1.6.0','<')){
					$url = 'index.php?option=com_config&amp;controller=component&amp;component=com_hecmailing&amp;path=';
				}
				else {
					// Modif Joomla 1.6+
					$url = "index.php?option=com_config&view=component&component=com_hecmailing&path=&tmpl=component";
				}
	           echo '<iframe src="'. $url . '" width=100% height=600 scrolling=auto frameborder=0 > </iframe>';
	        ?>

	      </fieldset></td></tr></table>

	   <?php

	}    







}

?>