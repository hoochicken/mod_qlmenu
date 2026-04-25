<?php
/**
 * mod_qlmenu
 *
 * @copyright  Copyright (C) 2026. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

namespace Hoochicken\Module\Qlmenu\Site\Helper;

class ErrorCollection
{
    /** @var ErrorItem[] */
    private array $items = [];

    public function add(ErrorItem $error): void
    {
        $this->items[] = $error;
    }

    /**
     * @param ErrorItem[] $errors
     */
    public function set(array $errors): void
    {
        $this->items = $errors;
    }

    /**
     * @return ErrorItem[]
     */
    public function get(): array
    {
        return $this->items;
    }

    public function hasErrors(): bool
    {
        return 0 < count($this->items);
    }
}

