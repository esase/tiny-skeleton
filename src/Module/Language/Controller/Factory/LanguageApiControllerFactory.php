<?php

namespace Tiny\Skeleton\Module\Language\Controller\Factory;

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Tiny\ServiceManager\ServiceManager;
use Tiny\Skeleton\Module\Language\Controller\LanguageApiController;
use Tiny\Skeleton\Module\Language\Service\LanguageService;

class LanguageApiControllerFactory
{

    /**
     * @param  ServiceManager  $serviceManager
     *
     * @return object
     */
    public function __invoke(
        ServiceManager $serviceManager
    ) {
        return new LanguageApiController (
            $serviceManager->get(LanguageService::class)
        );
    }

}
