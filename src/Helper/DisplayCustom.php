<?php
/**
 * mod_qlmenu
 *
 * @copyright  Copyright (C) 2026. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

namespace Hoochicken\Module\Qlmenu\Site\Helper;

use Joomla\CMS\Menu\MenuItem;
use stdClass;

require_once __DIR__ . '/DisplayBasicInterface.php';
require_once __DIR__ . '/DisplayCustomInterface.php';

class DisplayCustom extends DisplayBasic implements DisplayBasicInterface, DisplayCustomInterface
{
    /** @var MenuItem[]  */
    private array $menuItems = [];
    private $active = null;

    public function __construct(ParametersCustom $params, stdClass $module)
    {
        parent::__construct($params, $module);
    }

    public function getParametersCustom(): ParametersCustom
    {
        return $this->parametersCustom;
    }

    public function getMenuItems(): array
    {
        return $this->menuItems;
    }

    public function setMenuItems(array $menuItems): DisplayCustom
    {
        $this->menuItems = $menuItems;
        return $this;
    }

    public function getActive()
    {
        return $this->active;
    }

    public function setActive($active): DisplayCustom
    {
        $this->active = $active;
        return $this;
    }
}
