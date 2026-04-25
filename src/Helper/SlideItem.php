<?php
/**
 * mod_qlmenu
 *
 * @copyright  Copyright (C) 2026. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

namespace Hoochicken\Module\Qlmenu\Site\Helper;

class SlideItem
{
    private bool $display;
    private string $image;
    private string $imageRaw;
    private string $title;
    private string $text;

    public function isDisplay(): bool
    {
        return $this->display;
    }

    public function setDisplay(bool $display): void
    {
        $this->display = $display;
    }

    public function getImage(bool $specialChars = true): string
    {
        return StringHelper::transFormSpecialChars($this->image, $specialChars);
    }

    public function existsImage(): bool
    {
        $path = sprintf('%s/%s', JPATH_ROOT, $this->image);
        return file_exists($path);
    }

    public function setImage(string $image): void
    {
        $this->imageRaw = $image;
        $this->image = StringHelper::cleanupFileName($image);
    }

    public function getTitle(bool $specialChars = true): string
    {
        return StringHelper::transFormSpecialChars($this->title, $specialChars);
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function existsTitle(): bool
    {
        return trim($this->title ?? '') !== '';
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function existsText(): bool
    {
        return trim($this->text ?? '') !== '';
    }

    public function extistsTet(): bool
    {
        return $this->existsText();
    }
}

