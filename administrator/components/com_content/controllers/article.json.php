<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * @package     Joomla.Administrator
 * @subpackage  com_content
 * @since       1.6
 */
class ContentControllerArticle extends JControllerForm {
    /**
     * Class constructor.
     *
     * @param   array  $config  A named array of configuration variables.
     *
     * @since   1.6
     */
    public function __construct($config = array()) {
        parent::__construct($config);
    }

    /**
     * Method override to check if you can add a new record.
     *
     * @param   array  $data  An array of input data.
     *
     * @return  boolean
     *
     * @since   1.6
     */
    protected function allowAdd($data = array()) {
        $user = JFactory::getUser();
        $categoryId = JArrayHelper::getValue($data, 'catid', $this->input->getInt('filter_category_id'), 'int');
        $allow = null;

        if ($categoryId)
        {
            // If the category has been passed in the data or URL check it.
            $allow = $user->authorise('core.create', 'com_content.category.' . $categoryId);
        }

        if ($allow === null)
        {
            // In the absense of better information, revert to the component permissions.
            return parent::allowAdd();
        }
        else
        {
            return $allow;
        }
    }

    /**
     * Method override to check if you can edit an existing record.
     *
     * @param   array   $data  An array of input data.
     * @param   string  $key   The name of the key for the primary key.
     *
     * @return  boolean
     *
     * @since   1.6
     */
    protected function allowEdit($data = array(), $key = 'id') {
        $recordId = (int) isset($data[$key]) ? $data[$key] : 0;
        $user = JFactory::getUser();
        $userId = $user->get('id');

        // Check general edit permission first.
        if ($user->authorise('core.edit', 'com_content.article.' . $recordId))
        {
            return true;
        }

        // Fallback on edit.own.
        // First test if the permission is available.
        if ($user->authorise('core.edit.own', 'com_content.article.' . $recordId))
        {
            // Now test the owner is the user.
            $ownerId = (int) isset($data['created_by']) ? $data['created_by'] : 0;
            if (empty($ownerId) && $recordId)
            {
                // Need to do a lookup from the model.
                $record = $this->getModel()->getItem($recordId);

                if (empty($record))
                {
                    return false;
                }

                $ownerId = $record->created_by;
            }

            // If the owner matches 'me' then do the test.
            if ($ownerId == $userId) {
                return true;
            }
        }

        // Since there is no asset tracking, revert to the component permissions.
        return parent::allowEdit($data, $key);
    }

    public function save($key = null, $urlVar = null) {
        //. Pass checkToken
        $app = JFactory::getApplication();
        $input = $app->input;
        $token = JSession::getFormToken(false);
        if($token) {
            $input->post->set($token, 1);
        }

        $doc = JSONDocument::getInstance();
        $result = parent::save($key, $urlVar);
        $doc->setMessage($this->messageType.': '.$this->message);

        if(!$result) {
            $doc->setStatus(406);
            $messages = $app->getMessageQueue();
            $i = 0;
            foreach($messages as $message) {
                $i++;
                $msg = $message['type'].': '.$message['message'];
                $doc->addHeader('message['.$i.']', $msg);
            }

            $errors = $this->getErrors();
            for($i = 0; $i < count($errors); $i++) {
                $doc->addHeader('error['.$i.']', $errors[$i]);
            }


        } else {
            $doc->setStatus(201);
            $doc->setData(array('result' => $result));
            //TODO: return link to access to this article
        }
        $this->setRedirect(false);
    }


}