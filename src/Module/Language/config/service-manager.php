<?php

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Tiny\Skeleton\Module\Language;

return [
    'shared' => [
        // controller
        Language\Controller\LanguageApiController::class => Language\Controller\Factory\LanguageApiControllerFactory::class,

        // service
        Language\Service\LanguageService::class          => Language\Service\Factory\LanguageServiceFactory::class,
    ],
];
