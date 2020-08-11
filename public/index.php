<?php

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashevn@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Tiny\Skeleton\Core\Bootstrap\Bootstrap;
use Tiny\Skeleton\Core\Bootstrap\BootstrapUtils;

require_once __DIR__.'/../vendor/autoload.php';

$bootstrap = new Bootstrap(
    new BootstrapUtils(dirname(__DIR__)),
    (!getenv('APPLICATION_ENV') || getenv('APPLICATION_ENV') === 'prod')
);

// load modules configs
$configsArray = $bootstrap->loadModulesConfigs(
    require_once __DIR__.'/../modules.php'
);

// init the service manager
$serviceManager = $bootstrap->initServiceManager($configsArray);

// init the configs service (the raw configs array should be wrapped in an object)
$bootstrap->initConfigsService(
    $serviceManager,
    $configsArray
);

// init routing and find a matched route
$route = $bootstrap->initRouting($serviceManager);

// init a matched controller
$response = $bootstrap->initController($serviceManager, $route);

// display the response
echo $response->getResponseForDisplaying();
