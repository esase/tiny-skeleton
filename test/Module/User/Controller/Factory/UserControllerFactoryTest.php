<?php

namespace Tiny\Skeleton\Module\User\Controller\Factory;

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use PHPUnit\Framework\TestCase;
use Tiny\ServiceManager\ServiceManager;
use Tiny\Skeleton\Module\User\Service\UserService;
use stdClass;

class UserControllerFactoryTest extends TestCase
{

    public function testInvokeMethod()
    {
        $serviceManagerMock = $this->createMock(ServiceManager::class);
        $serviceManagerMock->expects($this->once())
            ->method('get')
            ->willReturn(
                $this->createStub(UserService::class)
            );

        $factory = new UserControllerFactory();
        $object = $factory(
            $serviceManagerMock,
            stdClass::class
        );

        $this->assertInstanceOf(
            stdClass::class,
            $object
        );
    }

}
