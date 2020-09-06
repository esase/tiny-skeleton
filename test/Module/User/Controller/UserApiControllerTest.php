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
        $responseStub = $this->createStub(
            AbstractResponse::class
        );
        $userServiceMock = $this->createMock(
            UserService::class
        );
        $userServiceMock->expects($this->once())
            ->method('getAllUsers')
            ->willReturn([]);

        $controllerMock = $this->getMockBuilder(UserApiController::class)
            ->onlyMethods(['jsonResponse'])
            ->setConstructorArgs([$userServiceMock])
            ->getMock();

        $controllerMock->expects($this->once())
            ->method('jsonResponse')
            ->with(
                $responseStub,
                []
            );

        $controllerMock->list($responseStub);
    }

}
