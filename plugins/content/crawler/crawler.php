<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Content.Contact
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Contact Plugin
 *
 * @package     Joomla.Plugin
 * @subpackage  Content.Contact
 * @since       3.2
 */
class PlgContentCrawler extends JPlugin {
    /**
     * Plugin that check
     *
     * @param   string   $context  The context of the content being passed to the plugin.
     * @param   mixed    $table Article object
     * @param   mixed    $isNew.
     *
     * @return  boolean	True on success.
     */
    public function onContentBeforeSave($context, $table, $isNew) {
        $allowed_contexts = array('com_content.article');

        if (!in_array($context, $allowed_contexts) || !$isNew) {
            return true;
        }
        if(empty($table->xreference)) {
            return true;
        }

        $originLink = $table->xreference;
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id')
            ->from('#__content as c')
            ->where('c.xreference = '.$db->quote($originLink));
        $db->setQuery($query);
        $result = $db->loadObject();
        if(!empty($result) && !empty($result->id)) {
            //TODO: how to set error message
            return false;
        }

        return true;
    }
}
