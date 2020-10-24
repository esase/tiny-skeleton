<?php

namespace Tiny\Skeleton\Module\LanguageKey\Form\Factory;

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
use Tiny\Skeleton\Module\LanguageKey\Form\LanguageKeyFormBuilder;
use Tiny\Skeleton\Module\LanguageKey\Service\LanguageKeyService;

class LanguageKeyFormBuilderFactory
{

    /**
     * @param  ServiceManager  $serviceManager
     *
     * @return LanguageKeyFormBuilder
     */
    public function __invoke(
        ServiceManager $serviceManager
    ) {
        return new LanguageKeyFormBuilder (
            new Form(),
            $serviceManager->get(LanguageKeyService::class)
        );
    }

}
