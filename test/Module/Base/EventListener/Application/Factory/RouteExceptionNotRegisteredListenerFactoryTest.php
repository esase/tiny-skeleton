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

use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\TestCase;
use Tiny\ServiceManager\ServiceManager;
use Tiny\Skeleton\Module\Base\EventListener\Application\RouteExceptionNotRegisteredListener;

class RouteExceptionNotRegisteredListenerFactoryTest extends TestCase
{
    use PHPMock;

    public function testInvokeMethod()
    {
        $sapiName = $this->getFunctionMock(
            __NAMESPACE__,
            'php_sapi_name'
        );
        $sapiName->expects($this->once())->willReturn('cli');

        $factory = new RouteExceptionNotRegisteredListenerFactory();
        $object = $factory(
            $this->createStub(ServiceManager::class)
        );

        $this->assertInstanceOf(
            RouteExceptionNotRegisteredListener::class,
            $object
        );
    }

}
