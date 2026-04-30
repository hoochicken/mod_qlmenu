<?php

/**
 * @package     Joomla.Site
 * @subpackage  mod_menu
 *
 * @copyright   (C) 2009 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Language\Text;

/**
 * @var stdClass $module
 * @var string $class_sfx
 * @var array $list
 * @var \Joomla\CMS\Service\Provider\Application $app
 * @var \Joomla\Registry\Registry $params
 * @var int $default_id
 * @var array $path
 * @var int $active_id
 */

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$document = Factory::getApplication()->getDocument();
$wa = $document->getWebAssetManager();
$wa->getRegistry()->addExtensionRegistryFile('mod_qlmenu');
$wa->useScript('mod_qlmenu.script');
$wa->useStyle('mod_qlmenu.style');
$wa->addInlineStyle('* { background:red; }');



$tagId = $params->get('tag_id', '') ?: 'mod-menu' . $module->id;
// $id = htmlspecialchars($tagId, ENT_QUOTES, 'UTF-8');
$id = sprintf('mod-qlmenu-%s', $module->id);
$startLevel = (int)$params->get('startLevel', 1);

// set class
foreach ($list as $i => &$item) {
    $itemParams = $item->getParams();
    $class = ['nav-item', sprintf('item-%s', $item->id)];
    if ($item->id == $default_id) {
        $class[] = 'default';
    }
    if ($item->id == $active_id || ($item->type === 'alias' && $itemParams->get('aliasoptions') == $active_id)) {
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

    if (in_array($item->id, $path)) {
        $class[] = 'active';
    } elseif ($item->type === 'alias') {
        $aliasToId = $itemParams->get('aliasoptions');

        if (count($path) > 0 && $aliasToId == $path[count($path) - 1]) {
            $class[] = 'active';
        } elseif (in_array($aliasToId, $path)) {
            $class[] = 'alias-parent-active';
        }
    }

    $item->getParams()->set('class', implode(' ', $class));
}

// The menu class is deprecated. Use mod-menu instead
?>
<button class="mod-qlmenu-mobile-toggle" aria-expanded="false">
    ☰ Menu
</button>


<ul id="<?php echo $id; ?>" class="mod-qlmenu-ul mod-list <?php echo $class_sfx; ?>">
    <?php foreach ($list as $i => &$item) {
        $itemParams = $item->getParams();
        $class = $item->getParams()->get('class', '');

        echo '<li class="' . $class . '">';

        // The next item is deeper - add toggle only here it is a heading or separator
        // if ($item->deeper && (int)$item->level === $startLevel && in_array($item->type, ['separator', 'heading'])) {
        if ($item->deeper && in_array($item->type, ['separator', 'heading'])) {
            // Add a toggle button.
            echo '<button class="mod-qlmenu__toggle-sub" aria-expanded="false">';
        }

        switch ($item->type) :
            case 'separator':
            case 'component':
            case 'heading':
            case 'url':
                require ModuleHelper::getLayoutPath('mod_menu', 'default_' . $item->type);
                break;

            default:
                require ModuleHelper::getLayoutPath('mod_menu', 'default_url');
                break;
        endswitch;

        // The next item is deeper.
        if ($item->deeper) {
            // Check type - add only on first level
            // @todo aria-label - set in menu item ???
            if (true || (int)$item->level === $startLevel) {
                switch ($item->type) {
                    case 'heading':
                    case 'separator':
                        echo '<span class="icon-chevron-down" aria-hidden="true">' .
                                '</span></button>';
                        break;

                    default:
                        echo '<button class="mod-qlmenu__toggle-sub" aria-expanded="false">' .
                                '<span class="icon-chevron-down" aria-hidden="true"></span>' .
                                '<span class="visually-hidden">' . Text::sprintf('MOD_QLMENU_TOGGLE_SUBMENU_LABEL', $item->title) . '</span>' .
                                '</button>';
                }
            }
            echo '<ul class="mod-menu__sub list-unstyled small">';
        } elseif ($item->shallower) {
            // The next item is shallower.
            echo '</li>';
            echo str_repeat('</ul></li>', $item->level_diff);
        } else {
            // The next item is on the same level.
            echo '</li>';
        }
    }
    ?></ul>
