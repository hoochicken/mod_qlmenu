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
    public function __construct(ParametersCustom $params, stdClass $module)
    {
        parent::__construct($params, $module);
    }
}
