<?php

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashevn@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Tiny\ServiceManager\Factory\InvokableFactory;
use Tiny\Skeleton\Module\User;

return [
    'service_manager' => [
        'shared' => [
            // controller
            User\Controller\UserController::class    => User\Controller\Factory\UserControllerFactory::class,
            User\Controller\UserCliController::class => User\Controller\Factory\UserCliControllerFactory::class,

            // service
            User\Service\UserService::class          => InvokableFactory::class,
        ],
    ],
    'routes'          => [
        'http'    => [
            [
                'request'     => '/users',
                'controller'  => User\Controller\UserController::class,
                'action_list' => 'list',
            ],
        ],
        'console' => [
            [
                'request'     => 'users list',
                'controller'  => User\Controller\UserCliController::class,
                'action_list' => 'list',
            ],
        ],
    ],
];
