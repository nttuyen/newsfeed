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
 * View class for a list of banners.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_banners
 * @since       1.6
 */
class CrawlerViewSites extends JViewLegacy {

    protected  $state;
    protected  $sidebar;

	/**
	 * Method to display the view.
	 *
	 * @param   string  $tpl  A template file to load. [optional]
	 *
	 * @return  mixed  A string if successful, otherwise a JError object.
	 *
	 * @since   1.6
	 */
	public function display($tpl = null) {

        //TODO:
        $this->state = new JRegistry();

		CrawlerHelper::addSubmenu('sites');
		$this->addToolbar();
		$this->sidebar = JHtmlSidebar::render();

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function addToolbar() {
		require_once JPATH_COMPONENT . '/helpers/crawler.php';

		$canDo = JHelperContent::getActions('com_crawler', 'category', $this->state->get('filter.category_id'));
		$user = JFactory::getUser();

		// Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');

		JToolbarHelper::title(JText::_('COM_CRAWLER_MANAGER_SITES'), 'banners.png');

		if (count($user->getAuthorisedCategories('com_crawler', 'core.create')) > 0) {
			JToolbarHelper::addNew('site.add');
		}

		if (($canDo->get('core.edit'))) {
			JToolbarHelper::editList('site.edit');
		}

		if ($canDo->get('core.edit.state')) {
			if ($this->state->get('filter.state') != 2) {
				JToolbarHelper::publish('sites.publish', 'JTOOLBAR_PUBLISH', true);
				JToolbarHelper::unpublish('sites.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			}

			if ($this->state->get('filter.state') != -1) {
				if ($this->state->get('filter.state') != 2) {
					JToolbarHelper::archiveList('sites.archive');
				} elseif ($this->state->get('filter.state') == 2) {
					JToolbarHelper::unarchiveList('sites.publish');
				}
			}
		}

		if ($canDo->get('core.edit.state')) {
			JToolbarHelper::checkin('sites.checkin');
		}

		if ($this->state->get('filter.state') == -2 && $canDo->get('core.delete')) {
			JToolbarHelper::deleteList('', 'sites.delete', 'JTOOLBAR_EMPTY_TRASH');
		} elseif ($canDo->get('core.edit.state')) {
			JToolbarHelper::trash('sites.trash');
		}

		// Add a batch button
		if ($user->authorise('core.create', 'com_crawler') && $user->authorise('core.edit', 'com_crawler') && $user->authorise('core.edit.state', 'com_crawler')) {
			JHtml::_('bootstrap.modal', 'collapseModal');
			$title = JText::_('JTOOLBAR_BATCH');

			// Instantiate a new JLayoutFile instance and render the batch button
			$layout = new JLayoutFile('joomla.toolbar.batch');

			$dhtml = $layout->render(array('title' => $title));
			$bar->appendButton('Custom', $dhtml, 'batch');
		}

		if ($user->authorise('core.admin', 'com_crawler')) {
			JToolbarHelper::preferences('com_crawler');
		}

		JToolbarHelper::help('JHELP_COMPONENTS_CRAWLER_SITES');
	}

	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 *
	 * @since   3.0
	 */
	protected function getSortFields() {
		return array(
			'ordering' => JText::_('JGRID_HEADING_ORDERING'),
			'a.state' => JText::_('JSTATUS'),
			'a.name' => JText::_('COM_BANNERS_HEADING_NAME'),
			'a.sticky' => JText::_('COM_BANNERS_HEADING_STICKY'),
			'client_name' => JText::_('COM_BANNERS_HEADING_CLIENT'),
			'impmade' => JText::_('COM_BANNERS_HEADING_IMPRESSIONS'),
			'clicks' => JText::_('COM_BANNERS_HEADING_CLICKS'),
			'a.language' => JText::_('JGRID_HEADING_LANGUAGE'),
			'a.id' => JText::_('JGRID_HEADING_ID')
		);
	}
}
