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
use Tiny\Http;
use Tiny\Router;
use Tiny\Skeleton\Application\EventManager\ControllerEvent;
use Tiny\Skeleton\Application\Service\ConfigService;
use Tiny\Skeleton\Module\Base;
use Tiny\View\View;

class AfterCallingControllerViewInitListenerTest extends TestCase
{

    public function testInvokeMethod()
    {
        $configServiceMock = $this->createMock(
            ConfigService::class
        );
        $configServiceMock->expects($this->once())
            ->method('getConfig')
            ->with('view', [])
            ->willReturn([
                'base_layout_path' => 'test_layout'
            ]);

        $eventManagerStub = $this->createMock(
            EventManager::class
        );

        $viewUtilsMock = $this->createMock(
            Base\Utils\ViewHelperUtils::class
        );
        $viewUtilsMock->expects($this->exactly(2))
            ->method('getTemplatePath')
            ->withConsecutive(
                ['test_layout', 'Base'],
                ['TestController/index', 'Test']
            )
            ->willReturn('test_template');

        $viewUtilsMock->expects($this->once())
            ->method('extractModuleName')
            ->with('\\Test\\Controller\\TestController')
            ->willReturn('Test');

        $listener = new AfterCallingControllerViewInitListener(
            $configServiceMock,
            $eventManagerStub,
            $viewUtilsMock
        );

        $viewMock = $this->createMock(View::class);
        $viewMock->expects($this->once())
            ->method('setLayoutPath')
            ->with('test_template')
            ->will($this->returnSelf());
        $viewMock->expects($this->once())
            ->method('setTemplatePath')
            ->with('test_template')
            ->will($this->returnSelf());
        $viewMock->expects($this->once())
            ->method('setEventManager')
            ->with($eventManagerStub);

        $responseMock = $this->createStub(Http\AbstractResponse::class);
        $responseMock->expects($this->once())
            ->method('getResponse')
            ->willReturn($viewMock);
        $responseMock->expects($this->once())
            ->method('setResponse')
            ->with($viewMock);

        $routeMock = $this->createStub(Router\Route::class);
        $routeMock->expects($this->exactly(2))
            ->method('getController')
            ->willReturn('\\Test\\Controller\\TestController');
        $routeMock->expects($this->once())
            ->method('getMatchedAction')
            ->willReturn('index');

        $eventMock = $this->createStub(
            ControllerEvent::class
        );
        $eventMock->expects($this->once())
            ->method('getData')
            ->willReturn($responseMock);
        $eventMock->expects($this->once())
            ->method('getParams')
            ->willReturn(
                [
                    'route'    => $routeMock,
                ]
            );
        $eventMock->expects($this->once())
            ->method('setData')
            ->with($responseMock);

        $listener($eventMock);
    }

}
