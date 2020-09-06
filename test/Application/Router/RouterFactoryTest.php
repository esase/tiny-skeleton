<?php

namespace Tiny\Skeleton\Application\Router\Factory;

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use PHPUnit\Framework\TestCase;
use Tiny\Router\Router;
use Tiny\ServiceManager\ServiceManager;
use Tiny\Http\Request;

class RouterFactoryTest extends TestCase
{

    public function testInvokeMethod()
    {
        $serviceManagerMock = $this->createMock(ServiceManager::class);
        $serviceManagerMock->expects($this->once())
            ->method('get')
            ->willReturn(
                $this->createStub(Request::class)
            );

        $factory = new RouterFactory();
        $object = $factory($serviceManagerMock);

        $this->assertInstanceOf(
            Router::class,
            $object
        );
    }

}
