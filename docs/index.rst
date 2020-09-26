.. _index-skeleton-label:


Tiny Skeleton
=============

It's a trying to union all the **Tiny's** packages in one  to make a demo web
application which may be used as a foundation for you needs (as a :code:`web api server` or :code:`traditional web application`
or even as a :code:`console app`).

Application installation
------------------------

Clone the skeleton:


.. code-block:: bash

    $ git clone https://github.com/esase/tiny-skeleton.git

Docker Installation
-------------------

For your convenience, we prepared a docker image which includes everything to start working with the skeleton.

- Install the `docker` locally - https://docs.docker.com/install/.
- For launching the `docker` containers you need to run the command: :code:`docker-compose up -d` in the project's root dir.
- For stopping running containers you need to run the command: :code:`docker-compose stop`.


After the `docker` launching the project is available on: http://localhost:8080 or http://your.ip:8080

**Don't forget to install all the Composer's libraries using the command:**

.. code-block:: bash

    docker exec -it tiny-skeleton-web composer install

Application's Lifecycle
-----------------------

-----------
Entry point
-----------

We use the `Front controller <https://en.wikipedia.org/wiki/Front_controller>`_ pattern to capture all incoming :code:`web` and :code:`console` requests as well.
The  :code:`public/index.php` it's a requests handler  which runs the application.

Aside from launching the handler also defines the working environment
(there are two possible modes: :code:`dev` and :code:`prod`) which are responsible for displaying and catching errors (see the :ref:`Error handling` chapter).
The quick example of :code:`public/index.php`:

.. code-block:: php

    <?php

        // sourced from: public/index.php

        $applicationEnv = getenv('APPLICATION_ENV') ?: 'dev';
        ...

        require_once 'vendor/autoload.php';
        require_once 'error-handler.php';
        require_once 'application-env/'.$applicationEnv.'.php';

        ...

When all initialization steps are finished the handler runs the application using the following way:

.. code-block:: php

    <?php

        // sourced from: public/index.php

        $application = new Application\Application(
            new Application\Bootstrapper(
                new Application\BootstrapperUtils(getcwd()),
                $isProdEnv
            ),
            $isCliContext,
            require_once 'modules.php'
        );

        echo $application->run();

-------------
Bootstrapping
-------------

Bellow we check a look the details of **bootstrapping** process which contains of **8 steps**.

**************************
1. Loading modules configs
**************************


The skeleton follows a modular structure, it means each module provides it's own config file which may include
(`listeners`, `routes`, `factories`, etc)  for managing the application’s state.

.. code-block:: php

    <?php
        // sourced from: src/Application/Application.php

        $configsArray = $this->bootstrapper->loadModulesConfigs(
            $this->registeredModules
        );

The list of all defined modules (:code:`$this->registeredModules`) is stored in the root's :code:`modules.php` file:

.. code-block:: php

    <?php

        // sourced from: modules.php

        return [
            'Base',
            'User',
            ...
        ];

Generally speaking the :code:`application` collects all modules configs and merges they in a one global config.
Example of a config file:

.. code-block:: php

    <?php

        // sourced from: src/Module/Base/config.php

        return [
            'site' => [
                'name' => 'Test site'
            ],
            'modules_root' => dirname(__DIR__),
            'view'            => [
                'base_layout_path'   => 'layout/base',
                'template_extension' => 'phtml',
            ],
            'service_manager' => require_once 'config/service-manager.php',
            'listeners'       => require_once 'config/listeners.php',
            ...
        ];

***********************
2. Init service manager
***********************

The service manager layer is responsible for registering any kind of services
(`controllers`, `listeners`, `utils`, `view helpers`, etc).
It looks like a big registry where you can get any service using factories (:ref:`view more details <index-service-manager-label>`).

.. code-block:: php

    <?php

        // sourced from: src/Application/Application.php

        $serviceManager = $this->bootstrapper->initServiceManager(
            $configsArray
        );

Services definitions are stored in `config files`:

.. code-block:: php

    <?php

        // sourced from: src/Module/Base/config/service-manager.php

        return [
            'shared' => [ // means we need only singletons
                // application listener
                Base\EventListener\Application\AfterCallingControllerViewInitListener::class => Base\EventListener\Application\Factory\AfterCallingControllerViewInitListenerFactory::class,
                ...

                // controller
                Base\Controller\NotFoundController::class                                    => InvokableFactory::class,
                ...
            ],
            'discrete' => [ // means we always need a new class instance
                // utils
                Base\Utils\ViewHelperUtils::class                                            => Base\Utils\Factory\ViewHelperUtilsFactory::class,
                ...
            ]
        ];

The config structure it’s a simple map with service names and its factories (classes which are responsible for creating those).

**PS:** To not to make `modules main config` to big we split it on a few small parts, example:

.. code-block:: php

    <?php

        // sourced from: src/Module/Base/config.php

        return [
            'site' => [
                'name' => 'Test site'
            ],
            ...
            // both service manager and listeners configs are stored separately
            'service_manager' => require_once 'config/service-manager.php',
            'listeners'       => require_once 'config/listeners.php',
        ];

So it's a good practice which you also should follow.

*********************
3. Init event manager
*********************

We use the event manager to make a communication among the different parts of application (:ref:`view more details <index-event-manager-label>`),
for instance we may notify listeners about an action or even ask provide us with some data.

.. code-block:: php

    <?php

        // sourced from: src/Application/Application.php

        $this->bootstrapper->initEventManager(
            $serviceManager->get(EventManager::class),
            $configsArray
        );

Listeners definitions also are stored in `config files`:

.. code-block:: php

    <?php

        // sourced from: src/Module/Base/config/listeners.php

        return [
            // application
            [
                'event'    => EventManager\ControllerEvent::EVENT_BEFORE_CALLING_CONTROLLER,
                'listener' => EventListener\Application\BeforeCallingControllerCorsListener::class,
                'priority' => -1000,
            ],
            ...
            // view helper
            [
                'event'    => View::EVENT_CALL_VIEW_HELPER.'config',
                'listener' => EventListener\ViewHelper\ViewHelperConfigListener::class,
            ],
            ...
        ];

It’s a list of named events and their handlers. Optionally you may setup a listener's :code:`priority` to manage their calling order.

**********************
4. Init config service
**********************

To make raw collected modules configs available in the application we need to register them as a service.

.. code-block:: php

    <?php

        // sourced from: src/Application/Application.php

        $this->bootstrapper->initConfigsService(
            $serviceManager->get(EventManager::class),
            $serviceManager->get(ConfigService::class),
            $configsArray
        );

Whenever you need an access to that configs you may inject the `config service` into you class and get access to any config value:

.. code-block:: php

    <?php

        // sourced from: src/Module/Base/EventListener/Application/AfterCallingControllerViewInitListener.php

        // a factory
        return new AfterCallingControllerViewInitListener(
            $serviceManager->get(ConfigService::class),
            ...
        );

        ...

        // somewhere inside the AfterCallingControllerViewInitListener
        $configValue = $this->configService->getConfig('config_key');
        ...

The final collected list of configs maybe modified by :code:`listeners` in the :code:`Event manager`.
Read more at: :ref:`Configs events`

**************
5. Init routes
**************

On this step application collects and registers routes which are used in the navigation.

.. code-block:: php

    <?php

        // sourced from: src/Application/Application.php

        $this->bootstrapper->initRoutes(
            $serviceManager->get(EventManager::class),
            $serviceManager->get(Router::class),
            $serviceManager->get(ConfigService::class),
            $this->isCliContext // auto detect the current context
        );

For the performance reason application collects only routes related to the current context. Context may be either :code:`console` or :code:`http|http_api`.
Routes definitions are stored in `config files`:

.. code-block:: php

    <?php

        // sourced from: src/Module/User/config/routes.php

        return [
            'http'     => [
                [
                    'request'     => '/users',
                    'controller'  => Controller\UserController::class,
                    'action_list' => [
                        Request::METHOD_GET  => 'list',
                        Request::METHOD_POST => 'create',
                    ],
                ],
            ],
            'http_api' => [
                  [
                    'request'     => '/api/v1/users',
                    'controller'  => Controller\UserApiController::class,
                    'action_list' => [
                        Request::METHOD_GET  => 'list',
                        Request::METHOD_POST => 'create',
                    ],
                ],
            ],
            'console'  => [
                [
                    'request'     => 'user list',
                    'controller'  => Controller\UserCliController::class,
                    'action_list' => 'list',
                ],
            ],
        ];

We split the :code:`http` and :code:`http api` routes due to different error handling strategy.
For example when the :code:`404` error occurred we display a normal `404 page`  but for the api routes whe display :code:`json response`.

The routes registration process maybe changed by :code:`listeners`.
For instance you can add a new route or delete some of existing ones using different criteria. Read more at: :ref:`Route events`

**************
6. Init router
**************

The router's main job is to find a `matched route` inside registered routes using a request query or throw an exception if it cannot be found.

.. code-block:: php

    <?php

        // sourced from: src/Application/Application.php

        $route = $this->bootstrapper->initRouter(
            $serviceManager->get(EventManager::class),
            $serviceManager->get(Router::class)
        );

Using :code:`listeners` in this case you can manipulate of searching a matched
route or catch the :code:`Exception` when route is not found and show a `404 page` as an example.
Read more at: :ref:`Router events`

******************
7. Init controller
******************

When a :code:`Route` is found  we are able to call an associated controller's method and get a response.

.. code-block:: php

    <?php

        // sourced from: src/Application/Application.php

        $response = $this->bootstrapper->initController(
            $serviceManager->get(EventManager::class),
            $serviceManager->get($route->getController()),
            $serviceManager->get(Http\Request::class),
            $serviceManager->get(Http\AbstractResponse::class),
            $route
        );

Like in all the previous examples here you also is available to control the :code:`execution flow` using listeners.
For example before execute a  method we may check a `user's role` or even `gzip` the received response after the execution,
you are free to implement anything you want.
Read more at: :ref:`Controller events`

****************
8. Init response
****************

The latest step in the life cycle process. The received response from the controller from the previous step is triggering to listeners,
then it displays in a browser or in the console.

.. code-block:: php

    <?php

        // sourced from: src/Application/Application.php

        $responseText = $this->bootstrapper->initResponse(
            $serviceManager->get(EventManager::class),
            $response,
            $route->getController(),
            $route->getMatchedAction()
        );

So it's a good place to process the response. For instance you may wrap received response with your custom content.
For example you may show a profiler information.
Read more at: :ref:`Response events`

Lifecycle events
----------------

Lifecycle events help you influence on the bootstrapping process using event listeners .

--------------
Configs events
--------------

When the :code:`application` finishes collecting configs from modules it triggers an :code:`Event`
passing a raw list of configs (`a merged array`) to its  listeners:

.. code-block:: php

    <?php

        // sourced from: src/Application/Bootstrapper.php

        // src/Application/EventManager/ConfigEvent.php
        $setEvent = new ConfigEvent($configsArray); // a raw list of configs
        $eventManager->trigger(
            ConfigEvent::EVENT_SET_CONFIGS,
            $setEvent
        );

        // register processed configs in the `ConfigService`
        $configsService->setConfigs($setEvent->getData());

So it gives us a beautiful opportunity to change the final config list from any custom module.
In the example below we will try to implement a listener which changes some of existing config value.
So lets imagine we have a module's config like:

.. code-block:: php

    <?php

        return [
            'test' => 'test_value'
        ];

Our target is to change the :code:`test` config value with a different one. For that we need a :code:`listener` class,
lets say it would be the: :code:`Module/CustomModule/EventListener/Application/SetConfigChangerListener.php`

.. code-block:: php

    <?php

        namespace Tiny\Skeleton\Module\CustomModule\EventListener\Application;

        use Tiny\Skeleton\Application\EventManager\ConfigEvent;

        class SetConfigChangerListener
        {
            /**
             * @param  ConfigEvent  $event
             */
            public function __invoke(ConfigEvent $event)
            {
                $configs = $event->getData();

                // change the the config value
                if (isset($configs['test'])) {
                    $configs['test'] = 'new_test_value';
                }

                $event->setData($configs);
            }
        }

Now we only need to register the :code:`listener` in the config file:

.. code-block:: php

    <?php

        // Module/CustomModule/config.php

        use Tiny\Skeleton\Application\EventManager;
        use Tiny\Skeleton\Module\CustomModule\EventListener;

        return [
            'listeners' => [
                // application
                [
                    'event'    => EventManager\ConfigEvent::EVENT_SET_CONFIGS,
                    'listener' => EventListener\Application\SetConfigChangerListener::class,
                ],
            ]
        ];

------------
Route events
------------

Every time when the :code:`application` registers a new route (collected from `modules configs`) it triggers an :code:`Event`
passing an instance of :code:`Router\Route` to its listeners:

.. code-block:: php

    <?php

        // sourced from: src/Application/Bootstrapper.php

        $route = new Router\Route(
            $request,
            $controller,
            $actionList,
            ($route['type'] ?? Router\Route::TYPE_LITERAL),
            ($route['request_params'] ?? []),
            ($route['spec'] ?? ''),
            $context
        );

        // src/Application/EventManager/RouteEvent.php
        $registerEvent = new RouteEvent($route);
        $eventManager->trigger(
            RouteEvent::EVENT_REGISTER_ROUTE,
            $registerEvent
        );

        // register the processed route
        $router->registerRoute($registerEvent->getData());

How can we use that? For instance there is an integration of `CORS <https://developer.mozilla.org/en/docs/Web/HTTP/CORS>`_
in the application which just adds the :code:`HTTP` method :code:`OPTIONS` to each route automatically.
Lets check it closer: (:code:`Module/Base/EventListener/Application/RegisterRouteCorsListener.php`):

.. code-block:: php

    <?php

        // sourced from: src/Module/Base/EventListener/Application/RegisterRouteCorsListener.php

        namespace Tiny\Skeleton\Module\Base\EventListener\Application;

        use Tiny\Skeleton\Application\EventManager\RouteEvent;
        use Tiny\Http\Request;
        use Tiny\Router\Route;

        class RegisterRouteCorsListener
        {

            /**
             * @var Request
             */
            private Request $request;

            /**
             * RegisterRouteCorsListener constructor.
             *
             * @param  Request  $request
             */
            public function __construct(Request $request)
            {
                $this->request = $request;
            }

            /**
             * @param  RouteEvent  $event
             */
            public function __invoke(RouteEvent $event)
            {
                // whenever we receive the 'OPTIONS' request from a browser we assign the 'OPTIONS' method to each route
                if ($this->request->isOptions()) {
                    /** @var Route $route */
                    $route = $event->getData();

                    if (is_array($route->getActionList())) {
                        // modify the route
                        $route->setActionList(
                            array_merge(
                                $route->getActionList(), [
                                    Request::METHOD_OPTIONS => 'index', // now we also support OPTIONS, and you don't need to define it manually
                                ]
                            )
                        );

                        $event->setData($route);
                    }
                }
            }

        }

The listener is is registered in the :code:`config file`:

.. code-block:: php

    <?php

        // sourced from: src/Module/Base/config/listeners.php

        use Tiny\Skeleton\Application\EventManager;
        use Tiny\Skeleton\Module\Base\EventListener;

        return [
            'listeners' => [
                // application
                [
                    'event'    => EventManager\RouteEvent::EVENT_REGISTER_ROUTE,
                    'listener' => EventListener\Application\RegisterRouteCorsListener::class,
                ],
            ]
        ];

-------------
Router events
-------------

On the router initialization step the router tries to find a matched route analyzing a request string and registered routes.
There are three possible events triggered by the router init method:

* :code:`RouteEvent::EVENT_BEFORE_MATCHING_ROUTE` - triggers before start matching routes.
* :code:`RouteEvent::EVENT_AFTER_MATCHING_ROUTE` - triggers after a route is found.
* :code:`RouteEvent::EVENT_ROUTE_EXCEPTION` - triggers when a route cannot be found.

the full method looks like:

.. code-block:: php

    <?php

        // sourced from: src/Application/Bootstrapper.php

        try {
            // src/Application/EventManager/RouteEvent.php
            $beforeEvent = new RouteEvent();
            $eventManager->trigger(
                RouteEvent::EVENT_BEFORE_MATCHING_ROUTE,
                $beforeEvent
            );

            // return a modified route
            if ($beforeEvent->getData()) {
                return $beforeEvent->getData();
            }

            // find a matched route
            $route = $router->getMatchedRoute();

            $afterEvent = new RouteEvent($route);
            $eventManager->trigger(
                RouteEvent::EVENT_AFTER_MATCHING_ROUTE,
                $afterEvent
            );

            return $afterEvent->getData();
        } catch (Throwable $e) {
            $routeExceptionEvent = new RouteEvent(
                null, [
                    'exception' => $e,
                ]
            );
            $eventManager->trigger(
                RouteEvent::EVENT_ROUTE_EXCEPTION,
                $routeExceptionEvent
            );

            // return a modified route
            if ($routeExceptionEvent->getData()) {
                return $routeExceptionEvent->getData();
            }

            throw $e;
        }

You can subscribe to any of those events and return a custom :code:`route` which depends on you needs.
But in our example we will register a listener for handling a :code:`404` page (`Not found`) when the :code:`RouteEvent::EVENT_ROUTE_EXCEPTION` is triggered.

So let's create a new :code:`listener` class in your module (suppose it's a `CustomModule`):

.. code-block:: php

    <?php

    namespace Tiny\Skeleton\Module\CustomModule\EventListener\Application;

    use Tiny\Skeleton\Application\EventManager\RouteEvent;
    use Tiny\Router\Route;
    use Tiny\Skeleton\Module\CustomModule\Controller\NotFoundController;

    class RouteExceptionNotRegisteredListener
    {
        /**
         * @param  RouteEvent  $event
         */
        public function __invoke(RouteEvent $event)
        {
            // by default the 'NotFoundController' will be assigned for all non existing routes
            $route = new Route(
                '',
                NotFoundController::class,
                'index'
            );
            $route->setMatchedAction('index');

            // return our custom route
            $event->setData(
                $route
            );
        }

    }

Now we need to register it in the configs:

.. code-block:: php

    <?php

        // Module/CustomModule/config.php

        use Tiny\Skeleton\Application\EventManager;
        use Tiny\Skeleton\Module\CustomModule\EventListener;

        return [
            'listeners' => [
                // application
                [
                    'event'    => EventManager\RouteEvent::EVENT_ROUTE_EXCEPTION,
                    'listener' => EventListener\Application\RouteExceptionNotRegisteredListener::class,
                ],
            ]
        ];

-----------------
Controller events
-----------------

When a matched :code:`route` is found by the :code:`router` it calls a related controller's method to get a response
which will be returned and displayed.
There are three possible events triggered by the controller init method:

* :code:`RouteEvent::EVENT_BEFORE_CALLING_CONTROLLER` - triggers before execution a controller's method.
* :code:`RouteEvent::EVENT_AFTER_CALLING_CONTROLLER` - triggers after the controller's execution.
* :code:`RouteEvent::EVENT_CONTROLLER_EXCEPTION` - triggers when the execution gives exceptions.

the full method looks like:

.. code-block:: php

    <?php

        // sourced from: src/Application/Bootstrapper.php

        try {
            $beforeEvent = new ControllerEvent(
                null, [
                    'route' => $route,
                ]
            );
            $eventManager->trigger(
                ControllerEvent::EVENT_BEFORE_CALLING_CONTROLLER,
                $beforeEvent
            );

            // return a modified response
            if ($beforeEvent->getData()) {
                return $beforeEvent->getData();
            }

            // call the controller's action
            $controller->{$route->getMatchedAction()}($response, $request);

            $afterEvent = new ControllerEvent(
                $response, [
                    'route' => $route,
                ]
            );
            $eventManager->trigger(
                ControllerEvent::EVENT_AFTER_CALLING_CONTROLLER,
                $afterEvent
            );

            return $afterEvent->getData();
        } catch (Throwable $e) {
            $requestExceptionEvent = new ControllerEvent(
                null, [
                    'exception' => $e,
                    'route'     => $route,
                ]
            );
            $eventManager->trigger(
                ControllerEvent::EVENT_CONTROLLER_EXCEPTION,
                $requestExceptionEvent
            );

            // return a modified response
            if ($requestExceptionEvent->getData()) {
                return $requestExceptionEvent->getData();
            }

            throw $e;
        }

Again you may use any of those events to implement a custom logic. In example below we will try to implement
a very simple listener which checks if a user is `logged in` before execution a controller's method.
And if it not the user will be redirected to a login page.

We need to create a new listener class in your module (suppose it’s a `CustomModule`):

.. code-block:: php

    <?php

    namespace Tiny\Skeleton\Module\CustomModule\EventListener\Application;

    use Tiny\Skeleton\Application\EventManager\RouteEvent;
    use Tiny\Http;
    use Tiny\Router\Route;
    use AuthService;

    class BeforeCallingControllerAuthGuardListener
    {

        /**
         * @var Http\AbstractResponse
         */
        private Http\AbstractResponse $response;

        /**
         * @var AuthService
         */
        private AuthService $authService;

        /**
         * @var Http\ResponseHttpUtils
         */
        private Http\ResponseHttpUtils $httpUtils;

        /**
         * BeforeCallingControllerAuthGuardListener constructor.
         *
         * @param  Http\AbstractResponse   $response
         */
        public function __construct(
            Http\AbstractResponse $response,
            AuthService $authService,
            Http\ResponseHttpUtils $httpUtils
        ) {
            $this->response = $response;
            $this->authService = $authService;
            $this->httpUtils = $httpUtils;
        }

        /**
         * @param  ControllerEvent  $event
         */
        public function __invoke(ControllerEvent $event)
        {
            if (!$this->authService->isAuthenticated()) {
                // return empty response and send the location header
                $this->httpUtils->sendHeaders([
                    'Location: http://www.example.com/login'
                ]);
                $event->setData($this->response);
            }
        }

    }

As you can see in our demonstration we use dependency injections. To make it clear you need to read the chapter - :ref:`Factories`.
Also don't forget to register the listener in the configs:

.. code-block:: php

    <?php

        // Module/CustomModule/config.php

        use Tiny\Skeleton\Application\EventManager;
        use Tiny\Skeleton\Module\CustomModule\EventListener;

        return [
            'listeners' => [
                // application
                [
                    'event'    => EventManager\ControllerEvent::EVENT_BEFORE_CALLING_CONTROLLER,
                    'listener' => EventListener\Application\BeforeCallingControllerAuthGuardListener::class,
                ],
            ]
        ];

---------------
Response events
---------------

The final step in the :code:`Life Cycle events` which triggers an :code:`Event`
passing an instance of the :code:`Response` object received from a controller to its listeners.

.. code-block:: php

    <?php

        // sourced from: src/Application/Bootstrapper.php

        // src/Application/EventManager/ControllerEvent.php
        $beforeEvent = new ControllerEvent(
            $response, // a controller's response
            [
                'route' => $route
            ]
        );
        $eventManager->trigger(
            ControllerEvent::EVENT_BEFORE_DISPLAYING_RESPONSE,
            $beforeEvent
        );

        /** @var Http\AbstractResponse $response */
        $response = $beforeEvent->getData();
        $responseString = $response->getResponseForDisplaying();

        return null !== $responseString ? $responseString : '';

It's a good place to inject something helpful in the :code:`Response`.
In example bellow we add a `Google analytic code` without touching html templates.
This approach allows us to easily remove or modify the analytic code and we really don't care what templates are used.

A new listener would be like: (suppose it’s a `CustomModule`):

.. code-block:: php

    <?php

    namespace Tiny\Skeleton\Module\CustomModule\EventListener\Application;

    use Tiny\Skeleton\Application\EventManager\ControllerEvent;
    use Tiny\Router\Route;
    use Tiny\Skeleton\Application\Bootstrapper;
    use Tiny\Http\AbstractResponse;
    use Tiny\View\View;

    class BeforeDisplayingResponseGoogleAnalyticListener
    {

        /**
         * @param  ControllerEvent  $event
         */
        public function __invoke(ControllerEvent $event)
        {
            /** @var Route $route */
            $route = $event->getParams()['route'];

            // we only need to inject content in `http` responses (all other like: `cli`, `http_api` should be skipped)
            if ($route->getContext() === Bootstrapper::ROUTE_CONTEXT_HTTP) {
                /** @var AbstractResponse $response */
                $response = $event->getData();
                $controllerResponse = $response->getResponse();

                if ($controllerResponse instanceof View) {
                    $pageContent = $controllerResponse->__toString();

                    // add the analytic code
                    $pageContent .= '<you analytic code here>';

                    // modify the response
                    $response->setResponse($pageContent);
                    $event->setData($response);
                }
            }
        }

    }

And register the listener in the configs:

.. code-block:: php

    <?php

        // Module/CustomModule/config.php

        use Tiny\Skeleton\Application\EventManager;
        use Tiny\Skeleton\Module\CustomModule\EventListener;

        return [
            'listeners' => [
                // application
                [
                    'event'    => EventManager\ControllerEvent::EVENT_BEFORE_DISPLAYING_RESPONSE,
                    'listener' => EventListener\Application\BeforeDisplayingResponseGoogleAnalyticListener::class,
                ],
            ]
        ];


Application Files structure
---------------------------

Factories
---------

Whenever you need a  :code:`Class file` (`a listener`, `a service`, `a controller`, etc) you have to register that class in configs,
aside from that each class should have its own :code:`factory` which initializes the class object and resolves its dependencies.

Lets consider we need to have a service :code:`TestService` (suppose it’s a `CustomModule`), let's create a a file:

.. code-block:: php

    <?php

    namespace Tiny\Skeleton\Module\CustomModule\Service;

    use Tiny\Skeleton\Module\AnotherCustomModule\Service\AnotherTestService;

    class TestService
    {
        /**
         * @var AnotherTestService
         */
        private $anotherTestService;

        /**
         * TestService constructor.
         *
         * @param  AnotherTestService   $anotherTestService
         */
        public function __construct(AnotherTestService $anotherTestService)
        {
            // this dependency will be resolved in a factory below
            $this->anotherTestService = $anotherTestService;
            ...
        }

        ...
    }

Now we need a  :code:`factory` for that:

.. code-block:: php

    <?php

        namespace Tiny\Skeleton\Module\CustomModule\Service\Factory;

        use Tiny\Skeleton\Module\AnotherCustomModule\Service\AnotherTestService;

        class TestServiceFactory
        {

            /**
             * @param  ServiceManager  $serviceManager
             *
             * @return TestService
             */
            public function __invoke(ServiceManager $serviceManager): TestService
            {
                return new TestService(
                    // the 'Service manager' helps us to resolve dependencies
                    $serviceManager->get(AnotherTestService::class)
                );
            }

        }

And the final thing, we need to register those both classes in the `config`:

.. code-block:: php

    <?php

        // Module/CustomModule/config/service-manager.php

        use Tiny\Skeleton\Module\CustomModule;

        return [
            'shared' => [
                // service
                CustomModule\Service\TestService::class => CustomModule\Service\Factory\TestServiceFactory::class,
            ],
        ];

You can make that service either :code:`Singleton` or :code:`Discrete` :ref:`view more details <index-service-manager-label>`.

Controllers
-----------

View helpers
------------

Error handling
--------------


