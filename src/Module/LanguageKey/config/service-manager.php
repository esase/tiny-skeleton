<?php

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Tiny\ServiceManager\Factory\InvokableFactory;
use Tiny\Skeleton\Module\LanguageKey;

return [
    'shared' => [
        // controller
        LanguageKey\Controller\LanguageKeyApiController::class => LanguageKey\Controller\Factory\LanguageKeyApiControllerFactory::class,

        // service
        LanguageKey\Service\LanguageKeyService::class          => LanguageKey\Service\Factory\LanguageKeyServiceFactory::class,

        // form
        LanguageKey\Form\LanguageKeyBuilder::class             => LanguageKey\Form\Factory\LanguageKeyBuilderFactory::class
    ],
];
