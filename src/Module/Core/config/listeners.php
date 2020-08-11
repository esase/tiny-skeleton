<?php

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashevn@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Tiny\Skeleton\Module\Core;

return [
    [
        'event'    => Core\EventManager\RouteEvent::EVENT_BEFORE_MATCHING,
        'listener' => Core\EventListener\RouteBeforeCors::class,
        'priority' => -1000,
    ],
];
