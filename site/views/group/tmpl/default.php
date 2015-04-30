<?php 
/**
 * @version 1.8.2
 * @package hecmailing
 * @copyright 2009-2013 Hecsoft.net
 * @license http://www.gnu.org/licenses/gpl-3.0.html
 * @link http://joomla.hecsoft.net
 * @author H Cyr
 **/
defined ('_JEXEC') or die ('restricted access'); 
jimport('joomla.html.html');
JHTML::_('behavior.tooltip');

// Modif Joomla 1.6+
$mainframe = JFactory::getApplication();
$document = JFactory::getDocument();
// Modif pour J1.6+ : change $mainframe->addCustomHeadTag en   $document->addCustomTag
$document->addCustomTag('<link rel="stylesheet" href="components/com_hecmailing/css/toolbar.css" type="text/css" media="screen" />');
$document->addCustomTag ('<link rel="stylesheet" href="components/com_hecmailing/css/dialog.css" type="text/css" media="screen" />');

    	JHTML::_('behavior.tooltip');
    	jimport('joomla.html.pane');
      // TODO: allowAllClose should default true in J!1.6, so remove the array when it does.
    	//$pane = JPane::getInstance('sliders', array('allowAllClose' => true));
    	JFilterOutput::objectHTMLSafe( $row, ENT_QUOTES, 'misc' );
    	$cparams = JComponentHelper::getParams ('com_hecmailing');
    	$document =JFactory::getDocument();
    	$burl = "";
    	$document->addScript($burl."components/com_hecmailing/libraries/jt/dom-drag.js");
    	$document->addScript($burl."components/com_hecmailing/libraries/jt/jt_.js");
    	$document->addScript($burl."components/com_hecmailing/libraries/jt/jt_DialogBox.js");
    	$document->addScript($burl."components/com_hecmailing/libraries/jt/jt_AppDialogs.js");
    	$document->addStyleSheet($burl."components/com_hecmailing/libraries/jt/jt_DialogBox.css");
    	$document->addStyleSheet($burl."components/com_hecmailing/libraries/jt/veils.css");
    	$document->addScript( "administrator/components/com_hecmailing/admin.hecmailing.js");
    	$row=$this->row;
    	$lists=$this->lists;
		$detail=$this->detail;
		$params=$this->params;
		$users=$this->users;
		$groups=$this->groups;
		$perms=$this->perms;
?>
	<script type="text/javascript">
  <!--
  		jt_DialogBox.imagePath = '../components/com_hecmailing/libraries/jt/';
  		jt_DialogBox.imagePath = '<?php echo JURI::base( true ).'/components/com_hecmailing/libraries/jt/';?>';
  		submit_MustName = '<?php echo JText::_( 'YOU_MUST_PROVIDE_A_NAME', true ); ?>';
  		text_user ="<?php echo JText::_('USER', true); ?>";
  		text_mail="<?php echo JText::_('EMAIL', true); ?>";
  		text_group="<?php echo JText::_('GROUP', true); ?>";
  		text_noitem='<?php echo JText::_("NO_SELECTED_ITEM"); ?>';
  		text_wantremove='<?php echo JText::_('WANT_REMOVE', true) ?>';
  		text_items='<?php echo JText::_('ITEMS', true) ?> ';
  		text_perms='<?php echo JText::_('PERMISSIONS', true) ?> ';
  		text_noperm='<?php echo JText::_("NO_SELECTED_PERM_ITEM"); ?>';
  		base_url="";
  //-->
  </script>
  <LINK rel="stylesheet" type="text/css" href="components/com_hecmailing/css/dialog.css">
  <div id="dialog_container"></div>
  <div id="dialog" class="hecdialog" style="display:none;"  >
  <div id='dialog_title' style="display:none;"><?php echo JText::_( "NEW_USER" ); ?></div>
  <div class="image"><img src="components/com_hecmailing/images/user64.png" ></div>
  <div class="content"><br/><?php echo JText::_('SELECT_USER_BELOW'); ?><br/><br/>
   <?php echo JText::_('USER')." : ".$users ?></div>
     <div class="buttons"><button onclick="javascript:addUser();return false;"><img src="components/com_hecmailing/images/ok16.png" ><?php echo JText::_('ADD'); ?></button><button onclick="javascript:cancel();return false;"><img src="components/com_hecmailing/images/cancel16.png" ><?php echo JText::_('CANCEL'); ?></button></div> 
     </div>
      <div id="dialog2"  class="hecdialog" style="display:none;" >
      <div id='dialog2_title' style="display:none;"><?php echo JText::_( "NEW_GROUP" ); ?></div>
         <div class="image" ><img width="64px" src="components/com_hecmailing/images/group64.png" ></div>
         <div class="content">
           <?php echo JText::_('SELECT_GROUP_BELOW') ?><br/><br/>
           <?php echo JText::_('GROUP')." : ".$jgroups ?>
         </div>
         <div class="buttons"><button onclick="javascript:addGroupe();return false;"><img src="components/com_hecmailing/images/ok16.png" ><?php echo JText::_('ADD'); ?></button><button onclick="javascript:cancel();return false;"><img src="components/com_hecmailing/images/cancel16.png" ><?php echo JText::_('CANCEL'); ?></button></div> 
      </div>
      <div id="dialog3"  class="hecdialog" style="display:none;">
      <div id='dialog3_title' style="display:none;"><?php echo JText::_( "DELETE" ); ?></div>
         <div class="image" ><img width="64px" src="components/com_hecmailing/images/poubelle64.png" ></div>
         <div id="dialog3_msg"  class="content"><?php echo JText::_('REMOVE_ALL_SELECTED'); ?></div>
         <div class="buttons"><button onclick="javascript:deleteRows();return false;"><img src="components/com_hecmailing/images/ok16.png" ><?php echo JText::_('REMOVE'); ?></button><button onclick="javascript:cancel();return false;"><img src="components/com_hecmailing/images/cancel16.png" ><?php echo JText::_('CANCEL'); ?></button></div> 
      </div>
      <div id="dialog4"  class="hecdialog" style="display:none;">
      	<div id='dialog4_title' style="display:none;"><?php echo JText::_( "NEW_MAIL" ); ?></div>	
         <div class="image" ><img width="64px" src="components/com_hecmailing/images/email64.png" ></div>
         <div class="content">
           <?php echo JText::_('ENTER_EMAIL_BELOW') ?><br/><br/>
           <?php echo JText::_('EMAIL')." : "?><input type="text" name="newmail" id="newmail" value="" width="95%" />
         </div>
         <div class="buttons"><button onclick="javascript:addMail();return false;"><img src="../components/com_hecmailing/images/ok16.png" ><?php echo JText::_('ADD'); ?></button><button onclick="javascript:cancel();return false;"><img src="components/com_hecmailing/images/cancel16.png" ><?php echo JText::_('CANCEL'); ?></button></div> 
      </div>
      <div id="dialog5" class="hecdialog"  style="display:none;"  >
      	<div id='dialog5_title' style="display:none;"><?php echo JText::_( "NEW_USER_PERM" ); ?></div>
         <div class="image"><img src="components/com_hecmailing/images/user64.png" ></div>
         <div class="content"><br/>
			<table width="100%"><tr><td colspan="2">
           <?php echo JText::_('SELECT_USER_BELOW'); ?><br/></td></tr>
		   <tr><td><?php echo JText::_('USER')." : </td><td>".$users ?></td></tr>
		   <tr><td colspan="2"><?php echo JText::_('RIGHTS'); ?></td></tr>
		   <tr><td><?php echo JText::_('RIGHT_SEND_MAIL'). ":"; ?></td><td><input type="checkbox" name="right_send" id="right_send" checked="checked"></td></tr>
		   <!--<tr><td><?php echo JText::_('RIGHT_EDIT'). ":"; ?></td><td><input type="checkbox" name="right_edit" id="right_edit" ></td></tr>-->
		   <tr><td><?php echo JText::_('RIGHT_MANAGE'). ":"; ?></td><td><input type="checkbox" name="right_manage" id="right_manage"></td></tr>
		   <tr><td><?php echo JText::_('RIGHT_GRANT'). ":"; ?></td><td><input type="checkbox" name="right_grant" id="right_grant"></td></tr>
		   </table>
		   </div>
        <div class="buttons"><button onclick="javascript:addUserPerm();return false;"><img src="components/com_hecmailing/images/ok16.png" ><?php echo JText::_('ADD'); ?></button><button onclick="javascript:cancel();return false;"><img src="components/com_hecmailing/images/cancel16.png" ><?php echo JText::_('CANCEL'); ?></button></div> 
   </div>
   <div id="dialog6" class="hecdialog" style="display:none;" >
   		<div id='dialog6_title' style="display:none;"><?php echo JText::_( "NEW_GROUP_PERM" ); ?></div>
        <div class="image" ><img width="64px" src="components/com_hecmailing/images/group64.png" ></div>
        <div class="content">
		<table width="100%">
			<tr><td colspan="2"><?php echo JText::_('SELECT_GROUP_BELOW') ?><br/></td></tr>
			<tr><td><?php echo JText::_('GROUP')." : </td><td>".$jgroups ?></td></tr>
		<tr><td colspan="2"><?php echo JText::_('RIGHTS'); ?></td></tr>
		   <tr><td><?php echo JText::_('RIGHT_SEND_MAIL'). ":"; ?></td><td><input type="checkbox" name="rightg_send" id="rightg_send" checked="checked"></td></tr>
		   <tr><td><?php echo JText::_('RIGHT_MANAGE'). ":"; ?></td><td><input type="checkbox" name="rightg_manage" id="rightg_manage" ></td></tr>
		   <tr><td><?php echo JText::_('RIGHT_GRANT'). ":"; ?></td><td><input type="checkbox" name="rightg_grant" id="rightg_grant" ></td></tr>
		</table>
		
        </div>
        <div class="buttons"><button onclick="javascript:addGroupePerm();return false;"><img src="components/com_hecmailing/images/ok16.png" ><?php echo JText::_('ADD'); ?></button><button onclick="javascript:cancel();return false;"><img src="components/com_hecmailing/images/cancel16.png" ><?php echo JText::_('CANCEL'); ?></button></div> 
   </div>
   <div id="dialog7"  class="hecdialog" style="display:none;">
   <div id='dialog7_title' style="display:none;"><?php echo JText::_( "DELETE_PERM" ); ?></div>
		<div class="image" ><img width="64px" src="components/com_hecmailing/images/poubelle64.png" ></div>
        <div id="dialog7_msg" class="content"><?php echo JText::_('REMOVE_ALL_SELECTED'); ?></div>
        <div class="buttons"><button onclick="javascript:deleteRowsPerm();return false;"><img src="components/com_hecmailing/images/ok16.png" ><?php echo JText::_('REMOVE'); ?></button><button onclick="javascript:cancel();return false;"><img src="components/com_hecmailing/images/cancel16.png" ><?php echo JText::_('CANCEL'); ?></button></div> 
    </div>
	<form action="index.php" method="post" name="adminForm">
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
             <button id="btnnewuser" onclick="javascript:showAddNewUser();return false;" ><img src="components/com_hecmailing/images/user16.png" ><?php echo JText::_( 'NEW_USER' ); ?></button>
             <button id="btnnewmail" onclick="javascript:showAddNewMail();return false;" ><img src="components/com_hecmailing/images/email16.png" ><?php echo JText::_( 'NEW_MAIL' ); ?></button>
             <button id="btnnewgroupe" onclick="javascript:showAddNewGroupe();return false;" ><img src="components/com_hecmailing/images/group16.png" ><?php echo JText::_( 'NEW_GROUP' ); ?></button>
             <button id="delete" onclick="javascript:showDeleteEntry();return false;" ><img src="components/com_hecmailing/images/poubelle16.png" ><?php echo JText::_( 'DELETE' ); ?></button>
             <button id="btnimport" onclick="javascript:showImport();return false;" ><img src="components/com_hecmailing/images/email16.png" ><?php echo JText::_( 'IMPORT' ); ?></button>
             <div id="dialog_import" name="dialog_import" class="hecdialogx" style="display:none;" >
              <div class="header" ><?php echo JText::_( 'IMPORT_EMAIL' ); ?></div>
               <div class="content">
                <table style="width:100%;align:center;"><tr><th><?php echo JText::_('CHOOSE_FILE') ?></th><th><?php echo JText::_('DELIMITER') ?></th>
                <th><?php echo JText::_('EMAIL_COLUMN') ?></th><th><?php echo JText::_('LENGTH') ?></th><th><?php echo JText::_('MODE') ?></th></tr>

             <tr><td><input type="file" name="import_file" id="import_file" value="" width="95%" /></td>

             <td style="align:center"><select name="import_delimiter" id="import_delimiter"  >

                <option value="1"><?php echo JText::_('DELIMITER_TAB') ?></option>

                <option value="2"><?php echo JText::_('DELIMITER_SEMI_COLON') ?></option>

                <option value="3"><?php echo JText::_('DELIMITER_COLON') ?></option>

                <option value="4"><?php echo JText::_('DELIMITER_SPACE') ?></option>

                <option value="9"><?php echo JText::_('DELIMITER_FIXE') ?></option></select>   </td>

              <td><input name="import_column" id="import_column" size="2" type="text"></td>

              <td><input name="import_len" id="import_len" size="2" type="text"></td>

              <td style="align:center"><select name="import_mode" id="import_mode"  >

                <option value="1"><?php echo JText::_('MODE_APPEND') ?></option>

                <option value="2"><?php echo JText::_('MODE_DELETE') ?></option>

                <option value="3"><?php echo JText::_('MODE_REPLACE') ?></option></select></td>

              </tr></table>

           </div>

            

        </div>

				<table class="adminlist" id="detail" width="100%">

				<thead>

				  <th class="title"></th><th class="title"><?php echo JText::_('TYPE'); ?></th><th class="title"><?php echo JText::_('NAME'); ?></th>

				</thead>

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
                  echo "<td><img src=\"components/com_hecmailing/images/user16.png\" >".JText::_("USER")."</td><td>".$r[2]."</td>";
                  break;
              case 2: 
                  echo "<td><img src=\"components/com_hecmailing/images/user16.png\" >".JText::_("USER")."</td><td>".$r[4]."</td>";
                  break;
              case 3: 
                  echo "<td><img src=\"components/com_hecmailing/images/group16.png\" >".JText::_("GROUP")."</td><td>".$r[5]."</td>";
                  break;
              case 4: 
                  echo "<td><img src=\"components/com_hecmailing/images/email16.png\" >".JText::_("EMAIL")."</td><td>".$r[2]."</td>";
                  break;
            }
            echo "</tr>";
          }
          echo "<input type=\"hidden\" name=\"nbold\" id=\"nbold\" value=\"".$i."\"/>";
          echo "<input type=\"hidden\" name=\"nbnew\" id=\"nbnew\" value=\"0\"/>";
          echo "<input type=\"hidden\" name=\"todel\" id=\"todel\" value=\"\"/>";
          echo "<input type=\"hidden\" name=\"toimport\" id=\"toimport\" value=\"0\"/>";
        ?>				
				</tbody>
				</table>
			</fieldset>
			<fieldset class="adminform">
				<legend><?php echo JText::_( 'PERMISSIONS' ); ?></legend>
				<button id="btnnewuser" onclick="javascript:showAddNewUserPerm();return false;" ><img src="components/com_hecmailing/images/user16.png" ><?php echo JText::_( 'NEW_USER' ); ?></button>		
				<button id="btnnewgroupe" onclick="javascript:showAddNewGroupePerm();return false;" ><img src="components/com_hecmailing/images/group16.png" ><?php echo JText::_( 'NEW_GROUP' ); ?></button>
				<button id="delete" onclick="javascript:showDeletePermEntry();return false;" ><img src="components/com_hecmailing/images/poubelle16.png" ><?php echo JText::_( 'DELETE' ); ?></button>
				<table class="adminlist" id="permissions" width="100%">
				<thead>
				  <th class="title"></th><th class="title"><?php echo JText::_('TYPE'); ?></th><th class="title"><?php echo JText::_('NAME'); ?></th><th class="title"><?php echo JText::_('RIGHT_SEND_MAIL'); ?></th>
				  <th class="title"><?php echo JText::_('RIGHT_MANAGE'); ?></th><th class="title"><?php echo JText::_('RIGHT_GRANT'); ?></th>
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
            				echo "<td><img src=\"components/com_hecmailing/images/user16.png\" >".JText::_("USER")."</td><td>".$r[3]."</td>";
	           			}
            			else
            			{
            				echo "<td><img src=\"components/com_hecmailing/images/group16.png\" >".JText::_("GROUP")."</td><td>".$r[4]."</td>";
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
		<input type="submit" value="Enregister"><button onClick="javascript:window.close();return false;">Annuler</button>
		<input type="hidden" name="option" value="com_hecmailing" />
		<input type="hidden" name="idgroup" value="<?php echo $row->grp_id_groupe; ?>" />
		<input type="hidden" name="task" value="save_group" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>
		

<?php echo JHTML::_( 'form.token' ); ?>

