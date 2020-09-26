<?php

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tiny\Skeleton\Application;

use Exception;
use PHPUnit\Framework\TestCase;
use stdClass;
use Tiny\EventManager\EventManager;
use Tiny\Skeleton\Application\EventManager as ApplicationEventManager;
use Tiny\Skeleton\Application\Exception\InvalidArgumentException;
use Tiny\Skeleton\Application\Service\ConfigService;
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
        $this->expectException(InvalidArgumentException::class);
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

    public function testInitRouterMethodUsingExceptionAndExceptionListener()
    {
        $routeStub = $this->createMock(
            Router\Route::class
        );

        $exception = new Exception('Error occurred');

        $eventManagerMock = $this->createMock(
            EventManager::class
        );

        $eventManagerMock->expects($this->exactly(2))
            ->method('trigger')
            ->withConsecutive(
                [ApplicationEventManager\RouteEvent::EVENT_BEFORE_MATCHING_ROUTE,
                 $this->isInstanceOf(
                     ApplicationEventManager\RouteEvent::class
                 )],
                [ApplicationEventManager\RouteEvent::EVENT_ROUTE_EXCEPTION,
                 $this->isInstanceOf(ApplicationEventManager\RouteEvent::class)]
            )
            ->will(
                $this->returnCallback(
                    function (string $eventName,
                        ApplicationEventManager\RouteEvent $event
                    ) use ($routeStub, $exception) {
                        // emulation of route not found
                        if ($eventName
                            == ApplicationEventManager\RouteEvent::EVENT_BEFORE_MATCHING_ROUTE
                        ) {
                            throw $exception;
                        }

                        // next event should provide a default route
                        $this->assertEquals(
                            ApplicationEventManager\RouteEvent::EVENT_ROUTE_EXCEPTION,
                            $eventName
                        );

                        $params = $event->getParams();

                        $this->assertSame($exception, $params['exception']);

                        // add a route
                        $event->setData($routeStub);
                    }
                )
            );

        $bootstrap = new Bootstrapper(
            $this->createMock(
                BootstrapperUtils::class
            ),
            true
        );

        $routerStub = $this->createStub(
            Router\Router::class
        );

        $route = $bootstrap->initRouter(
            $eventManagerMock,
            $routerStub
        );

        // make sure we received a default route from a listener
        $this->assertSame($routeStub, $route);
    }

    public function testInitRouterMethodUsingException()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage(
            'Error occurred'
        );

        $exception = new Exception('Error occurred');

        $eventManagerMock = $this->createMock(
            EventManager::class
        );

        $eventManagerMock->expects($this->exactly(2))
            ->method('trigger')
            ->withConsecutive(
                [ApplicationEventManager\RouteEvent::EVENT_BEFORE_MATCHING_ROUTE,
                 $this->isInstanceOf(
                     ApplicationEventManager\RouteEvent::class
                 )],
                [ApplicationEventManager\RouteEvent::EVENT_ROUTE_EXCEPTION,
                 $this->isInstanceOf(ApplicationEventManager\RouteEvent::class)]
            )
            ->will(
                $this->returnCallback(
                    function (string $eventName,
                        ApplicationEventManager\RouteEvent $event
                    ) use ($exception) {
                        // emulation of route not found
                        if ($eventName
                            == ApplicationEventManager\RouteEvent::EVENT_BEFORE_MATCHING_ROUTE
                        ) {
                            throw $exception;
                        }
                    }
                )
            );

        $bootstrap = new Bootstrapper(
            $this->createMock(
                BootstrapperUtils::class
            ),
            true
        );

        $routerStub = $this->createStub(
            Router\Router::class
        );

        $bootstrap->initRouter(
            $eventManagerMock,
            $routerStub
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
                ApplicationEventManager\RouteEvent::EVENT_BEFORE_MATCHING_ROUTE,
                $this->isInstanceOf(ApplicationEventManager\RouteEvent::class)
            )
            ->will(
                $this->returnCallback(
                    function (string $eventName,
                        ApplicationEventManager\RouteEvent $eventParams
                    ) use (
                        $routeModifiedStub
                    ) {
                        $this->assertEquals(
                            ApplicationEventManager\RouteEvent::EVENT_BEFORE_MATCHING_ROUTE,
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

    public function testInitRoutesMethodUsingConsoleRoute()
    {
        $eventManagerMock = $this->createMock(
            EventManager::class
        );

        $eventManagerMock->expects($this->once())
            ->method('trigger')
            ->with(
                ApplicationEventManager\RouteEvent::EVENT_REGISTER_ROUTE,
                $this->isInstanceOf(ApplicationEventManager\RouteEvent::class)
            );

        $routerMock = $this->createMock(
            Router\Router::class
        );
        $routerMock->expects($this->once())
            ->method('registerRoute')
            ->with(
                $this->isInstanceOf(Router\Route::class)
            )
            ->will(
                $this->returnCallback(
                    function (Router\Route $route) use ($routerMock) {
                        // make sure the Route is constructed properly
                        $this->assertEquals(
                            'users list',
                            $route->getRequest()
                        );

                        $this->assertEquals(
                            'TestConsoleController',
                            $route->getController()
                        );

                        $this->assertEquals(
                            'list',
                            $route->getActionList()
                        );

                        $this->assertEquals(
                            'literal',
                            $route->getType()
                        );

                        $this->assertEquals(
                            [],
                            $route->getRequestParams()
                        );

                        $this->assertEquals(
                            '',
                            $route->getSpec()
                        );

                        $this->assertEquals(
                            'cli',
                            $route->getContext()
                        );

                        return $routerMock;
                    }
                )
            );;

        $configServiceMock = $this->createMock(
            ConfigService::class
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

    public function testInitRoutesMethodUsingHttpRoute()
    {
        $eventManagerMock = $this->createMock(
            EventManager::class
        );

        $eventManagerMock->expects($this->once())
            ->method('trigger')
            ->with(
                ApplicationEventManager\RouteEvent::EVENT_REGISTER_ROUTE,
                $this->isInstanceOf(ApplicationEventManager\RouteEvent::class)
            );

        $routerMock = $this->createMock(
            Router\Router::class
        );
        $routerMock->expects($this->once())
            ->method('registerRoute')
            ->with(
                $this->isInstanceOf(Router\Route::class)
            )
            ->will(
                $this->returnCallback(
                    function (Router\Route $route) use ($routerMock) {
                        // make sure the Route is constructed properly
                        $this->assertEquals(
                            '/users',
                            $route->getRequest()
                        );

                        $this->assertEquals(
                            'TestController',
                            $route->getController()
                        );

                        $this->assertEquals(
                            'list',
                            $route->getActionList()
                        );

                        $this->assertEquals(
                            'literal',
                            $route->getType()
                        );

                        $this->assertEquals(
                            [],
                            $route->getRequestParams()
                        );

                        $this->assertEquals(
                            '',
                            $route->getSpec()
                        );

                        $this->assertEquals(
                            'http',
                            $route->getContext()
                        );

                        return $routerMock;
                    }
                )
            );

        $configServiceMock = $this->createMock(
            ConfigService::class
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


    public function testInitRoutesMethodUsingHttpApiRoute()
    {
        $eventManagerMock = $this->createMock(
            EventManager::class
        );

        $eventManagerMock->expects($this->once())
            ->method('trigger')
            ->with(
                ApplicationEventManager\RouteEvent::EVENT_REGISTER_ROUTE,
                $this->isInstanceOf(ApplicationEventManager\RouteEvent::class)
            );

        $routerMock = $this->createMock(
            Router\Router::class
        );
        $routerMock->expects($this->once())
            ->method('registerRoute')
            ->with(
                $this->isInstanceOf(Router\Route::class)
            )
            ->will(
                $this->returnCallback(
                    function (Router\Route $route) use ($routerMock) {
                        // make sure the Route is constructed properly
                        $this->assertEquals(
                            '/users',
                            $route->getRequest()
                        );

                        $this->assertEquals(
                            'TestApiController',
                            $route->getController()
                        );

                        $this->assertEquals(
                            'list',
                            $route->getActionList()
                        );

                        $this->assertEquals(
                            'literal',
                            $route->getType()
                        );

                        $this->assertEquals(
                            [],
                            $route->getRequestParams()
                        );

                        $this->assertEquals(
                            '',
                            $route->getSpec()
                        );

                        $this->assertEquals(
                            'http_api',
                            $route->getContext()
                        );

                        return $routerMock;
                    }
                )
            );

        $configServiceMock = $this->createMock(
            ConfigService::class
        );

        $configServiceMock->expects($this->once())
            ->method('getConfig')
            ->with('routes', [])
            ->willReturn(
                [
                    'http_api' => [
                        [
                            'request'     => '/users',
                            'controller'  => 'TestApiController',
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

    public function testInitRoutesUsingEmptyConfig()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Routes are missing, check you config'
        );

        $eventManagerStub = $this->createMock(
            EventManager::class
        );

        $routerStub = $this->createMock(
            Router\Router::class
        );

        $configServiceMock = $this->createMock(
            ConfigService::class
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
        $this->expectException(InvalidArgumentException::class);
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
            ConfigService::class
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
                ApplicationEventManager\RouteEvent::EVENT_REGISTER_ROUTE,
                $this->isInstanceOf(ApplicationEventManager\RouteEvent::class)
            )
            ->will(
                $this->returnCallback(
                    function (string $eventName,
                        ApplicationEventManager\RouteEvent $event
                    ) {
                        $this->assertEquals(
                            ApplicationEventManager\RouteEvent::EVENT_REGISTER_ROUTE,
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
            ConfigService::class
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
                [ApplicationEventManager\RouteEvent::EVENT_BEFORE_MATCHING_ROUTE,
                 $this->isInstanceOf(
                     ApplicationEventManager\RouteEvent::class
                 )],
                [ApplicationEventManager\RouteEvent::EVENT_AFTER_MATCHING_ROUTE,
                 $this->isInstanceOf(ApplicationEventManager\RouteEvent::class)]
            )
            ->will(
                $this->returnCallback(
                    function (string $eventName,
                        ApplicationEventManager\RouteEvent $event
                    ) use (
                        $routeInitialStub,
                        $routeModifiedStub
                    ) {
                        if ($eventName
                            == ApplicationEventManager\RouteEvent::EVENT_AFTER_MATCHING_ROUTE
                        ) {
                            // check the event's data
                            $this->assertEquals(
                                $routeInitialStub, $event->getData()
                            );

                            // now replace the route with modified one
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
                ApplicationEventManager\ConfigEvent::EVENT_SET_CONFIGS,
                $this->isInstanceOf(ApplicationEventManager\ConfigEvent::class)
            );

        $configServiceMock = $this->createMock(
            ConfigService::class
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
                ApplicationEventManager\ConfigEvent::EVENT_SET_CONFIGS,
                $this->isInstanceOf(ApplicationEventManager\ConfigEvent::class)
            )
            ->will(
                $this->returnCallback(
                    function (string $eventName,
                        ApplicationEventManager\ConfigEvent $event
                    ) use ($modifiedConfigs, $initialConfigs) {
                        $this->assertEquals(
                            ApplicationEventManager\ConfigEvent::EVENT_SET_CONFIGS,
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
            ConfigService::class
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
        $this->expectException(InvalidArgumentException::class);
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
        $applicationConfig = [
            'services' => [1],
        ];
        $module1Config = [
            'services' => [2],
        ];
        $module2Config = [
            'services'    => [3],
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

        $bootstrapUtilsMock->expects($this->once())
            ->method('loadApplicationConfigArray')
            ->willReturn($applicationConfig);

        $bootstrapUtilsMock->expects($this->exactly(2))
            ->method('loadModuleConfigArray')
            ->will($this->onConsecutiveCalls($module1Config, $module2Config));

        $bootstrapUtilsMock->expects($this->once())
            ->method('saveCachedModulesConfigArray')
            ->with(
                [
                    'services'    => [1, 2, 3],
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
                'services'    => [1, 2, 3],
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
        $applicationConfig = [
            'services' => [1],
        ];
        $module1Config = [
            'services' => [2],
        ];
        $module2Config = [
            'services'    => [3],
            'controllers' => [
                'TestController' => 'TestControllerFactory',
            ],
        ];

        $bootstrapUtilsMock = $this->createMock(
            BootstrapperUtils::class
        );

        $bootstrapUtilsMock->expects($this->once())
            ->method('loadApplicationConfigArray')
            ->willReturn($applicationConfig);

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
                'services'    => [1, 2, 3],
                'controllers' => [
                    'TestController' => 'TestControllerFactory',
                ],
            ], $configs
        );
    }

    public function testInitControllerMethodUsingExceptionAndExceptionListener()
    {
        $routeStub = $this->createStub(Router\Route::class);

        $responseStub = $this->createMock(
            Http\AbstractResponse::class
        );

        $exception = new Exception('Error occurred');

        $eventManagerMock = $this->createMock(
            EventManager::class
        );

        $eventManagerMock->expects($this->exactly(2))
            ->method('trigger')
            ->withConsecutive(
                [ApplicationEventManager\ControllerEvent::EVENT_BEFORE_CALLING_CONTROLLER,
                 $this->isInstanceOf(
                     ApplicationEventManager\ControllerEvent::class
                 )],
                [ApplicationEventManager\ControllerEvent::EVENT_CONTROLLER_EXCEPTION,
                 $this->isInstanceOf(
                     ApplicationEventManager\ControllerEvent::class
                 )]
            )
            ->will(
                $this->returnCallback(
                    function (string $eventName,
                        ApplicationEventManager\ControllerEvent $event
                    ) use ($routeStub, $responseStub, $exception) {
                        // emulation of the not found exception
                        if ($eventName
                            == ApplicationEventManager\ControllerEvent::EVENT_BEFORE_CALLING_CONTROLLER
                        ) {
                            throw $exception;
                        }

                        // next event should provide a default response
                        $this->assertEquals(
                            ApplicationEventManager\ControllerEvent::EVENT_CONTROLLER_EXCEPTION,
                            $eventName
                        );

                        $params = $event->getParams();

                        $this->assertSame($exception, $params['exception']);
                        $this->assertSame($routeStub, $params['route']);

                        // add a response
                        $event->setData($responseStub);
                    }
                )
            );

        $bootstrap = new Bootstrapper(
            $this->createMock(
                BootstrapperUtils::class
            ),
            true
        );

        $response = $bootstrap->initController(
            $eventManagerMock,
            $this->createStub(stdClass::class),
            $this->createMock(
                Http\Request::class
            ),
            $responseStub,
            $routeStub
        );

        // make sure we received a default response from a listener
        $this->assertSame($responseStub, $response);
    }

    public function testInitControllerMethodUsingExceptionAndException()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage(
            'Error occurred'
        );

        $routeStub = $this->createStub(Router\Route::class);

        $responseStub = $this->createMock(
            Http\AbstractResponse::class
        );

        $exception = new Exception('Error occurred');

        $eventManagerMock = $this->createMock(
            EventManager::class
        );

        $eventManagerMock->expects($this->exactly(2))
            ->method('trigger')
            ->withConsecutive(
                [ApplicationEventManager\ControllerEvent::EVENT_BEFORE_CALLING_CONTROLLER,
                 $this->isInstanceOf(
                     ApplicationEventManager\ControllerEvent::class
                 )],
                [ApplicationEventManager\ControllerEvent::EVENT_CONTROLLER_EXCEPTION,
                 $this->isInstanceOf(
                     ApplicationEventManager\ControllerEvent::class
                 )]
            )
            ->will(
                $this->returnCallback(
                    function (string $eventName,
                        ApplicationEventManager\ControllerEvent $event
                    ) use ($routeStub, $responseStub, $exception) {
                        // emulation of the not found exception
                        if ($eventName
                            == ApplicationEventManager\ControllerEvent::EVENT_BEFORE_CALLING_CONTROLLER
                        ) {
                            throw $exception;
                        }
                    }
                )
            );

        $bootstrap = new Bootstrapper(
            $this->createMock(
                BootstrapperUtils::class
            ),
            true
        );

        $bootstrap->initController(
            $eventManagerMock,
            $this->createStub(stdClass::class),
            $this->createMock(
                Http\Request::class
            ),
            $responseStub,
            $routeStub
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

        $routeStub = $this->createStub(Router\Route::class);

        $eventManagerMock->expects($this->once())
            ->method('trigger')
            ->with(
                ApplicationEventManager\ControllerEvent::EVENT_BEFORE_CALLING_CONTROLLER,
                $this->isInstanceOf(
                    ApplicationEventManager\ControllerEvent::class
                )
            )
            ->will(
                $this->returnCallback(
                    function (string $eventName,
                        ApplicationEventManager\ControllerEvent $event
                    ) use (
                        $routeStub,
                        $responseModifiedStub
                    ) {
                        $this->assertEquals(
                            ApplicationEventManager\ControllerEvent::EVENT_BEFORE_CALLING_CONTROLLER,
                            $eventName
                        );

                        // check the event's params
                        $this->assertEquals(
                            [
                                'route' => $routeStub,
                            ], $event->getParams()
                        );

                        // add a modified response
                        $event->setData($responseModifiedStub);
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
            $routeStub
        );

        $this->assertSame($responseModifiedStub, $response);
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

        $routeMock = $this->createMock(Router\Route::class);
        $routeMock->expects($this->once())
            ->method('getMatchedAction')
            ->willReturn('list');

        $eventManagerMock->expects($this->exactly(2))
            ->method('trigger')
            ->withConsecutive(
                [ApplicationEventManager\ControllerEvent::EVENT_BEFORE_CALLING_CONTROLLER,
                 $this->isInstanceOf(
                     ApplicationEventManager\ControllerEvent::class
                 )],
                [ApplicationEventManager\ControllerEvent::EVENT_AFTER_CALLING_CONTROLLER,
                 $this->isInstanceOf(
                     ApplicationEventManager\ControllerEvent::class
                 )]
            )
            ->will(
                $this->returnCallback(
                    function (string $eventName,
                        ApplicationEventManager\ControllerEvent $event
                    ) use (
                        $responseInitialStub,
                        $responseModifiedStub,
                        $routeMock
                    ) {
                        if ($eventName
                            == ApplicationEventManager\ControllerEvent::EVENT_AFTER_CALLING_CONTROLLER
                        ) {
                            // check the event's data and params
                            $this->assertEquals(
                                $responseInitialStub,
                                $event->getData()
                            );
                            $this->assertEquals(
                                [
                                    'route' => $routeMock,
                                ], $event->getParams()
                            );

                            // now we replace the response with the modified one
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
            $routeMock
        );

        $this->assertSame($responseModifiedStub, $response);
    }

    public function testInitResponseMethodUsingBeforeDisplayingEvent()
    {
        $routeStub = $this->createMock(
            Router\Route::class
        );

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
                ApplicationEventManager\ControllerEvent::EVENT_BEFORE_DISPLAYING_RESPONSE,
                $this->isInstanceOf(
                    ApplicationEventManager\ControllerEvent::class
                )
            )
            ->will(
                $this->returnCallback(
                    function (string $eventName,
                        ApplicationEventManager\ControllerEvent $event
                    ) use (
                        $routeStub,
                        $responseInitialStub,
                        $responseModifiedMock
                    ) {
                        // check the event's name
                        $this->assertEquals(
                            ApplicationEventManager\ControllerEvent::EVENT_BEFORE_DISPLAYING_RESPONSE,
                            $eventName
                        );

                        // check the event's data and params
                        $this->assertEquals(
                            $responseInitialStub, $event->getData()
                        );
                        $this->assertEquals(
                            [
                                'route' => $routeStub,
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
            $routeStub
        );

        $this->assertSame('testResponseString', $responseText);
    }

}
