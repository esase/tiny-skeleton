<?php

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Tiny\Skeleton\Module\Translation;

return [
    'shared' => [
        // controller
        Translation\Controller\TranslationApiController::class     => Translation\Controller\Factory\TranslationApiControllerFactory::class,
        Translation\Controller\TranslationConsoleController::class => Translation\Controller\Factory\TranslationConsoleControllerFactory::class,

        // service
        Translation\Service\TranslationService::class              => Translation\Service\Factory\TranslationServiceFactory::class,

        // form
        Translation\Form\TranslationFormBuilder::class             => Translation\Form\Factory\TranslationFormBuilderFactory::class
    ],
];
