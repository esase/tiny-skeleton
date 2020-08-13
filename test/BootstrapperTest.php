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

class BootstrapperTest extends TestCase
{

    public function testInitEventManagerMethod()
    {
        $configs['listeners'] = [
            [
                'event'    => 'test_event',
                'listener' => 'TestListener',
                'priority' => -10,
            ],
        ];

        $eventManagerMock = $this->createMock(
            EventManager::class
        );

        $eventManagerMock->expects($this->once())
            ->method('subscribe')
            ->with('test_event', 'TestListener', -10);

        $bootstrap = new Bootstrapper(
            $this->createMock(
                BootstrapperUtils::class
            ),
            true
        );

        $bootstrap->initEventManager(
            $eventManagerMock,
            $configs
        );
    }

    public function testInitEventManagerMethodUsingEmptyParams()
    {
        $this->expectException(Core\Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Event name or listener class is missing, check you config'
        );

        $configs['listeners'] = [
            [
            ],
        ];

        $eventManagerStub = $this->createMock(
            EventManager::class
        );

        $bootstrap = new Bootstrapper(
            $this->createMock(
                BootstrapperUtils::class
            ),
            true
        );

        $bootstrap->initEventManager(
            $eventManagerStub,
            $configs
        );
    }

    public function testInitRouterMethodUsingBeforeMatchingEvent()
    {
        $routeModifiedStub = $this->createMock(
            Router\Route::class
        );

        $eventManagerMock = $this->createMock(
            EventManager::class
        );

        $eventManagerMock->expects($this->once())
            ->method('trigger')
            ->with(
                Core\EventManager\RouteEvent::EVENT_BEFORE_MATCHING_ROUTE,
                $this->isInstanceOf(Core\EventManager\RouteEvent::class)
            )
            ->will(
                $this->returnCallback(
                    function (string $eventName,
                        Core\EventManager\RouteEvent $eventParams
                    ) use (
                        $routeModifiedStub
                    ) {
                        $this->assertEquals(
                            Core\EventManager\RouteEvent::EVENT_BEFORE_MATCHING_ROUTE,
                            $eventName
                        );

                        // add a modified route
                        $eventParams->setData($routeModifiedStub);
                    }
                )
            );

        $routerStub = $this->createStub(
            Router\Router::class
        );

        $bootstrap = new Bootstrapper(
            $this->createMock(
                BootstrapperUtils::class
            ),
            true
        );

        $route = $bootstrap->initRouter(
            $eventManagerMock,
            $routerStub
        );

        $this->assertSame($routeModifiedStub, $route);
    }

    public function testInitRouterMethodUsingOnlyRouter()
    {
        $routeInitialStub = $this->createMock(
            Router\Route::class
        );

        $eventManagerMock = $this->createMock(
            EventManager::class
        );

        $eventManagerMock->expects($this->exactly(2))
            ->method('trigger')
            ->withConsecutive(
                [Core\EventManager\RouteEvent::EVENT_BEFORE_MATCHING_ROUTE,
                 $this->isInstanceOf(Core\EventManager\RouteEvent::class)],
                [Core\EventManager\RouteEvent::EVENT_AFTER_MATCHING_ROUTE,
                 $this->isInstanceOf(Core\EventManager\RouteEvent::class)]
            );

        $routerMock = $this->createMock(
            Router\Router::class
        );
        $routerMock->expects($this->once())
            ->method('getMatchedRoute')
            ->willReturn($routeInitialStub);

        $bootstrap = new Bootstrapper(
            $this->createMock(
                BootstrapperUtils::class
            ),
            true
        );

        $route = $bootstrap->initRouter(
            $eventManagerMock,
            $routerMock
        );

        $this->assertSame($routeInitialStub, $route);
    }

    public function testInitRoutes()
    {
        $eventManagerMock = $this->createMock(
            EventManager::class
        );

        $eventManagerMock->expects($this->once())
            ->method('trigger')
            ->with(
                Core\EventManager\RouteEvent::EVENT_REGISTER_ROUTE,
                $this->isInstanceOf(Core\EventManager\RouteEvent::class)
            );

        $routerMock = $this->createMock(
            Router\Router::class
        );
        $routerMock->expects($this->once())
            ->method('registerRoute')
            ->with(
                $this->isInstanceOf(Router\Route::class)
            );

        $configServiceMock = $this->createMock(
            Core\Service\ConfigService::class
        );

        $configServiceMock->expects($this->once())
            ->method('getConfig')
            ->with('routes', [])
            ->willReturn(
                [
                    'console' => [
                        [
                            'request'     => 'users list',
                            'controller'  => 'TestConsoleController',
                            'action_list' => 'list',
                        ],
                    ],
                ]
            );

        $bootstrap = new Bootstrapper(
            $this->createMock(
                BootstrapperUtils::class
            ),
            true
        );

        $bootstrap->initRoutes(
            $eventManagerMock,
            $routerMock,
            $configServiceMock,
            true
        );
    }

    public function testInitRoutesUsingEmptyConfig()
    {
        $this->expectException(Core\Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Both http and console routes are missing, check you config'
        );

        $eventManagerStub = $this->createMock(
            EventManager::class
        );

        $routerStub = $this->createMock(
            Router\Router::class
        );

        $configServiceMock = $this->createMock(
            Core\Service\ConfigService::class
        );

        $configServiceMock->expects($this->once())
            ->method('getConfig')
            ->with('routes', []);

        $bootstrap = new Bootstrapper(
            $this->createMock(
                BootstrapperUtils::class
            ),
            true
        );

        $bootstrap->initRoutes(
            $eventManagerStub,
            $routerStub,
            $configServiceMock,
            true
        );
    }

    public function testInitRoutesUsingEmptyRouterConfig()
    {
        $this->expectException(Core\Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'One of: request, controller or action list is empty, check you config'
        );

        $eventManagerStub = $this->createMock(
            EventManager::class
        );

        $routerStub = $this->createMock(
            Router\Router::class
        );

        $configServiceMock = $this->createMock(
            Core\Service\ConfigService::class
        );

        $configServiceMock->expects($this->once())
            ->method('getConfig')
            ->with('routes', [])
            ->willReturn(
                [
                    'console' => [
                        [
                        ],
                    ],
                ]
            );

        $bootstrap = new Bootstrapper(
            $this->createMock(
                BootstrapperUtils::class
            ),
            true
        );

        $bootstrap->initRoutes(
            $eventManagerStub,
            $routerStub,
            $configServiceMock,
            true
        );
    }

    public function testInitRoutesUsingRegisterEvent()
    {
        $eventManagerMock = $this->createMock(
            EventManager::class
        );

        $eventManagerMock->expects($this->once())
            ->method('trigger')
            ->with(
                Core\EventManager\RouteEvent::EVENT_REGISTER_ROUTE,
                $this->isInstanceOf(Core\EventManager\RouteEvent::class)
            )
            ->will(
                $this->returnCallback(
                    function (string $eventName,
                        Core\EventManager\RouteEvent $event
                    ) {
                        $this->assertEquals(
                            Core\EventManager\RouteEvent::EVENT_REGISTER_ROUTE,
                            $eventName
                        );

                        // modify the route object
                        /** @var Router\Route $route */
                        $route = $event->getData();

                        // ensure we received the initial route object
                        $this->assertEquals('/users', $route->getRequest());
                        $this->assertEquals(
                            'TestController', $route->getController()
                        );

                        // modify the initial controller's name
                        $route->setController('TestModifiedController');
                    }
                )
            );

        $routerMock = $this->createMock(
            Router\Router::class
        );
        $routerMock->expects($this->once())
            ->method('registerRoute')
            ->with(
                $this->callback(
                    function (Router\Route $route) {
                        // check the modified property by the event
                        return 'TestModifiedController'
                            === $route->getController();
                    }
                )
            );

        $configServiceMock = $this->createMock(
            Core\Service\ConfigService::class
        );

        $configServiceMock->expects($this->once())
            ->method('getConfig')
            ->with('routes', [])
            ->willReturn(
                [
                    'http' => [
                        [
                            'request'     => '/users',
                            'controller'  => 'TestController',
                            'action_list' => 'list',
                        ],
                    ],
                ]
            );

        $bootstrap = new Bootstrapper(
            $this->createMock(
                BootstrapperUtils::class
            ),
            true
        );

        $bootstrap->initRoutes(
            $eventManagerMock,
            $routerMock,
            $configServiceMock,
            false
        );
    }

    public function testInitRouterMethodUsingAfterMatchingEvent()
    {
        $routeInitialStub = $this->createMock(
            Router\Route::class
        );

        $routeModifiedStub = $this->createMock(
            Router\Route::class
        );

        $eventManagerMock = $this->createMock(
            EventManager::class
        );

        $eventManagerMock->expects($this->exactly(2))
            ->method('trigger')
            ->withConsecutive(
                [Core\EventManager\RouteEvent::EVENT_BEFORE_MATCHING_ROUTE,
                 $this->isInstanceOf(Core\EventManager\RouteEvent::class)],
                [Core\EventManager\RouteEvent::EVENT_AFTER_MATCHING_ROUTE,
                 $this->isInstanceOf(Core\EventManager\RouteEvent::class)]
            )
            ->will(
                $this->returnCallback(
                    function (string $eventName,
                        Core\EventManager\RouteEvent $event
                    ) use (
                        $routeInitialStub,
                        $routeModifiedStub
                    ) {
                        if ($eventName
                            == Core\EventManager\RouteEvent::EVENT_AFTER_MATCHING_ROUTE
                        ) {
                            // check the event's params
                            $this->assertEquals(
                                [
                                    'route' => $routeInitialStub,
                                ], $event->getParams()
                            );

                            // now replace the route with another one
                            $event->setData($routeModifiedStub);
                        }
                    }
                )
            );

        $routerMock = $this->createMock(
            Router\Router::class
        );
        $routerMock->expects($this->once())
            ->method('getMatchedRoute')
            ->willReturn($routeInitialStub);

        $bootstrap = new Bootstrapper(
            $this->createMock(
                BootstrapperUtils::class
            ),
            true
        );

        $route = $bootstrap->initRouter(
            $eventManagerMock,
            $routerMock
        );

        // we expect to get a modified route from the event
        $this->assertSame($routeModifiedStub, $route);
    }

    public function testInitConfigsServiceMethod()
    {
        $configs = [
            'test' => 'test',
        ];

        $eventManagerMock = $this->createMock(
            EventManager::class
        );

        $eventManagerMock->expects($this->once())
            ->method('trigger')
            ->with(
                Core\EventManager\ConfigEvent::EVENT_SET_CONFIGS,
                $this->isInstanceOf(Core\EventManager\ConfigEvent::class)
            );

        $configServiceMock = $this->createMock(
            Core\Service\ConfigService::class
        );

        $configServiceMock->expects($this->once())
            ->method('setConfigs')
            ->with($configs);

        $bootstrap = new Bootstrapper(
            $this->createMock(
                BootstrapperUtils::class
            ),
            true
        );

        $bootstrap->initConfigsService(
            $eventManagerMock,
            $configServiceMock,
            $configs
        );
    }

    public function testInitConfigsServiceMethodUsingSetConfigsEvent()
    {
        $initialConfigs = [
            'test' => 'test',
        ];

        $modifiedConfigs = [
            'test2' => 'test2',
        ];

        $eventManagerMock = $this->createMock(
            EventManager::class
        );

        $eventManagerMock->expects($this->once())
            ->method('trigger')
            ->with(
                Core\EventManager\ConfigEvent::EVENT_SET_CONFIGS,
                $this->isInstanceOf(Core\EventManager\ConfigEvent::class)
            )
            ->will(
                $this->returnCallback(
                    function (string $eventName,
                        Core\EventManager\ConfigEvent $event
                    ) use ($modifiedConfigs, $initialConfigs) {
                        $this->assertEquals(
                            Core\EventManager\ConfigEvent::EVENT_SET_CONFIGS,
                            $eventName
                        );
                        // ensure we received the initial configs
                        $this->assertEquals($initialConfigs, $event->getData());

                        // modify the configs
                        $event->setData($modifiedConfigs);
                    }
                )
            );

        $configServiceMock = $this->createMock(
            Core\Service\ConfigService::class
        );

        $configServiceMock->expects($this->once())
            ->method('setConfigs')
            ->with($modifiedConfigs);

        $bootstrap = new Bootstrapper(
            $this->createMock(
                BootstrapperUtils::class
            ),
            true
        );

        $bootstrap->initConfigsService(
            $eventManagerMock,
            $configServiceMock,
            $initialConfigs
        );
    }

    public function testInitServiceManagerMethod()
    {
        $bootstrap = new Bootstrapper(
            $this->createMock(
                BootstrapperUtils::class
            ),
            true
        );

        $serviceManager = $bootstrap->initServiceManager(
            [
                'service_manager' => [
                    'shared'   => [
                        'TestSharedClass' => 'TestSharedClassFactory',
                    ],
                    'discrete' => [
                        'TestDiscreteClass' => 'TestDiscreteClassFactory',
                    ],
                ],
            ]
        );

        $this->assertTrue($serviceManager->has('TestSharedClass'));
        $this->assertTrue($serviceManager->has('TestDiscreteClass'));
    }

    public function testInitServiceManagerMethodUsingEmptyConfig()
    {
        $this->expectException(Core\Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Both shared and discrete services are empty, check you config'
        );

        $bootstrap = new Bootstrapper(
            $this->createMock(
                BootstrapperUtils::class
            ),
            true
        );

        $bootstrap->initServiceManager([]);
    }

    public function testLoadModulesConfigsMethodUsingProdEnvAndNotCachedConfigs(
    )
    {
        $module1Config = [
            'services' => [1],
        ];
        $module2Config = [
            'services'    => [2],
            'controllers' => [
                'TestController' => 'TestControllerFactory',
            ],
        ];

        $bootstrapUtilsMock = $this->createMock(
            BootstrapperUtils::class
        );

        $bootstrapUtilsMock->expects($this->once())
            ->method('loadCachedModulesConfigArray')
            ->willReturn(null);

        $bootstrapUtilsMock->expects($this->exactly(2))
            ->method('loadModuleConfigArray')
            ->will($this->onConsecutiveCalls($module1Config, $module2Config));

        $bootstrapUtilsMock->expects($this->once())
            ->method('saveCachedModulesConfigArray')
            ->with(
                [
                    'services'    => [1, 2],
                    'controllers' => [
                        'TestController' => 'TestControllerFactory',
                    ],
                ]
            );

        $bootstrap = new Bootstrapper(
            $bootstrapUtilsMock,
            true
        );

        $configs = $bootstrap->loadModulesConfigs(
            [
                'Test1',
                'Test2',
            ]
        );

        // configs should be properly merged
        $this->assertEquals(
            [
                'services'    => [1, 2],
                'controllers' => [
                    'TestController' => 'TestControllerFactory',
                ],
            ], $configs
        );
    }

    public function testLoadModulesConfigsMethodUsingProdEnvAndCachedConfigs()
    {
        $cachedModuleConfig = [
            'controllers' => [
                'TestController' => 'TestControllerFactory',
            ],
        ];
        $bootstrapUtilsMock = $this->createMock(
            BootstrapperUtils::class
        );
        $bootstrapUtilsMock->expects($this->once())
            ->method('loadCachedModulesConfigArray')
            ->willReturn($cachedModuleConfig);

        $bootstrap = new Bootstrapper(
            $bootstrapUtilsMock,
            true
        );

        $configs = $bootstrap->loadModulesConfigs(
            [
                'Test1',
                'Test2',
            ]
        );

        // configs should be properly merged
        $this->assertEquals($cachedModuleConfig, $configs);
    }

    public function testLoadModulesConfigsMethodUsingDevEnvironment()
    {
        $module1Config = [
            'services' => [1],
        ];
        $module2Config = [
            'services'    => [2],
            'controllers' => [
                'TestController' => 'TestControllerFactory',
            ],
        ];

        $bootstrapUtilsMock = $this->createMock(
            BootstrapperUtils::class
        );
        $bootstrapUtilsMock->expects($this->exactly(2))
            ->method('loadModuleConfigArray')
            ->will($this->onConsecutiveCalls($module1Config, $module2Config));

        $bootstrap = new Bootstrapper(
            $bootstrapUtilsMock,
            false
        );

        $configs = $bootstrap->loadModulesConfigs(
            [
                'Test1',
                'Test2',
            ]
        );

        // configs should be properly merged
        $this->assertEquals(
            [
                'services'    => [1, 2],
                'controllers' => [
                    'TestController' => 'TestControllerFactory',
                ],
            ], $configs
        );
    }

    public function testInitControllerMethodUsingBeforeCallingEvent()
    {
        $responseModifiedStub = $this->createMock(
            Http\AbstractResponse::class
        );

        $eventManagerMock = $this->createMock(
            EventManager::class
        );

        $eventManagerMock->expects($this->once())
            ->method('trigger')
            ->with(
                Core\EventManager\ControllerEvent::EVENT_BEFORE_CALLING_CONTROLLER,
                $this->isInstanceOf(Core\EventManager\ControllerEvent::class)
            )
            ->will(
                $this->returnCallback(
                    function (string $eventName,
                        Core\EventManager\ControllerEvent $eventParams
                    ) use (
                        $responseModifiedStub
                    ) {
                        $this->assertEquals(
                            Core\EventManager\ControllerEvent::EVENT_BEFORE_CALLING_CONTROLLER,
                            $eventName
                        );

                        // add a modified response
                        $eventParams->setData($responseModifiedStub);
                    }
                )
            );

        $controllerMock = $this->getMockBuilder(stdClass::class)
            ->getMock();

        $requestStub = $this->createMock(
            Http\Request::class
        );

        $responseStub = $this->createMock(
            Http\AbstractResponse::class
        );

        $bootstrap = new Bootstrapper(
            $this->createMock(
                BootstrapperUtils::class
            ),
            true
        );

        $response = $bootstrap->initController(
            $eventManagerMock,
            $controllerMock,
            $requestStub,
            $responseStub,
            'index'
        );

        $this->assertSame($responseModifiedStub, $response);
    }

    public function testInitControllerMethodUsingOnlyResponse()
    {
        $eventManagerMock = $this->createMock(
            EventManager::class
        );

        $eventManagerMock->expects($this->exactly(2))
            ->method('trigger')
            ->withConsecutive(
                [Core\EventManager\ControllerEvent::EVENT_BEFORE_CALLING_CONTROLLER,
                 $this->isInstanceOf(Core\EventManager\ControllerEvent::class)],
                [Core\EventManager\ControllerEvent::EVENT_AFTER_CALLING_CONTROLLER,
                 $this->isInstanceOf(Core\EventManager\ControllerEvent::class)]
            );

        $requestStub = $this->createMock(
            Http\Request::class
        );

        $responseStub = $this->createMock(
            Http\AbstractResponse::class
        );

        $controllerMock = $this->getMockBuilder(stdClass::class)
            ->addMethods(['index'])
            ->getMock();

        $controllerMock->expects($this->once())
            ->method('index')
            ->with($responseStub, $requestStub);

        $bootstrap = new Bootstrapper(
            $this->createMock(
                BootstrapperUtils::class
            ),
            true
        );

        $response = $bootstrap->initController(
            $eventManagerMock,
            $controllerMock,
            $requestStub,
            $responseStub,
            'index'
        );

        $this->assertSame($responseStub, $response);
    }

    public function testInitControllerMethodUsingAfterCallingEvent()
    {
        $responseInitialStub = $this->createMock(
            Http\AbstractResponse::class
        );

        $responseModifiedStub = $this->createMock(
            Http\AbstractResponse::class
        );

        $eventManagerMock = $this->createMock(
            EventManager::class
        );

        $eventManagerMock->expects($this->exactly(2))
            ->method('trigger')
            ->withConsecutive(
                [Core\EventManager\ControllerEvent::EVENT_BEFORE_CALLING_CONTROLLER,
                 $this->isInstanceOf(Core\EventManager\ControllerEvent::class)],
                [Core\EventManager\ControllerEvent::EVENT_AFTER_CALLING_CONTROLLER,
                 $this->isInstanceOf(Core\EventManager\ControllerEvent::class)]
            )
            ->will(
                $this->returnCallback(
                    function (string $eventName,
                        Core\EventManager\ControllerEvent $event
                    ) use (
                        $responseInitialStub,
                        $responseModifiedStub
                    ) {
                        if ($eventName
                            == Core\EventManager\ControllerEvent::EVENT_AFTER_CALLING_CONTROLLER
                        ) {
                            // check the event's params
                            $this->assertEquals(
                                [
                                    'response' => $responseInitialStub,
                                ], $event->getParams()
                            );

                            // now replace the response with another one
                            $event->setData($responseModifiedStub);
                        }
                    }
                )
            );

        $requestStub = $this->createMock(
            Http\Request::class
        );

        $controllerMock = $this->getMockBuilder(stdClass::class)
            ->addMethods(['list'])
            ->getMock();

        $controllerMock->expects($this->once())
            ->method('list')
            ->with($responseInitialStub, $requestStub);

        $bootstrap = new Bootstrapper(
            $this->createMock(
                BootstrapperUtils::class
            ),
            true
        );

        $response = $bootstrap->initController(
            $eventManagerMock,
            $controllerMock,
            $requestStub,
            $responseInitialStub,
            'list'
        );

        $this->assertSame($responseModifiedStub, $response);
    }

    public function testInitResponseMethodUsingBeforeDisplayingEvent()
    {
        $responseInitialStub = $this->createMock(
            Http\AbstractResponse::class
        );

        $responseModifiedMock = $this->createMock(
            Http\AbstractResponse::class
        );
        $responseModifiedMock->expects($this->once())
            ->method('getResponseForDisplaying')
            ->willReturn('testResponseString');

        $eventManagerMock = $this->createMock(
            EventManager::class
        );

        $eventManagerMock->expects($this->once())
            ->method('trigger')
            ->with(
                Core\EventManager\ControllerEvent::EVENT_BEFORE_DISPLAYING_RESPONSE,
                $this->isInstanceOf(Core\EventManager\ControllerEvent::class)
            )
            ->will(
                $this->returnCallback(
                    function (string $eventName,
                        Core\EventManager\ControllerEvent $event
                    ) use (
                        $responseInitialStub,
                        $responseModifiedMock
                    ) {
                        // check the event's name
                        $this->assertEquals(
                            Core\EventManager\ControllerEvent::EVENT_BEFORE_DISPLAYING_RESPONSE,
                            $eventName
                        );

                        // check the event's params
                        $this->assertEquals(
                            [
                                'response'   => $responseInitialStub,
                                'controller' => 'TestController',
                                'action'     => 'index',
                            ], $event->getParams()
                        );

                        // add a modified response
                        $event->setData($responseModifiedMock);
                    }
                )
            );

        $bootstrap = new Bootstrapper(
            $this->createMock(
                BootstrapperUtils::class
            ),
            true
        );

        $responseText = $bootstrap->initResponse(
            $eventManagerMock,
            $responseInitialStub,
            'TestController',
            'index'
        );

        $this->assertSame('testResponseString', $responseText);
    }

    public function testInitResponseMethodUsingOnlyResponse()
    {
        $responseInitialMock = $this->createMock(
            Http\AbstractResponse::class
        );
        $responseInitialMock->expects($this->once())
            ->method('getResponseForDisplaying')
            ->willReturn('testResponseString');

        $eventManagerMock = $this->createMock(
            EventManager::class
        );

        $eventManagerMock->expects($this->once())
            ->method('trigger')
            ->with(
                Core\EventManager\ControllerEvent::EVENT_BEFORE_DISPLAYING_RESPONSE,
                $this->isInstanceOf(Core\EventManager\ControllerEvent::class)
            );

        $bootstrap = new Bootstrapper(
            $this->createMock(
                BootstrapperUtils::class
            ),
            true
        );

        $responseText = $bootstrap->initResponse(
            $eventManagerMock,
            $responseInitialMock,
            'TestController',
            'index'
        );

        $this->assertSame('testResponseString', $responseText);
    }

}
