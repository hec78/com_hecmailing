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
class hecMailingViewSending extends JViewLegacy 
{ 
    
   function display ($tpl=null) 
   { 
		// Modif Joomla 1.6+
		$app = JFactory::getApplication();
     
   
		$currentuser= JFactory::getUser();
		$pparams =$app->getParams();
			
	  	$model = $this->getModel('Sending'); 
	  	$idMessage = JRequest::getInt('idmessage', 0);
	  	$this->assignRef('idmessage', $idMessage);
      	$message = $model->getMessage($idMessage);
      	$params 	= JComponentHelper::getParams( 'com_hecmailing' );
      	$count=$params->get('send_count','1');
      	$this->assignRef('count', $count);
      	if (!$message)
      	{
      		$this->_layout = 'nomessage';
      	}
      	else
      	{
      		$this->assignRef('message', $message);
      		$viewLayout = JRequest::getVar( 'layout', 'default' );
	    	$this->_layout = $viewLayout;
      	}
      	parent::display($tpl); 
   } 
   
   
} 
?> 
