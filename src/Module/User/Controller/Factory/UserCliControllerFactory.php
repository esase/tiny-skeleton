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
use Tiny\Skeleton\Module\User;

class UserCliControllerFactory
{

    /**
     * @param  ServiceManager  $serviceManager
     *
     * @return User\Controller\UserCliController
     */
    public function __invoke(ServiceManager $serviceManager
    ): User\Controller\UserCliController {
        /** @var User\Service\UserService $userService */
        $userService = $serviceManager->get(
            User\Service\UserService::class
        );

        return new User\Controller\UserCliController(
            $userService
        );
    }

}
