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
use Tiny\EventManager\EventManager;
use Tiny\ServiceManager\ServiceManager;
use Tiny\Skeleton\Application\Service\ConfigService;
use Tiny\Skeleton\Module\Core;
use Tiny\Skeleton\Module\Core\EventListener\Application\AfterCallingControllerViewInitListener;

class AfterCallingControllerViewInitListenerFactoryTest extends TestCase
{

    public function testInvokeMethod()
    {
        $serviceManagerMock = $this->createMock(ServiceManager::class);
        $serviceManagerMock->expects($this->exactly(3))
            ->method('get')
            ->withConsecutive(
                [ConfigService::class],
                [EventManager::class],
                [Core\Utils\ViewHelperUtils::class]
            )
            ->will(
                $this->returnCallback(
                    function (string $serviceName) {
                        switch ($serviceName) {
                            case ConfigService::class:
                                return $this->createStub(
                                    ConfigService::class
                                );

                            case EventManager::class:
                                return $this->createStub(
                                    EventManager::class
                                );

                            case Core\Utils\ViewHelperUtils::class:
                                return $this->createStub(
                                    Core\Utils\ViewHelperUtils::class
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
