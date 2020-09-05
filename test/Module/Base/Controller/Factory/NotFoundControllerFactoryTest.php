<?php

namespace Tiny\Skeleton\Module\Base\Controller\Factory;

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
use Tiny\Skeleton\Module\Base\Controller\NotFoundController;
use Tiny\Skeleton\Module\Base\Service\NotFoundService;

class NotFoundControllerFactoryTest extends TestCase
{

    public function testInvokeMethod()
    {
        $serviceManagerMock = $this->createMock(ServiceManager::class);
        $serviceManagerMock->expects($this->once())
            ->method('get')
            ->willReturn(
                $this->createStub(NotFoundService::class)
            );

        $factory = new NotFoundControllerFactory();
        $object = $factory(
            $serviceManagerMock
        );

        $this->assertInstanceOf(
            NotFoundController::class,
            $object
        );
    }

}
