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
     */
    public function initEventManager(
        EventManager $eventManager,
        Core\Service\ConfigService $configsService
    ) {
        $listeners = $configsService->getConfig('listeners', []);

        foreach ($listeners as $listener) {
            $eventManager->subscribe(
                ($listener['event'] ?? ''),
                ($listener['listener'] ?? ''),
                ($listener['priority'] ?? 100)
            );
        }
    }

    /**
     * @param  EventManager   $eventManger
     * @param  Router\Router  $router
     *
     * @return Router\Route
     */
    public function initRouting(
        EventManager $eventManger,
        Router\Router $router
    ): Router\Route {
        // trigger the routing events chain
        $beforeEvent = new Core\EventManager\RouteEvent();
        $eventManger->trigger(
            Core\EventManager\RouteEvent::EVENT_BEFORE_MATCHING,
            $beforeEvent
        );

        if ($beforeEvent->getData()) {
            return $beforeEvent->getData();
        }

        // find a matched route
        $route = $router->getMatchedRoute();

        $afterEvent = new Core\EventManager\RouteEvent(null, [
            'route' => $route
        ]);
        $eventManger->trigger(
            Core\EventManager\RouteEvent::EVENT_AFTER_MATCHING,
            $afterEvent
        );

        if ($afterEvent->getData()) {
            return $afterEvent->getData();
        }

        return $route;
    }

    /**
     * @param  object                 $controller
     * @param  Http\Request           $request
     * @param  Http\AbstractResponse  $response
     * @param  string                 $action
     *
     * @return Http\AbstractResponse
     */
    public function initController(
        object $controller,
        Http\Request $request,
        Http\AbstractResponse $response,
        string $action
    ): Http\AbstractResponse {
        // invoke the controller's action
        $controller->$action($response, $request);

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
