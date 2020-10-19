<?php

namespace Tiny\Skeleton\Module\LanguageKey\Service\Factory;

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
use Tiny\Skeleton\Module\LanguageKey\Service\LanguageKeyService;

class LanguageKeyServiceFactory
{

    /**
     * @param  ServiceManager  $serviceManager
     *
     * @return LanguageKeyService
     */
    public function __invoke(ServiceManager $serviceManager) {
        return new LanguageKeyService(
            $serviceManager->get(DbService::class)
        );
    }

}
