<?php

namespace Tiny\Skeleton\Module\Translation\Form\Factory;

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Tiny\ServiceManager\ServiceManager;
use Tiny\Skeleton\Application\Form\Form;
use Tiny\Skeleton\Module\Language\Service\LanguageService;
use Tiny\Skeleton\Module\LanguageKey\Service\LanguageKeyService;
use Tiny\Skeleton\Module\Translation\Form\TranslationFormBuilder;
use Tiny\Skeleton\Module\Translation\Service\TranslationService;

class TranslationFormBuilderFactory
{

    /**
     * @param  ServiceManager  $serviceManager
     *
     * @return TranslationFormBuilder
     */
    public function __invoke(
        ServiceManager $serviceManager
    ) {
        return new TranslationFormBuilder (
            new Form(),
            $serviceManager->get(TranslationService::class),
            $serviceManager->get(LanguageKeyService::class),
            $serviceManager->get(LanguageService::class)
        );
    }

}
