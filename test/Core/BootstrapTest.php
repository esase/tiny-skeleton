<?php

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashevn@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tiny\Skeleton\Core;

use PHPUnit\Framework\TestCase;
use Tiny\ServiceManager\ServiceManager;
use Tiny\Skeleton\Module\Base;
use Tiny\Router;

class BootstrapTest extends TestCase
{

    public function testInitRoutingMethod()
    {
        $routeStub = $this->createMock(
            Router\Route::class
        );

        $routerMock = $this->createMock(
            Router\Router::class
        );

        $routerMock->expects($this->once())
            ->method('getMatchedRoute')
            ->willReturn($routeStub);

        $serviceManagerMock = $this->createMock(
            ServiceManager::class
        );

        $serviceManagerMock->expects($this->once())
            ->method('get')
            ->with(Router\Router::class)
            ->willReturn($routerMock);

        $bootstrap = new Bootstrap(
            $this->createMock(
                BootstrapUtils::class
            ),
            true
        );

        $route = $bootstrap->initRouting($serviceManagerMock);

        $this->assertSame($routeStub, $route);
    }

    public function testInitConfigsServiceMethod()
    {
        $configs = [
            'test' => 'test'
        ];

        $configServiceMock = $this->createMock(
            Base\Service\ConfigService::class
        );

        $configServiceMock->expects($this->once())
            ->method('setConfigs')
            ->with($configs);

        $serviceManagerMock = $this->createMock(
            ServiceManager::class
        );

        $serviceManagerMock->expects($this->once())
            ->method('get')
            ->with(Base\Service\ConfigService::class)
            ->willReturn($configServiceMock);

        $bootstrap = new Bootstrap(
            $this->createMock(
                BootstrapUtils::class
            ),
            true
        );

        $bootstrap->initConfigsService(
            $serviceManagerMock,
            $configs
        );
    }

    public function testInitServiceManagerMethod()
    {
        $bootstrap = new Bootstrap(
            $this->createMock(
                BootstrapUtils::class
            ),
            true
        );

        $serviceManager = $bootstrap->initServiceManager([
            'service_manager' => [
                'shared' => [
                    'TestSharedClass' => 'TestSharedClassFactory'
                ],
                'discrete' => [
                    'TestDiscreteClass' => 'TestDiscreteClassFactory'
                ]
            ]
        ]);

        $this->assertTrue($serviceManager->has('TestSharedClass'));
        $this->assertTrue($serviceManager->has('TestDiscreteClass'));
    }

    public function testLoadModulesConfigsMethodUsingProdEnvAndNotCachedConfigs()
    {
        $module1Config = [
            'services' => [1]
        ];
        $module2Config = [
            'services' => [2],
            'controllers' => [
                'TestController' => 'TestControllerFactory'
            ]
        ];

        $bootstrapUtilsMock = $this->createMock(
            BootstrapUtils::class
        );

        $bootstrapUtilsMock->expects($this->once())
            ->method('loadCachedModulesConfigArray')
            ->willReturn(null);

        $bootstrapUtilsMock->expects($this->exactly(2))
            ->method('loadModuleConfigArray')
            ->will($this->onConsecutiveCalls($module1Config, $module2Config));

        $bootstrapUtilsMock->expects($this->once())
            ->method('saveCachedModulesConfigArray')
            ->with([
                'services' => [1, 2],
                'controllers' => [
                    'TestController' => 'TestControllerFactory'
                ]
            ]);

        $bootstrap = new Bootstrap(
            $bootstrapUtilsMock,
            true
        );

        $configs = $bootstrap->loadModulesConfigs([
            'Test1',
            'Test2'
        ]);

        // configs should be properly merged
        $this->assertEquals([
            'services' => [1, 2],
            'controllers' => [
                'TestController' => 'TestControllerFactory'
            ]
        ], $configs);
    }

    public function testLoadModulesConfigsMethodUsingProdEnvAndCachedConfigs()
    {
        $cachedModuleConfig = [
            'controllers' => [
                'TestController' => 'TestControllerFactory'
            ]
        ];
        $bootstrapUtilsMock = $this->createMock(
            BootstrapUtils::class
        );
        $bootstrapUtilsMock->expects($this->once())
            ->method('loadCachedModulesConfigArray')
            ->willReturn($cachedModuleConfig);

        $bootstrap = new Bootstrap(
            $bootstrapUtilsMock,
            true
        );

        $configs = $bootstrap->loadModulesConfigs([
            'Test1',
            'Test2'
        ]);

        // configs should be properly merged
        $this->assertEquals($cachedModuleConfig, $configs);
    }

    public function testLoadModulesConfigsMethodUsingDevEnvironment()
    {
        $module1Config = [
            'services' => [1]
        ];
        $module2Config = [
            'services' => [2],
            'controllers' => [
                'TestController' => 'TestControllerFactory'
            ]
        ];

        $bootstrapUtilsMock = $this->createMock(
            BootstrapUtils::class
        );
        $bootstrapUtilsMock->expects($this->exactly(2))
            ->method('loadModuleConfigArray')
            ->will($this->onConsecutiveCalls($module1Config, $module2Config));

        $bootstrap = new Bootstrap(
            $bootstrapUtilsMock,
            false
        );

        $configs = $bootstrap->loadModulesConfigs([
            'Test1',
            'Test2'
        ]);

        // configs should be properly merged
        $this->assertEquals([
            'services' => [1, 2],
            'controllers' => [
                'TestController' => 'TestControllerFactory'
            ]
        ], $configs);
    }

}
