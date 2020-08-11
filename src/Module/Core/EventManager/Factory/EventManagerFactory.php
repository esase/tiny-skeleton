<?php

namespace Tiny\Skeleton\Module\Core\EventManager\Factory;

use Tiny\EventManager\EventManager;
use Tiny\ServiceManager\ServiceManager;

class EventManagerFactory
{

    /**
     * @param  ServiceManager  $serviceManager
     *
     * @return EventManager
     */
    public function __invoke(ServiceManager $serviceManager): EventManager
    {
        return new EventManager(
            $serviceManager
        );
    }

}
