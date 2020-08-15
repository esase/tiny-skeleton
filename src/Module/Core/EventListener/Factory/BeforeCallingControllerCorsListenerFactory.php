<?php

namespace Tiny\Skeleton\Module\Core\EventListener\Factory;

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Tiny\Http;
use Tiny\ServiceManager\ServiceManager;
use Tiny\Skeleton\Module\Core\EventListener\BeforeCallingControllerCorsListener;

class BeforeCallingControllerCorsListenerFactory
{

    /**
     * @param  ServiceManager  $serviceManager
     *
     * @return BeforeCallingControllerCorsListener
     */
    public function __invoke(ServiceManager $serviceManager
    ): BeforeCallingControllerCorsListener {
        return new BeforeCallingControllerCorsListener(
            $serviceManager->get(Http\Request::class),
            $serviceManager->get(Http\AbstractResponse::class),
            $serviceManager->get(Http\ResponseHttpUtils::class),
            ($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'] ?? null),
            ($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'] ?? null)
        );
    }

}
