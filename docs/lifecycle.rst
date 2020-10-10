.. _index-skeleton-lifecycle-label:

Application Lifecycle
=======================

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

Bootstrapping
-------------

Bellow we check a look the details of **bootstrapping** process which contains of **8 steps**.

--------------------------
1. Loading modules configs
--------------------------

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

-----------------------
2. Init service manager
-----------------------

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

---------------------
3. Init event manager
---------------------

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

-----------------------
4. Init config service
-----------------------

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

--------------
5. Init routes
--------------

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

--------------
6. Init router
--------------

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

------------------
7. Init controller
------------------

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

----------------
8. Init response
----------------

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
