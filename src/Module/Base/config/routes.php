<?php

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashevn@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Tiny\Skeleton\Module\Base\Controller;

return [
    'http' => [
        [
            'request'     => '/',
            'controller'  => Controller\HomeController::class,
            'action_list' => 'index',
        ],
    ],
];
