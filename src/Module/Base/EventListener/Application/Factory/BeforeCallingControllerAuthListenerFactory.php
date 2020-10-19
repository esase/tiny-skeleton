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

use Tiny\Http\AbstractResponse;
use Tiny\ServiceManager\ServiceManager;
use Tiny\Skeleton\Module\Base\EventListener\Application\BeforeCallingControllerAuthListener;
use Tiny\Skeleton\Module\Base\Service\AuthService;

class BeforeCallingControllerAuthListenerFactory
{

    /**
     * @param  ServiceManager  $serviceManager
     *
     * @return BeforeCallingControllerAuthListener
     */
    public function __invoke(ServiceManager $serviceManager
    ): BeforeCallingControllerAuthListener {
        return new BeforeCallingControllerAuthListener(
            $serviceManager->get(AuthService::class),
            $serviceManager->get(AbstractResponse::class)
        );
    }

}
