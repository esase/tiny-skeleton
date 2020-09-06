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

use Throwable;
use Tiny\EventManager\EventManager;
use Tiny\ServiceManager\ServiceManager;
use Tiny\Skeleton\Application\EventManager\ConfigEvent;
use Tiny\Skeleton\Application\EventManager\ControllerEvent;
use Tiny\Skeleton\Application\EventManager\RouteEvent;
use Tiny\Skeleton\Application\Exception\InvalidArgumentException;
use Tiny\Skeleton\Application\Service\ConfigService;
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
            throw new InvalidArgumentException(
                'Both shared and discrete services are empty, check you config'
            );
        }

        return new ServiceManager(
            $shared,
            $discrete
        );
    }

    /**
     * @param  EventManager   $eventManager
     * @param  ConfigService  $configsService
     * @param  array          $configsArray
     */
    public function initConfigsService(
        EventManager $eventManager,
        ConfigService $configsService,
        array $configsArray
    ) {
        $setEvent = new ConfigEvent($configsArray);
        $eventManager->trigger(
            ConfigEvent::EVENT_SET_CONFIGS,
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
                throw new InvalidArgumentException(
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
     * @param  EventManager   $eventManager
     * @param  Router\Router  $router
     * @param  ConfigService  $configsService
     * @param  bool           $isConsole
     */
    public function initRoutes(
        EventManager $eventManager,
        Router\Router $router,
        ConfigService $configsService,
        bool $isConsole = false
    ) {
        $allRoutes = $configsService->getConfig('routes', []);

        $consoleRoutes = $allRoutes['console'] ?? [];
        $httpRoutes = $allRoutes['http'] ?? [];

        // we only need to fetch specific routes (either http or console ones)
        $activeRoutes = $isConsole ? $consoleRoutes : $httpRoutes;

        if (!$consoleRoutes && !$httpRoutes) {
            throw new InvalidArgumentException(
                'Both http and console routes are missing, check you config'
            );
        }

        foreach ($activeRoutes as $route) {
            $request = $route['request'] ?? '';
            $controller = $route['controller'] ?? '';
            $actionList = $route['action_list'] ?? '';

            if (!$request || !$controller || !$actionList) {
                throw new InvalidArgumentException(
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

            $registerEvent = new RouteEvent($route);
            $eventManager->trigger(
                RouteEvent::EVENT_REGISTER_ROUTE,
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
     * @throws Throwable
     */
    public function initRouter(
        EventManager $eventManager,
        Router\Router $router
    ): Router\Route {
        try {
            // trigger the router's events chain
            $beforeEvent = new RouteEvent();
            $eventManager->trigger(
                RouteEvent::EVENT_BEFORE_MATCHING_ROUTE,
                $beforeEvent
            );

            // return a modified route
            if ($beforeEvent->getData()) {
                return $beforeEvent->getData();
            }

            // find a matched route
            $route = $router->getMatchedRoute();

            $afterEvent = new RouteEvent($route);
            $eventManager->trigger(
                RouteEvent::EVENT_AFTER_MATCHING_ROUTE,
                $afterEvent
            );

            return $afterEvent->getData();
        }
        catch (Throwable $e) {
            $routeExceptionEvent = new RouteEvent(
                null, [
                    'exception' => $e
                ]
            );
            $eventManager->trigger(
                RouteEvent::EVENT_ROUTE_EXCEPTION,
                $routeExceptionEvent
            );

            // return a modified route
            if ($routeExceptionEvent->getData()) {
                return $routeExceptionEvent->getData();
            }

            throw $e;
        }
    }

    /**
     * @param  EventManager           $eventManager
     * @param  object                 $controller
     * @param  Http\Request           $request
     * @param  Http\AbstractResponse  $response
     * @param  Router\Route           $route
     *
     * @return Http\AbstractResponse
     * @throws Throwable
     */
    public function initController(
        EventManager $eventManager,
        object $controller,
        Http\Request $request,
        Http\AbstractResponse $response,
        Router\Route $route
    ): Http\AbstractResponse {
        try {
            // trigger the controller's events chain
            $beforeEvent = new ControllerEvent(
                null, [
                    'route' => $route,
                ]
            );
            $eventManager->trigger(
                ControllerEvent::EVENT_BEFORE_CALLING_CONTROLLER,
                $beforeEvent
            );

            // return a modified response
            if ($beforeEvent->getData()) {
                return $beforeEvent->getData();
            }

            // call the controller's action
            $controller->{$route->getMatchedAction()}($response, $request);

            $afterEvent = new ControllerEvent(
                $response, [
                    'route' => $route,
                ]
            );
            $eventManager->trigger(
                ControllerEvent::EVENT_AFTER_CALLING_CONTROLLER,
                $afterEvent
            );

            return $afterEvent->getData();
        } catch (Throwable $e) {
            $requestExceptionEvent = new ControllerEvent(
                null, [
                    'exception' => $e,
                    'route'     => $route,
                ]
            );
            $eventManager->trigger(
                ControllerEvent::EVENT_CONTROLLER_EXCEPTION,
                $requestExceptionEvent
            );

            // return a modified response
            if ($requestExceptionEvent->getData()) {
                return $requestExceptionEvent->getData();
            }

            throw $e;
        }
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
        $beforeEvent = new ControllerEvent(
            $response,
            [
                'controller' => $controller,
                'action'     => $action,
            ]
        );
        $eventManager->trigger(
            ControllerEvent::EVENT_BEFORE_DISPLAYING_RESPONSE,
            $beforeEvent
        );

        /** @var Http\AbstractResponse $response */
        $response = $beforeEvent->getData();
        $responseString = $response->getResponseForDisplaying();

        return null !== $responseString ? $responseString : '';
    }

    /**
     * @param  array  $modules
     *
     * @return array
     */
    private function collectModulesConfigs(array $modules): array
    {
        // load application config
        $configs = $this->utils->loadApplicationConfigArray();

        // load modules config
        foreach ($modules as $module) {
            $configs = array_merge_recursive(
                $configs,
                $this->utils->loadModuleConfigArray($module)
            );
        }

        return $configs;
    }

}
