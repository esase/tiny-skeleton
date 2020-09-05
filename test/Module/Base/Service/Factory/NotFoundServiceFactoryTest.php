<?php

namespace Tiny\Skeleton\Module\Base\Service\Factory;

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\TestCase;
use Tiny\EventManager\EventManager;
use Tiny\ServiceManager\ServiceManager;
use Tiny\Skeleton\Module\Base\Service\NotFoundService;
use Tiny\Skeleton\Module\Base;

class NotFoundServiceFactoryTest extends TestCase
{

    use PHPMock;

    public function testInvokeMethod()
    {
        $serviceManagerMock = $this->createMock(ServiceManager::class);
        $serviceManagerMock->expects($this->exactly(2))
            ->method('get')
            ->withConsecutive(
                [Base\Utils\ViewHelperUtils::class],
                [EventManager::class],
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

        $sapiName = $this->getFunctionMock(
            __NAMESPACE__,
            'php_sapi_name'
        );
        $sapiName->expects($this->once())->willReturn('cli');

        $factory = new NotFoundServiceFactory();
        $object = $factory(
            $serviceManagerMock
        );

        $this->assertInstanceOf(
            NotFoundService::class,
            $object
        );
    }

}
