<?php

namespace Tiny\Skeleton\Module\Base\EventListener\ViewHelper\Factory;


/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Tiny\ServiceManager\ServiceManager;
use Tiny\Skeleton\Application\Service\ConfigService;
use Tiny\Skeleton\Module\Base;
use Tiny\Skeleton\Module\Base\EventListener\ViewHelper\ViewHelperConfigListener;

class ViewHelperConfigListenerFactory
{

    /**
     * @param  ServiceManager  $serviceManager
     *
     * @return ViewHelperConfigListener
     */
    public function __invoke(ServiceManager $serviceManager
    ): ViewHelperConfigListener {
        return new ViewHelperConfigListener(
            $serviceManager->get(ConfigService::class)
        );
    }

}
