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
    const DEFAULT_AUTOPLAY_MS = 3000;

    private ?SlideCollection $slideCollection = null;

    public function __construct(Registry $params, stdClass $module)
    {
        parent::__construct($params, $module);
    }

    public function hasSlides(): bool
    {
        return !is_null($this->slideCollection) && $this->slideCollection->hasSlides();
    }

    public function getSlides(): ?SlideCollection
    {
        return $this->slideCollection;
    }

    public function setSlides(?SlideCollection $slideCollection): void
    {
        $this->slideCollection = $slideCollection;
    }

    public function getAutoplayMs(): int
    {
        return (int)($this->params?->get('autoplayMs', static::DEFAULT_AUTOPLAY_MS) ?? static::DEFAULT_AUTOPLAY_MS);
    }

    public function getBoxAlign(): string
    {
        $align = (string)($this->params?->get('boxAlign', 'left') ?? 'left');

        return $align === 'right' ? 'right' : 'left';
    }

    public function displayNavigationPrevNext(): bool
    {
        return (bool)($this->params?->get('displayNavigationPrevNext', 1) ?? 1);
    }

    public function displayNavigationDots(): bool
    {
        return (bool)($this->params?->get('displayNavigationDots', 1) ?? 1);
    }
}
