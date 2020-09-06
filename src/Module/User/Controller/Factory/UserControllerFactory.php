<?php

namespace Tiny\Skeleton\Module\User\Controller\Factory;

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Tiny\ServiceManager\ServiceManager;
use Tiny\Skeleton\Module\User\Service\UserService;

class UserControllerFactory
{

    /**
     * @param  ServiceManager  $serviceManager
     * @param  string          $targetClass
     *
     * @return object
     */
    public function __invoke(
        ServiceManager $serviceManager,
        string $targetClass
    ) {
        return new $targetClass (
            $serviceManager->get(
                UserService::class
            )
        );
    }

}
