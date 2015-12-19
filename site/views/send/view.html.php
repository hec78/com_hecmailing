<?php
/**
* @version 1.8.0
* @package hecMailing for Joomla
* @subpackage : View Form (Sending mail form)
* @module views.form.tmpl.view.html.php
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
 
defined('_JEXEC') or die ('restricted access'); 

jimport('joomla.application.component.view'); 
jimport('joomla.html.toolbar');
class hecMailingViewSend extends JViewLegacy 
{ 
    
   function display ($tpl=null) 
   { 
		// Modif Joomla 1.6+
		$app = JFactory::getApplication();
		$state      = $this->get('State');
		$this->item = $this->get('Item');
		$this->form = $this->get('Form');
   
		$currentuser= JFactory::getUser();
		$pparams =$app->getParams();
			
		  $model = $this->getModel('Send'); 
	      $groupe=0; 
	      $send_all=($pparams->get('send_all','0')=='1');
	      
	      if ($pparams->get('backup_mail','0')=='1'){$backup_mail = "checked=\"checked\"";}else{$backup_mail="";}
	      $signature=$pparams->get('default_signature','');
	      $default_use_readtag= $pparams->get('default_use_readtag','0');
	      $readtagopt=array();
	      $readtagopt[] = JHTML::_('select.option', 0, JText::_('COM_HECMAILING_READTAG_POSITION_NONE'), 'message_readtag_notification', 'message_readtag_notification_label');
	      $readtagopt[] = JHTML::_('select.option', 1, JText::_('COM_HECMAILING_READTAG_POSITION_TOP'), 'message_readtag_notification', 'message_readtag_notification_label');
	      $readtagopt[] = JHTML::_('select.option', 2, JText::_('COM_HECMAILING_READTAG_POSITION_BOTTOM'), 'message_readtag_notification', 'message_readtag_notification_label');
	      $readtagopt[] = JHTML::_('select.option', 3, JText::_('COM_HECMAILING_READTAG_POSITION_OTHER'), 'message_readtag_notification', 'message_readtag_notification_label');
	      $use_readtag = JHTML::_('select.genericlist',  $readtagopt, 'use_readtag', 'class="inputbox" size="1" ', 'message_readtag_notification', 'message_readtag_notification_label', intval($default_use_readtag));
	      
	      $default_sender =intval($pparams->get('default_sender','0'));
	      if ($pparams->get('image_incorpore','1')=='1') { $image_incorpore = "checked=\"checked\"";  } else { $image_incorpore="";  }
	      if ($pparams->get('ask_select_group','1')=='1') {  $askselect = true;  $groupe=-2; } else {  $askselect=false;  }
	      if ($pparams->get('show_mail_sent','1')=='1') { 	$show_mail_sent = true;  } else { 	$show_mail_sent = false;   }
	  
	   	  $send_all =$pparams->get('send_all','0');
	   	  $upload_input_count =$pparams->get('attach_input_count','0');
	      
	      $browse_path = $pparams->get('browse_path','/images/stories');
	      $height = $pparams->get('edit_width','400');
	      $width = $pparams->get('edit_height','400');
	      
	      $groupelist = $model->getGroupes($send_all,false, $askselect);
	      if (!$groupelist)
	      {
	        $groupes = JText::_("COM_HECMAILING_NO_GROUP");
	      }
	      else
	      {
	        
	        $groupes = JHTML::_('select.genericlist',  $groupelist[0], 'groupe', 'class="inputbox" size="1" onchange="showManageButton(this)"', 'grp_id_groupe', 'grp_nm_groupe', intval($groupe));
	        $rights = "var rights={".join(",",$groupelist[1])."};";
	      }
	      
	      $tmpfrom = $model->getFrom();
	      $default =$tmpfrom[$default_sender];  
	      $from = JHTML::_('select.genericlist',  $tmpfrom, 'from', 'class="inputbox" size="1"', 'email', 'name', $default->email);
	      $idtemplate = $app->input->getInt('idTemplate', 0, 'post');
		  $idmessage = $app->input->getInt('idmessage', 0);
		  $savedlist = $model->getSavedMails();
		  if ($savedlist)
		  {
        	#$saved = JHTML::_('select.genericlist',  $savedlist, 'saved', 'class="inputbox" size="1" onchange="javascript:submitbutton(\'load\');"', 'msg_id_message', 'msg_vl_subject', intval($idmsg));
        	$saved = JHTML::_('select.genericlist',  $savedlist, 'saved', 'class="inputbox" size="1" ', 'msg_id_message', 'msg_lb_message', intval($idmsg));
      		}else {
       			$saved=JText::_("COM_HECMAILING_NO_SAVED_MAIL");
      		}  
      
      if ($idmessage>0)
      {
      	
      	$infomsg = $model->getMessage($idmessage);
      	//$this->assignRef('idmsg', 0);
      	$this->assignRef('idmessage', $infomsg->id);
        $this->assignRef('subject', $infomsg->message_subject);
        $this->assignRef('body', $infomsg->message_body);
        $this->assignRef('attachment', $infomsg->attachment);
        
      }
      else if ($idtemplate>0)
      {
        $infomsg = $model->getSavedMail($idtemplate);
        $this->assignRef('idmsg', $infomsg[0]);
        $this->assignRef('subject', $infomsg[1]);
        $this->assignRef('body', $infomsg[2]);
        $att=array();
        $this->assignRef('attachment',$att);
      }
      else
      {
      	$body="\n<div id='signature'>".$signature."</div>";
      	$subject="";
      	$att=array();
      
      	$this->assignRef('idmsg', $idmsg);
        $this->assignRef('subject', $subject);
        $this->assignRef('body', $body);
      	$this->assignRef('attachment',$att);
      }
  	  $msg='';
  	  $this->assignRef('msg', $msg);
  	  $this->assignRef('signature', $signature);
  	  $this->assignRef('use_readtag', $use_readtag);
      $this->assignRef('groupes', $groupes);
      $this->assignRef('show_mail_sent', $show_mail_sent);
      $this->assignRef('rights', $rights);
      $this->assignRef('from', $from);
      $this->assignRef('default_use_profil', $default_use_profil);
      $this->assignRef('upload_input_count', $upload_input_count);
      $this->assignRef('saved', $saved);
      $this->assignRef('height', $height);
      $this->assignRef('width', $width);
      $this->assignRef('backup_mail', $backup_mail);
      $this->assignRef('browse_path', $browse_path);
      $this->assignRef('image_incorpore', $image_incorpore);
      $viewLayout = JRequest::getVar( 'layout', 'default' );
	  $this->_layout = $viewLayout;
      
      parent::display($tpl); 
   } 
   
   
} 
?> 
