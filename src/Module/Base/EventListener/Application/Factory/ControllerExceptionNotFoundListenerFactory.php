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
use Tiny\Skeleton\Module\Base\EventListener\Application\ControllerExceptionNotFoundListener;
use Tiny\Skeleton\Module\Base\Service\NotFoundService;
use Tiny\Http\AbstractResponse;

class ControllerExceptionNotFoundListenerFactory
{

    /**
     * @param  ServiceManager  $serviceManager
     *
     * @return ControllerExceptionNotFoundListener
     */
    public function __invoke(ServiceManager $serviceManager
    ): ControllerExceptionNotFoundListener {
        return new ControllerExceptionNotFoundListener(
            $serviceManager->get(AbstractResponse::class),
            $serviceManager->get(NotFoundService::class)
        );
    }

}
