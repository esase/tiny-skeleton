<?php

namespace Tiny\Skeleton\Module\Base\Controller;

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
use Tiny\View\View;

class NotFoundControllerTest extends TestCase
{

    public function testIndexMethod()
    {
        $responseMock = $this->createMock(
            AbstractResponse::class
        );
        $responseMock->expects($this->once())
            ->method('setResponse')
            ->with($this->isInstanceOf(View::class))
            ->will($this->returnSelf());
        $responseMock->expects($this->once())
            ->method('setCode')
            ->with(404)
            ->will($this->returnSelf());
        $responseMock->expects($this->once())
            ->method('setResponseType')
            ->with('text/html')
            ->will($this->returnSelf());

        $controller = new NotFoundController();
        $controller->index($responseMock);
    }

}
