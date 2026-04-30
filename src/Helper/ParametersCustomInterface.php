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

    public function getLevel1stColorHover(): string;

    public function getLevel1stBackgroundColorHover(): string;

    public function getLevel1stColorActive(): string;

    public function getLevel1stBackgroundColorActive(): string;

    public function getLevel1stPadding(): string;

    public function getLevel1stFontSize(): string;

    public function getLevel2ndColor(): string;

    public function getLevel2ndBackgroundColor(): string;

    public function getLevel2ndColorHover(): string;

    public function getLevel2ndBackgroundColorHover(): string;

    public function getLevel2ndColorActive(): string;

    public function getLevel2ndBackgroundColorActive(): string;

    public function getLevel2ndPadding(): string;

    public function getLevel2ndFontSize(): string;
}
