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

require_once __DIR__ . '/../vendor/autoload.php';

$bootstrap = new Bootstrap();

// load modules configs
$configsArray = $bootstrap->loadModulesConfigs([
    'Base',
    'User'
]);

// init the service manager
$bootstrap->initServiceManager($configsArray);

// init the configs service (we need to be able to fetch modules configs later)
$bootstrap->initConfigsService($configsArray);

// init routing and find a matched route
$route = $bootstrap->initRouting();

// init a controller from the matched route
$response = $bootstrap->initController($route);

// display the response
echo $response->getResponseForDisplaying();
