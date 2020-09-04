<?php

namespace Tiny\Skeleton\Application\EventManager\Factory;

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use PHPUnit\Framework\TestCase;
use Tiny\EventManager\EventManager;
use Tiny\ServiceManager\ServiceManager;

class EventManagerFactoryTest extends TestCase
{

    public function testInvokeMethod()
    {
        $serviceManagerStub = $this->createMock(ServiceManager::class);

        $listenerFactory = new EventManagerFactory();
        $listener = $listenerFactory($serviceManagerStub);

        $this->assertInstanceOf(
            EventManager::class,
            $listener
        );
    }

}
