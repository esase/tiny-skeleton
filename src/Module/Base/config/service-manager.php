<?php

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashevn@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Tiny\Skeleton\Module\Base;
use Tiny\ServiceManager\Factory\InvokableFactory;
use Tiny\Skeleton\Module\Base\Http;
use Tiny\Skeleton\Module\Base\Router;
use Tiny\Skeleton\Module\Base\Controller;

return [
    'shared' => [
        // router
        Tiny\Router\Router::class         => Router\Factory\RouterFactory::class,

        // http
        Tiny\Http\Request::class          => Http\Factory\RequestFactory::class,
        Tiny\Http\AbstractResponse::class => Http\Factory\ResponseFactory::class,

        // service
        Base\Service\ConfigService::class => InvokableFactory::class,

        // controller
        Controller\HomeController::class  => InvokableFactory::class,
    ],
];
