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

defined('_JEXEC') or die;

/**
 * Methods supporting a list of user group records.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_users
 * @since       1.6
 */
class HecMailingModelGroups extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param   array  An optional associative array of configuration settings.
	 * @see     JController
	 * @since   1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'g.grp_id_groupe',
				'title','g.grp_nm_groupe', 
				'desc','g.grp_cm_groupe',
				'nb_items', 'grp_nb_items'
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since   1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Load the filter state.
		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_hecmailing');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('g.grp_nm_groupe', 'asc');
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id    A prefix for the store id.
	 *
	 * @return  string  A store id.
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.search');

		return parent::getStoreId($id);
	}

	/**
	 * Gets the list of groups and adds expensive joins to the result set.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 * @since   1.6
	 */
	public function getItems()
	{
		$db = $this->getDbo();
		// Get a storage key.
		$store = $this->getStoreId();

		// Try to load the data from internal storage.
		if (empty($this->cache[$store]))
		{
			$items = parent::getItems();

			// Bail out on an error or empty list.
			if (empty($items))
			{
				$this->cache[$store] = $items;

				return $items;
			}

			// Add the items to the internal cache.
			$this->cache[$store] = $items;
		}

		return $this->cache[$store];
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  JDatabaseQuery
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		// SELECT g.*, count(gd.grp_id_groupe) as grp_nb_item '
	 // ' FROM #__hecmailing_groups AS g Left Join #__hecmailing_groupdetail gd on g.grp_id_groupe=gd.grp_id_groupe '
	 //$where
	 //'GROUP BY grp_id_groupe'
		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'g.*'
			)
		);
		$query->from($db->quoteName('#__hecmailing_groups') . ' AS g');

		// Add the number of item for each groupe.
		$query->select('COUNT(DISTINCT gd.grp_id_groupe) AS grp_nb_item')
			->join('LEFT OUTER', $db->quoteName('#__hecmailing_groupdetail') . ' AS gd ON g.grp_id_groupe=gd.grp_id_groupe ')
			->group('g.grp_id_groupe');

		// Filter the comments over the search string if set.
		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('g.grp_id_groupe = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
				$query->where('g.grp_nm_groupe LIKE ' . $search);
			}
		}

		// Add the list ordering clause.
		$query->order($db->escape($this->getState('list.ordering', 'g.grp_nm_groupe')) . ' ' . $db->escape($this->getState('list.direction', 'ASC')));

		
		return $query;
	}
	
	
	/**
	 * Method to publish/unpublish rows.
	 *
	 * @param   int  1 for publish, 0 for unpublish.
	 * @param   array  An array of item ids.
	 * @return  boolean  Returns true on success, false on failure.
	 * @since   1.6
	 */
	public function publish($published,$cid)
	{
		$app = JFactory::getApplication();
		$db		=JFactory::getDBO();
		JArrayHelper::toInteger($cid);
		
		if (count( $cid )) {
			$cids = implode( ',', $cid );
			$query = 'UPDATE #__hecmailing_groups'
					. ' SET published='.$published
					. ' WHERE grp_id_groupe IN ( '. $cids .' )';
			$db->setQuery( $query );
			if (!$db->query()) {
				$this->error=$db->getErrorMsg(true);
				return false;
			}
		}
		else
		{
			$error = JText::_("COM_HECMAILING_GROUP_ERROR_NOGROUP");
			return false;
		}
		return true;
	}
	
}
