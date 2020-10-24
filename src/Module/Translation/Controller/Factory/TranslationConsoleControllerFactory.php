<?php

namespace Tiny\Skeleton\Module\Translation\Controller\Factory;

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Tiny\ServiceManager\ServiceManager;
use Tiny\Skeleton\Module\Translation\Controller\TranslationConsoleController;
use Tiny\Skeleton\Module\Translation\Service\TranslationService;

class TranslationConsoleControllerFactory
{

    /**
     * @param  ServiceManager  $serviceManager
     *
     * @return TranslationConsoleController
     */
    public function __invoke(
        ServiceManager $serviceManager
    ) {
        return new TranslationConsoleController (
            $serviceManager->get(TranslationService::class)
        );
    }

}
