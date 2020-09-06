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
use Tiny\Skeleton\Module\Base\EventListener\Application\ControllerExceptionNotFoundListener;
use Tiny\Skeleton\Module\Base\Service\NotFoundService;
use Tiny\Http\AbstractResponse;

class ControllerExceptionNotFoundListenerFactoryTest extends TestCase
{

    public function testInvokeMethod()
    {
        $serviceManagerMock = $this->createMock(ServiceManager::class);
        $serviceManagerMock->expects($this->exactly(2))
            ->method('get')
            ->withConsecutive(
                [AbstractResponse::class],
                [NotFoundService::class]
            )
        ->will(
            $this->returnCallback(
                function (string $serviceName) {
                    return $this->createStub(
                        $serviceName
                    );
                }
            )
        );

        $factory = new ControllerExceptionNotFoundListenerFactory();
        $object = $factory($serviceManagerMock);

        $this->assertInstanceOf(
            ControllerExceptionNotFoundListener::class,
            $object
        );
    }

}
