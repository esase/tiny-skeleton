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
use Tiny\ServiceManager\ServiceManager;
use Tiny\Skeleton\Module\Base\EventListener\ViewHelper\Factory\ViewHelperUrlListenerFactory;
use Tiny\Router\Router;
use Tiny\Skeleton\Module\Base\Utils\ViewHelperUtils;

class ViewHelperUrlListenerFactoryTest extends TestCase
{

    public function testInvokeMethod()
    {

        $serviceManagerMock = $this->createMock(ServiceManager::class);
        $serviceManagerMock->expects($this->exactly(2))
            ->method('get')
            ->withConsecutive(
                [Router::class],
                [ViewHelperUtils::class]
            )
            ->will(
                $this->returnCallback(
                    function (string $serviceName) {
                        switch ($serviceName) {
                            case Router::class:
                                return $this->createStub(
                                    Router::class
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

        $factory = new ViewHelperUrlListenerFactory();
        $object = $factory($serviceManagerMock);

        $this->assertInstanceOf(
            ViewHelperUrlListener::class,
            $object
        );
    }

}
