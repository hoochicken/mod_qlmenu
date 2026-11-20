<?php

/**
 * @package     Hoochicken\Module\Qlmenu
 *
 * @copyright   Copyright (C) 2026 Mareike Riegel. All rights reserved.
 * @license     GNU General Public License version 2 or later;
 */

namespace Hoochicken\Module\Qlmenu\Site\Helper;

\defined('_JEXEC') or die;

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Cache\CacheControllerFactoryInterface;
use Joomla\CMS\Cache\Controller\OutputController;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Menu\MenuItem;
use Joomla\CMS\Router\Route;
use Joomla\Registry\Registry;

/**
 * Helper for mod_qlmenu, obtained through the HelperFactory:
 * $this->getHelperFactory()->getHelper('QlmenuHelper')
 */
class QlmenuHelper
{
    /**
     * Name of the item param that carries the computed <li> CSS classes.
     */
    public const ITEM_CLASS_PARAM = 'qlmenu_class';

    /**
     * Get a list of the menu items.
     *
     * @param   Registry                 $params      The module options.
     * @param   CMSApplicationInterface  $app         The application
     * @param   array                    $path        Tree of the base item
     * @param   int                      $defaultId   Id of the default (home) menu item
     * @param   int                      $activeId    Id of the active menu item
     *
     * @return  MenuItem[]
     */
    public function getItems(Registry $params, CMSApplicationInterface $app, array $path = [], int $defaultId = 0, int $activeId = 0): array
    {
        $items = $this->getVisibleItems($params, $app, $path);

        // Classes depend on the current page, so they are added after the (page independent) cache.
        return $this->addClassToMenuItems($items, $path, $defaultId, $activeId);
    }

    /**
     * Loads the filtered and prepared menu items; cached per menu, view levels and base item.
     *
     * @return  MenuItem[]
     */
    private function getVisibleItems(Registry $params, CMSApplicationInterface $app, array $path): array
    {
        $base   = $this->getBaseItem($params, $app);
        $levels = $app->getIdentity()->getAuthorisedViewLevels();
        asort($levels);

        $cacheKey = 'menu_items' . $params . implode(',', $levels) . '.' . $base->id;

        /** @var OutputController $cache */
        $cache = Factory::getContainer()->get(CacheControllerFactoryInterface::class)
            ->createCacheController('output', ['defaultgroup' => 'mod_qlmenu']);

        if ($cache->contains($cacheKey)) {
            return $cache->get($cacheKey);
        }

        $start          = (int) $params->get('startLevel', 1);
        $end            = (int) $params->get('endLevel', 0);
        $showAll        = (int) $params->get('showAllChildren', 1);
        $items          = $app->getMenu()->getItems('menutype', $params->get('menutype'));
        $hidden_parents = [];
        $lastitem       = 0;

        if (!$items) {
            $cache->store([], $cacheKey);

            return [];
        }

        $inputVars = $app->getInput()->getArray();

        foreach ($items as $i => $item) {
            $item->parent = false;
            $itemParams   = $item->getParams();

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

            $item->deeper     = false;
            $item->shallower  = false;
            $item->level_diff = 0;

            if (isset($items[$lastitem])) {
                $items[$lastitem]->deeper     = ($item->level > $items[$lastitem]->level);
                $items[$lastitem]->shallower  = ($item->level < $items[$lastitem]->level);
                $items[$lastitem]->level_diff = ($items[$lastitem]->level - $item->level);
            }

            $lastitem     = $i;
            $item->active = false;
            $item->flink  = $item->link;

            switch ($item->type) {
                case 'separator':
                case 'heading':
                    // No further action needed.
                    break;

                case 'url':
                    if (str_starts_with($item->link, 'index.php?') && !str_contains($item->link, 'Itemid=')) {
                        // If this is an internal Joomla link, ensure the Itemid is set.
                        $item->flink = $item->link . '&Itemid=' . $item->id;
                    }
                    break;

                case 'alias':
                    $item->flink = 'index.php?Itemid=' . $itemParams->get('aliasoptions');

                    // Get the language of the target menu item when site is multilingual
                    if (Multilanguage::isEnabled()) {
                        $newItem = $app->getMenu()->getItem((int) $itemParams->get('aliasoptions'));

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

            $item->flink = str_contains($item->flink, 'index.php?') && strcasecmp(substr($item->flink, 0, 4), 'http')
                ? Route::_($item->flink, true, $itemParams->get('secure'))
                : Route::_($item->flink);

            // Prevent double encoding: $item objects are shared between menu modules.
            $item->title          = htmlspecialchars($item->title, ENT_COMPAT, 'UTF-8', false);
            $item->menu_icon      = htmlspecialchars($itemParams->get('menu_icon_css', ''), ENT_COMPAT, 'UTF-8', false);
            $item->anchor_css     = htmlspecialchars($itemParams->get('menu-anchor_css', ''), ENT_COMPAT, 'UTF-8', false);
            $item->anchor_title   = htmlspecialchars($itemParams->get('menu-anchor_title', ''), ENT_COMPAT, 'UTF-8', false);
            $item->anchor_rel     = htmlspecialchars($itemParams->get('menu-anchor_rel', ''), ENT_COMPAT, 'UTF-8', false);
            $item->menu_image     = htmlspecialchars($itemParams->get('menu_image', ''), ENT_COMPAT, 'UTF-8', false);
            $item->menu_image_css = htmlspecialchars($itemParams->get('menu_image_css', ''), ENT_COMPAT, 'UTF-8', false);
        }

        if (isset($items[$lastitem])) {
            $items[$lastitem]->deeper     = (($start ?: 1) > $items[$lastitem]->level);
            $items[$lastitem]->shallower  = (($start ?: 1) < $items[$lastitem]->level);
            $items[$lastitem]->level_diff = ($items[$lastitem]->level - ($start ?: 1));
        }

        $cache->store($items, $cacheKey);

        return $items;
    }

    /**
     * @param   MenuItem[]  $items
     *
     * @return  MenuItem[]
     */
    private function addClassToMenuItems(array $items, array $path, int $defaultId, int $activeId): array
    {
        foreach ($items as $item) {
            // MenuItem has no class attribute, so the computed classes are stored in its params.
            $item->getParams()->set(self::ITEM_CLASS_PARAM, $this->getClass($item, $path, $defaultId, $activeId));
        }

        return $items;
    }

    private function getClass(MenuItem $item, array $path, int $defaultId, int $activeId): string
    {
        $itemParams = $item->getParams();
        $class      = ['nav-item', 'item-' . $item->id];

        if ((int) $item->id === $defaultId) {
            $class[] = 'default';
        }

        if ((int) $item->id === $activeId || ($item->type === 'alias' && (int) $itemParams->get('aliasoptions') === $activeId)) {
            $class[] = 'current';
        }

        if ($item->type === 'separator') {
            $class[] = 'divider';
        }

        if ($item->deeper) {
            $class[] = 'deeper';
        }

        if ($item->parent) {
            $class[] = 'parent';
        }

        if (\in_array($item->id, $path)) {
            $class[] = 'active';
        } elseif ($item->type === 'alias') {
            $aliasToId = $itemParams->get('aliasoptions');

            if (\count($path) > 0 && $aliasToId == $path[\count($path) - 1]) {
                $class[] = 'active';
            } elseif (\in_array($aliasToId, $path)) {
                $class[] = 'alias-parent-active';
            }
        }

        return implode(' ', $class);
    }

    public function getBaseItem(Registry $params, CMSApplicationInterface $app): MenuItem
    {
        $base = $params->get('base') ? $app->getMenu()->getItem((int) $params->get('base')) : null;

        return $base ?: $this->getActiveItem($app);
    }

    public function getActiveItem(CMSApplicationInterface $app): MenuItem
    {
        return $app->getMenu()->getActive() ?: $this->getDefaultItem($app);
    }

    public function getDefaultItem(CMSApplicationInterface $app): MenuItem
    {
        $menu = $app->getMenu();

        return Multilanguage::isEnabled() ? $menu->getDefault($app->getLanguage()->getTag()) : $menu->getDefault();
    }

    /**
     * Builds the per-module CSS from the style params, scoped to the menu's HTML id
     * so several qlmenu modules on one page can be styled independently.
     */
    public function getInlineCss(ParametersCustom $p, string $menuId): string
    {
        $root = 'ul#' . $menuId;
        $l2   = $root . ' > li > ul.mod-menu__sub';
        $l3   = $l2 . ' > li > ul.mod-menu__sub';

        $rules = [
            $root . ' > li > *' => [
                'color'            => $p->getLevel1stColor(),
                'background-color' => $p->getLevel1stBackgroundColor(),
                'padding'          => $p->getLevel1stPadding(),
                'font-size'        => $p->getLevel1stFontSize(),
                'min-width'        => $p->getLevel1stWidth(),
            ],
            $root . ' > li:hover > *' => [
                'color'            => $p->getLevel1stColorHover(),
                'background-color' => $p->getLevel1stBackgroundColorHover(),
            ],
            $root . ' > li.active > *' => [
                'color'            => $p->getLevel1stColorActive(),
                'background-color' => $p->getLevel1stBackgroundColorActive(),
            ],
            $l2 => [
                'min-width'        => $p->getLevel2ndWidth(),
                'background-color' => $p->getLevel2ndBackgroundColor(),
            ],
            $l2 . ' > li > *' => [
                'color'            => $p->getLevel2ndColor(),
                'background-color' => $p->getLevel2ndBackgroundColor(),
                'padding'          => $p->getLevel2ndPadding(),
                'font-size'        => $p->getLevel2ndFontSize(),
                'border-bottom'    => $p->getLevel2ndBorderWidth() . ' solid ' . $p->getLevel2ndBorderColor(),
            ],
            $l2 . ' > li:hover > *' => [
                'color'            => $p->getLevel2ndColorHover(),
                'background-color' => $p->getLevel2ndBackgroundColorHover(),
            ],
            $l2 . ' > li.active > *' => [
                'color'            => $p->getLevel2ndColorActive(),
                'background-color' => $p->getLevel2ndBackgroundColorActive(),
            ],
            $l3 => [
                'min-width'        => $p->getLevel3rdWidth(),
                'background-color' => $p->getLevel3rdBackgroundColor(),
            ],
            $l3 . ' > li > *' => [
                'color'            => $p->getLevel3rdColor(),
                'background-color' => $p->getLevel3rdBackgroundColor(),
                'padding'          => $p->getLevel3rdPadding(),
                'font-size'        => $p->getLevel3rdFontSize(),
                'border'           => $p->getLevel3rdBorderWidth() . ' solid ' . $p->getLevel3rdBorderColor(),
            ],
            $l3 . ' > li:hover > *' => [
                'color'            => $p->getLevel3rdColorHover(),
                'background-color' => $p->getLevel3rdBackgroundColorHover(),
            ],
            $l3 . ' > li.active > *' => [
                'color'            => $p->getLevel3rdColorActive(),
                'background-color' => $p->getLevel3rdBackgroundColorActive(),
            ],
        ];

        $css = [];

        foreach ($rules as $selector => $declarations) {
            $lines = [];

            foreach ($declarations as $property => $value) {
                $value = $this->sanitizeCssValue($value);

                if ($value !== '') {
                    $lines[] = $property . ': ' . $value . ';';
                }
            }

            if ($lines) {
                $css[] = $selector . ' { ' . implode(' ', $lines) . ' }';
            }
        }

        return implode("\n", $css);
    }

    /**
     * Strips characters that could break out of a CSS declaration (free-text params like padding/width).
     */
    private function sanitizeCssValue(string $value): string
    {
        return trim(str_replace([';', '{', '}', '<', '>', '"', "'", '\\'], '', $value));
    }
}
