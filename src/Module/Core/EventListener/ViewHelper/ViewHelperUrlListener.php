<?php

namespace Tiny\Skeleton\Module\Core\EventListener\ViewHelper;

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Tiny\EventManager\Event;
use Tiny\Skeleton\Module\Core;
use Tiny\Router;

class ViewHelperUrlListener
{

    /**
     * @var Router\Router
     */
    private Router\Router $router;

    /**
     * ViewHelperUrlListener constructor.
     *
     * @param  Router\Router  $router
     */
    public function __construct(
        Router\Router $router
    ) {
        $this->router = $router;
    }

    /**
     * @param  Event  $event
     */
    public function __invoke(Event $event)
    {
        $arguments = $event->getParams()['arguments'];
        list($controller, $action, $module) = $arguments;

        $event->setData(
            $this->router->assembleRequest(
                vsprintf('Tiny\Skeleton\Module\%s\Controller\%s', [
                    $module,
                    $controller
                ]),
                $action,
                ($arguments[3] ?? [])
            )
        );
    }

}
