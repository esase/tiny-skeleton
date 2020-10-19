<?php

namespace Tiny\Skeleton\Module\Language\Service\Factory;

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
use Tiny\Skeleton\Module\Language\Service\LanguageService;

class LanguageServiceFactory
{

    /**
     * @param  ServiceManager  $serviceManager
     *
     * @return LanguageService
     */
    public function __invoke(ServiceManager $serviceManager) {
        return new LanguageService(
            $serviceManager->get(DbService::class)
        );
    }

}
