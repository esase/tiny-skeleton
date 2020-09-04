<?php

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Tiny\Skeleton\Module\Base;

return [
    'shared' => [
        // application listener
        Base\EventListener\Application\AfterCallingControllerViewInitListener::class => Base\EventListener\Application\Factory\AfterCallingControllerViewInitListenerFactory::class,
        Base\EventListener\Application\BeforeCallingControllerCorsListener::class    => Base\EventListener\Application\Factory\BeforeCallingControllerCorsListenerFactory::class,
        Base\EventListener\Application\RegisterRouteCorsListener::class              => Base\EventListener\Application\Factory\RegisterRouteCorsListenerFactory::class,
        Base\EventListener\Application\BeforeDisplayingResponseCorsListener::class   => Base\EventListener\Application\Factory\BeforeDisplayingResponseCorsListenerFactory::class,

        // view helper listener
        Base\EventListener\ViewHelper\ViewHelperConfigListener::class                => Base\EventListener\ViewHelper\Factory\ViewHelperConfigListenerFactory::class,
        Base\EventListener\ViewHelper\ViewHelperUrlListener::class                   => Base\EventListener\ViewHelper\Factory\ViewHelperUrlListenerFactory::class,
        Base\EventListener\ViewHelper\ViewHelperPartialViewListener::class           => Base\EventListener\ViewHelper\Factory\ViewHelperPartialViewListenerFactory::class,

        // utils
        Base\Utils\ViewHelperUtils::class                                            => Base\Utils\Factory\ViewHelperUtilsFactory::class,
    ],
];
