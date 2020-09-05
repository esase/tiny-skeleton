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
use Tiny\Http;
use Tiny\Skeleton\Module\Base\EventListener\Application\BeforeDisplayingResponseCorsListener;

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

        $factory = new BeforeDisplayingResponseCorsListenerFactory();
        $object = $factory($serviceManagerMock);

        $this->assertInstanceOf(
            BeforeDisplayingResponseCorsListener::class,
            $object
        );
    }

}
