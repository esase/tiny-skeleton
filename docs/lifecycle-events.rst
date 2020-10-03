.. _index-skeleton-lifecycle-events-label:

Lifecycle events
================

Lifecycle events help you influence on the bootstrapping process using event listeners .


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
