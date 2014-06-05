<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_login
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Login Controller
 *
 * @package     Joomla.Administrator
 * @subpackage  com_login
 * @since       1.5
 */
class LoginControllerApi extends JControllerLegacy {
    /**
     * @Override
     * @return JControllerLegacy|void
     */
    public function display() {

    }

    public function login() {
        //TODO: need verify request
        $app = JFactory::getApplication();
        $input = $app->input;
        $doc = JSONDocument::getInstance();

        $username = $input->getUsername('username');
        $password = $input->getString('password');
        if(!$username || !$password) {
            //Required username and password
            $doc->setStatus(412);
            $doc->setMessage('You must provide username and password for login');
            return;
        }

        $credentials = array(
            'username' => $username,
            'password' => $password,
            'secretkey' => $input->get('secretkey', '', 'post', 'string'),
        );
        $result = $app->login($credentials, array('action' => 'core.login.admin'));
        if ($result && !($result instanceof Exception)) {
            //Login successfully
            $doc->setStatus(200);
            $doc->setMessage('Login successfully');
        } else {
            //Login failure
            $doc->setStatus(401);
            $doc->setMessage('Wrong username/password, please try again!');
        }
    }
}