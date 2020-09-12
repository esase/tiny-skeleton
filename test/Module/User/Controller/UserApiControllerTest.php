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

class UserApiControllerTest extends TestCase
{

    public function testListMethod()
    {
        $responseMock = $this->createMock(
            AbstractResponse::class
        );
        $responseMock->expects($this->once())
            ->method('setResponse')
            ->with(json_encode([]))
            ->will($this->returnSelf());
        $responseMock->expects($this->once())
            ->method('setCode')
            ->with(200)
            ->will($this->returnSelf());
        $responseMock->expects($this->once())
            ->method('setResponseType')
            ->with('application/json')
            ->will($this->returnSelf());

        $userServiceMock = $this->createMock(
            UserService::class
        );
        $userServiceMock->expects($this->once())
            ->method('getAllUsers')
            ->willReturn([]);

        $controller = new UserApiController($userServiceMock);
        $controller->list($responseMock);
    }

}
