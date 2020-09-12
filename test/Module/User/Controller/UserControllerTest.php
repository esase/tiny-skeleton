<?php

namespace Tiny\Skeleton\Module\User\Controller;

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
use Tiny\Skeleton\Module\User\Service\UserService;
use Tiny\View\View;

class UserControllerTest extends TestCase
{

    public function testListMethod()
    {
        $responseMock = $this->createMock(
            AbstractResponse::class
        );
        $responseMock->expects($this->once())
            ->method('setResponse')
            ->with($this->isInstanceOf(View::class))
            ->will(
                $this->returnCallback(
                    function (View $view) use ($responseMock) {
                        // make sure the View is constructed properly
                        $this->assertEquals(
                            ['users' => []],
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

        $userServiceMock = $this->createMock(
            UserService::class
        );
        $userServiceMock->expects($this->once())
            ->method('getAllUsers')
            ->willReturn([]);

        $controller = new UserController($userServiceMock);
        $controller->list($responseMock);
    }

}
