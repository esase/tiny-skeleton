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
use Tiny\Skeleton\Application\Service\ConfigService;
use Tiny\EventManager\Event;
use Tiny\Skeleton\Module\Base\EventListener\ViewHelper\ViewHelperConfigListener;

class ViewHelperConfigListenerTest extends TestCase
{

    public function testInvokeMethod()
    {
        $configServiceMock = $this->createMock(
            ConfigService::class
        );
        $configServiceMock->expects($this->once())
            ->method('getConfig')
            ->with('test')
            ->willReturn('test_value');

        $eventMock = $this->createMock(
            Event::class
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
