<?php

/**
 * @package     Hoochicken\Module\Qlmenu
 *
 * @copyright   Copyright (C) 2026 Mareike Riegel. All rights reserved.
 * @license     GNU General Public License version 2 or later;
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Extension\Service\Provider\HelperFactory;
use Joomla\CMS\Extension\Service\Provider\Module;
use Joomla\CMS\Extension\Service\Provider\ModuleDispatcherFactory;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

/**
 * Service provider for mod_qlmenu.
 *
 * Classes in src/ are autoloaded via the <namespace> declared in the manifest,
 * so no manual require_once is needed.
 */
return new class () implements ServiceProviderInterface {
    public function register(Container $container): void
    {
        $container->registerServiceProvider(new ModuleDispatcherFactory('\\Hoochicken\\Module\\Qlmenu'));
        $container->registerServiceProvider(new HelperFactory('\\Hoochicken\\Module\\Qlmenu\\Site\\Helper'));
        $container->registerServiceProvider(new Module());
    }
};
