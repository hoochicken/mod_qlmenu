<?php
/**
 * mod_qlmenu
 *
 * @copyright  Copyright (C) 2026. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

namespace Hoochicken\Module\Qlmenu\Site\Helper;

class MessageCollection
{
    /** @var MessageItem[] */
    private array $items = [];

    public function add(MessageItem $message): void
    {
        $this->items[] = $message;
    }

    /**
     * @param MessageItem[] $messages
     */
    public function set(array $messages): void
    {
        $this->items = $messages;
    }

    /**
     * @return MessageItem[]
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

