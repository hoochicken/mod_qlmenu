<?php
/**
 * @package     Hoochicken\Module\Qlmenu
 *
 * @copyright   Copyright (C) 2026 Mareike Riegel. All rights reserved.
 * @license     GNU General Public License version 2 or later;
 */

namespace Hoochicken\Module\Qlmenu\Site\Helper;

defined('_JEXEC') or die;

class StringHelper
{
    public static function transFormSpecialChars(string $string, bool $specialChars = true): string
    {
        if ($specialChars) {
            return $string;
        }
        return htmlspecialchars($string);
    }

    public static function cleanupFileName(string $filename): string
    {
        if (!str_contains($filename, '#')) {
            return $filename;
        }
        $filePathParts = explode('#', $filename);
        return $filePathParts[0];
    }
}
