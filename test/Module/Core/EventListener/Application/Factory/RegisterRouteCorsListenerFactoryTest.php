<?php

namespace Tiny\Skeleton\Module\Core\EventListener\Application\Factory;

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
use Tiny\Skeleton\Module\Core;
use Tiny\Http;
use Tiny\Skeleton\Module\Core\EventListener\Application\RegisterRouteCorsListener;

class RegisterRouteCorsListenerFactoryTest extends TestCase
{

    public function testInvokeMethod()
    {
        $serviceManagerMock = $this->createMock(ServiceManager::class);
        $serviceManagerMock->expects($this->once())
            ->method('get')
            ->willReturn(
                $this->createStub(Http\Request::class)
            );

        $listenerFactory = new RegisterRouteCorsListenerFactory();
        $listener = $listenerFactory($serviceManagerMock);

        $this->assertInstanceOf(
            RegisterRouteCorsListener::class,
            $listener
        );
    }

}
