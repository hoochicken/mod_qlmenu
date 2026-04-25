<?php
/**
 * @package     Hoochicken\Module\Qlmenu
 *
 * @copyright   Copyright (C) 2026 Mareike Riegel. All rights reserved.
 * @license     GNU General Public License version 2 or later;
 */

defined('_JEXEC') or die;

use Joomla\CMS\Extension\Service\Provider\HelperFactory;
use Joomla\CMS\Extension\Service\Provider\Module;
use Joomla\CMS\Extension\Service\Provider\ModuleDispatcherFactory;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

return new class() implements ServiceProviderInterface
{
    const PHP_VERSION_NAMESPACE = '8.4.0';

    public function register(Container $container)
    {
        static::requireOnceIfPhpDoesNotKnowamespaces();

        $container->registerServiceProvider(new ModuleDispatcherFactory('\\Hoochicken\\Module\\Qlmenu'));
        $container->registerServiceProvider(new HelperFactory('\\Hoochicken\\Module\\Qlmenu\\Site\\Helper'));
        $container->registerServiceProvider(new Module());
    }

    private static function requireOnceIfPhpDoesNotKnowamespaces(): void
    {
        if (static::checkPhpVersionKnowsNamesspaces()) {
            return;
        }

        require_once __DIR__ . '/../src/Dispatcher/Dispatcher.php';
        require_once __DIR__ . '/../src/Helper/ParametersBasic.php';
        require_once __DIR__ . '/../src/Helper/ParametersBasicInterface.php';
        require_once __DIR__ . '/../src/Helper/ParametersCustom.php';
        require_once __DIR__ . '/../src/Helper/ParametersCustomInterface.php';
        require_once __DIR__ . '/../src/Helper/MessageItem.php';
        require_once __DIR__ . '/../src/Helper/MessageCollection.php';
        require_once __DIR__ . '/../src/Helper/SlideItem.php';
        require_once __DIR__ . '/../src/Helper/SlideCollection.php';
        require_once __DIR__ . '/../src/Helper/StringHelper.php';
    }

    private static function checkPhpVersionKnowsNamesspaces(): bool
    {
        $phpVersion = phpversion();
        return (version_compare($phpVersion, static::PHP_VERSION_NAMESPACE, '>='));
    }
};
