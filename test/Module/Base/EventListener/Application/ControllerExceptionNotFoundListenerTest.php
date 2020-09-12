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
use Tiny\EventManager\EventManager;
use Tiny\Http\AbstractResponse;
use Tiny\Router\Route;
use Tiny\Skeleton\Application\Bootstrapper;
use Tiny\Skeleton\Application\EventManager\ControllerEvent;
use Tiny\Skeleton\Application\Exception\Request\NotFoundException;
use Tiny\Skeleton\Module\Base\Utils\ViewHelperUtils;
use Tiny\View\View;

class ControllerExceptionNotFoundListenerTest extends TestCase
{

    public function testInvokeMethodUsingJsonErrorResponse()
    {
        $exception = new NotFoundException();
        $routeMock = $this->createMock(Route::class);
        $routeMock->expects($this->once())
            ->method('getContext')
            ->willReturn(Bootstrapper::ROUTE_CONTEXT_CLI);

        $responseMock = $this->createMock(AbstractResponse::class);
        $responseMock->expects($this->once())
            ->method('setResponse')
            ->with(json_encode(
                [
                    'error' => 'Not found',
                    'code'  => 404,
                ]
            ))
            ->will($this->returnSelf());
        $responseMock->expects($this->once())
            ->method('setCode')
            ->with(200)
            ->will($this->returnSelf());
        $responseMock->expects($this->once())
            ->method('setResponseType')
            ->with('application/json')
            ->will($this->returnSelf());

        $eventMock = $this->createMock(ControllerEvent::class);
        $eventMock->expects($this->once())
            ->method('getParams')
            ->willReturn([
                'exception' => $exception,
                'route' => $routeMock
            ]);
        $eventMock->expects($this->once())
            ->method('setData')
            ->with($responseMock);

        $listener = new ControllerExceptionNotFoundListener(
            $responseMock,
            $this->createStub(EventManager::class),
            $this->createStub(ViewHelperUtils::class)
        );

        $listener($eventMock);
    }

    public function testInvokeMethodUsingViewErrorResponse()
    {
        $eventManagerStub = $this->createStub(EventManager::class);
        $exception = new NotFoundException('Test error');
        $routeMock = $this->createMock(Route::class);
        $routeMock->expects($this->once())
            ->method('getContext')
            ->willReturn(Bootstrapper::ROUTE_CONTEXT_HTTP);

        $responseMock = $this->createMock(AbstractResponse::class);
        $responseMock->expects($this->once())
            ->method('setResponse')
            ->with($this->isInstanceOf(View::class))
            ->will(
                $this->returnCallback(
                    function (View $view) use ($responseMock, $eventManagerStub) {
                        // make sure the View is constructed properly
                        $this->assertEquals(
                            'test_template',
                            $view->getTemplatePath()
                        );

                        $this->assertEquals(
                            'test_template',
                            $view->getLayoutPath()
                        );

                        $this->assertEquals(
                            ['message' => 'Test error'],
                            $view->getVariables()
                        );

                        $this->assertSame(
                            $eventManagerStub,
                            $view->getEventManager()
                        );

                        return $responseMock;
                    }
                )
            );

        $responseMock->expects($this->once())
            ->method('setCode')
            ->with(404)
            ->will($this->returnSelf());
        $responseMock->expects($this->once())
            ->method('setResponseType')
            ->with('text/html')
            ->will($this->returnSelf());

        $eventMock = $this->createMock(ControllerEvent::class);
        $eventMock->expects($this->once())
            ->method('getParams')
            ->willReturn([
                'exception' => $exception,
                'route' => $routeMock
            ]);
        $eventMock->expects($this->once())
            ->method('setData')
            ->with($responseMock);

        $viewHelperUtilsMock = $this->createMock(
            ViewHelperUtils::class
        );
        $viewHelperUtilsMock->expects($this->exactly(2))
            ->method('getTemplatePath')
            ->withConsecutive(
                ['NotFoundController/index', 'Base'],
                ['layout/base', 'Base']
            )
            ->willReturn('test_template');

        $listener = new ControllerExceptionNotFoundListener(
            $responseMock,
            $eventManagerStub,
            $viewHelperUtilsMock
        );

        $listener($eventMock);
    }

}
