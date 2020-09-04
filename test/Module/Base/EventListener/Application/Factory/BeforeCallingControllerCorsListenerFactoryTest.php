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
use Tiny\ServiceManager\ServiceManager;
use Tiny\Skeleton\Module\Base;
use Tiny\Http;
use Tiny\Skeleton\Module\Base\EventListener\Application\BeforeCallingControllerCorsListener;

class BeforeCallingControllerCorsListenerFactoryTest extends TestCase
{

    public function testInvokeMethod()
    {
        $serviceManagerMock = $this->createMock(ServiceManager::class);
        $serviceManagerMock->expects($this->exactly(3))
            ->method('get')
            ->withConsecutive(
                [Http\Request::class],
                [Http\AbstractResponse::class],
                [Http\ResponseHttpUtils::class]
            )
        ->will(
            $this->returnCallback(
                function (string $serviceName) {
                    switch ($serviceName) {
                        case Http\Request::class:
                            return $this->createStub(
                                Http\Request::class
                            );

                        case Http\AbstractResponse::class:
                            return $this->createStub(
                                Http\AbstractResponse::class
                            );

                        case Http\ResponseHttpUtils::class:
                            return $this->createStub(
                                Http\ResponseHttpUtils::class
                            );

                        default :
                            return null;
                    }
                }
            )
        );

        $listenerFactory = new BeforeCallingControllerCorsListenerFactory();
        $listener = $listenerFactory($serviceManagerMock);

        $this->assertInstanceOf(
            BeforeCallingControllerCorsListener::class,
            $listener
        );
    }

}
