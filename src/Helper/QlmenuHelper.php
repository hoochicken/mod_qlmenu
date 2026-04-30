<?php
/**
 * @package     Hoochicken\Module\Qlmenu
 *
 * @copyright   Copyright (C) 2026 Mareike Riegel. All rights reserved.
 * @license     GNU General Public License version 2 or later;
 */

namespace Hoochicken\Module\Qlmenu\Site\Helper;

defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Cache\CacheControllerFactoryInterface;
use Joomla\CMS\Cache\Controller\OutputController;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\Registry\Registry;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Database\DatabaseInterface;

class QlmenuHelper
{
    /**
     * Get a list of the menu items.
     *
     * @param Registry                  &$params The module options.
     * @param CMSApplicationInterface $app The application
     *
     * @return  array
     *
     * @since   5.4.0
     */
    public function getItems(Registry &$params, CMSApplicationInterface $app, array $path = [], int $default_id = 0, int $active_id = 0): array
    {
        $menu = $app->getMenu();

        // Get active menu item
        $base = $this->getBaseItem($params, $app);
        $levels = $app->getIdentity()->getAuthorisedViewLevels();
        asort($levels);

        // Compose cache key
        $cacheKey = 'menu_items' . $params . implode(',', $levels) . '.' . $base->id;

        /** @var OutputController $cache */
        $cache = Factory::getContainer()->get(CacheControllerFactoryInterface::class)
            ->createCacheController('output', ['defaultgroup' => 'mod_menu']);

        if ($cache->contains($cacheKey)) {
            return $cache->get($cacheKey);
        }
        $path = $base->tree;
        $start = (int)$params->get('startLevel', 1);
        $end = (int)$params->get('endLevel', 0);
        $showAll = $params->get('showAllChildren', 1);
        $items = $menu->getItems('menutype', $params->get('menutype'));
        $hidden_parents = [];
        $lastitem = 0;

        if (!$items) {
            $cache->store([], $cacheKey);
            return [];
        }

        $inputVars = $app->getInput()->getArray();

        foreach ($items as $i => $item) {
            $item->parent = false;
            $itemParams = $item->getParams();

            if (isset($items[$lastitem]) && $items[$lastitem]->id == $item->parent_id && $itemParams->get('menu_show', 1) == 1) {
                $items[$lastitem]->parent = true;
            }

            if (
                ($start && $start > $item->level)
                || ($end && $item->level > $end)
                || (!$showAll && $item->level > 1 && !\in_array($item->parent_id, $path))
                || ($start > 1 && !\in_array($item->tree[$start - 2], $path))
            ) {
                unset($items[$i]);
                continue;
            }

            // Exclude item with menu item option set to exclude from menu modules
            if (($itemParams->get('menu_show', 1) == 0) || \in_array($item->parent_id, $hidden_parents)) {
                $hidden_parents[] = $item->id;
                unset($items[$i]);
                continue;
            }

            $item->current = true;

            foreach ($item->query as $key => $value) {
                if (!isset($inputVars[$key]) || $inputVars[$key] !== $value) {
                    $item->current = false;
                    break;
                }
            }

            $item->deeper = false;
            $item->shallower = false;
            $item->level_diff = 0;

            if (isset($items[$lastitem])) {
                $items[$lastitem]->deeper = ($item->level > $items[$lastitem]->level);
                $items[$lastitem]->shallower = ($item->level < $items[$lastitem]->level);
                $items[$lastitem]->level_diff = ($items[$lastitem]->level - $item->level);
            }

            $lastitem = $i;
            $item->active = false;
            $item->flink = $item->link;

            // Reverted back for CMS version 2.5.6
            switch ($item->type) {
                case 'separator':
                    break;

                case 'heading':
                    // No further action needed.
                    break;

                case 'url':
                    if ((str_starts_with($item->link, 'index.php?')) && (!str_contains($item->link, 'Itemid='))) {
                        // If this is an internal Joomla link, ensure the Itemid is set.
                        $item->flink = $item->link . '&Itemid=' . $item->id;
                    }
                    break;

                case 'alias':
                    $item->flink = 'index.php?Itemid=' . $itemParams->get('aliasoptions');

                    // Get the language of the target menu item when site is multilingual
                    if (Multilanguage::isEnabled()) {
                        $newItem = $app->getMenu()->getItem((int)$itemParams->get('aliasoptions'));

                        // Use language code if not set to ALL
                        if ($newItem != null && $newItem->language && $newItem->language !== '*') {
                            $item->flink .= '&lang=' . $newItem->language;
                        }
                    }
                    break;

                default:
                    $item->flink = 'index.php?Itemid=' . $item->id;
                    break;
            }

            $item->flink = (str_contains($item->flink, 'index.php?')) && strcasecmp(substr($item->flink, 0, 4), 'http')
                ? Route::_($item->flink, true, $itemParams->get('secure'))
                : Route::_($item->flink);

            // We prevent the double encoding because for some reason the $item is shared for menu modules and we get double encoding
            // when the cause of that is found the argument should be removed
            $item->title = htmlspecialchars($item->title, ENT_COMPAT, 'UTF-8', false);
            $item->menu_icon = htmlspecialchars($itemParams->get('menu_icon_css', ''), ENT_COMPAT, 'UTF-8', false);
            $item->anchor_css = htmlspecialchars($itemParams->get('menu-anchor_css', ''), ENT_COMPAT, 'UTF-8', false);
            $item->anchor_title = htmlspecialchars($itemParams->get('menu-anchor_title', ''), ENT_COMPAT, 'UTF-8', false);
            $item->anchor_rel = htmlspecialchars($itemParams->get('menu-anchor_rel', ''), ENT_COMPAT, 'UTF-8', false);
            $item->menu_image = htmlspecialchars($itemParams->get('menu_image', ''), ENT_COMPAT, 'UTF-8', false);
            $item->menu_image_css = htmlspecialchars($itemParams->get('menu_image_css', ''), ENT_COMPAT, 'UTF-8', false);
        }

        if (isset($items[$lastitem])) {
            $items[$lastitem]->deeper = (($start ?: 1) > $items[$lastitem]->level);
            $items[$lastitem]->shallower = (($start ?: 1) < $items[$lastitem]->level);
            $items[$lastitem]->level_diff = ($items[$lastitem]->level - ($start ?: 1));
        }

        $items = $this->addClassToMenuItems($items, $path, $default_id, $active_id);
        $cache->store($items, $cacheKey);
        return $items;
    }

    private function addClassToMenuItems(array $items, array $path = [], int $default_id = 0, int $active_id = 0): array
    {
        foreach ($items as $i => &$item) {
            $class = static::getClass($item, $path, $default_id, $active_id);
            // we write class into ach items params, because there is no direct attribute class
            $item->getParams()->set('class', $class);
        }
        return $items;
    }

    private static function getClass($item, array $path, int $default_id, int $active_id): string
    {
        $itemParams = $item->getParams();
        $class = ['nav-item', sprintf('item-%s', $item->id)];
        if ((int)$item->id === $default_id) {
            $class[] = 'default';
        }
        if ((int)$item->id === $active_id || ('alias' === $item->type && (int)$itemParams->get('aliasoptions') === $active_id)) {
            $class[] = 'current';
        }

        if ('separator' === $item->type) {
            $class[] = 'divider';
        }

        if ($item->deeper) {
            $class[] = 'deeper';
        }

        if ($item->parent) {
            $class[] = 'parent';
        }

        if (in_array($item->id, $path)) {
            $class[] = 'active';
        } elseif ('alias' === $item->type) {
            $aliasToId = $itemParams->get('aliasoptions');
            if (count($path) > 0 && $aliasToId == $path[count($path) - 1]) {
                $class[] = 'active';
            } elseif (in_array($aliasToId, $path)) {
                $class[] = 'alias-parent-active';
            }
        }
        return implode(' ', $class);
    }

    /**
     * Get base menu item.
     *
     * @param Registry                   &$params The module options.
     * @param CMSApplicationInterface $app The application
     *
     * @return  object
     *
     * @since    5.4.0
     */
    public function getBaseItem(Registry &$params, CMSApplicationInterface $app): object
    {
        // Get base menu item from parameters
        if ($params->get('base')) {
            $base = $app->getMenu()->getItem($params->get('base'));
        } else {
            $base = false;
        }

        // Use active menu item if no base found
        if (!$base) {
            $base = $this->getActiveItem($app);
        }

        return $base;
    }

    /**
     * Get active menu item.
     *
     * @param CMSApplicationInterface $app The application
     *
     * @return  object
     *
     * @since    5.4.0
     */
    public function getActiveItem(CMSApplicationInterface $app): object
    {
        $menu = $app->getMenu();

        return $menu->getActive() ?: $this->getDefaultItem($app);
    }

    /**
     * Get default menu item (home page) for current language.
     *
     * @param CMSApplicationInterface $app The application
     *
     * @return  object
     *
     * @since   5.4.0
     */
    public function getDefaultItem(CMSApplicationInterface $app): object
    {
        $menu = $app->getMenu();

        // Look for the home menu
        if (Multilanguage::isEnabled()) {
            return $menu->getDefault($app->getLanguage()->getTag());
        }

        return $menu->getDefault();
    }


    /**
     * Get a list of the menu items.
     *
     * @param Registry  &$params The module options.
     *
     * @return  array
     *
     * @since   1.5
     *
     * @deprecated 5.4.0 will be removed in 7.0
     *             Use the non-static method getItems
     *             Example: Factory::getApplication()->bootModule('mod_menu', 'site')
     *                          ->getHelper('MenuHelper')
     *                          ->getItems($params, $app)
     */
    public static function getList(&$params)
    {
        return (new self())->getItems($params, Factory::getApplication());
    }

    /**
     * Get base menu item.
     *
     * @param Registry  &$params The module options.
     *
     * @return  object
     *
     * @since    3.0.2
     *
     * @deprecated 5.4.0 will be removed in 7.0
     *             Use the non-static method getBaseItem
     *             Example: Factory::getApplication()->bootModule('mod_menu', 'site')
     *                          ->getHelper('MenuHelper')
     *                          ->getBaseItem($params, $app)
     */
    public static function getBase(&$params)
    {
        return (new self())->getBaseItem($params, Factory::getApplication());
    }

    /**
     * Get active menu item.
     *
     * @param Registry  &$params The module options.
     *
     * @return  object
     *
     * @since    3.0.2
     *
     * @deprecated 5.4.0 will be removed in 7.0
     *             Use the non-static method getActiveItem
     *             Example: Factory::getApplication()->bootModule('mod_menu', 'site')
     *                          ->getHelper('MenuHelper')
     *                          ->getActiveItem($app)
     */
    public static function getActive(&$params)
    {
        return (new self())->getActiveItem(Factory::getApplication());
    }

    /**
     * Get default menu item (home page) for current language.
     *
     * @return  object
     *
     * @deprecated 5.4.0 will be removed in 7.0
     *             Use the non-static method getDefaultItem
     *             Example: Factory::getApplication()->bootModule('mod_menu', 'site')
     *                          ->getHelper('MenuHelper')
     *                          ->getDefaultItem($app)
     */
    public static function getDefault()
    {
        return (new self())->getDefaultItem(Factory::getApplication());
    }
}