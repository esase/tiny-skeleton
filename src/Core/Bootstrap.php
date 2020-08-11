<?php

namespace Tiny\Skeleton\Core;

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashevn@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Tiny\Router;
use Tiny\ServiceManager\ServiceManager;
use Tiny\Skeleton\Module\Base;

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
     * @param  ServiceManager  $serviceManager
     * @param  array           $configsArray
     */
    public function initConfigsService(
        ServiceManager $serviceManager,
        array $configsArray
    ) {
        /** @var  Base\Service\ConfigService $configsService */
        $configsService = $serviceManager->get(
            Base\Service\ConfigService::class
        );

        $configsService->setConfigs($configsArray);
    }

    /**
     * @param  ServiceManager  $serviceManager
     *
     * @return Router\Route
     */
    public function initRouting(ServiceManager $serviceManager): Router\Route
    {
        // find a matched route
        /** @var  Router\Router $router */
        $router = $serviceManager->get(Router\Router::class);

        return $router->getMatchedRoute();
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

    //

    //

    //

    //
    //    /**
    //     * @param  Route  $route
    //     *
    //     * @return Http\AbstractResponse
    //     */
    //    public function initController(Route $route): Http\AbstractResponse
    //    {
    //        // create a controller instance
    //        $controller = $this->serviceManager->get($route->getController());
    //
    //        /** @var  Http\Request $request */
    //        $request = $this->serviceManager->get(Http\Request::class);
    //
    //        /** @var  Http\AbstractResponse $response */
    //        $response = $this->serviceManager->get(Http\AbstractResponse::class);
    //
    //        // invoke the controller's action
    //        $controller->{$route->getMatchedAction()}(
    //            $response,
    //            $request
    //        );
    //
    //        return $response;
    //    }

}
