<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_banners
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Banners component helper.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_banners
 * @since       1.6
 */
class CrawlerHelper extends JHelperContent {
	/**
	 * Configure the Linkbar.
	 *
	 * @param   string  $vName  The name of the active view.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public static function addSubmenu($vName)
	{
		JHtmlSidebar::addEntry(
			JText::_('COM_CRAWLER_SUBMENU_SITES'),
			'index.php?option=com_crawler&view=sites',
			$vName == 'banners'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_CRAWLER_SUBMENU_CATEGORIES'),
			'index.php?option=com_categories&extension=com_crawler',
			$vName == 'categories'
		);
	}
}
