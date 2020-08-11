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

use Tiny\Http;
use Tiny\Router\Route;
use Tiny\Router\Router;
use Tiny\ServiceManager\ServiceManager;
use Tiny\Skeleton\Module\Base;

class Bootstrap
{

    const CONFIG_FILE = 'config.php';

//    /**
//     * @var ServiceManager
//     */
//    private ServiceManager $serviceManager;

    /**
     * @param  array  $modules
     *
     * @return array
     */
    public function loadModulesConfigs(array $modules)
    {
        $configs = [];

        foreach ($modules as $module) {
            $moduleConfigPath = vsprintf(
                '%s/Module/%s/%s', [
                    __DIR__,
                    basename($module),
                    self::CONFIG_FILE,
                ]
            );

            $configs = array_merge_recursive(
                $configs,
                require_once $moduleConfigPath
            );
        }

        return $configs;
    }
//
//    /**
//     * @param  array  $configs
//     */
//    public function initServiceManager(array $configs)
//    {
//        $this->serviceManager = new ServiceManager(
//            ($configs['service_manager']['shared'] ?? []),
//            ($configs['service_manager']['discrete'] ?? [])
//        );
//    }
//
//    /**
//     * @param  array  $configsArray
//     */
//    public function initConfigsService(array $configsArray)
//    {
//        /** @var  Base\Service\ConfigService $configsService */
//        $configsService = $this->serviceManager->get(
//            Base\Service\ConfigService::class
//        );
//        $configsService->setConfigs($configsArray);
//    }
//
//    /**
//     * @return Route
//     */
//    public function initRouting(): Route
//    {
//        // find a matched route
//        /** @var  Router $router */
//        $router = $this->serviceManager->get(Router::class);
//
//        return $router->getMatchedRoute();
//    }
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
