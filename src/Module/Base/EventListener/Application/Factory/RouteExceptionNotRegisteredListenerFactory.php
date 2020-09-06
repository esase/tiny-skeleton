<?php

namespace Tiny\Skeleton\Module\Base\EventListener\Application\Factory;

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Tiny\ServiceManager\ServiceManager;
use Tiny\Skeleton\Module\Base\EventListener\Application\RouteExceptionNotRegisteredListener;

class RouteExceptionNotRegisteredListenerFactory
{

    /**
     * @param  ServiceManager  $serviceManager
     *
     * @return RouteExceptionNotRegisteredListener
     */
    public function __invoke(ServiceManager $serviceManager
    ): RouteExceptionNotRegisteredListener {
        return new RouteExceptionNotRegisteredListener(
            php_sapi_name() === 'cli'
        );
    }

}
