<?php
/**
 * mod_qlmenu
 *
 * @copyright  Copyright (C) 2026. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

namespace Hoochicken\Module\Qlmenu\Site\Helper;

use Joomla\Registry\Registry;
use stdClass;

require_once __DIR__ . '/ParametersBasicInterface.php';
require_once __DIR__ . '/ParametersCustomInterface.php';

class ParametersCustom extends ParametersBasic implements ParametersBasicInterface, ParametersCustomInterface
{
    private const DEFAULT_MENUTYPE = '';
    private const DEFAULT_BASE = 0;
    private const DEFAULT_START_LEVEL = 1;
    private const DEFAULT_END_LEVEL = 0;
    private const DEFAULT_SHOW_ALL_CHILDREN = true;
    private const DEFAULT_LEVEL_1ST_COLOR = '#0000ff';
    private const DEFAULT_LEVEL_1ST_BACKGROUND_COLOR = '#ffffff';
    private const DEFAULT_LEVEL_2ND_COLOR = '#00ffff';
    private const DEFAULT_LEVEL_2ND_BACKGROUND_COLOR = '#ffffff';

    public function __construct(Registry $params, stdClass $module)
    {
        parent::__construct($params, $module);
    }

    public function getMenutype(): string
    {
        return (string)($this->params?->get('menutype', static::DEFAULT_MENUTYPE) ?? static::DEFAULT_MENUTYPE);
    }

    public function getBase(): int
    {
        return (int)($this->params?->get('base', static::DEFAULT_BASE) ?? static::DEFAULT_BASE);
    }

    public function getStartLevel(): int
    {
        return (int)($this->params?->get('startLevel', static::DEFAULT_START_LEVEL) ?? static::DEFAULT_START_LEVEL);
    }

    public function getEndLevel(): int
    {
        return (int)($this->params?->get('endLevel', static::DEFAULT_END_LEVEL) ?? static::DEFAULT_END_LEVEL);
    }

    public function showAllChildren(): bool
    {
        return (bool)($this->params?->get('showAllChildren', static::DEFAULT_SHOW_ALL_CHILDREN) ?? static::DEFAULT_SHOW_ALL_CHILDREN);
    }

    public function getLevel1stColor(): string
    {
        return (string)($this->params?->get('level_1st_color', static::DEFAULT_LEVEL_1ST_COLOR) ?? static::DEFAULT_LEVEL_1ST_COLOR);
    }

    public function getLevel1stBackgroundColor(): string
    {
        return (string)($this->params?->get('level_1st_background_color', static::DEFAULT_LEVEL_1ST_BACKGROUND_COLOR) ?? static::DEFAULT_LEVEL_1ST_BACKGROUND_COLOR);
    }

    public function getLevel2ndColor(): string
    {
        return (string)($this->params?->get('level_2nd_color', static::DEFAULT_LEVEL_2ND_COLOR) ?? static::DEFAULT_LEVEL_2ND_COLOR);
    }

    public function getLevel2ndBackgroundColor(): string
    {
        return (string)($this->params?->get('level_2nd_background_color', static::DEFAULT_LEVEL_2ND_BACKGROUND_COLOR) ?? static::DEFAULT_LEVEL_2ND_BACKGROUND_COLOR);
    }
}
