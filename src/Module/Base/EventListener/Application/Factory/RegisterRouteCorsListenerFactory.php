<?php

namespace Tiny\Skeleton\Module\Base\EventListener\Application\Factory;

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Tiny\Http\Request;
use Tiny\ServiceManager\ServiceManager;
use Tiny\Skeleton\Module\Base\EventListener\Application\RegisterRouteCorsListener;

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
            $serviceManager->get(Request::class)
        );
    }

}
