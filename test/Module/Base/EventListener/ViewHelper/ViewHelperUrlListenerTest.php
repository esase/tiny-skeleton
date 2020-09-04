<?php

namespace Tiny\Skeleton\Module\Base\EventListener\Base;

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use PHPUnit\Framework\TestCase;
use Tiny\EventManager;
use Tiny\Skeleton\Module\Base;
use Tiny\Skeleton\Module\Base\EventListener\ViewHelper\ViewHelperUrlListener;
use Tiny\Router;
use Tiny\Skeleton\Module\Base\Utils\ViewHelperUtils;

class ViewHelperUrlListenerTest extends TestCase
{

    public function testInvokeMethod()
    {
        $routerMock = $this->createMock(
            Router\Router::class
        );
        $routerMock->expects($this->once())
            ->method('assembleRequest')
            ->with(
                'Tiny\Skeleton\Module\Test\Controller\TestController',
                'index',
                ['test']
            )
            ->willReturn('/test');

        $eventMock = $this->createMock(
            EventManager\Event::class
        );
        $eventMock->expects($this->once())
            ->method('getParams')
            ->willReturn(
                [
                    'arguments' => [
                        'TestController',
                        'index',
                        'Test',
                        ['test']
                    ],
                ]
            );
        $eventMock->expects($this->once())
            ->method('setData')
            ->with('/test');

        $viewHelperUtilsMock = $this->createMock(
            ViewHelperUtils::class
        );
        $viewHelperUtilsMock->expects($this->once())
            ->method('getControllerPath')
            ->with('TestController', 'Test')
            ->willReturn('Tiny\Skeleton\Module\Test\Controller\TestController');

        $listener = new ViewHelperUrlListener(
            $routerMock,
            $viewHelperUtilsMock
        );

        $listener($eventMock);
    }

}
