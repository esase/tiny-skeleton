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
use Tiny\Http\Request;
use Tiny\ServiceManager\ServiceManager;
use Tiny\Skeleton\Module\Base;

class RequestFactoryTest extends TestCase
{

    use PHPMock;

    public function testInvokeMethod()
    {
        $sapiName = $this->getFunctionMock(
            __NAMESPACE__,
            'php_sapi_name'
        );
        $sapiName->expects($this->once())->willReturn('cli');

        $listenerFactory = new RequestFactory();
        $listener = $listenerFactory(
            $this->createStub(ServiceManager::class)
        );

        $this->assertInstanceOf(
            Request::class,
            $listener
        );
    }

}
