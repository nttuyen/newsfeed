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
 * View to edit an article.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_contentapi
 * @since       1.6
 */
class ContentAPIViewNextcontent extends JViewLegacy {
	public function display($tpl = null) {
		$item = $this->get('Item');
		$doc = JFactory::getDocument();
		if($item) {
			$doc->setStatus(200);
			$doc->setMessage("OK");
			$doc->setData($item);
		} else {
			$doc->setStatus(404);
			$doc->setMessage('There is no content need to be crawled');
		}

		return;
	}
}