<?php

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Tiny\Skeleton\Module\Core;

return [
    [
        'event'    => Core\EventManager\RouteEvent::EVENT_REGISTER_ROUTE,
        'listener' => Core\EventListener\RegisterRouteCorsListener::class
    ],
    [
        'event'    => Core\EventManager\ControllerEvent::EVENT_BEFORE_CALLING_CONTROLLER,
        'listener' => Core\EventListener\BeforeCallingControllerCorsListener::class,
        'priority' => -1000
    ],
    [
        'event'    => Core\EventManager\ControllerEvent::EVENT_BEFORE_DISPLAYING_RESPONSE,
        'listener' => Core\EventListener\BeforeDisplayingResponseCorsListener::class
    ],
];
