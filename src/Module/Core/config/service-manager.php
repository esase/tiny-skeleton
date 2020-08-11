<?php

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashevn@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Tiny\Skeleton\Module\Core;
use Tiny\ServiceManager\Factory\InvokableFactory;
use Tiny\Skeleton\Module\Core\Http;
use Tiny\Skeleton\Module\Core\Router;
use Tiny\Skeleton\Module\Core\EventManager;

return [
    'shared' => [
        // event manager
        Tiny\EventManager\EventManager::class => Core\EventManager\Factory\EventManagerFactory::class,

        // router
        Tiny\Router\Router::class             => Core\Router\Factory\RouterFactory::class,

        // http
        Tiny\Http\Request::class              => Core\Http\Factory\RequestFactory::class,
        Tiny\Http\AbstractResponse::class     => Core\Http\Factory\ResponseFactory::class,

        // service
        Core\Service\ConfigService::class     => InvokableFactory::class,
    ],
];
