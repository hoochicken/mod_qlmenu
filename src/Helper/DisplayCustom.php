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

require_once __DIR__ . '/DisplayBasicInterface.php';
require_once __DIR__ . '/DisplayCustomInterface.php';

class DisplayCustom extends DisplayBasic implements DisplayBasicInterface, DisplayCustomInterface
{
    public function __construct(Registry $params, stdClass $module)
    {
        parent::__construct($params, $module);
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
