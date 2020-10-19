<?php

namespace Tiny\Skeleton\Module\Base\Service\Factory;

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
use Tiny\Skeleton\Module\Base\Service\AuthService;

class AuthServiceFactory
{

    /**
     * @param  ServiceManager  $serviceManager
     *
     * @return AuthService
     */
    public function __invoke(ServiceManager $serviceManager) {
        return new AuthService(
            $serviceManager->get(DbService::class)
        );
    }

}
