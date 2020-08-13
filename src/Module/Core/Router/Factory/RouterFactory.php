<?php

namespace Tiny\Skeleton\Module\Core\Router\Factory;

use Tiny\Http\Request;
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
        $router = new Router(
            $serviceManager->get(Request::class)
        );

        return $router;
    }

}