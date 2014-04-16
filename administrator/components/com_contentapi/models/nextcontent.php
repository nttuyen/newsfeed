<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

require_once JPATH_ADMINISTRATOR . '/components/com_content/models/article.php';
/**
 * Item Model for an Article.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_content
 * @since       1.6
 */
class ContentAPIModelNextcontent extends ContentModelArticle {
    /**
     * Follow for getting content:
     * - Content must be published
     * - Publish Date and crawled Date must before current date
     * - Order by hits then crawled date then publish date
     * @param null $pk
     * @return mixed|null
     */
    public function getItem($pk = null) {

        //Follow for getting content:
        //
        //This method is not optimized, it execute too many query
        //But now, i do not understand well about content of joomla
        //So, i need depend on Article model for loading content
        //And now, it is acceptable
        $originLink = '';
        $cid = 0;
		if(empty($pk)) {
            $now = date('Y-m-d H:i:s');
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('c.id as id')
                ->select('cr.origin_link as origin_link')
                ->select('cr.id as cid')
                ->from('#__content AS c')
                ->leftJoin('#__content_crawled AS cr ON c.id = cr.id')
                ->where('c.state = 1')
                ->where("(c.fulltext is NULL OR c.fulltext = '')")
                ->where("c.publish_up <= '".$now."'")
                ->where("(cr.crawled_time is NULL OR cr.crawled_time <= '$now')")
                ->where("(cr.next_crawled_time is NULL OR cr.next_crawled_time <= '$now')")
                ->order('c.hits DESC, cr.next_crawled_time ASC, c.publish_up ASC');
            //$sql = str_replace('#__', 'tbl_', $query->__toString());
            $db->setQuery($query, 0, 1);
            $result = $db->loadObject();
            if($result) {
                $cid = $result->cid;
                $pk = $result->id;
                if($result->origin_link) {
                    $originLink = $result->origin_link;
                }
            }
		}
		if(empty($pk)) {
			return NULL;
		}

		$item = parent::getItem($pk);
        if($item) {
            $db = JFactory::getDbo();
            $item->originLink = $originLink;

            $now = date('Y-m-d H:i:s');
            $nextHour = date('Y-m-d H:i:s', strtotime('+1 hour'));

            $contentCrawled = new stdClass();
            $contentCrawled->id = $item->id;
            $contentCrawled->crawled_time = $now;
            $contentCrawled->next_crawled_time = $nextHour;

            if(empty($cid)) {
                $contentCrawled->origin_link = $item->metadata['xreference'];
                $db->insertObject('#__content_crawled', $contentCrawled);
            } else {
                $db->updateObject('#__content_crawled', $contentCrawled, 'id');
            }
        }

        return $item;
	}
}