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

class AbstractControllerTest extends TestCase
{

    public function testViewResponseMethod()
    {
        $responseMock = $this->createStub(
            AbstractResponse::class
        );
        $responseMock->expects($this->once())
            ->method('setResponse')
            ->with($this->isInstanceOf(View::class))
            ->will(
                $this->returnCallback(
                    function (View $view) use ($responseMock
                    ) {
                        // make sure the View is constructed properly
                        $this->assertEquals(
                            ['test' => 'test_variable'],
                            $view->getVariables()
                        );

                        return $responseMock;
                    }
                )
            );
        $responseMock->expects($this->once())
            ->method('setCode')
            ->with(200)
            ->will($this->returnSelf());
        $responseMock->expects($this->once())
            ->method('setResponseType')
            ->with('text/html')
            ->will($this->returnSelf());

        $controller = $this->getMockForAbstractClass(AbstractController::class);
        $controller->viewResponse($responseMock, [
            'test' => 'test_variable'
        ]);
    }

    public function testJsonResponseMethod()
    {
        $responseMock = $this->createStub(
            AbstractResponse::class
        );
        $responseMock->expects($this->once())
            ->method('setResponse')
            ->with(json_encode([
                'test' => 'test_variable'
            ]))
            ->will($this->returnSelf());
        $responseMock->expects($this->once())
            ->method('setCode')
            ->with(404)
            ->will($this->returnSelf());
        $responseMock->expects($this->once())
            ->method('setResponseType')
            ->with('application/json')
            ->will($this->returnSelf());

        $controller = $this->getMockForAbstractClass(AbstractController::class);
        $controller->jsonResponse($responseMock, [
            'test' => 'test_variable'
        ], 404);
    }

    public function testTextResponseMethod()
    {
        $responseMock = $this->createStub(
            AbstractResponse::class
        );
        $responseMock->expects($this->once())
            ->method('setResponse')
            ->with('test_message')
            ->will($this->returnSelf());
        $responseMock->expects($this->once())
            ->method('setCode')
            ->with(200)
            ->will($this->returnSelf());
        $responseMock->expects($this->once())
            ->method('setResponseType')
            ->with('text/plain')
            ->will($this->returnSelf());

        $controller = $this->getMockForAbstractClass(AbstractController::class);
        $controller->textResponse($responseMock, 'test_message');
    }

}
