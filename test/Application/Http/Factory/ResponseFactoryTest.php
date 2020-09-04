<?php

namespace Tiny\Skeleton\Application\Http\Factory;

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
use Tiny\ServiceManager\ServiceManager;
use Tiny\Skeleton\Module\Core;
use Tiny\Http;

class ResponseFactoryTest extends TestCase
{

    use PHPMock;

    public function testInvokeMethodUsingCliMode()
    {
        $sapiName = $this->getFunctionMock(
            __NAMESPACE__,
            'php_sapi_name'
        );
        $sapiName->expects($this->once())->willReturn('cli');

        $listenerFactory = new ResponseFactory();
        $listener = $listenerFactory(
            $this->createStub(ServiceManager::class)
        );

        $this->assertInstanceOf(
            Http\ResponseCli::class,
            $listener
        );
    }

    public function testInvokeMethodUsingHttpMode()
    {
        $sapiName = $this->getFunctionMock(
            __NAMESPACE__,
            'php_sapi_name'
        );
        $sapiName->expects($this->once())->willReturn('apache2handler');

        $serviceManagerMock = $this->createMock(ServiceManager::class);
        $serviceManagerMock->expects($this->once())
            ->method('get')
            ->willReturn(
                $this->createStub(Http\ResponseHttpUtils::class)
            );

        $listenerFactory = new ResponseFactory();
        $listener = $listenerFactory(
            $serviceManagerMock
        );

        $this->assertInstanceOf(
            Http\ResponseHttp::class,
            $listener
        );
    }

}
