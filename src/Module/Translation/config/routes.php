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
use Tiny\Skeleton\Module\Translation\Controller;

return [
    'http_api' => [
        [
            'request'     => '/api/translations',
            'controller'  => Controller\TranslationApiController::class,
            'action_list' => [
                Request::METHOD_GET  => 'list',
                Request::METHOD_POST  => 'create',
            ],
        ],
        [
            'request'     => '|^/api/translations/(?P<id>\d+)$|i',
            'controller'  => Controller\TranslationApiController::class,
            'action_list' => [
                Request::METHOD_DELETE  => 'delete',
                Request::METHOD_PUT  => 'update'
            ],
            'type' => 'regexp',
            'request_params' => ['id'],
            'spec' => '/api/translations/%id%',
        ],
        [
            'request'     => '|^/api/translations/export/(?P<id>\d+)$|i',
            'controller'  => Controller\TranslationApiController::class,
            'action_list' => [
                Request::METHOD_GET  => 'export',
            ],
            'type' => 'regexp',
            'request_params' => ['id'],
            'spec' => '/api/translations/export/%format%',
        ],
    ],
    'console' => [
        [
            'request'     => 'translations automatic',
            'controller'  => Controller\TranslationConsoleController::class,
            'action_list' => 'automaticTranslate',
        ],
    ]
];
