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
class hecMailingViewForm extends JView 
{ 
    
   function display ($tpl=null) 
   { 
		// Modif Joomla 1.6+
		$mainframe = JFactory::getApplication();
     
   
		$currentuser= &JFactory::getUser();
		$pparams = &$mainframe->getParams();
			
	  $model = & $this->getModel(); 
      $groupe=0; 
      if ($pparams->get('send_all','0')=='1')
      {
        $send_all = true;
      }
      else
      {
        $send_all=false;
      }
      
      if ($pparams->get('backup_mail','0')=='1')
      {
        $backup_mail = "checked=\"checked\"";
        
      }
      else
      {
        $backup_mail="";
      }
      
    if ($pparams->get('default_use_profil','0')=='1')
      {
        $default_use_profil = "checked=\"checked\"";
        
      }
      else
      {
        $default_use_profil="";
      }
      
      if ($pparams->get('image_incorpore','1')=='1')
      {
        $image_incorpore = "checked=\"checked\"";
        
      }
      else
      {
        $image_incorpore="";
      }
      if ($pparams->get('ask_select_group','1')=='1')
      {
        $askselect = true;
        $groupe=-2;
      }
      else
      {
        $askselect=false;
      }
      if ($pparams->get('show_mail_sent','1')=='1')
      {
      	$show_mail_sent = true;
      
      }
      else
      {
      	$show_mail_sent = false;
      }
       
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
      $from = JHTML::_('select.genericlist',  $tmpfrom, 'from', 'class="inputbox" size="1"', 'email', 'name');
     $idmsg = JRequest::getInt('idTemplate', 0, 'post');
     $idlog = JRequest::getInt('idlog', 0);
		  $savedlist = $model->getSavedMails();
		  if ($savedlist)
		  {
        #$saved = JHTML::_('select.genericlist',  $savedlist, 'saved', 'class="inputbox" size="1" onchange="javascript:submitbutton(\'load\');"', 'msg_id_message', 'msg_vl_subject', intval($idmsg));
        $saved = JHTML::_('select.genericlist',  $savedlist, 'saved', 'class="inputbox" size="1" ', 'msg_id_message', 'msg_lb_message', intval($idmsg));
      }
      else {
       $saved=JText::_("COM_HECMAILING_NO_SAVED_MAIL");;}  
      
      if ($idlog>0)
      {
      	
      	$infomsg = $model->getLogDetail($idlog);
      	//$this->assignRef('idmsg', 0);
      	$this->assignRef('idlog', $infomsg->log_id_message);
        $this->assignRef('subject', $infomsg->log_vl_subject);
        $this->assignRef('body', $infomsg->log_vl_body);
        $this->assignRef('attachment', $infomsg->attachment);
        
      }
      else if ($idmsg>0)
      {
        $infomsg = $model->getSavedMail($idmsg);
        $this->assignRef('idmsg', $infomsg[0]);
        $this->assignRef('subject', $infomsg[1]);
        $this->assignRef('body', $infomsg[2]);
        $att=array();
        $this->assignRef('attachment',$att);
      }
      else
      {
      	$body="";
      	$subject="";
      	$att=array();
      
      	$this->assignRef('idmsg', $idmsg);
        $this->assignRef('subject', $subject);
        $this->assignRef('body', $body);
      	$this->assignRef('attachment',$att);
      }
  		$msg='';
  		$this->assignRef('msg', $msg);
      $this->assignRef('groupes', $groupes);
      $this->assignRef('show_mail_sent', $show_mail_sent);
      $this->assignRef('rights', $rights);
      $this->assignRef('from', $from);
      $this->assignRef('default_use_profil', $default_use_profil);
      $this->assignRef('upload_input_count', intval($upload_input_count));
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
