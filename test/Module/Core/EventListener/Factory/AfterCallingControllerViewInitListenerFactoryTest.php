<?php

namespace Tiny\Skeleton\Module\Core\EventListener;

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use PHPUnit\Framework\TestCase;
use Tiny\Skeleton\Module\Core\EventListener\Factory\AfterCallingControllerViewInitListenerFactory;
use Tiny\ServiceManager\ServiceManager;
use Tiny\Skeleton\Module\Core;

class AfterCallingControllerViewInitListenerFactoryTest extends TestCase
{

    public function testInvokeMethod()
    {
        $serviceManagerMock = $this->createMock(ServiceManager::class);
        $serviceManagerMock->expects($this->once())
            ->method('get')
            ->willReturn(
                $this->createStub(Core\Service\ConfigService::class)
            );

        $listenerFactory = new AfterCallingControllerViewInitListenerFactory();
        $listener = $listenerFactory($serviceManagerMock);

        $this->assertInstanceOf(
            AfterCallingControllerViewInitListener::class,
            $listener
        );
    }

}