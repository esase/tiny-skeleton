<?php

namespace Tiny\Skeleton\Module\Base\Utils\Factory;

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
use Tiny\Skeleton\Application\Service\ConfigService;
use Tiny\Skeleton\Module\Base;

class ViewHelperUtilsFactoryTest extends TestCase
{

    public function testInvokeMethod()
    {
        $serviceManagerMock = $this->createMock(ServiceManager::class);
        $serviceManagerMock->expects($this->once())
            ->method('get')
            ->willReturn(
                $this->createStub(ConfigService::class)
            );

        $listenerFactory = new ViewHelperUtilsFactory();
        $listener = $listenerFactory($serviceManagerMock);

        $this->assertInstanceOf(
            Base\Utils\ViewHelperUtils::class,
            $listener
        );
    }

}
