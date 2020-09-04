<?php

namespace Tiny\Skeleton\Module\Core\EventListener\Core;

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Tiny\Skeleton\Application\EventManager\RouteEvent;
use Tiny\Skeleton\Module\Core;
use Tiny\Http;
use Tiny\Router;

class RegisterRouteCorsListener
{

    /**
     * @var Http\Request
     */
    private Http\Request $request;

    /**
     * RegisterRouteCorsListener constructor.
     *
     * @param  Http\Request  $request
     */
    public function __construct(Http\Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param RouteEvent  $event
     */
    public function __invoke(RouteEvent $event)
    {
        if ($this->request->isOptions()) {
            /** @var Router\Route $route */
            $route = $event->getData();

            // assign the 'OPTIONS' method to each route
            if (is_array($route->getActionList())) {
                $route->setActionList(
                    array_merge(
                        $route->getActionList(), [
                        Http\Request::METHOD_OPTIONS => 'index',
                    ]
                    )
                );

                $event->setData($route);
            }
        }
    }

}
