<?php
/**
* @version 3.4.0
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
		$this->idmessage= 0;
		$currentuser= JFactory::getUser();
		$pparams =$app->getParams();
		$rights="";	
		  $model = $this->getModel('Send'); 
	      $groupe=0; 
	      
	      
	      if ($pparams->get('backup_mail','0')=='1'){$backup_mail = "checked=\"checked\"";}else{$backup_mail="";}
	      $signature=$pparams->get('default_signature','');
	      $default_use_readtag= $pparams->get('default_use_readtag','0');
	         
	      $default_sender =intval($pparams->get('default_sender','0'));
	      if ($pparams->get('image_incorpore','1')=='1') { $image_incorpore = "checked=\"checked\"";  } else { $image_incorpore="";  }
	      if ($pparams->get('ask_select_group','1')=='1') {  $askselect = true;  $groupe=-2; } else {  $askselect=false;  }
	      if ($pparams->get('show_mail_sent','1')=='1') { 	$show_mail_sent = true;  } else { 	$show_mail_sent = false;   }
	  
	   	  $send_all =$pparams->get('send_all','0');
	   	  $upload_input_count =$pparams->get('attach_input_count','0');
	      
	      $browse_path = $pparams->get('browse_path','/images/stories');
	      $height = $pparams->get('edit_width','400');
	      $width = $pparams->get('edit_height','400');
	      $tmpfrom = $model->getFrom();
	      $default =$tmpfrom[$default_sender];
	      $from=$default->email;
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
      	$this->idmessage= $idmessage;
        $this->subject= $infomsg->message_subject;
        $this->body= $infomsg->message_body;
        $this->attachment=$infomsg->attachment;
        $groupe=$infomsg->group_id;
        $from=$infomsg->message_from;
      }
      else if ($idtemplate>0)
      {
        $infomsg = $model->getSavedMail($idtemplate);
        $this->idmsg= $infomsg[0];
        $this->subject= $infomsg[1];
        $this->body= $infomsg[2];
        
        $att=array();
        $this->attachment=$att;
      }
      else
      {
      	$body="\n<div id='signature'>".$signature."</div>";
      	$subject="";
      	$att=array();
      
      	$this->idmsg= 0;
        $this->subject= $subject;
        $this->body= $body;
      	$this->attachment=$att;
      }
      
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
       
      foreach($tmpfrom as $frm)
      {
      	$emailsplit=explode(";",$frm->email);
      	if ($emailsplit[0]==$from) { $from=$frm->email; break;}
      }
      $from = JHTML::_('select.genericlist',  $tmpfrom, 'from', 'class="inputbox" size="1"', 'email', 'name', $from);
      
           
  	  $msg='';
  	  $this->msg= $msg;
  	  $this->signature= $signature;
  	  //$this->use_readtag= $use_readtag;
  	  //$this->groupe=$groupe;
      //$this->groupes= $groupes;
      $this->show_mail_sent= $show_mail_sent;
      $this->rights= $rights;
      $this->from= $from;
      $this->default_use_profil= false; //$default_use_profil;
      $this->upload_input_count= $upload_input_count;
      $this->saved= $saved;
      $this->height= $height;
      $this->width= $width;
      $this->backup_mail= $backup_mail;
      $this->browse_path= $browse_path;
      $this->image_incorpore= $image_incorpore;
      $viewLayout = $app->input->get( 'layout', 'default' );
	  $this->_layout = $viewLayout;
      
      parent::display($tpl); 
   } 
   
   
} 
?> 
