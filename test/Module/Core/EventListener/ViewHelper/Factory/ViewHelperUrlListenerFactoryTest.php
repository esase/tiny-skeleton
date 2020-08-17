<?php

namespace Tiny\Skeleton\Module\Core\EventListener\ViewHelper;

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
use Tiny\Skeleton\Module\Core\EventListener\ViewHelper\Factory\ViewHelperUrlListenerFactory;
use Tiny\Router;

class ViewHelperUrlListenerFactoryTest extends TestCase
{

    public function testInvokeMethod()
    {
        $serviceManagerMock = $this->createMock(ServiceManager::class);
        $serviceManagerMock->expects($this->once())
            ->method('get')
            ->willReturn(
                $this->createStub(Router\Router::class)
            );

        $listenerFactory = new ViewHelperUrlListenerFactory();
        $listener = $listenerFactory($serviceManagerMock);

        $this->assertInstanceOf(
            ViewHelperUrlListener::class,
            $listener
        );
    }

}
