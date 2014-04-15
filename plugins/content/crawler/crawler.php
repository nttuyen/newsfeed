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
    private $allowed_contexts = array('com_content.article');

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
        if (!in_array($context, $this->allowed_contexts) || !$isNew) {
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

    /**
     * @param $context
     * @param $article
     * @param $isNew
     */
    public function onContentAfterSave($context, $article, $isNew) {
        if (!in_array($context, $this->allowed_contexts) || !$isNew) {
            return;
        }
        $app = JFactory::getApplication();
        $input = $app->input;
        $originLink = $input->getString('originLink', '');
        if($originLink) {
            $db = JFactory::getDbo();
            $crawled = new stdClass();
            $crawled->id = $article->id;
            $crawled->origin_link = $originLink;
            $crawled->crawled_time = $article->publish_up;
            $db->insertObject('#__content_crawled', $crawled);
        }
    }
}
