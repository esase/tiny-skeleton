<?php

namespace Tiny\Skeleton\Module\Core\EventListener;

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use PHPUnit\Framework\TestCase;
use ReflectionException;
use Tiny\Http;
use Tiny\Router;
use Tiny\Skeleton\Module\Core;
use Tiny\Skeleton\View;

class AfterCallingControllerViewInitListenerTest extends TestCase
{

    /**
     * @throws ReflectionException
     */
    public function testInvokeMethod()
    {
        $configServiceMock = $this->createMock(
            Core\Service\ConfigService::class
        );
        $configServiceMock->expects($this->once())
            ->method('getConfig')
            ->with('view', [])
            ->willReturn([
                'base_layout_path' => 'test_layout',
                'template_path_mask' => '{module}/view/{controller_name}/{action}.phtml'
            ]);

        $listener = new AfterCallingControllerViewInitListener(
            $configServiceMock
        );

        $viewMock = $this->createMock(View::class);
        $viewMock->expects($this->once())
            ->method('setLayoutPath')
            ->with('test_layout')
            ->will($this->returnSelf());
        $viewMock->expects($this->once())
            ->method('setTemplatePath')
            ->with($this->stringContains('Module/Core/view/AfterCallingControllerViewInitListenerTest/index.phtml'))
            ->will($this->returnSelf());

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
            ->willReturn(AfterCallingControllerViewInitListenerTest::class);
        $routeMock->expects($this->once())
            ->method('getMatchedAction')
            ->willReturn('index');

        $eventMock = $this->createStub(
            Core\EventManager\ControllerEvent::class
        );
        $eventMock->expects($this->once())
            ->method('getParams')
            ->willReturn(
                [
                    'response' => $responseMock,
                    'route'    => $routeMock,
                ]
            );
        $eventMock->expects($this->once())
            ->method('setData')
            ->with($responseMock);

        $listener($eventMock);
    }

}
