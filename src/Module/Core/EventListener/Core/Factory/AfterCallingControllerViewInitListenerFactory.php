<?php

namespace Tiny\Skeleton\Module\Core\EventListener\Core\Factory;

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Tiny\EventManager\EventManager;
use Tiny\ServiceManager\ServiceManager;
use Tiny\Skeleton\Module\Core\EventListener\Core\AfterCallingControllerViewInitListener;
use Tiny\Skeleton\Module\Core;

class AfterCallingControllerViewInitListenerFactory
{

    /**
     * @param  ServiceManager  $serviceManager
     *
     * @return AfterCallingControllerViewInitListener
     */
    public function __invoke(ServiceManager $serviceManager
    ): AfterCallingControllerViewInitListener {
        return new AfterCallingControllerViewInitListener(
            $serviceManager->get(Core\Service\ConfigService::class),
            $serviceManager->get(EventManager::class),
            $serviceManager->get(Core\Utils\ViewHelperUtils::class)
        );
    }

}
