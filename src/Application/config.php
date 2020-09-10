<?php

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Tiny\EventManager\EventManager;
use Tiny\Router\Router;
use Tiny\ServiceManager\Factory\InvokableFactory;
use Tiny\Skeleton\Application;

return [
    'service_manager'  => [
        'shared' => [
            // event manager
            EventManager::class                      => Application\EventManager\Factory\EventManagerFactory::class,

            // http
            Tiny\Http\Request::class                 => Application\Http\Factory\RequestFactory::class,
            Tiny\Http\AbstractResponse::class        => Application\Http\Factory\ResponseFactory::class,
            Tiny\Http\ResponseHttpUtils::class       => InvokableFactory::class,

            // router
            Router::class                            => Application\Router\Factory\RouterFactory::class,

            // service
            Application\Service\ConfigService::class => InvokableFactory::class,
        ],
    ],
];
