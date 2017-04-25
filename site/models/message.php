<?php
/**
 * @version   3.4.0
 * @package   HEC Mailing for Joomla
 * @copyright Copyright (C) 1999-2017 Hecsoft All rights reserved.
 * @author    Hervé CYR
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
// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modelitem');
jimport('joomla.event.dispatcher');

/**
 * Kwpmessage model.
 */
class HecMailingModelMessage extends JModelItem
{

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since    1.6
	 */
	protected function populateState()
	{
		$app = JFactory::getApplication('com_hecmailing');

		// Load state from the request userState on edit or from the passed variable on default
		if (JFactory::getApplication()->input->get('layout') == 'edit')
		{
			$id = JFactory::getApplication()->getUserState('com_hecmailing.edit.message.id');
		}
		else
		{
			$id = JFactory::getApplication()->input->get('id');
			JFactory::getApplication()->setUserState('com_hecmailing.edit.message.id', $id);
		}
		$this->setState('message.id', $id);

		// Load the parameters.
		$params       = $app->getParams();
		$params_array = $params->toArray();
		if (isset($params_array['item_id']))
		{
			$this->setState('message.id', $params_array['item_id']);
		}
		$this->setState('params', $params);
	}

	/**
	 * Method to get an ojbect.
	 *
	 * @param    integer    The id of the object to get.
	 *
	 * @return    mixed    Object on success, false on failure.
	 */
	public function &getData($id = null)
	{
		if ($this->_item === null)
		{
			$db=JFactory::getDbo();
			$this->_item = false;

			if (empty($id))
			{
				$id = $this->getState('message.id');
			}

			// Get a level row instance.
			$table = $this->getTable();

			// Attempt to load the row.
			if ($table->load($id))
			{
				// Convert the JTable to a clean JObject.
				$properties  = $table->getProperties(1);
				$this->_item = ArrayHelper::toObject($properties, 'JObject');
				
				$query = $db->getQuery(true);
				$query->select('*')->from("#__users")->where("id=".$this->_item->user_id);
				$db->setQuery($query);
				$this->_item->user=$db->loadObject();
				
				$query = $db->getQuery(true);
				$query->select('grp_nm_groupe as group_name, grp_cm_groupe as group_comment')->from("#__hecmailing_groups")->where("grp_id_groupe=".$this->_item->group_id);
				$db->setQuery($query);
				$this->_item->group=$db->loadObject();
				
				$query = $db->getQuery(true);
				$query->select('*')->from("#__hecmailing_message_attachment")->where("message_id=".$this->_item->id);
				$db->setQuery($query);
				$attachments=$db->loadObjectList();
				
				$query = $db->getQuery(true);
				$query->select('*')->from("#__hecmailing_message_recipient")->where("message_id=".$this->_item->id);
				$db->setQuery($query);
				$recipients=$db->loadObjectList();
				
				$this->_item->attachments = $attachments;
				$this->_item->recipients = $recipients;
				$nbtosend=0;
				$nbsent=0;
				$nbread=0;
				$nberr=0;
				$nbexcluded=0;
				foreach($recipients as $recip){
					switch($recip->status)
					{
						case 0:
							$nbtosend++;
							break;
						case 1:
							$nbsent++;
							break;
						case 2:
							$nbread++;
							break;
						case 8:
							$nbexcluded++;
							break;
						case 9:
						default:
							$nberr++;
							break;
					}
				}
				$this->_item->nbtosend=$nbtosend;
				$this->_item->nbsent=$nbsent;
				$this->_item->nbread=$nbread;
				$this->_item->nberr=$nberr;
				$this->_item->nbexcluded=$nbexcluded;
			}
		}

		
					
		return $this->_item;
	}

	public function getTable($type = 'Message', $prefix = 'HecMailingTable', $config = array())
	{
		$this->addTablePath(JPATH_ADMINISTRATOR . '/components/com_hecmailing/tables');

		return JTable::getInstance($type, $prefix, $config);
	}

	public function getItemIdByAlias($alias)
	{
		$table = $this->getTable();

		$table->load(array( 'alias' => $alias ));

		return $table->id;
	}


	public function getCategoryName($id)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query
			->select('title')
			->from('#__categories')
			->where('id = ' . $id);
		$db->setQuery($query);

		return $db->loadObject();
	}


	public function delete($id)
	{
		$table = $this->getTable();

		return $table->delete($id);
	}

	function answer($messageId, $recipientId, $question, $answer, $hashcode)
	{
		$db=$this->getDBO();
		try {
			$query = $db->getQuery(true);
			$query->select("`token`,`answer_code`")
				->from("`#__hecmailing_answers`")
				->where( "`message_id`=".intval($messageId)." AND `recipient_id`=".intval($recipientId)."  
						AND `question_code`=".$db->quote($question));
			$db->setQuery($query);
			$info = $db->loadObject();
			
			if ($info)
			{
				$hashcheck=crypt($info->token,$answer);
				if ($hashcheck==$hashcode)
				{
					
					$query = $db->getQuery(true);
					$query->update("`#__hecmailing_answers`")
						->set("`answer_code`=".$db->quote($answer))
						->set("`answer_timestamp`=now()")
						->where( "`message_id`=".intval($messageId)." AND `recipient_id`=".intval($recipientId)."
						AND `question_code`=".$db->quote($question));
					if  ($db->execute())
						return array(true,"");
					else return array(false,JText::_("COM_HECMAILING_ANSWER_ERROR_UPDATE"));
				}
				else
				{
					return array(false,JText::_("COM_HECMAILING_ANSWER_ERROR_BADTOKEN"));
				}
			}
			else
			{
				return array(false,JText::_("COM_HECMAILING_ANSWER_ERROR_NOTEXISTS"));
			}
		}
		catch (Exception $e)
		{
			return array(false,JText::_("COM_HECMAILING_ANSWER_ERROR_QUERY"));
		}
	}

}
