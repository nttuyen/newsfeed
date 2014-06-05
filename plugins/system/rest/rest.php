<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  System.cache
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Joomla! Rest Plugin.
 *
 * @package     Joomla.Plugin
 * @subpackage  System.rest
 * @since       1.5
 */
class PlgSystemRest extends JPlugin {

    private $component = false;

	/**
	 * Constructor.
	 *
	 * @param   object  &$subject  The object to observe.
	 * @param   array   $config    An optional associative array of configuration settings.
	 *
	 * @since   1.5
	 */
	function __construct(& $subject, $config) {
		parent::__construct($subject, $config);
	}

	function onAfterInitialise() {
        JLoader::register('JSONDocument', JPATH_PLUGINS.'/system/rest/JSONDocument.php');
        $app = JFactory::getApplication();
        $this->component = $app->input->getCmd('option', '');
        $document = JFactory::getDocument();
        if($document instanceof JDocumentJSON) {
            $document = JSONDocument::getInstance();
            JFactory::$document = $document;

            //Register override handle error exception
            set_exception_handler(array('PlgSystemRest', 'errorPage'));
        }
	}

    function onAfterRoute() {
        $app = JFactory::getApplication();
        $doc = JFactory::getDocument();
        //In administrator
        if($app instanceof JApplicationAdministrator && $doc instanceof JSONDocument) {
            $option = JAdministratorHelper::findOption();
            if($option == 'com_login' && $this->component != 'com_login') {
                throw new Exception('You have not permission to access to this resource', 401);
            }
        }
    }

	function onAfterRender() {
	}

    public static function errorPage(Exception $error) {
        $file = $error->getFile();
        if($file == __FILE__) {
            $doc = JSONDocument::getInstance();
            $doc->setStatus($error->getCode());
            $doc->setMessage($error->getMessage());
            $doc->render();
            echo JFactory::getApplication()->toString();
        } else {
            JErrorPage::render($error);
        }
    }
}
