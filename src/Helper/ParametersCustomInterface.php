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

interface ParametersCustomInterface
{
    public function getMenutype(): string;

    public function getBase(): int;

    public function getStartLevel(): int;

    public function getEndLevel(): int;

    public function showAllChildren(): bool;

    public function getLevel1stColor(): string;

    public function getLevel1stBackgroundColor(): string;

    public function getLevel2ndColor(): string;

    public function getLevel2ndBackgroundColor(): string;
}
