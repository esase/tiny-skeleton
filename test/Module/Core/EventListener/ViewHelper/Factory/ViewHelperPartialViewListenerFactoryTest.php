<?php

namespace Tiny\Skeleton\Module\Core\EventListener\ViewHelper;

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
use Tiny\Skeleton\Module\Core;
use Tiny\Skeleton\Module\Core\EventListener\ViewHelper\Factory\ViewHelperPartialViewListenerFactory;

class ViewHelperPartialViewListenerFactoryTest extends TestCase
{

    public function testInvokeMethod()
    {
        $serviceManagerMock = $this->createMock(ServiceManager::class);
        $serviceManagerMock->expects($this->exactly(2))
            ->method('get')
            ->withConsecutive(
                [EventManager::class],
                [Core\Utils\ViewHelperUtils::class]
            )
            ->will(
                $this->returnCallback(
                    function (string $serviceName) {
                        switch ($serviceName) {
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

        $listenerFactory = new ViewHelperPartialViewListenerFactory();
        $listener = $listenerFactory($serviceManagerMock);

        $this->assertInstanceOf(
            ViewHelperPartialViewListener::class,
            $listener
        );
    }

}
