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
    public function hasSlides(): bool;

    public function getSlides(): ?SlideCollection;

    public function setSlides(?SlideCollection $slideCollection): void;

    public function getAutoplayMs(): int;

    public function getBoxAlign(): string;

    public function displayNavigationPrevNext(): bool;

    public function displayNavigationDots(): bool;
}
