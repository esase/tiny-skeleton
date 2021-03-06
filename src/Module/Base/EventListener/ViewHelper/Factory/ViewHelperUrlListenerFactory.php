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
use Tiny\Router\Router;
use Tiny\Skeleton\Module\Base\EventListener\ViewHelper\ViewHelperUrlListener;
use Tiny\Skeleton\Module\Base\Utils\ViewHelperUtils;

class ViewHelperUrlListenerFactory
{

    /**
     * @param  ServiceManager  $serviceManager
     *
     * @return ViewHelperUrlListener
     */
    public function __invoke(ServiceManager $serviceManager
    ): ViewHelperUrlListener {
        return new ViewHelperUrlListener(
            $serviceManager->get(Router::class),
            $serviceManager->get(ViewHelperUtils::class)
        );
    }

}
