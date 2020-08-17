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
use Tiny\Skeleton\Module\Core;
use Tiny\Skeleton\Module\Core\EventListener\ViewHelper\ViewHelperConfigListener;

class ViewHelperConfigListenerTest extends TestCase
{

    public function testInvokeMethod()
    {
        $configServiceMock = $this->createMock(
            Core\Service\ConfigService::class
        );
        $configServiceMock->expects($this->once())
            ->method('getConfig')
            ->with('test')
            ->willReturn('test_value');

        $eventMock = $this->createStub(
            Core\EventManager\RouteEvent::class
        );
        $eventMock->expects($this->once())
            ->method('getParams')
            ->willReturn(
                [
                    'arguments' => [
                        'test',
                    ],
                ]
            );
        $eventMock->expects($this->once())
            ->method('setData')
            ->with('test_value');

        $listener = new ViewHelperConfigListener(
            $configServiceMock
        );

        $listener($eventMock);
    }

}
