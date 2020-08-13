<?php

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashevn@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Tiny\EventManager\EventManager;
use Tiny\Skeleton\Bootstrap;
use Tiny\Skeleton\BootstrapUtils;
use Tiny\Skeleton\Module\Core;
use Tiny\Router;
use Tiny\Http;

require_once __DIR__.'/../vendor/autoload.php';

$isProdEnvironment = !getenv('APPLICATION_ENV')
    || getenv('APPLICATION_ENV') === 'prod';

$bootstrap = new Bootstrap(
    new BootstrapUtils(dirname(__DIR__)),
    $isProdEnvironment
);

// load modules configs
$configsArray = $bootstrap->loadModulesConfigs(
    require_once __DIR__.'/../modules.php'
);

// init the service manager
$serviceManager = $bootstrap->initServiceManager($configsArray);

// init the event manager
$bootstrap->initEventManager(
    $serviceManager->get(EventManager::class),
    $configsArray
);

// init the configs service (the raw configs array must be wrapped in an object)
$bootstrap->initConfigsService(
    $serviceManager->get(EventManager::class),
    $serviceManager->get(Core\Service\ConfigService::class),
    $configsArray
);

// init routes
$bootstrap->initRoutes(
    $serviceManager->get(EventManager::class),
    $serviceManager->get(Router\Router::class),
    $serviceManager->get(Core\Service\ConfigService::class),
    php_sapi_name() === 'cli'
);

// init the router and find a matched route
$route = $bootstrap->initRouter(
    $serviceManager->get(EventManager::class),
    $serviceManager->get(Router\Router::class)
);

// init a matched controller
$response = $bootstrap->initController(
    $serviceManager->get(EventManager::class),
    $serviceManager->get($route->getController()),
    $serviceManager->get(Http\Request::class),
    $serviceManager->get(Http\AbstractResponse::class),
    $route->getMatchedAction()
);

// init the response
$responseText = $bootstrap->initResponse(
    $serviceManager->get(EventManager::class),
    $response,
    $route->getController(),
    $route->getMatchedAction()
);

// display the response
echo $responseText;
