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
		$app = JFactory::getApplication();
        $currentuser= JFactory::getUser();
		$pparams = $app->getParams();
		$model = $this->getModel(); 
		//$this->form = $this->get('Form');
		$data = $app->input->get('jform', '', 'array');
		$this->form = $model->getForm($data);
		$captcha_show_logged = ($pparams->get('captcha_show_logged','1')=='1');
		$use_captcha = ($pparams->get('captcha_use','1')=='1');
		$redirect = $pparams->get('contact_redirect','');
		$title = JText::_($pparams->get('contact_title','COM_HECMAILING_CONTACT'));

		
		if (!$currentuser->guest)
		{
			$email = $currentuser->email;
			$name = $currentuser->name;
		}
		else
		{
			$email="";
			$name="";
		}

		$defaultCaptcha = $app->get('captcha');
		if ($app->isClient('site'))
		{
			$defaultCaptcha = $app->getParams()->get('captcha', $defaultCaptcha);
		}
		
		$this->title=$title;
		$this->captcha_show_logged=$captcha_show_logged;
		$this->showCaptcha = ($use_captcha && (JFactory::getUser()->guest || !$captcha_show_logged) && $defaultCaptcha);
		$viewLayout = $app->input->get( 'layout', 'default' );
		$this->_layout = $viewLayout;

        parent::display($tpl); 
	} 

} 

?> 

