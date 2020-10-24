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
use Tiny\Skeleton\Module\Language\Controller;

return [
    'http_api' => [
        [
            'request'     => '/api/languages',
            'controller'  => Controller\LanguageApiController::class,
            'action_list' => [
                Request::METHOD_GET  => 'list',
            ],
        ],
    ],
];
