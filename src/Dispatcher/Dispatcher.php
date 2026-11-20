<?php

/**
 * @package     Hoochicken\Module\Qlmenu
 *
 * @copyright   Copyright (C) 2026 Mareike Riegel. All rights reserved.
 * @license     GNU General Public License version 2 or later;
 */

namespace Hoochicken\Module\Qlmenu\Site\Dispatcher;

\defined('_JEXEC') or die;

use Hoochicken\Module\Qlmenu\Site\Helper\DisplayCustom;
use Hoochicken\Module\Qlmenu\Site\Helper\ParametersCustom;
use Hoochicken\Module\Qlmenu\Site\Helper\QlmenuHelper;
use Joomla\CMS\Dispatcher\AbstractModuleDispatcher;
use Joomla\CMS\Helper\HelperFactoryAwareInterface;
use Joomla\CMS\Helper\HelperFactoryAwareTrait;

/**
 * Dispatcher for mod_qlmenu.
 *
 * AbstractModuleDispatcher::dispatch() loads the language, calls getLayoutData(),
 * extracts the returned array into the layout scope and includes the configured
 * layout from tmpl/ (or a template override).
 */
class Dispatcher extends AbstractModuleDispatcher implements HelperFactoryAwareInterface
{
    use HelperFactoryAwareTrait;

    private ?array $layoutData = null;

    /**
     * Runs the dispatcher. Nothing is rendered when the menu has no visible items.
     *
     * @return  void
     */
    public function dispatch()
    {
        $this->loadLanguage();

        $displayData = $this->getLayoutData();

        if (empty($displayData['list'])) {
            return;
        }

        parent::dispatch();
    }

    /**
     * Returns the layout data.
     *
     * @return  array
     */
    protected function getLayoutData()
    {
        // dispatch() calls this once itself and once via parent::dispatch(); build it only once.
        if ($this->layoutData !== null) {
            return $this->layoutData;
        }

        $data   = parent::getLayoutData();
        $app    = $data['app'];
        $params = $data['params'];

        /** @var QlmenuHelper $menuHelper */
        $menuHelper = $this->getHelperFactory()->getHelper('QlmenuHelper');

        $parametersCustom = new ParametersCustom($params, $this->module);

        $base    = $menuHelper->getBaseItem($params, $app);
        $active  = $menuHelper->getActiveItem($app);
        $default = $menuHelper->getDefaultItem($app);
        $list    = $menuHelper->getItems($params, $app, $base->tree, (int) $default->id, (int) $active->id);

        $displayModel = new DisplayCustom($parametersCustom, $this->module);
        $displayModel->setMenuItems($list);
        $displayModel->setActive($active);
        $displayModel->setInlineCss(
            $parametersCustom->useStyles() ? $menuHelper->getInlineCss($parametersCustom, $this->getMenuId()) : ''
        );

        $data['list']         = $list;
        $data['base']         = $base;
        $data['active']       = $active;
        $data['active_id']    = (int) $active->id;
        $data['default']      = $default;
        $data['default_id']   = (int) $default->id;
        $data['path']         = $base->tree;
        $data['showAll']      = $parametersCustom->showAllChildren();
        $data['class_sfx']    = htmlspecialchars($params->get('class_sfx', ''), ENT_COMPAT, 'UTF-8');
        $data['menuId']       = $this->getMenuId();
        $data['displayModel'] = $displayModel;

        return $this->layoutData = $data;
    }

    /**
     * The HTML id of the <ul>; used to scope the generated inline CSS to this module instance.
     */
    private function getMenuId(): string
    {
        return 'mod-qlmenu-' . (int) $this->module->id;
    }
}
