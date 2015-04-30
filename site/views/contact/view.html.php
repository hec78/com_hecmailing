<?php 
/**
* @version 1.7.0
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

class hecMailingViewContact extends JViewLegacy 
{ 
	function display ($tpl=null) 
	{ 
      
		// Modif Joomla 1.6+
		$mainframe = JFactory::getApplication();
        $currentuser= JFactory::getUser();
		$pparams = $mainframe->getParams();
		$model = $this->getModel(); 
		$contact = JRequest::getInt('contact', 0);  
		$height = $pparams->get('edit_width','400');
		$width = $pparams->get('edit_height','400');
		$captcha_theme = $pparams->get('captcha_theme','white');
		$captcha_show_logged = $pparams->get('captcha_show_logged','1');
		$publickey = $pparams->get('captcha_public_key','6LexAQwAAAAAANErhkc2zLD3wmsiZWeU1Cstc50_');
		if ($captcha_show_logged =='1') $captcha_show_logged=true;
		else $captcha_show_logged=false;
		$title = $pparams->get('contact_title',JText::_('CONTACT'));
		$c    = $model->getContacts();
		$contactlist=$c[0];
		$infolist = $c[1]; 
		$infoid = $c[2];
		$names=$c[3];

		if ($contactlist)
		{
			if (count($contactlist)>2)
			{
				$contacts = JHTML::_('select.genericlist',  $contactlist, 'contact', 'class="inputbox" size="1" onchange="showInfo(this.value)"', 'ct_id_contact', 'ct_nm_contact', intval($contact));
            }
			else
			{
                $contacts = '<input type="hidden" id="contact" name="contact" value="'.$infoid[0].'">';
            }
		}
		else 
		{
			$contacts="";
		}  

		if ($contact>0)
		{
			$contactinfo = $model->getContactInfo($contact);
		}
		else
		{
			$contactinfo=false;
		}
		if (!$currentuser->guest)
		{
			$email = $currentuser->email;
			$name = $currentuser->name;
			$lang = $currentuser->getParam('language', '');
		}
		else
		{
			$email="";
			$name="";
			$lang = $currentuser->getParam('language', '');
		}

		JLoader::import ( 'hecmailing',JPATH_COMPONENT_ADMINISTRATOR.DS."helpers");
		$ver =   HecMailingHelper::getComponentVersion();

		/* Recuperer les champs déjà saisis */
		$email = JRequest::getString('email', $email, 'post');
		$name = JRequest::getString('name', $name, 'post');
		$subject = JRequest::getString('subject', '', 'post');
		$body = JRequest::getVar('body', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$logmail 		= JRequest::getString('backup_mail', 0, 'post');
		$msg="";
		$backup_mail="checked";
		$this->assignRef('contacts', $contacts);
		$this->assignRef('names', $names);
		$this->assignRef('version', $ver);
		$this->assignRef('contact', $contact);
		$this->assignRef('contactinfo', $contactinfo);
		$this->assignRef('infolist', $infolist);
		$this->assignRef('infoid', $infoid);
		$this->assignRef('height', $height);
		$this->assignRef('width', $width);
		$this->assignRef('title', $title);
		$this->assignRef('email', $email);
		$this->assignRef('lang', $lang);
		$this->assignRef('name', $name);
		$this->assignRef('publickey', $publickey);
		$this->assignRef('subject', $subject);
		$this->assignRef('body', $body);
		$this->assignRef('logmail', $logmail);
		$this->assignRef('captcha_theme', $captcha_theme);
		$this->assignRef('captcha_show_logged', $captcha_show_logged);
		$this->assignRef('msg', $msg);
		$this->assignRef('backup_mail', $backup_mail);
		$viewLayout = JRequest::getVar( 'layout', 'default' );
		$this->_layout = $viewLayout;

        parent::display($tpl); 
	} 

} 

?> 

