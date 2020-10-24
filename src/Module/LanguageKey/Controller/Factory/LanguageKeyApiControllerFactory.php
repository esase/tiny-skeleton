<?php

namespace Tiny\Skeleton\Module\LanguageKey\Controller\Factory;

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Tiny\ServiceManager\ServiceManager;
use Tiny\Skeleton\Module\LanguageKey\Controller\LanguageKeyApiController;
use Tiny\Skeleton\Module\LanguageKey\Form\LanguageKeyFormBuilder;
use Tiny\Skeleton\Module\LanguageKey\Service\LanguageKeyService;

class LanguageKeyApiControllerFactory
{

    /**
     * @param  ServiceManager  $serviceManager
     *
     * @return LanguagekeyApiController
     */
    public function __invoke(
        ServiceManager $serviceManager
    ) {
        return new LanguagekeyApiController (
            $serviceManager->get(LanguageKeyService::class),
            $serviceManager->get(LanguageKeyFormBuilder::class)
        );
    }

}
