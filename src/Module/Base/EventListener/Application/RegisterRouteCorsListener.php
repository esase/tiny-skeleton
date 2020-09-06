<?php

namespace Tiny\Skeleton\Module\Base\EventListener\Application;

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Tiny\Skeleton\Application\EventManager\RouteEvent;
use Tiny\Http\Request;
use Tiny\Router\Route;

class RegisterRouteCorsListener
{

    /**
     * @var Request
     */
    private Request $request;

    /**
     * RegisterRouteCorsListener constructor.
     *
     * @param  Request  $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param  RouteEvent  $event
     */
    public function __invoke(RouteEvent $event)
    {
        if ($this->request->isOptions()) {
            /** @var Route $route */
            $route = $event->getData();

            // assign the 'OPTIONS' method to each route
            if (is_array($route->getActionList())) {
                $route->setActionList(
                    array_merge(
                        $route->getActionList(), [
                            Request::METHOD_OPTIONS => 'index',
                        ]
                    )
                );

                $event->setData($route);
            }
        }
    }

}
