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
use Tiny\Skeleton\Module\Base;
use Tiny\View\View;

return [
    // application
    [
        'event'    => EventManager\RouteEvent::EVENT_REGISTER_ROUTE,
        'listener' => Base\EventListener\Application\RegisterRouteCorsListener::class,
    ],
    [
        'event'    => EventManager\ControllerEvent::EVENT_BEFORE_CALLING_CONTROLLER,
        'listener' => Base\EventListener\Application\BeforeCallingControllerCorsListener::class,
        'priority' => -1000,
    ],
    [
        'event'    => EventManager\ControllerEvent::EVENT_BEFORE_DISPLAYING_RESPONSE,
        'listener' => Base\EventListener\Application\BeforeDisplayingResponseCorsListener::class,
    ],
    [
        'event'    => EventManager\ControllerEvent::EVENT_AFTER_CALLING_CONTROLLER,
        'listener' => Base\EventListener\Application\AfterCallingControllerViewInitListener::class,
    ],
    // view helper
    [
        'event'    => View::EVENT_CALL_VIEW_HELPER.'config',
        'listener' => Base\EventListener\ViewHelper\ViewHelperConfigListener::class,
    ],
    [
        'event'    => View::EVENT_CALL_VIEW_HELPER.'url',
        'listener' => Base\EventListener\ViewHelper\ViewHelperUrlListener::class,
    ],
    [
        'event'    => View::EVENT_CALL_VIEW_HELPER.'partialView',
        'listener' => Base\EventListener\ViewHelper\ViewHelperPartialViewListener::class,
    ],
];
