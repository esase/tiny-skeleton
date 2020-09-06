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
use Tiny\Router;
use Tiny\Skeleton\Module\Base\Controller\NotFoundController;

class RouteExceptionNotRegisteredListener
{

    /**
     * @var bool
     */
    private bool $isCliContext;

    /**
     * RouteExceptionNotRegisteredListener constructor.
     *
     * @param  bool  $isCliContext
     */
    public function __construct(bool $isCliContext)
    {
        $this->isCliContext = $isCliContext;
    }

    /**
     * @param  RouteEvent  $event
     */
    public function __invoke(RouteEvent $event)
    {
        // by default the 'NotFoundController' will be assigned for all non existing routes
        if (!$this->isCliContext) {
            $route = new Router\Route(
                '',
                NotFoundController::class,
                'index'
            );
            $route->setMatchedAction('index');

            $event->setData(
                $route
            );
        }
    }

}
