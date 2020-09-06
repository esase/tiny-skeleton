<?php

namespace Tiny\Skeleton\Module\Base\EventListener\ViewHelper;

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
use Tiny\Skeleton\Module\Base\EventListener\ViewHelper\Factory\ViewHelperPartialViewListenerFactory;
use Tiny\Skeleton\Module\Base\Utils\ViewHelperUtils;

class ViewHelperPartialViewListenerFactoryTest extends TestCase
{

    public function testInvokeMethod()
    {
        $serviceManagerMock = $this->createMock(ServiceManager::class);
        $serviceManagerMock->expects($this->exactly(2))
            ->method('get')
            ->withConsecutive(
                [EventManager::class],
                [ViewHelperUtils::class]
            )
            ->will(
                $this->returnCallback(
                    function (string $serviceName) {
                        switch ($serviceName) {
                            case EventManager::class:
                                return $this->createStub(
                                    EventManager::class
                                );

                            case ViewHelperUtils::class:
                                return $this->createStub(
                                    ViewHelperUtils::class
                                );

                            default :
                                return null;
                        }
                    }
                )
            );

        $factory = new ViewHelperPartialViewListenerFactory();
        $object = $factory($serviceManagerMock);

        $this->assertInstanceOf(
            ViewHelperPartialViewListener::class,
            $object
        );
    }

}
