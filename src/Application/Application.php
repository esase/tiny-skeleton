<?php

namespace Tiny\Skeleton\Application;

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Tiny\EventManager\EventManager;
use Tiny\Router;
use Tiny\Http;
use Tiny\Skeleton\Application\Service\ConfigService;

class Application
{

    /**
     * @var Bootstrapper
     */
    private Bootstrapper $bootstrapper;

    /**
     * @var bool
     */
    private bool $isCliContext;

    /**
     * @var array
     */
    private array $registeredModules;

    /**
     * Application constructor.
     *
     * @param  Bootstrapper  $bootstrapper
     * @param  bool          $isCliContext
     * @param  array         $registeredModules
     */
    public function __construct(
        Bootstrapper $bootstrapper,
        bool $isCliContext,
        array $registeredModules
    ) {
        $this->bootstrapper = $bootstrapper;
        $this->isCliContext = $isCliContext;
        $this->registeredModules = $registeredModules;
    }

    /**
     * @return string
     */
    public function run(): string
    {
        // load modules configs
        $configsArray = $this->bootstrapper->loadModulesConfigs(
            $this->registeredModules
        );

        // init the service manager
        $serviceManager = $this->bootstrapper->initServiceManager(
            $configsArray
        );

        // init the event manager
        $this->bootstrapper->initEventManager(
            $serviceManager->get(EventManager::class),
            $configsArray
        );

        // init the configs service (the raw configs array must be wrapped in an object)
        $this->bootstrapper->initConfigsService(
            $serviceManager->get(EventManager::class),
            $serviceManager->get(ConfigService::class),
            $configsArray
        );

        // init routes
        $this->bootstrapper->initRoutes(
            $serviceManager->get(EventManager::class),
            $serviceManager->get(Router\Router::class),
            $serviceManager->get(ConfigService::class),
            $this->isCliContext
        );

        // init the router and find a matched route
        $route = $this->bootstrapper->initRouter(
            $serviceManager->get(EventManager::class),
            $serviceManager->get(Router\Router::class)
        );

        // init a matched controller
        $response = $this->bootstrapper->initController(
            $serviceManager->get(EventManager::class),
            $serviceManager->get($route->getController()),
            $serviceManager->get(Http\Request::class),
            $serviceManager->get(Http\AbstractResponse::class),
            $route
        );

        // init the response
        $responseText = $this->bootstrapper->initResponse(
            $serviceManager->get(EventManager::class),
            $response,
            $route->getController(),
            $route->getMatchedAction()
        );

        return $responseText;
    }

}
