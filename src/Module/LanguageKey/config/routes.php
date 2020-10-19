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
use Tiny\Skeleton\Module\LanguageKey\Controller;

return [
    'http'     => [
    ],
    'http_api' => [
        [
            'request'     => '/api/keys',
            'controller'  => Controller\LanguageKeyApiController::class,
            'action_list' => [
                Request::METHOD_GET  => 'list',
                Request::METHOD_POST  => 'create',
            ],
        ],
        [
            'request'     => '|^/api/keys/(?P<id>\d+)$|i',
            'controller'  => Controller\LanguageKeyApiController::class,
            'action_list' => [
                Request::METHOD_DELETE  => 'delete',
            ],
            'type' => 'regexp',
            'request_params' => ['id'],
            'spec' => '/api/keys/%id%',
        ],
    ],
    'console'  => [
    ],
];
