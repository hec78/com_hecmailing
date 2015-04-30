<?php
/**
* @version 3.3.0
* @package hecMailing for Joomla
* @copyright Copyright (C) 2005-2013 Hecsoft All rights reserved.
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

/* No direct access */

defined ('_JEXEC') or die ('restricted access');

/* Correction pour Joomla 3 */
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);



$task = JRequest::getVar('task','');

$controller = JControllerLegacy::getInstance('HecMailing');
$controller->execute(JFactory::getApplication()->input->get('task', 'display'));
$controller->redirect();
?> 