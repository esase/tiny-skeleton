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

return [
    'service_manager' => [
        'shared' => [
            // service
            Base\Service\ConfigService::class => InvokableFactory::class
        ]
    ]
];
