<?php

namespace Tiny\Skeleton\Module\Core\EventListener\Core;

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use PHPUnit\Framework\TestCase;
use Tiny\EventManager\EventManager;
use Tiny\Skeleton\Module\Core\EventListener\Core\Factory\AfterCallingControllerViewInitListenerFactory;
use Tiny\ServiceManager\ServiceManager;
use Tiny\Skeleton\Module\Core;

class AfterCallingControllerViewInitListenerFactoryTest extends TestCase
{

    public function testInvokeMethod()
    {
        $serviceManagerMock = $this->createMock(ServiceManager::class);
        $serviceManagerMock->expects($this->exactly(2))
            ->method('get')
            ->withConsecutive(
                [Core\Service\ConfigService::class],
                [EventManager::class]
            )
            ->will(
                $this->returnCallback(
                    function (string $serviceName) {
                        switch ($serviceName) {
                            case Core\Service\ConfigService::class:
                                return $this->createStub(
                                    Core\Service\ConfigService::class
                                );

                            case EventManager::class:
                                return $this->createStub(
                                    EventManager::class
                                );

                            default :
                                return null;
                        }
                    }
                )
            );

        $listenerFactory = new AfterCallingControllerViewInitListenerFactory();
        $listener = $listenerFactory($serviceManagerMock);

        $this->assertInstanceOf(
            AfterCallingControllerViewInitListener::class,
            $listener
        );
    }

}
