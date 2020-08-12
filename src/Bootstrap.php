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

class Bootstrap
{

    /**
     * @var BootstrapUtils
     */
    private BootstrapUtils $utils;

    /**
     * @var bool
     */
    private bool $isProdEnvironment;

    /**
     * Bootstrap constructor.
     *
     * @param  BootstrapUtils  $utils
     * @param  bool            $isProdEnvironment
     */
    public function __construct(
        BootstrapUtils $utils,
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
    public function loadModulesConfigs(array $modules)
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
        return new ServiceManager(
            ($configs['service_manager']['shared'] ?? []),
            ($configs['service_manager']['discrete'] ?? [])
        );
    }

    /**
     * @param  Core\Service\ConfigService  $configsService
     * @param  array                       $configsArray
     */
    public function initConfigsService(
        Core\Service\ConfigService $configsService,
        array $configsArray
    ) {
        $configsService->setConfigs($configsArray);
    }

    /**
     * @param  EventManager                $eventManager
     * @param  Core\Service\ConfigService  $configsService
     * @param  int                         $defaultPriority
     */
    public function initEventManager(
        EventManager $eventManager,
        Core\Service\ConfigService $configsService,
        int $defaultPriority = 100
    ) {
        $listeners = $configsService->getConfig('listeners', []);

        foreach ($listeners as $listener) {
            $eventManager->subscribe(
                ($listener['event'] ?? ''),
                ($listener['listener'] ?? ''),
                ($listener['priority'] ?? $defaultPriority)
            );
        }
    }

    /**
     * @param  EventManager                $eventManger
     * @param  Router\Router               $router
     * @param  Core\Service\ConfigService  $configsService
     * @param  bool                        $isConsole
     */
    public function initRoutes(
        EventManager $eventManger,
        Router\Router $router,
        Core\Service\ConfigService $configsService,
        bool $isConsole = false
    ) {
        $allRoutes = $configsService->getConfig('routes', []);

        // we only need to fetch specific routes (either http or console ones)
        $routes = $isConsole ? ($allRoutes['console'] ?? [])
            : ($allRoutes['http'] ?? []);

        foreach ($routes as $route) {
            $route = new Router\Route(
                ($route['request'] ?? ''),
                ($route['controller'] ?? ''),
                ($route['action_list'] ?? ''),
                ($route['type'] ?? Router\Route::TYPE_LITERAL),
                ($route['request_params'] ?? []),
                ($route['spec'] ?? '')
            );

            $registerEvent = new Core\EventManager\RouteEvent($route);
            $eventManger->trigger(
                Core\EventManager\RouteEvent::EVENT_REGISTER_ROUTE,
                $registerEvent
            );

            $router->registerRoute($registerEvent->getData());
        }
    }

    /**
     * @param  EventManager   $eventManger
     * @param  Router\Router  $router
     *
     * @return Router\Route
     */
    public function initRouter(
        EventManager $eventManger,
        Router\Router $router
    ): Router\Route {
        // trigger the router's events chain
        $beforeEvent = new Core\EventManager\RouteEvent();
        $eventManger->trigger(
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
        $eventManger->trigger(
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
     * @param  EventManager           $eventManger
     * @param  object                 $controller
     * @param  Http\Request           $request
     * @param  Http\AbstractResponse  $response
     * @param  string                 $action
     *
     * @return Http\AbstractResponse
     */
    public function initController(
        EventManager $eventManger,
        object $controller,
        Http\Request $request,
        Http\AbstractResponse $response,
        string $action
    ): Http\AbstractResponse {
        // trigger the controller's events chain
        $beforeEvent = new Core\EventManager\ControllerEvent();
        $eventManger->trigger(
            Core\EventManager\ControllerEvent::EVENT_BEFORE_CALLING_CONTROLLER,
            $beforeEvent
        );

        // return a modified response
        if ($beforeEvent->getData()) {
            return $beforeEvent->getData();
        }

        // call the controller's action
        $controller->$action($response, $request);

        $afterEvent = new Core\EventManager\ControllerEvent(
            null, [
                'response' => $response,
            ]
        );
        $eventManger->trigger(
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
