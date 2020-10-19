<?php

namespace Tiny\Skeleton\Application\Service\Factory;

use Tiny\ServiceManager\ServiceManager;
use Tiny\Skeleton\Application\Service\ConfigService;
use Tiny\Skeleton\Application\Service\DbService;

class DbServiceFactory
{

    /**
     * @param  ServiceManager  $serviceManager
     *
     * @return DbService
     */
    public function __invoke(ServiceManager $serviceManager): DbService
    {
        /** @var ConfigService $config */
        $config = $serviceManager->get(ConfigService::class);

        return new DbService(
            $config->getConfig('db')['host'],
            $config->getConfig('db')['username'],
            $config->getConfig('db')['password'],
            $config->getConfig('db')['db_name'],
        );
    }

}
