<?php 
/**
* @version 1.7.0
* @package hecMailing for Joomla
* @copyright Copyright (C) 2009 Hecsoft All rights reserved.
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
*
* Change Log
*   1.7.0 : Compatible Joomla 1.6+ 
* 		0.6.0 : Ajout fonctionnalite contact
*		0.5.0 : Correction probleme adresse/nom envoye par
*						Sauvegarde des emails envoyes
*		0.4.0 : Correction suppression item dans groupe
*		0.3.0 : Prise en charge groupe 0 (tous membres) dans getMailAdrFromGroupe
*		0.1.0 : Version d'origine
*/
defined('_JEXEC') or die ('restricted access'); 

jimport('joomla.application.component.model'); 

/**
 * hecMailing Component Form Model
 *
 * @package		Joomla
 */
class HecMailingModelGroup extends JModelLegacy 
{ 
   /**
	 * User id
	 *
	 * @var int
	 */
   var $_id = 0; 
   var $_object=null;
   
   /**
   *    Contructor 
   **/
   function __construct() 
   { 
  	  parent::__construct(); 
   	  $this->params = &JComponentHelper::getParams( 'com_hecmailing' );
      $this->isLog = $this->params->get('Log');
      $this->isLog = true;
      if ($this->isLog)
      {
        $this->_log = &JLog::getInstance('com_hecmailing.log.php');
      }
   } 

   
  /**
	 * Method to write text into component log
	 *
	 * @access	public
	 * @param	string Text to write
	 */
   function Log($text)
   {  
      if ($this->isLog)
      {
        $this->_log->addEntry(array('comment' => $text));
      }
   }






} 

?> 

