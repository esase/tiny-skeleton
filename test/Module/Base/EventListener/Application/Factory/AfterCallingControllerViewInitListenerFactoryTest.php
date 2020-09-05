<?php

namespace Tiny\Skeleton\Module\Base\EventListener\Application\Factory;

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
use Tiny\Skeleton\Module\Base;
use Tiny\Skeleton\Module\Base\EventListener\Application\AfterCallingControllerViewInitListener;

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
                [Base\Utils\ViewHelperUtils::class]
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

                            case Base\Utils\ViewHelperUtils::class:
                                return $this->createStub(
                                    Base\Utils\ViewHelperUtils::class
                                );

                            default :
                                return null;
                        }
                    }
                )
            );

        $factory = new AfterCallingControllerViewInitListenerFactory();
        $object = $factory($serviceManagerMock);

        $this->assertInstanceOf(
            AfterCallingControllerViewInitListener::class,
            $object
        );
    }

}
