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

use PHPUnit\Framework\TestCase;
use stdClass;
use Tiny\EventManager\EventManager;
use Tiny\ServiceManager\ServiceManager;
use Tiny\Skeleton\Application\Service\ConfigService;
use Tiny\Router;
use Tiny\Http;

class ApplicationTest extends TestCase
{

    public function testRunMethod()
    {
        $registeredModules = ['Test'];

        $serviceManagerMock = $this->createMock(
            ServiceManager::class
        );

        $serviceManagerMock->expects($this->exactly(13))
            ->method('get')
            ->withConsecutive(
                [EventManager::class],
                [EventManager::class],
                [ConfigService::class],
                [EventManager::class],
                [Router\Router::class],
                [ConfigService::class],
                [EventManager::class],
                [Router\Router::class],
                [EventManager::class],
                ['TestController'],
                [Http\Request::class],
                [Http\AbstractResponse::class],
                [EventManager::class],
            )
            ->will(
                $this->returnCallback(
                    function (string $serviceName) {
                        switch ($serviceName) {
                            case EventManager::class:
                                return $this->createStub(
                                    EventManager::class
                                );

                            case ConfigService::class:
                                return $this->createStub(
                                    ConfigService::class
                                );

                            case Router\Router::class:
                                return $this->createStub(
                                    Router\Router::class
                                );

                            case 'TestController' :
                                return new stdClass();

                            case Http\Request::class:
                                return $this->createStub(
                                    Http\Request::class
                                );

                            case Http\AbstractResponse::class:
                                return $this->createStub(
                                    Http\AbstractResponse::class
                                );

                            default :
                                return null;
                        }
                    }
                )
            );

        $bootstrap = $this->createMock(
            Bootstrapper::class
        );

        $bootstrap->expects($this->once())
            ->method('loadModulesConfigs')
            ->with($registeredModules)
            ->willReturn([]);

        $bootstrap->expects($this->once())
            ->method('initServiceManager')
            ->with([])
            ->willReturn($serviceManagerMock);

        $bootstrap->expects($this->once())
            ->method('initEventManager')
            ->with(
                $this->isInstanceOf(EventManager::class),
                []
            );

        $bootstrap->expects($this->once())
            ->method('initConfigsService')
            ->with(
                $this->isInstanceOf(EventManager::class),
                $this->isInstanceOf(ConfigService::class),
                []
            );

        $bootstrap->expects($this->once())
            ->method('initRoutes')
            ->with(
                $this->isInstanceOf(EventManager::class),
                $this->isInstanceOf(Router\Router::class),
                $this->isInstanceOf(ConfigService::class),
                false
            );

        $routeMock = $this->createMock(
            Router\Route::class
        );
        $routeMock->expects($this->once())
            ->method('getController')
            ->willReturn('TestController');

        $bootstrap->expects($this->once())
            ->method('initRouter')
            ->with(
                $this->isInstanceOf(EventManager::class),
                $this->isInstanceOf(Router\Router::class)
            )
            ->willReturn($routeMock);

        $responseStub = $this->createStub(
            Http\AbstractResponse::class
        );

        $bootstrap->expects($this->once())
            ->method('initController')
            ->with(
                $this->isInstanceOf(EventManager::class),
                $this->isInstanceOf(stdClass::class),
                $this->isInstanceOf(Http\Request::class),
                $this->isInstanceOf(Http\AbstractResponse::class),
                $routeMock
            )
            ->willReturn($responseStub);

        $bootstrap->expects($this->once())
            ->method('initResponse')
            ->with(
                $this->isInstanceOf(EventManager::class),
                $responseStub,
                $routeMock
            )
        ->willReturn('testResponseText');

        $application = new Application(
            $bootstrap,
            false,
            $registeredModules
        );

        $responseText = $application->run();

        $this->assertEquals('testResponseText', $responseText);
    }

}
