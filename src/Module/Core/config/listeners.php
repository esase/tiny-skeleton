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
use Tiny\Skeleton\View;

return [
    // core
    [
        'event'    => Core\EventManager\RouteEvent::EVENT_REGISTER_ROUTE,
        'listener' => Core\EventListener\Core\RegisterRouteCorsListener::class
    ],
    [
        'event'    => Core\EventManager\ControllerEvent::EVENT_BEFORE_CALLING_CONTROLLER,
        'listener' => Core\EventListener\Core\BeforeCallingControllerCorsListener::class,
        'priority' => -1000
    ],
    [
        'event'    => Core\EventManager\ControllerEvent::EVENT_BEFORE_DISPLAYING_RESPONSE,
        'listener' => Core\EventListener\Core\BeforeDisplayingResponseCorsListener::class
    ],
    [
        'event'    => Core\EventManager\ControllerEvent::EVENT_AFTER_CALLING_CONTROLLER,
        'listener' => Core\EventListener\Core\AfterCallingControllerViewInitListener::class
    ],
    [
        'event'    => View::EVENT_CALL_VIEW_HELPER . 'config',
        'listener' => Core\EventListener\ViewHelper\ViewHelperConfigListener::class
    ],
    // view helper
    [
        'event'    => View::EVENT_CALL_VIEW_HELPER . 'url',
        'listener' => Core\EventListener\ViewHelper\ViewHelperUrlListener::class
    ],
    [
        'event'    => View::EVENT_CALL_VIEW_HELPER . 'partialView',
        'listener' => Core\EventListener\ViewHelper\ViewHelperPartialViewListener::class
    ],
];
