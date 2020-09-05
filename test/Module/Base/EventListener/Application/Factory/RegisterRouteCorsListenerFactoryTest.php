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
use Tiny\Skeleton\Module\Base\EventListener\Application\RegisterRouteCorsListener;

class RegisterRouteCorsListenerFactoryTest extends TestCase
{

    public function testInvokeMethod()
    {
        $serviceManagerMock = $this->createMock(ServiceManager::class);
        $serviceManagerMock->expects($this->once())
            ->method('get')
            ->willReturn(
                $this->createStub(Http\Request::class)
            );

        $factory = new RegisterRouteCorsListenerFactory();
        $object = $factory($serviceManagerMock);

        $this->assertInstanceOf(
            RegisterRouteCorsListener::class,
            $object
        );
    }

}
