<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * HTML Article View class for the Content component
 *
 * @package     Joomla.Site
 * @subpackage  com_content
 * @since       1.5
 */
class ContentViewArticle extends JViewLegacy {
    protected $item;
    protected $params;
    protected $print;
    protected $state;
    protected $user;

    public function display($tpl = null) {
        $app		= JFactory::getApplication();
        $user		= JFactory::getUser();
        $dispatcher	= JEventDispatcher::getInstance();

        $this->item		= $this->get('Item');
        $this->print	= $app->input->getBool('print');
        $this->state	= $this->get('State');
        $this->user		= $user;

        // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
            JError::raiseWarning(500, implode("\n", $errors));
            return false;
        }

        // Create a shortcut for $item.
        $item = $this->item;
        $item->tagLayout = new JLayoutFile('joomla.content.tags');

        // Add router helpers.
        $item->slug			= $item->alias ? ($item->id.':'.$item->alias) : $item->id;
        $item->catslug		= $item->category_alias ? ($item->catid.':'.$item->category_alias) : $item->catid;
        $item->parent_slug	= $item->parent_alias ? ($item->parent_id . ':' . $item->parent_alias) : $item->parent_id;

        // No link for ROOT category
        if ($item->parent_alias == 'root')
        {
            $item->parent_slug = null;
        }

        // TODO: Change based on shownoauth
        $item->readmore_link = JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catslug));

        // Merge article params. If this is single-article view, menu params override article params
        // Otherwise, article params override menu item params
        $this->params = $this->state->get('params');
        $active = $app->getMenu()->getActive();
        $temp = clone ($this->params);

        // Check to see which parameters should take priority
        if ($active)
        {
            $currentLink = $active->link;

            // If the current view is the active item and an article view for this article, then the menu item params take priority
            if (strpos($currentLink, 'view=article') && (strpos($currentLink, '&id='.(string) $item->id)))
            {
                // Load layout from active query (in case it is an alternative menu item)
                if (isset($active->query['layout']))
                {
                    $this->setLayout($active->query['layout']);
                }
                // Check for alternative layout of article
                elseif ($layout = $item->params->get('article_layout'))
                {
                    $this->setLayout($layout);
                }

                // $item->params are the article params, $temp are the menu item params
                // Merge so that the menu item params take priority
                $item->params->merge($temp);
            }
            else
            {
                // Current view is not a single article, so the article params take priority here
                // Merge the menu item params with the article params so that the article params take priority
                $temp->merge($item->params);
                $item->params = $temp;

                // Check for alternative layouts (since we are not in a single-article menu item)
                // Single-article menu item layout takes priority over alt layout for an article
                if ($layout = $item->params->get('article_layout'))
                {
                    $this->setLayout($layout);
                }
            }
        }
        else
        {
            // Merge so that article params take priority
            $temp->merge($item->params);
            $item->params = $temp;

            // Check for alternative layouts (since we are not in a single-article menu item)
            // Single-article menu item layout takes priority over alt layout for an article
            if ($layout = $item->params->get('article_layout'))
            {
                $this->setLayout($layout);
            }
        }

        $offset = $this->state->get('list.offset');

        // Check the view access to the article (the model has already computed the values).
        if ($item->params->get('access-view') != true && (($item->params->get('show_noauth') != true &&  $user->get('guest') )))
        {
            JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
            return;
        }

        if ($item->params->get('show_intro', '1') == '1')
        {
            $item->text = $item->introtext.' '.$item->fulltext;
        }
        elseif ($item->fulltext)
        {
            $item->text = $item->fulltext;
        }
        else
        {
            $item->text = $item->introtext;
        }

        $item->tags = new JHelperTags;
        $item->tags->getItemTags('com_content.article', $this->item->id);

        // Process the content plugins.

        JPluginHelper::importPlugin('content');
        $dispatcher->trigger('onContentPrepare', array ('com_content.article', &$item, &$this->params, $offset));

        $item->event = new stdClass;
        $results = $dispatcher->trigger('onContentAfterTitle', array('com_content.article', &$item, &$this->params, $offset));
        $item->event->afterDisplayTitle = trim(implode("\n", $results));

        $results = $dispatcher->trigger('onContentBeforeDisplay', array('com_content.article', &$item, &$this->params, $offset));
        $item->event->beforeDisplayContent = trim(implode("\n", $results));

        $results = $dispatcher->trigger('onContentAfterDisplay', array('com_content.article', &$item, &$this->params, $offset));
        $item->event->afterDisplayContent = trim(implode("\n", $results));

        // Increment the hit counter of the article.
        if (!$this->params->get('intro_only') && $offset == 0)
        {
            $model = $this->getModel();
            $model->hit();
        }

        echo json_encode($item);
    }
}