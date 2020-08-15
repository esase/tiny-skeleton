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
use Tiny\ServiceManager\ServiceManager;
use Tiny\Skeleton\Module\Core;
use Tiny\Skeleton\Module\Core\EventListener\Factory\BeforeDisplayingResponseCorsListenerFactory;
use Tiny\Http;

class BeforeDisplayingResponseCorsListenerFactoryTest extends TestCase
{

    public function testInvokeMethod()
    {
        $serviceManagerMock = $this->createMock(ServiceManager::class);
        $serviceManagerMock->expects($this->exactly(2))
            ->method('get')
            ->withConsecutive(
                [Http\Request::class],
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

        $listenerFactory = new BeforeDisplayingResponseCorsListenerFactory();
        $listener = $listenerFactory($serviceManagerMock);

        $this->assertInstanceOf(
            BeforeDisplayingResponseCorsListener::class,
            $listener
        );
    }

}
