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

use PHPUnit\Framework\TestCase;
use Tiny\Router;
use Tiny\Skeleton\Application\EventManager\RouteEvent;
use Tiny\Skeleton\Module\Base\Controller\NotFoundController;

class RouteExceptionNotRegisteredListenerTest extends TestCase
{

    public function testInvokeMethod()
    {
        $eventMock = $this->createMock(RouteEvent::class);
        $eventMock->expects($this->once())
            ->method('setData')
            ->with(
                $this->isInstanceOf(Router\Route::class)
            )
            ->will(
                $this->returnCallback(
                    function (Router\Route $route) use ($eventMock) {
                        // make sure the Route is constructed properly
                        $this->assertEquals(
                            '',
                            $route->getRequest()
                        );

                        $this->assertEquals(
                            NotFoundController::class,
                            $route->getController()
                        );

                        $this->assertEquals(
                            'index',
                            $route->getActionList()
                        );

                        $this->assertEquals(
                            'index',
                            $route->getMatchedAction()
                        );

                        return $eventMock;
                    }
                )
            );

        $listener = new RouteExceptionNotRegisteredListener(false);

        $listener($eventMock);
    }

}
