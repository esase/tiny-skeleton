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
use Tiny\Skeleton\Application\Service\ConfigService;
use Tiny\Skeleton\Application\Service\DbService;
use Tiny\Skeleton\Application\Utility\ZipUtility;
use Tiny\Skeleton\Module\Translation\Service\TranslationQueueService;
use Tiny\Skeleton\Module\Translation\Service\TranslationService;

class TranslationServiceFactory
{

    /**
     * @param  ServiceManager  $serviceManager
     *
     * @return TranslationService
     */
    public function __invoke(ServiceManager $serviceManager) {
        return new TranslationService(
            $serviceManager->get(DbService::class),
            $serviceManager->get(TranslationQueueService::class),
            $serviceManager->get(ConfigService::class),
            $serviceManager->get(ZipUtility::class)
        );
    }

}
