<?php

namespace Tiny\Skeleton\Module\Core\Router\Factory;

use Tiny\Http\Request;
use Tiny\Router\Route;
use Tiny\Router\Router;
use Tiny\ServiceManager\ServiceManager;
use Tiny\Skeleton\Module\Core;

class RouterFactory
{

    /**
     * @param  ServiceManager  $serviceManager
     *
     * @return Router
     */
    public function __invoke(ServiceManager $serviceManager): Router
    {
        /** @var  Request $request */
        $request = $serviceManager->get(Request::class);

        $router = new Router(
            $request
        );

        // register routes
        /** @var  Core\Service\ConfigService $configsService */
        $configsService = $serviceManager->get(
            Core\Service\ConfigService::class
        );

        $allRoutes = $configsService->getConfig('routes', []);
        $routes = php_sapi_name() === 'cli'
            ? ($allRoutes['console'] ?? []) // we need only console routes
            : ($allRoutes['http'] ?? []); // we need only http routes

        foreach ($routes as $route) {
            $router->registerRoute(
                new Route(
                    ($route['request'] ?? ''),
                    ($route['controller'] ?? ''),
                    ($route['action_list'] ?? ''),
                    ($route['type'] ?? Route::TYPE_LITERAL),
                    ($route['request_params'] ?? []),
                    ($route['spec'] ?? '')
                )
            );
        }

        return $router;
    }

}
