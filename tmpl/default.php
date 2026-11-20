<?php

/**
 * @package     Hoochicken\Module\Qlmenu
 *
 * @copyright   Copyright (C) 2026 Mareike Riegel. All rights reserved.
 * @license     GNU General Public License version 2 or later;
 */

\defined('_JEXEC') or die;

use Hoochicken\Module\Qlmenu\Site\Helper\DisplayCustom;
use Hoochicken\Module\Qlmenu\Site\Helper\QlmenuHelper;
use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Menu\MenuItem;
use Joomla\Registry\Registry;

/**
 * Variables extracted by AbstractModuleDispatcher from Dispatcher::getLayoutData():
 *
 * @var stdClass        $module
 * @var SiteApplication $app
 * @var Registry        $params
 * @var MenuItem[]      $list
 * @var string          $class_sfx
 * @var string          $menuId
 * @var DisplayCustom   $displayModel
 */

$wa = $app->getDocument()->getWebAssetManager();
$wa->getRegistry()->addExtensionRegistryFile('mod_qlmenu');
$wa->useScript('mod_qlmenu.script');
$wa->useStyle('mod_qlmenu.style');

// Empty when "Use module styles" is set to No.
if ($displayModel->getInlineCss() !== '') {
    $wa->addInlineStyle($displayModel->getInlineCss());
}

$navLabel = htmlspecialchars($module->title, ENT_QUOTES, 'UTF-8');
?>
<nav class="mod-qlmenu" aria-label="<?php echo $navLabel; ?>">
    <button type="button" class="mod-qlmenu-mobile-toggle" aria-expanded="false" aria-controls="<?php echo $menuId; ?>">
        <span class="icon-menu" aria-hidden="true"></span> <?php echo Text::_('MOD_QLMENU_MOBILE_TOGGLE'); ?>
    </button>
    <ul id="<?php echo $menuId; ?>" class="nav mod-qlmenu-ul mod-list <?php echo $class_sfx; ?>">
        <?php foreach ($list as $i => $item) {
            $itemParams = $item->getParams();
            $class      = $itemParams->get(QlmenuHelper::ITEM_CLASS_PARAM, '');

            echo '<li class="' . $class . '">';

            // Headings and separators with children are wrapped completely in the toggle button.
            if ($item->deeper && \in_array($item->type, ['separator', 'heading'])) {
                echo '<button type="button" class="mod-qlmenu__toggle-sub" aria-expanded="false">';
            }

            switch ($item->type) :
                case 'separator':
                case 'component':
                case 'heading':
                case 'url':
                    require ModuleHelper::getLayoutPath('mod_qlmenu', 'default_' . $item->type);
                    break;

                default:
                    require ModuleHelper::getLayoutPath('mod_qlmenu', 'default_url');
                    break;
            endswitch;

            if ($item->deeper) {
                // The next item is deeper.
                switch ($item->type) {
                    case 'heading':
                    case 'separator':
                        echo '<span class="icon-chevron-down" aria-hidden="true"></span></button>';
                        break;

                    default:
                        echo '<button type="button" class="mod-qlmenu__toggle-sub" aria-expanded="false">'
                            . '<span class="icon-chevron-down" aria-hidden="true"></span>'
                            . '<span class="visually-hidden">' . Text::sprintf('MOD_QLMENU_TOGGLE_SUBMENU_LABEL', $item->title) . '</span>'
                            . '</button>';
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
        } ?>
    </ul>
</nav>
