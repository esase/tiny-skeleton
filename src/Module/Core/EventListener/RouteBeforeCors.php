<?php

namespace Tiny\Skeleton\Module\Core\EventListener;

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashevn@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Tiny\Skeleton\Module\Core;
use Tiny\Router;

use Tiny\Skeleton\Module\User\Controller\UserController;

class RouteBeforeCors
{

    public function __invoke(Core\EventManager\RouteEvent $event)
    {
        $route = new Router\Route(
            'test',
            UserController::class,
            'index'
        );
        $route->setMatchedAction('liast');

        $event->setData($route)->setStopped(true);
    }

}