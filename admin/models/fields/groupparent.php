<?php
/**
* @version   3.4.0
* @package   HEC Mailing for Joomla
* @copyright Copyright (C) 1999-2017 Hecsoft All rights reserved.
* @author    Herve CYR
* @license   GNU/GPL
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
*/

defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('list');

/**
 * Form Field class for the Joomla Framework.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_users
 * @since       1.6
 */
class JFormFieldGroupParent extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var        string
	 * @since   1.6
	 */
	protected $type = 'GroupParent';

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 * @since   1.6
	 */
	protected function getOptions()
	{
		$options = array();

		$db = JFactory::getDbo();
		$user = JFactory::getUser();
		$query = $db->getQuery(true)
			->select('a.id AS value, a.title AS text, COUNT(DISTINCT b.id) AS level')
			->from('#__usergroups AS a')
			->join('LEFT', $db->quoteName('#__usergroups') . ' AS b ON a.lft > b.lft AND a.rgt < b.rgt');

		// Prevent parenting to children of this item.
		if ($id = $this->form->getValue('id'))
		{
			$query->join('LEFT', $db->quoteName('#__usergroups') . ' AS p ON p.id = ' . (int) $id)
				->where('NOT(a.lft >= p.lft AND a.rgt <= p.rgt)');
		}

		$query->group('a.id, a.title, a.lft, a.rgt')
			->order('a.lft ASC');

		// Get the options.
		$db->setQuery($query);

		try
		{
			$options = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}

		// Pad the option text with spaces using depth level as a multiplier.
		for ($i = 0, $n = count($options); $i < $n; $i++)
		{
			// Show groups only if user is super admin or group is not super admin
			if ($user->authorise('core.admin') || (!JAccess::checkGroup($options[$i]->value, 'core.admin')))
			{
				$options[$i]->text = str_repeat('- ', $options[$i]->level) . $options[$i]->text;
			}
			else
			{
				unset($options[$i]);
			}
		}

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
