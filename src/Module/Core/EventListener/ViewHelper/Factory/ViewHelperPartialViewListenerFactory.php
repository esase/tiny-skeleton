<?php

namespace Tiny\Skeleton\Module\Core\EventListener\ViewHelper\Factory;


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
use Tiny\Skeleton\Module\Core\EventListener\ViewHelper\ViewHelperPartialViewListener;

class ViewHelperPartialViewListenerFactory
{

    /**
     * @param  ServiceManager  $serviceManager
     *
     * @return ViewHelperPartialViewListener
     */
    public function __invoke(ServiceManager $serviceManager
    ): ViewHelperPartialViewListener {
        return new ViewHelperPartialViewListener(
            $serviceManager->get(EventManager::class)
        );
    }

}
