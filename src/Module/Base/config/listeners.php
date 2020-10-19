<?php

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Tiny\Skeleton\Application\EventManager;
use Tiny\Skeleton\Module\Base\EventListener;
use Tiny\View\View;

return [
    // application
    [
        'event'    => EventManager\RouteEvent::EVENT_REGISTER_ROUTE,
        'listener' => EventListener\Application\RegisterRouteCorsListener::class,
    ],
    [
        'event'    => EventManager\ControllerEvent::EVENT_BEFORE_CALLING_CONTROLLER,
        'listener' => EventListener\Application\BeforeCallingControllerCorsListener::class,
        'priority' => -1000,
    ],
    [
        'event'    => EventManager\ControllerEvent::EVENT_BEFORE_CALLING_CONTROLLER,
        'listener' => EventListener\Application\BeforeCallingControllerAuthListener::class,
    ],
    [
        'event'    => EventManager\ControllerEvent::EVENT_BEFORE_DISPLAYING_RESPONSE,
        'listener' => EventListener\Application\BeforeDisplayingResponseCorsListener::class,
    ],
    [
        'event'    => EventManager\ControllerEvent::EVENT_AFTER_CALLING_CONTROLLER,
        'listener' => EventListener\Application\AfterCallingControllerViewInitListener::class,
    ],
    [
        'event'    => EventManager\ControllerEvent::EVENT_CONTROLLER_EXCEPTION,
        'listener' => EventListener\Application\ControllerExceptionNotFoundListener::class,
    ],
    [
        'event'    => EventManager\RouteEvent::EVENT_ROUTE_EXCEPTION,
        'listener' => EventListener\Application\RouteExceptionNotRegisteredListener::class,
    ],
    // view helper
    [
        'event'    => View::EVENT_CALL_VIEW_HELPER.'config',
        'listener' => EventListener\ViewHelper\ViewHelperConfigListener::class,
    ],
    [
        'event'    => View::EVENT_CALL_VIEW_HELPER.'url',
        'listener' => EventListener\ViewHelper\ViewHelperUrlListener::class,
    ],
    [
        'event'    => View::EVENT_CALL_VIEW_HELPER.'partialView',
        'listener' => EventListener\ViewHelper\ViewHelperPartialViewListener::class,
    ],
];
