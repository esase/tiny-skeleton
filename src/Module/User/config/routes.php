<?php

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Tiny\Http\Request;
use Tiny\Skeleton\Module\User;

return [
    'http'    => [
        [
            'request'     => '/users',
            'controller'  => User\Controller\UserController::class,
            'action_list' => [
                Request::METHOD_GET => 'list',
                Request::METHOD_POST => 'create'
            ],
        ],
        [
            'request'     => '/api/users',
            'controller'  => User\Controller\UserApiController::class,
            'action_list' => [
                Request::METHOD_GET => 'list',
                Request::METHOD_POST => 'create'
            ],
        ],
    ],
    'console' => [
        [
            'request'     => 'users list',
            'controller'  => User\Controller\UserCliController::class,
            'action_list' => 'list',
        ],
    ],
];
