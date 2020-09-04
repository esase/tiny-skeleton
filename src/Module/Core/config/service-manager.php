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
    'shared' => [
        // application listener
        Core\EventListener\Application\AfterCallingControllerViewInitListener::class => Core\EventListener\Application\Factory\AfterCallingControllerViewInitListenerFactory::class,
        Core\EventListener\Application\BeforeCallingControllerCorsListener::class    => Core\EventListener\Application\Factory\BeforeCallingControllerCorsListenerFactory::class,
        Core\EventListener\Application\RegisterRouteCorsListener::class              => Core\EventListener\Application\Factory\RegisterRouteCorsListenerFactory::class,
        Core\EventListener\Application\BeforeDisplayingResponseCorsListener::class   => Core\EventListener\Application\Factory\BeforeDisplayingResponseCorsListenerFactory::class,

        // view helper listener
        Core\EventListener\ViewHelper\ViewHelperConfigListener::class                => Core\EventListener\ViewHelper\Factory\ViewHelperConfigListenerFactory::class,
        Core\EventListener\ViewHelper\ViewHelperUrlListener::class                   => Core\EventListener\ViewHelper\Factory\ViewHelperUrlListenerFactory::class,
        Core\EventListener\ViewHelper\ViewHelperPartialViewListener::class           => Core\EventListener\ViewHelper\Factory\ViewHelperPartialViewListenerFactory::class,

        // utils
        Core\Utils\ViewHelperUtils::class                                            => Core\Utils\Factory\ViewHelperUtilsFactory::class,
    ],
];
