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
use Tiny\Skeleton\Application\Exception\Request\NotFoundException;
use Tiny\Http;
use Tiny\Skeleton\Module\Base\Service\NotFoundService;

class ControllerExceptionNotFoundListenerTest extends TestCase
{

    public function testInvokeMethod()
    {
        $exceptionStub = $this->createStub(NotFoundException::class);

        $responseMock = $this->createMock(Http\AbstractResponse::class);
        $responseMock->expects($this->once())
            ->method('setCode')
            ->with(404)
            ->will($this->returnSelf());
        $responseMock->expects($this->once())
            ->method('setResponse')
            ->with('test_content');

        $serviceMock = $this->createMock(NotFoundService::class);
        $serviceMock->expects($this->once())
            ->method('getContent')
            ->willReturn('test_content');

        $eventMock = $this->createMock(ControllerEvent::class);
        $eventMock->expects($this->once())
            ->method('getParams')
            ->willReturn([
                'exception' => $exceptionStub
            ]);
        $eventMock->expects($this->once())
            ->method('setData')
            ->with($responseMock);

        $listener = new ControllerExceptionNotFoundListener(
            $responseMock,
            $serviceMock
        );

        $listener($eventMock);
    }

}
