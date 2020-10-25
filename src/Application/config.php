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
    'db'                      => [
        'host'     => 'mysql',
        'username' => 'root',
        'password' => 'tiny-skeleton-root',
        'db_name'  => 'tiny-skeleton-db',
    ],
    'data_dir'                => dirname(__DIR__, 2) . '/data/',
    'google_translate_config' => 'google_translate.json',
    'service_manager'         => [
        'shared' => [
            // event manager
            EventManager::class                              => Application\EventManager\Factory\EventManagerFactory::class,

            // http
            Tiny\Http\Request::class                         => Application\Http\Factory\RequestFactory::class,
            Tiny\Http\AbstractResponse::class                => Application\Http\Factory\ResponseFactory::class,
            Tiny\Http\ResponseHttpUtils::class               => InvokableFactory::class,

            // router
            Router::class                                    => Application\Router\Factory\RouterFactory::class,

            // service
            Application\Service\ConfigService::class         => InvokableFactory::class,
            Application\Service\DbService::class             => Application\Service\Factory\DbServiceFactory::class,
            Application\Service\TranslationApiService::class => Application\Service\Factory\TranslationApiServiceFactory::class,

            // utility
            Application\Utility\ZipUtility::class            => InvokableFactory::class,
        ],
    ],
];
