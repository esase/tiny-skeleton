<?php

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashevn@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tiny\Skeleton;

use PHPUnit\Framework\TestCase;
use stdClass;
use Tiny\EventManager\EventManager;
use Tiny\Skeleton\Module\Core;
use Tiny\Router;
use Tiny\Http;

class BootstrapTest extends TestCase
{

    public function testInitEventManagerMethod()
    {
        $configs = [
            [
                'event' => 'test_event',
                'listener' => 'TestListener',
                'priority' => -10
            ]
        ];

        $eventManagerMock = $this->createMock(
            EventManager::class
        );

        $eventManagerMock->expects($this->once())
            ->method('subscribe')
            ->with('test_event', 'TestListener', -10);

        $configServiceMock = $this->createMock(
            Core\Service\ConfigService::class
        );

        $configServiceMock->expects($this->once())
            ->method('getConfig')
            ->with('listeners', [])
            ->willReturn($configs);

        $bootstrap = new Bootstrap(
            $this->createMock(
                BootstrapUtils::class
            ),
            true
        );

        $bootstrap->initEventManager(
            $eventManagerMock,
            $configServiceMock
        );
    }

    public function testInitControllerMethod()
    {
        $action = 'test';

        $requestStub = $this->createMock(
            Http\Request::class
        );

        $responseStub = $this->createMock(
            Http\AbstractResponse::class
        );

        $controllerMock = $this->getMockBuilder(stdClass::class)
            ->addMethods(['test'])
            ->getMock();

        $controllerMock->expects($this->once())
            ->method('test')
            ->with($responseStub, $requestStub)
            ->willReturn($responseStub);

        $bootstrap = new Bootstrap(
            $this->createMock(
                BootstrapUtils::class
            ),
            true
        );

        $bootstrap->initController(
            $controllerMock,
            $requestStub,
            $responseStub,
            $action
        );
    }

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

        $bootstrap = new Bootstrap(
            $this->createMock(
                BootstrapUtils::class
            ),
            true
        );

        $route = $bootstrap->initRouting($routerMock);

        $this->assertSame($routeStub, $route);
    }

    public function testInitConfigsServiceMethod()
    {
        $configs = [
            'test' => 'test'
        ];

        $configServiceMock = $this->createMock(
            Core\Service\ConfigService::class
        );

        $configServiceMock->expects($this->once())
            ->method('setConfigs')
            ->with($configs);

        $bootstrap = new Bootstrap(
            $this->createMock(
                BootstrapUtils::class
            ),
            true
        );

        $bootstrap->initConfigsService(
            $configServiceMock,
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
