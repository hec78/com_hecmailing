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
// No direct access
defined('_JEXEC') or die;

require_once JPATH_COMPONENT . '/controller.php';

/**
 * Message controller class.
 */
class HecMailingeControllerMessage extends HecMailingController {

    /**
     * Method to check out an item for editing and redirect to the edit form.
     *
     * @since	1.6
     */
    public function edit() {
        $app = JFactory::getApplication();

        // Get the previous edit id (if any) and the current edit id.
        $previousId = (int) $app->getUserState('com_hecmailing.edit.message.id');
        $editId = $app->input->getInt('id', null, 'array');

        // Set the user id for the user to edit in the session.
        $app->setUserState('com_hecmailing.edit.message.id', $editId);

        // Get the model.
        $model = $this->getModel('Message', 'HecMailingModel');

        // Check out the item
        if ($editId) {
            $model->checkout($editId);
        }

        // Check in the previous user.
        if ($previousId && $previousId !== $editId) {
            $model->checkin($previousId);
        }

        // Redirect to the edit screen.
        $this->setRedirect(JRoute::_('index.php?option=com_hecmailing&view=message&layout=edit', false));
    }

    /**
     * Method to save a user's profile data.
     *
     * @return	void
     * @since	1.6
     */
    public function publish() {
        // Initialise variables.
        $app = JFactory::getApplication();

        //Checking if the user can remove object
        $user = JFactory::getUser();
        if ($user->authorise('core.edit', 'com_hecmailing') || $user->authorise('core.edit.state', 'com_hecmailing')) {
            $model = $this->getModel('Message', 'HecMailingModel');

            // Get the user data.
            $id = $app->input->getInt('id');
            $state = $app->input->getInt('state');

            // Attempt to save the data.
            $return = $model->publish($id, $state);

            // Check for errors.
            if ($return === false) {
                $this->setMessage(JText::sprintf('Save failed: %s', $model->getError()), 'warning');
            }

            // Clear the profile id from the session.
            $app->setUserState('com_hecmailing.edit.message.id', null);

            // Flush the data from the session.
            $app->setUserState('com_hecmailing.edit.message.data', null);

            // Redirect to the list screen.
            $this->setMessage(JText::_('COM_HECMAILING_ITEM_SAVED_SUCCESSFULLY'));
            $menu = & JSite::getMenu();
            $item = $menu->getActive();
            if (!$item) {
                // If there isn't any menu item active, redirect to list view
                $this->setRedirect(JRoute::_('index.php?option=com_hecmailing&view=messages', false));
            } else {
                $this->setRedirect(JRoute::_($item->link . $menuitemid, false));
            }
        } else {
            throw new Exception(500);
        }
    }

    public function remove() {

        // Initialise variables.
        $app = JFactory::getApplication();

        //Checking if the user can remove object
        $user = JFactory::getUser();
        if ($user->authorise('core.delete', 'com_hecmailing')) {
            $model = $this->getModel('Message', 'HecMailingModel');

            // Get the user data.
            $id = $app->input->getInt('id', 0);

            // Attempt to save the data.
            $return = $model->delete($id);


            // Check for errors.
            if ($return === false) {
                $this->setMessage(JText::sprintf('Delete failed', $model->getError()), 'warning');
            } else {
                // Check in the profile.
                if ($return) {
                    $model->checkin($return);
                }

                // Clear the profile id from the session.
                $app->setUserState('com_hecmailing.edit.message.id', null);

                // Flush the data from the session.
                $app->setUserState('com_hecmailing.edit.message.data', null);

                $this->setMessage(JText::_('COM_HECMAILING_ITEM_DELETED_SUCCESSFULLY'));
            }

            // Redirect to the list screen.
            $menu = & JSite::getMenu();
            $item = $menu->getActive();
            $this->setRedirect(JRoute::_($item->link, false));
        } else {
            throw new Exception(500);
        }
    }
    
    public function answer() {
    
    	// Initialise variables.
    	$app = JFactory::getApplication();
    	// Get the user data.
    	$message_id = $app->input->getInt('message_id', 0);
    	$recipient_id = $app->input->getInt('recipient_id', 0);
    	$question = $app->input->getString('question', '');
    	$answer = $app->input->getString('answer', '');
    	$hashcode = $app->input->getString('hascode', '');
    	
    }

}
