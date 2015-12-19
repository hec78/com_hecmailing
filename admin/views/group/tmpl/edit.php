<?php
/**
 * @package hecMailing for Joomla
 * @version		3.0.0
 *
 * @copyright Copyright (C) 2009-2015 Hecsoft All rights reserved.
 * @license GNU/GPL
 */

defined('_JEXEC') or die;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHTML::_('behavior.tooltip');
//jimport('joomla.html.pane');
//$pane = &JPane::getInstance('sliders', array('allowAllClose' => true));
JFilterOutput::objectHTMLSafe( $row, ENT_QUOTES, 'misc' );
$cparams = JComponentHelper::getParams ('com_hecmailing');
$document = JFactory::getDocument();
$burl = "../";
$document->addStyleSheet($burl."components/com_hecmailing/css/dialog.css");
$document->addScript("components/com_hecmailing/assets/js/group.js");
if(version_compare(JVERSION,'3.0.0','<')){
	$document->addScript("components/com_hecmailing/libraries/jquery-1.11.1.min.js");
}
$document->addScript("components/com_hecmailing/libraries/jquery-ui.min.js");
$document->addStyleSheet("components/com_hecmailing/libraries/jquery-ui.css");
?>
<script type="text/javascript">
  <!--
		webservice = '<?php echo JURI::base().'index.php?option=com_hecmailing&task=groups.getGroupContent';?>';
  		submit_MustName = '<?php echo JText::_( 'COM_HECMAILING_YOU_MUST_PROVIDE_A_NAME', true ); ?>';
  		text_user ="<?php echo JText::_('COM_HECMAILING_USER', true); ?>";
  		text_mail="<?php echo JText::_('COM_HECMAILING_EMAIL', true); ?>";
  		text_joomlagroup="<?php echo JText::_('COM_HECMAILING_JOOMLA_GROUP', true); ?>";
		text_hecmailinggroup="<?php echo JText::_('COM_HECMAILING_HECMAILING_GROUP', true); ?>";
  		text_noitem='<?php echo JText::_("COM_HECMAILING_NO_SELECTED_ITEM"); ?>';
  		text_wantremove='<?php echo JText::_('COM_HECMAILING_WANT_REMOVE', true) ?>';
  		text_items='<?php echo JText::_('COM_HECMAILING_ITEMS', true) ?> ';
  		text_perms='<?php echo JText::_('COM_HECMAILING_PERMISSIONS', true) ?> ';
  		text_noperm='<?php echo JText::_("COM_HECMAILING_NO_SELECTED_PERM_ITEM"); ?>';
		text_group="<?php echo JText::_('COM_HECMAILING_GROUP', true); ?>";
  //-->
</script>
<!-- DEBUT DIALOGUES -->
<div id="dialog_container"></div>
<div id="dialogUser" style="display:none;" title="<?php echo JText::_( "COM_HECMAILING_NEW_USER" ); ?>" >
<div class="image"><img src="../components/com_hecmailing/assets/images/user64.png" ></div>
<div class="content"><br/>
	<?php echo JText::_('COM_HECMAILING_SELECT_USER_BELOW'); ?><br/><br/>
	<?php echo JText::_('COM_HECMAILING_USER')." : ".$this->users ?></div><br/>
	<div class="buttons"><button onclick="javascript:addUser();return false;"><img src="../components/com_hecmailing/assets/images/ok16.png" ><?php echo JText::_('COM_HECMAILING_ADD'); ?></button>
	<button onclick="javascript:cancelUser();return false;"><img src="../components/com_hecmailing/assets/images/cancel16.png" ><?php echo JText::_('COM_HECMAILING_CANCEL'); ?></button>
</div> 
</div>

<div id="dialogDelEntry"  style="display:none;" title="<?php echo JText::_( "COM_HECMAILING_DELETE" ); ?>">

 <div class="image" ><img width="64px" src="../components/com_hecmailing/assets/images/poubelle64.png" ></div>
 <div id="dialogDelEntry_msg"  class="content"><?php echo JText::_('COM_HECMAILING_REMOVE_ALL_SELECTED'); ?></div><br/>
 <div class="buttons"><button onclick="javascript:deleteRows();return false;"><img src="../components/com_hecmailing/assets/images/ok16.png" ><?php echo JText::_('COM_HECMAILING_REMOVE'); ?></button>
 <button onclick="javascript:cancelDelEntry();return false;"><img src="../components/com_hecmailing/assets/images/cancel16.png" ><?php echo JText::_('COM_HECMAILING_CANCEL'); ?></button></div> 
</div>

<div id="dialogMail"  style="display:none;" title="<?php echo JText::_( "COM_HECMAILING_NEW_MAIL" ); ?>">

 <div class="image" ><img width="64px" src="../components/com_hecmailing/assets/images/email64.png" ></div>
 <div class="content">
   <?php echo JText::_('COM_HECMAILING_ENTER_EMAIL_BELOW') ?><br/><br/>
   <?php echo JText::_('COM_HECMAILING_EMAIL')." : "?><input type="text" name="newmail" id="newmail" value="" width="95%" />
 </div><br/>
 <div class="buttons"><button onclick="javascript:addMail();return false;"><img src="../components/com_hecmailing//assets/images/ok16.png" ><?php echo JText::_('COM_HECMAILING_ADD'); ?></button>
 <button onclick="javascript:cancelMail();return false;"><img src="../components/com_hecmailing/assets/images/cancel16.png" ><?php echo JText::_('COM_HECMAILING_CANCEL'); ?></button></div> 
</div>
<div id="dialogUserPerm"  style="display:none;"  title="<?php echo JText::_( "COM_HECMAILING_NEW_USER_PERM" ); ?>">
 <div class="image"><img src="../components/com_hecmailing/assets/images/user64.png" ></div>
 <div class="content"><br/>
	<table width="100%"><tr><td colspan="2">
   <?php echo JText::_('COM_HECMAILING_SELECT_USER_BELOW'); ?><br/></td></tr>
   <tr><td><?php echo JText::_('COM_HECMAILING_USER')." : </td><td>".$this->usersperm ?></td></tr>
   <tr><td colspan="2"><?php echo JText::_('COM_HECMAILING_RIGHTS'); ?></td></tr>
   <tr><td><?php echo JText::_('COM_HECMAILING_RIGHT_SEND_MAIL'). ":"; ?></td><td><?php echo JText::_('COM_HECMAILING_RIGHT_MANAGE'). ":"; ?></td><td><?php echo JText::_('COM_HECMAILING_RIGHT_GRANT'). ":"; ?></td></tr>
	 <tr><td><input type="checkbox" name="right_send" id="right_send" checked="checked" value="1"></td><td><input type="checkbox" name="right_manage" id="right_manage" value="1"></td><td><input type="checkbox" name="right_grant" id="right_grant" value="1"></td></tr>
   </table>
   </div><br/>
<div class="buttons"><button onclick="javascript:addUserPerm();return false;"><img src="../components/com_hecmailing/assets/images/ok16.png" ><?php echo JText::_('COM_HECMAILING_ADD'); ?></button>
<button onclick="javascript:cancelUserPerm();return false;"><img src="../components/com_hecmailing/assets/images/cancel16.png" ><?php echo JText::_('COM_HECMAILING_CANCEL'); ?></button></div> 
</div>
<div id="dialogGroupPerm"  style="display:none;" title="<?php echo JText::_( "COM_HECMAILING_NEW_GROUP_PERM" ); ?>" >
<div class="image" ><img width="64px" src="../components/com_hecmailing/assets/images/group64.png" ></div>
<div class="content">
<table width="100%">
	<tr><td colspan="2"><?php echo JText::_('COM_HECMAILING_SELECT_GROUP_BELOW') ?><br/></td></tr>
	<tr><td><?php echo JText::_('COM_HECMAILING_GROUP')." : </td><td>".$this->groupsperm ?></td></tr>
<tr><td colspan="2"><?php echo JText::_('COM_HECMAILING_RIGHTS'); ?></td></tr>
   <tr><td><?php echo JText::_('COM_HECMAILING_RIGHT_SEND_MAIL'). ":"; ?></td><td><?php echo JText::_('COM_HECMAILING_RIGHT_MANAGE'). ":"; ?></td><td><?php echo JText::_('COM_HECMAILING_RIGHT_GRANT'). ":"; ?></td></tr>
   <tr><td><input type="checkbox" name="rightg_send" id="rightg_send" checked="checked" value="1"></td><td><input type="checkbox" name="rightg_manage" id="rightg_manage" value="1"></td><td><input type="checkbox" name="rightg_grant" id="rightg_grant" value="1"></td></tr>
</table>

</div><br/>
<div class="buttons"><button onclick="javascript:addGroupePerm();return false;"><img src="../components/com_hecmailing/assets/images/ok16.png" ><?php echo JText::_('COM_HECMAILING_ADD'); ?></button>
<button onclick="javascript:cancelGroupPerm();return false;"><img src="../components/com_hecmailing/assets/images/cancel16.png" ><?php echo JText::_('COM_HECMAILING_CANCEL'); ?></button></div> 
</div>
<div id="dialogDelPerm"   style="display:none;" title="<?php echo JText::_( "COM_HECMAILING_DELETE_PERM" ); ?>">

<div class="image" ><img width="64px" src="../components/com_hecmailing/assets/images/poubelle64.png" ></div>
<div id="dialogDelPerm_msg" class="content"><?php echo JText::_('COM_HECMAILING_REMOVE_ALL_SELECTED'); ?></div><br/>
<div class="buttons"><button onclick="javascript:deleteRowsPerm();return false;"><img src="../components/com_hecmailing/assets/images/ok16.png" ><?php echo JText::_('COM_HECMAILING_REMOVE'); ?></button>
<button onclick="javascript:cancelDelPerm();return false;"><img src="../components/com_hecmailing/assets/images/cancel16.png" ><?php echo JText::_('COM_HECMAILING_CANCEL'); ?></button></div> 
</div>
<div id="dialogGroup"   title="<?php echo JText::_( "COM_HECMAILING_NEW_HECMAILING_GROUP" ); ?>" style="display:none;" >
 <div class="image" ><img width="64px" src="../components/com_hecmailing/assets/images/group64.png" ></div>
 <div class="content">
	<?php echo JText::_('COM_HECMAILING_SELECT_TYPEGROUP_BELOW') ?><br/>
   <?php echo JText::_('COM_HECMAILING_GROUP')." : ".$this->typesgroups ?><br/><br/>
   <?php echo JText::_('COM_HECMAILING_SELECT_GROUP_BELOW') ?><br/>
   <?php echo JText::_('COM_HECMAILING_GROUP')." : ".$this->hecgroups ?>
 </div><br/>
 <div class="buttons"><button onclick="javascript:addGroupe();return false;"><img src="../components/com_hecmailing/assets/images/ok16.png" ><?php echo JText::_('COM_HECMAILING_ADD'); ?></button>
 <button onclick="javascript:cancelGroup();return false;"><img src="../components/com_hecmailing/assets/images/cancel16.png" ><?php echo JText::_('COM_HECMAILING_CANCEL'); ?></button></div>
 
</div>
<!-- FIN DIALOGUES -->
<?php $row=$this->item; ?>
	<form action="<?php echo JRoute::_('index.php?option=com_hecmailing&layout=edit&id='.(int) $this->item->grp_id_groupe); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
		<div class="col">
			<fieldset class="adminform"><legend><?php echo JText::_( 'COM_HECMAILING_GROUP' ); ?></legend>
			<table class="admintable">
  			  <tr>
    				<td class="key"><label for="name"><?php echo JText::_( 'COM_HECMAILING_ID' ); ?>:</label></td>
		    		<td><?php echo $row->grp_id_groupe; ?><input type="hidden" name="grp_id_groupe" id="grp_id_groupe" value="<?php echo $row->grp_id_groupe; ?>" ></td>
          </tr><tr>
        		<td class="key"><label for="name"><?php echo JText::_( 'COM_HECMAILING_NAME' ); ?>:</label></td>
        		<td ><input class="inputbox" type="text" name="grp_nm_groupe" id="grp_nm_groupe" size="60" maxlength="255" value="<?php echo $row->grp_nm_groupe; ?>" /></td>
          </tr><tr>
        		<td class="key"><label for="name"><?php echo JText::_( 'COM_HECMAILING_COMMENT' ); ?>:</label></td>
        		<td ><textarea name="grp_cm_groupe" id="grp_cm_groupe" rows="3" cols="45" class="inputbox"><?php echo $row->grp_cm_groupe; ?></textarea></td>
          </tr><tr>
        		<td class="key"><label for="name"><?php echo JText::_( 'COM_HECMAILING_PUBLISHED' ); ?>:</label></td>
        		<td ><fieldset class="radio"><?php echo $this->published; ?></fieldset></td>
        	</tr></table>	</fieldset>
        	<fieldset class="adminform">
        	<legend><?php echo JText::_( 'COM_HECMAILING_GROUPDETAIL' ); ?></legend>
				<div class="button">
					 <button id="btnnewuser" onclick="javascript:showAddNewUser();return false;" ><img src="../components/com_hecmailing/assets/images/user16.png" ><?php echo JText::_( 'COM_HECMAILING_NEW_USER' ); ?></button>
					 <button id="btnnewmail" onclick="javascript:showAddNewMail();return false;" ><img src="../components/com_hecmailing/assets/images/email16.png" ><?php echo JText::_( 'COM_HECMAILING_NEW_MAIL' ); ?></button>
					 <button id="btnnewgroupe" onclick="javascript:showAddNewGroupe();return false;" ><img src="../components/com_hecmailing/assets/images/group16.png" ><?php echo JText::_( 'COM_HECMAILING_NEW_GROUP' ); ?></button>
					 <button id="delete" onclick="javascript:showDeleteEntry();return false;" ><img src="../components/com_hecmailing/assets/images/poubelle16.png" ><?php echo JText::_( 'COM_HECMAILING_DELETE' ); ?></button>
					 <button id="btnimport" onclick="javascript:showImport();return false;" ><img src="../components/com_hecmailing/assets/images/email16.png" ><?php echo JText::_( 'COM_HECMAILING_IMPORT' ); ?></button>
				</div>
				
				<div id="dialog_import" name="dialog_import" class="hecdialogx" style="display:none;" >
					<br/><hr>
					<div class="header" ><?php echo JText::_( 'COM_HECMAILING_IMPORT_EMAIL' ); ?></div>
					<div class="content">
						<table style="width:100%;align:center;">
						<tr><th><?php echo JText::_('COM_HECMAILING_CHOOSE_FILE') ?></th><th><?php echo JText::_('COM_HECMAILING_DELIMITER') ?></th><th><?php echo JText::_('COM_HECMAILING_LINE_DELIMITER') ?></th>
						<th><?php echo JText::_('COM_HECMAILING_EMAIL_COLUMN') ?></th><th><?php echo JText::_('COM_HECMAILING_LENGTH') ?></th><th><?php echo JText::_('COM_HECMAILING_MODE') ?></th></tr>
						<tr><td><input type="file" name="import_file" id="import_file" value="" width="95%" /></td>
						<td style="align:center"><select name="import_delimiter" id="import_delimiter"  >
							<option value="1"><?php echo JText::_('COM_HECMAILING_DELIMITER_TAB') ?></option>
							<option value="2"><?php echo JText::_('COM_HECMAILING_DELIMITER_SEMI_COLON') ?></option>
							<option value="3"><?php echo JText::_('COM_HECMAILING_DELIMITER_COLON') ?></option>
							<option value="4"><?php echo JText::_('COM_HECMAILING_DELIMITER_SPACE') ?></option>
							<option value="9"><?php echo JText::_('COM_HECMAILING_DELIMITER_FIXE') ?></option></select>   
						</td>
						<td style="align:center"><select name="import_linedelimiter" id="import_linedelimiter"  >
							<option value="*" ><?php echo JText::_('COM_HECMAILING_LINEDELIMITER_DEFAULT') ?></option>
							<option value="1"><?php echo JText::_('COM_HECMAILING_LINEDELIMITER_WINDOWS') ?></option>
							<option value="2"><?php echo JText::_('COM_HECMAILING_LINEDELIMITER_LINUX') ?></option>
							<option value="3"><?php echo JText::_('COM_HECMAILING_LINEDELIMITER_MAC') ?></option></select>
						</td>
						<td><input name="import_column" id="import_column" size="2" type="text" value="0"></td>
						<td><input name="import_len" id="import_len" size="2" type="text"></td>
						<td style="align:center"><select name="import_mode" id="import_mode"  >
							<option value="1"><?php echo JText::_('COM_HECMAILING_MODE_APPEND') ?></option>
							<option value="2"><?php echo JText::_('COM_HECMAILING_MODE_DELETE') ?></option>
							<option value="3"><?php echo JText::_('COM_HECMAILING_MODE_REPLACE') ?></option></select>
						</td>
						</tr></table>
					</div>
				</div>
				
				
					<table class="adminlist table table-striped" id="detail">
						<thead><tr><th class="title"></th><th class="title"><?php echo JText::_('COM_HECMAILING_TYPE'); ?></th><th class="title"><?php echo JText::_('COM_HECMAILING_NAME'); ?></th>
						</tr></thead>
						<tbody>
						<?php 
						  $i=0;$k=0;
						  if ($row->detail)
							foreach ($row->detail as $r)
							{
							   echo "<tr class=\"row".$k."\"><td><input type=\"checkbox\" name=\"suppress".$i."\" id=\"suppress".$i."\" value=\"".$r->gdet_id_detail."\"></td>";
							   $i++;
							   if ($k==1) $k=0;
							   else  $k=1;
								switch($r->gdet_cd_type)
								{
								  case 1: 
									  echo "<td><img src=\"../components/com_hecmailing/assets/images/user16.png\" >".JText::_("COM_HECMAILING_USER")."</td><td>".$r->gdet_vl_value."</td>";
									  break;
								  case 2: 
									  echo "<td><img src=\"../components/com_hecmailing/assets/images/user16.png\" >".JText::_("COM_HECMAILING_USER")."</td><td>".$r->name."</td>";
									  break;
								  case 3: 
									  echo "<td><img src=\"../components/com_hecmailing/assets/images/group16.png\" >".JText::_("COM_HECMAILING_JOOMLA_GROUP")."</td><td>".$r->jgroup_name."</td>";
									  break;
								  case 4: 
									  echo "<td><img src=\"../components/com_hecmailing/assets/images/email16.png\" >".JText::_("COM_HECMAILING_EMAIL")."</td><td>".$r->gdet_vl_value."</td>";
									  break;
								  case 5: 
									  echo "<td><img src=\"../components/com_hecmailing/assets/images/group16.png\" >".JText::_("COM_HECMAILING_HECMAILING_GROUP")."</td><td>".$r->grp_nm_groupe."</td>";
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
				<legend><?php echo JText::_( 'COM_HECMAILING_PERMISSIONS' ); ?></legend>
				<button id="btnnewuser" onclick="javascript:showAddNewUserPerm();return false;" ><img src="../components/com_hecmailing/assets/images/user16.png" ><?php echo JText::_( 'COM_HECMAILING_NEW_USER' ); ?></button>		
				<button id="btnnewgroupe" onclick="javascript:showAddNewGroupePerm();return false;" ><img src="../components/com_hecmailing/assets/images/group16.png" ><?php echo JText::_( 'COM_HECMAILING_NEW_GROUP' ); ?></button>
				<button id="delete" onclick="javascript:showDeletePermEntry();return false;" ><img src="../components/com_hecmailing/assets/images/poubelle16.png" ><?php echo JText::_( 'COM_HECMAILING_DELETE' ); ?></button>
				<table class="adminlist table table-striped" id="permissions">
				<thead>
				  <tr><th class="title"></th><th class="title"><?php echo JText::_('COM_HECMAILING_TYPE'); ?></th><th class="title"><?php echo JText::_('COM_HECMAILING_NAME'); ?></th><th class="title"><?php echo JText::_('COM_HECMAILING_RIGHT_SEND_MAIL'); ?></th>
				  <th class="title"><?php echo JText::_('COM_HECMAILING_RIGHT_MANAGE'); ?></th><th class="title"><?php echo JText::_('COM_HECMAILING_RIGHT_GRANT'); ?></th></tr>
				</thead>
				<tbody>
					<?php 
				  $i=0;$k=0;
          if ($this->item->perms)
				  foreach ($this->item->perms as $r)
				  {
						$i++;
            			echo "<tr class=\"row".$k."\"><td><input type=\"checkbox\" name=\"suppressperm".$i."\" id=\"suppressperm".$i."\" value=\"".$r->grp_id_groupe."-".$r->userid."-".$r->groupid."\" >
							<input type=\"hidden\" name=\"oldperm".$i."\" id=\"oldperm".$i."\" value=\"".$r->grp_id_groupe."-".$r->userid."-".$r->groupid."\"></td>";
			            
			            if ($k==1) $k=0;
			            else  $k=1;
            			if ($r->userid>0)
            			{
            				echo "<td><img src=\"../components/com_hecmailing/assets/images/user16.png\" >".JText::_("COM_HECMAILING_USER")."</td><td>".$r->name."</td>";
	           			}
            			else
            			{
            				echo "<td><img src=\"../components/com_hecmailing/assets/images/group16.png\" >".JText::_("COM_HECMAILING_JOOMLA_GROUP")."</td><td>".$r->jgroup_name."</td>";
            			}
						$rights=$r->flag;
												
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
		<div class="warning"><?php echo  JText::_( 'COM_HECMAILING_WARNING_CHANGE_GROUP' ); ?></div>
		<input type="hidden" name="option" value="com_hecmailing" />
		<input type="hidden" name="id" value="<?php echo $row->grp_id_groupe; ?>" />
		<input type="hidden" name="cid[]" value="<?php echo $row->grp_id_groupe; ?>" />
		<input type="hidden" name="task" value="" />
		<?php echo JHTML::_( 'form.token' ); ?>
</form>


