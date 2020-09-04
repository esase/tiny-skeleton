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

use PHPUnit\Framework\TestCase;
use Tiny\Router;
use Tiny\Skeleton\Application\EventManager\RouteEvent;
use Tiny\Skeleton\Module\Core;
use Tiny\Http;

class RegisterRouteCorsListenerTest extends TestCase
{

    public function testInvokeMethod()
    {
        $requestMock = $this->createMock(Http\Request::class);
        $requestMock->expects($this->once())
            ->method('isOptions')
            ->willReturn(true);

        $routeMock = $this->createStub(
            Router\Route::class
        );
        $routeMock->expects($this->exactly(2))
            ->method('getActionList')
            ->willReturn([
                'GET' => 'index'
            ]);
        $routeMock->expects($this->once())
            ->method('setActionList')
            ->with([
                'GET' => 'index',
                'OPTIONS' => 'index'
            ]);

        $eventMock = $this->createStub(
            RouteEvent::class
        );
        $eventMock->expects($this->once())
            ->method('getData')
            ->willReturn($routeMock);

        $eventMock->expects($this->once())
            ->method('setData')
            ->with($routeMock);

        $listener = new RegisterRouteCorsListener(
            $requestMock
        );

        $listener($eventMock);
    }

}
