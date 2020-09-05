<?php

namespace Tiny\Skeleton\Module\Base\Controller\Factory;

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Tiny\ServiceManager\ServiceManager;
use Tiny\Skeleton\Module\Base\Controller\NotFoundController;
use Tiny\Skeleton\Module\Base\Service\NotFoundService;

class NotFoundControllerFactory
{

    /**
     * @param  ServiceManager  $serviceManager
     *
     * @return NotFoundController
     */
    public function __invoke(ServiceManager $serviceManager
    ): NotFoundController {
        return new NotFoundController(
            $serviceManager->get(NotFoundService::class)
        );
    }

}

