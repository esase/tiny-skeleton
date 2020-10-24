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
use Tiny\Skeleton\Module\Translation\Controller\TranslationApiController;
use Tiny\Skeleton\Module\Translation\Form\TranslationFormBuilder;
use Tiny\Skeleton\Module\Translation\Service\TranslationService;

class TranslationApiControllerFactory
{

    /**
     * @param  ServiceManager  $serviceManager
     *
     * @return TranslationApiController
     */
    public function __invoke(
        ServiceManager $serviceManager
    ) {
        return new TranslationApiController (
            $serviceManager->get(TranslationService::class),
            $serviceManager->get(TranslationFormBuilder::class)
        );
    }

}
