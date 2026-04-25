<?php
/**
 * mod_qlmenu
 *
 * @copyright  Copyright (C) 2026. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

namespace Hoochicken\Module\Qlmenu\Site\Helper;

class SlideCollection
{
    /** @var SlideItem[] */
    private array $items = [];

    public function add(SlideItem $slide): void
    {
        $this->items[] = $slide;
    }

    /**
     * @param SlideItem[] $slides
     */
    public function set(array $slides): void
    {
        $this->items = $slides;
    }

    /**
     * @return SlideItem[]
     */
    public function get(): array
    {
        return $this->items;
    }

    public function hasSlides(): bool
    {
        return count($this->items) > 0;
    }
}
