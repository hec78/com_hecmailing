<?php
/**
* @package     HECMailing
* @subpackage  Main
*
* @copyright   Copyright (C) 2005 - 2014 HecSoft All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;
require_once JPATH_COMPONENT.'/controller.php';
/**
 * @package     HECMailing
 * @subpackage  Main
 */
class HecMailingControllerGroup extends HecMailingController
{
	/**
	 * The URL view item variable.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $view_item = 'group';

	/**
	 * The URL view list variable.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $view_list = null;

	
}