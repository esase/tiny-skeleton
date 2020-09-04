<?php

namespace Tiny\Skeleton\Module\Core\EventListener\Application\Factory;

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
use Tiny\Skeleton\Module\Core\EventListener\Application\BeforeDisplayingResponseCorsListener;

class BeforeDisplayingResponseCorsListenerFactory
{

    /**
     * @param  ServiceManager  $serviceManager
     *
     * @return BeforeDisplayingResponseCorsListener
     */
    public function __invoke(ServiceManager $serviceManager
    ): BeforeDisplayingResponseCorsListener {
        return new BeforeDisplayingResponseCorsListener(
            $serviceManager->get(Http\Request::class),
            $serviceManager->get(Http\ResponseHttpUtils::class),
            ($_SERVER['HTTP_ORIGIN'] ?? null)
        );
    }

}
