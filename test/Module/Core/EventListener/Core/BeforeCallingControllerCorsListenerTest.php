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
use Tiny\Http;
use Tiny\Router;
use Tiny\Skeleton\Module\Core;

class BeforeCallingControllerCorsListenerTest extends TestCase
{

    public function testInvokeMethod()
    {
        $requestMock = $this->createMock(Http\Request::class);
        $requestMock->expects($this->once())
            ->method('isOptions')
            ->willReturn(true);

        $httpUtilsMock = $this->createStub(Http\ResponseHttpUtils::class);
        $httpUtilsMock->expects($this->once())
            ->method('sendHeaders')
            ->with([
                'Access-Control-Allow-Methods: GET, POST',
                'Access-Control-Allow-Headers: Auth, Test'
            ]);

        $responseStub = $this->createStub(Http\AbstractResponse::class);

        $listener = new BeforeCallingControllerCorsListener(
            $requestMock,
            $responseStub,
            $httpUtilsMock,
            'OPTIONS',
            'Auth, Test'
        );

        $routeMock = $this->createStub(
            Router\Route::class
        );
        $routeMock->expects($this->exactly(2))
            ->method('getActionList')
            ->willReturn([
                'GET' => 'list',
                'POST' => 'create'
            ]);

        $eventMock = $this->createStub(
            Core\EventManager\ControllerEvent::class
        );
        $eventMock->expects($this->once())
            ->method('getParams')
            ->willReturn([
                'route' => $routeMock
            ]);

        $eventMock->expects($this->once())
            ->method('setData')
            ->with($responseStub);

        $listener($eventMock);
    }

    public function testInvokeMethodUsingAllHttpMethods()
    {
        $requestMock = $this->createMock(Http\Request::class);
        $requestMock->expects($this->once())
            ->method('isOptions')
            ->willReturn(true);

        $httpUtilsMock = $this->createStub(Http\ResponseHttpUtils::class);
        $httpUtilsMock->expects($this->once())
            ->method('sendHeaders')
            ->with([
                'Access-Control-Allow-Methods: GET, POST, DELETE, PUT, OPTIONS',
                'Access-Control-Allow-Headers: Auth, Test, Other'
            ]);

        $responseStub = $this->createStub(Http\AbstractResponse::class);

        $listener = new BeforeCallingControllerCorsListener(
            $requestMock,
            $responseStub,
            $httpUtilsMock,
            'OPTIONS',
            'Auth, Test, Other'
        );

        $routeMock = $this->createStub(
            Router\Route::class
        );
        $routeMock->expects($this->once())
            ->method('getActionList')
            ->willReturn('index'); // we don't specify http method, we are expecting the route is working with all http methods

        $eventMock = $this->createStub(
            Core\EventManager\ControllerEvent::class
        );
        $eventMock->expects($this->once())
            ->method('getParams')
            ->willReturn([
                'route' => $routeMock
            ]);

        $eventMock->expects($this->once())
            ->method('setData')
            ->with($responseStub);

        $listener($eventMock);
    }

}
