<?php

namespace Tiny\Skeleton;

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashevn@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Tiny\EventManager\EventManager;
use Tiny\ServiceManager\ServiceManager;
use Tiny\Skeleton\Module\Core;
use Tiny\Http;
use Tiny\Router;

class Bootstrapper
{

    /**
     * @var BootstrapperUtils
     */
    private BootstrapperUtils $utils;

    /**
     * @var bool
     */
    private bool $isProdEnvironment;

    /**
     * Bootstrap constructor.
     *
     * @param  BootstrapperUtils  $utils
     * @param  bool               $isProdEnvironment
     */
    public function __construct(
        BootstrapperUtils $utils,
        bool $isProdEnvironment
    ) {
        $this->utils = $utils;
        $this->isProdEnvironment = $isProdEnvironment;
    }

    /**
     * @param  array  $modules
     *
     * @return array
     */
    public function loadModulesConfigs(array $modules): array
    {
        // in the "dev" environment  we always return actual configs
        if (!$this->isProdEnvironment) {
            return $this->collectModulesConfigs($modules);
        }

        // in the "prod" environment we try to use cached configs
        $cachedConfigs = $this->utils->loadCachedModulesConfigArray();

        if ($cachedConfigs) {
            return $cachedConfigs;
        }

        // collect module configs
        $configs = $this->collectModulesConfigs($modules);

        // cache the collected configs and return
        $this->utils->saveCachedModulesConfigArray($configs);

        return $configs;
    }

    /**
     * @param  array  $configs
     *
     * @return ServiceManager
     */
    public function initServiceManager(array $configs): ServiceManager
    {
        $shared = ($configs['service_manager']['shared'] ?? []);
        $discrete = ($configs['service_manager']['discrete'] ?? []);

        if (!$shared && !$discrete) {
            throw new Core\Exception\InvalidArgumentException(
                'Both shared and discrete services are empty, check you config'
            );
        }

        return new ServiceManager(
            $shared,
            $discrete
        );
    }

    /**
     * @param  EventManager                $eventManager
     * @param  Core\Service\ConfigService  $configsService
     * @param  array                       $configsArray
     */
    public function initConfigsService(
        EventManager $eventManager,
        Core\Service\ConfigService $configsService,
        array $configsArray
    ) {
        $setEvent = new Core\EventManager\ConfigEvent($configsArray);
        $eventManager->trigger(
            Core\EventManager\ConfigEvent::EVENT_SET_CONFIGS,
            $setEvent
        );

        $configsService->setConfigs($setEvent->getData());
    }

    /**
     * @param  EventManager  $eventManager
     * @param  array         $configsArray
     * @param  int           $defaultPriority
     */
    public function initEventManager(
        EventManager $eventManager,
        array $configsArray,
        int $defaultPriority = 100
    ) {
        $listeners = $configsArray['listeners'] ?? [];

        // register listeners
        foreach ($listeners as $listener) {
            $eventName = $listener['event'] ?? '';
            $listenerClass = $listener['listener'] ?? '';

            if (!$eventName || !$listenerClass) {
                throw new Core\Exception\InvalidArgumentException(
                    'Event name or listener class is missing, check you config'
                );
            }

            $eventManager->subscribe(
                $eventName,
                $listenerClass,
                ($listener['priority'] ?? $defaultPriority)
            );
        }
    }

    /**
     * @param  EventManager                $eventManager
     * @param  Router\Router               $router
     * @param  Core\Service\ConfigService  $configsService
     * @param  bool                        $isConsole
     */
    public function initRoutes(
        EventManager $eventManager,
        Router\Router $router,
        Core\Service\ConfigService $configsService,
        bool $isConsole = false
    ) {
        $allRoutes = $configsService->getConfig('routes', []);

        $consoleRoutes = $allRoutes['console'] ?? [];
        $httpRoutes = $allRoutes['http'] ?? [];

        // we only need to fetch specific routes (either http or console ones)
        $activeRoutes = $isConsole ? $consoleRoutes : $httpRoutes;

        if (!$consoleRoutes && !$httpRoutes) {
            throw new Core\Exception\InvalidArgumentException(
                'Both http and console routes are missing, check you config'
            );
        }

        foreach ($activeRoutes as $route) {
            $request = $route['request'] ?? '';
            $controller = $route['controller'] ?? '';
            $actionList = $route['action_list'] ?? '';

            if (!$request || !$controller || !$actionList) {
                throw new Core\Exception\InvalidArgumentException(
                    'One of: request, controller or action list is empty, check you config'
                );
            }

            $route = new Router\Route(
                $request,
                $controller,
                $actionList,
                ($route['type'] ?? Router\Route::TYPE_LITERAL),
                ($route['request_params'] ?? []),
                ($route['spec'] ?? '')
            );

            $registerEvent = new Core\EventManager\RouteEvent($route);
            $eventManager->trigger(
                Core\EventManager\RouteEvent::EVENT_REGISTER_ROUTE,
                $registerEvent
            );

            $router->registerRoute($registerEvent->getData());
        }
    }

    /**
     * @param  EventManager   $eventManager
     * @param  Router\Router  $router
     *
     * @return Router\Route
     */
    public function initRouter(
        EventManager $eventManager,
        Router\Router $router
    ): Router\Route {
        // trigger the router's events chain
        $beforeEvent = new Core\EventManager\RouteEvent();
        $eventManager->trigger(
            Core\EventManager\RouteEvent::EVENT_BEFORE_MATCHING_ROUTE,
            $beforeEvent
        );

        // return a modified route
        if ($beforeEvent->getData()) {
            return $beforeEvent->getData();
        }

        // find a matched route
        $route = $router->getMatchedRoute();

        $afterEvent = new Core\EventManager\RouteEvent(
            null, [
                'route' => $route,
            ]
        );
        $eventManager->trigger(
            Core\EventManager\RouteEvent::EVENT_AFTER_MATCHING_ROUTE,
            $afterEvent
        );

        // return a modified route
        if ($afterEvent->getData()) {
            return $afterEvent->getData();
        }

        // return the initial matched route
        return $route;
    }

    /**
     * @param  EventManager           $eventManager
     * @param  object                 $controller
     * @param  Http\Request           $request
     * @param  Http\AbstractResponse  $response
     * @param  Router\Route           $route
     *
     * @return Http\AbstractResponse
     */
    public function initController(
        EventManager $eventManager,
        object $controller,
        Http\Request $request,
        Http\AbstractResponse $response,
        Router\Route $route
    ): Http\AbstractResponse {
        // trigger the controller's events chain
        $beforeEvent = new Core\EventManager\ControllerEvent(
            null, [
            'route' => $route,
        ]
        );
        $eventManager->trigger(
            Core\EventManager\ControllerEvent::EVENT_BEFORE_CALLING_CONTROLLER,
            $beforeEvent
        );

        // return a modified response
        if ($beforeEvent->getData()) {
            return $beforeEvent->getData();
        }

        // call the controller's action
        $controller->{$route->getMatchedAction()}($response, $request);

        $afterEvent = new Core\EventManager\ControllerEvent(
            null, [
                'response' => $response,
                'route'    => $route,
            ]
        );
        $eventManager->trigger(
            Core\EventManager\ControllerEvent::EVENT_AFTER_CALLING_CONTROLLER,
            $afterEvent
        );

        // return a modified response
        if ($afterEvent->getData()) {
            return $afterEvent->getData();
        }

        // return the initial response
        return $response;
    }

    /**
     * @param  EventManager           $eventManager
     * @param  Http\AbstractResponse  $response
     * @param  string                 $controller
     * @param  string                 $action
     *
     * @return string
     */
    public function initResponse(
        EventManager $eventManager,
        Http\AbstractResponse $response,
        string $controller,
        string $action
    ): string {
        // trigger the response's events chain
        $beforeEvent = new Core\EventManager\ControllerEvent(
            null,
            [
                'response'   => $response,
                'controller' => $controller,
                'action'     => $action,
            ]
        );
        $eventManager->trigger(
            Core\EventManager\ControllerEvent::EVENT_BEFORE_DISPLAYING_RESPONSE,
            $beforeEvent
        );

        // return a modified response
        if ($beforeEvent->getData()) {
            /** @var Http\AbstractResponse $response */
            $response = $beforeEvent->getData();

            return $response->getResponseForDisplaying() ?? '';
        }

        // return the initial response
        return $response->getResponseForDisplaying() ?? '';
    }

    /**
     * @param  array  $modules
     *
     * @return array
     */
    private function collectModulesConfigs(array $modules): array
    {
        $configs = [];
        foreach ($modules as $module) {
            $configs = array_merge_recursive(
                $configs,
                $this->utils->loadModuleConfigArray($module)
            );
        }

        return $configs;
    }

}
