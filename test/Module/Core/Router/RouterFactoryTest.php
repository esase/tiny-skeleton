<?php

namespace Tiny\Skeleton\Module\Core\Router\Factory;

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
use Tiny\Http;

class RouterFactoryTest extends TestCase
{

    public function testInvokeMethod()
    {
        $serviceManagerMock = $this->createMock(ServiceManager::class);
        $serviceManagerMock->expects($this->once())
            ->method('get')
            ->willReturn(
                $this->createStub(Http\Request::class)
            );

        $listenerFactory = new RouterFactory();
        $listener = $listenerFactory($serviceManagerMock);

        $this->assertInstanceOf(
            Router::class,
            $listener
        );
    }

}
