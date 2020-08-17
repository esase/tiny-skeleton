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
use Tiny\EventManager;
use Tiny\Skeleton\Module\Core;
use Tiny\Skeleton\Module\Core\EventListener\ViewHelper\ViewHelperUrlListener;
use Tiny\Router;

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

        $listener = new ViewHelperUrlListener(
            $routerMock
        );

        $listener($eventMock);
    }

}
