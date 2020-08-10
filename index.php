<?php

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashevn@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Tiny\Skeleton\Bootstrap;
use Tiny\Skeleton\Module\Base;

require_once __DIR__ . '/vendor/autoload.php';

$bootstrap = new Bootstrap();

// load configs
$configsArray = $bootstrap->loadModulesConfigs([
    'Base',
    'User'
]);

// init service manager
$serviceManager = $bootstrap->initServiceManager($configsArray);

// init the config service
/** @var  Base\Service\ConfigService $configsService */
$configsService = $serviceManager->get(Base\Service\ConfigService::class);
$configsService->setConfigs($configsArray);
