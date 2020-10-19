<?php

namespace Tiny\Skeleton\Module\User\Service\Factory;

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Tiny\ServiceManager\ServiceManager;
use Tiny\Skeleton\Application\Service\DbService;
use Tiny\Skeleton\Module\User\Service\UserService;

class UserServiceFactory
{

    /**
     * @param  ServiceManager  $serviceManager
     *
     * @return object
     */
    public function __invoke(ServiceManager $serviceManager) {
        return new UserService(
            $serviceManager->get(DbService::class)
        );
    }

}
