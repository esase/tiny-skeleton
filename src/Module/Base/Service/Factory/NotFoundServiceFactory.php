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

use Tiny\EventManager\EventManager;
use Tiny\ServiceManager\ServiceManager;
use Tiny\Skeleton\Module\Base\Service\NotFoundService;
use Tiny\Skeleton\Module\Base;

class NotFoundServiceFactory
{

    /**
     * @param  ServiceManager  $serviceManager
     *
     * @return NotFoundService
     */
    public function __invoke(ServiceManager $serviceManager
    ): NotFoundService {
        return new NotFoundService(
            $serviceManager->get(Base\Utils\ViewHelperUtils::class),
            $serviceManager->get(EventManager::class),
            php_sapi_name() === 'cli'
        );
    }

}
