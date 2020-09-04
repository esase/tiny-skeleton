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
use Tiny\Skeleton\Application\EventManager\ControllerEvent;
use Tiny\Skeleton\Module\Base;
use Tiny\Http;

class BeforeDisplayingResponseCorsListenerTest extends TestCase
{

    public function testInvokeMethod()
    {
        $requestMock = $this->createMock(Http\Request::class);
        $requestMock->expects($this->once())
            ->method('isConsole')
            ->willReturn(false);

        $httpUtilsMock = $this->createMock(Http\ResponseHttpUtils::class);
        $httpUtilsMock->expects($this->once())
            ->method('sendHeaders')
            ->with(
                [
                    'Access-Control-Allow-Origin: http://test.com',
                    'Access-Control-Allow-Credentials: true',
                    'Access-Control-Max-Age: 86400',
                ]
            );

        $listener = new BeforeDisplayingResponseCorsListener(
            $requestMock,
            $httpUtilsMock,
            'http://test.com'
        );

        $listener(
            $this->createStub(
                ControllerEvent::class
            )
        );
    }

}
