<?php

namespace Tiny\Skeleton\Module\Core\EventListener\Factory;

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashevn@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Tiny\Http;
use Tiny\ServiceManager\ServiceManager;
use Tiny\Skeleton\Module\Core\EventListener\RegisterRouteCorsListener;

class RegisterRouteCorsListenerFactory
{

    /**
     * @param  ServiceManager  $serviceManager
     *
     * @return RegisterRouteCorsListener
     */
    public function __invoke(ServiceManager $serviceManager
    ): RegisterRouteCorsListener {
        return new RegisterRouteCorsListener(
            $serviceManager->get(Http\Request::class)
        );
    }

}
