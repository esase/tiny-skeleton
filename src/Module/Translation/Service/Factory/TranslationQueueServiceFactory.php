<?php

namespace Tiny\Skeleton\Module\Translation\Service\Factory;

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
use Tiny\Skeleton\Module\Translation\Service\TranslationQueueService;

class TranslationQueueServiceFactory
{

    /**
     * @param  ServiceManager  $serviceManager
     *
     * @return TranslationQueueService
     */
    public function __invoke(ServiceManager $serviceManager) {
        return new TranslationQueueService(
            $serviceManager->get(DbService::class)
        );
    }

}
