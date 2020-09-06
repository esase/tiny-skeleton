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

use Tiny\EventManager\EventManager;
use Tiny\ServiceManager\ServiceManager;
use Tiny\Skeleton\Application\Service\ConfigService;
use Tiny\Skeleton\Module\Base\EventListener\Application\AfterCallingControllerViewInitListener;
use Tiny\Skeleton\Module\Base\Utils\ViewHelperUtils;

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
            $serviceManager->get(ConfigService::class),
            $serviceManager->get(EventManager::class),
            $serviceManager->get(ViewHelperUtils::class)
        );
    }

}
