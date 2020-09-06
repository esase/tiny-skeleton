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
use Tiny\Http\AbstractResponse;
use Tiny\Skeleton\Application\EventManager\ControllerEvent;
use Tiny\Skeleton\Application\Exception\Request\NotFoundException;
use Tiny\Skeleton\Module\Base\Service\NotFoundService;

class ControllerExceptionNotFoundListenerTest extends TestCase
{

    public function testInvokeMethod()
    {
        $exceptionMock = $this->createMock(NotFoundException::class);
        $exceptionMock->expects($this->once())
            ->method('getType')
            ->willReturn('html');

        $responseMock = $this->createMock(AbstractResponse::class);

        $serviceMock = $this->createMock(NotFoundService::class);
        $serviceMock->expects($this->once())
            ->method('getContent')
            ->with(
                $responseMock,
                'html',
                ''
            )
            ->willReturn($responseMock);

        $eventMock = $this->createMock(ControllerEvent::class);
        $eventMock->expects($this->once())
            ->method('getParams')
            ->willReturn([
                'exception' => $exceptionMock
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
