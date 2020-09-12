.. _index-skeleton-label:


Skeleton
========

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
The  :code:`public/index.php` is a requests handler  which runs the application.

Aside from launching the handler also defines the working environment
(there are two possible modes: :code:`dev` and :code:`prod`) which are responsible for displaying and catching errors.
The quick example of :code:`public/index.php`:

.. code-block:: php

    $applicationEnv = getenv('APPLICATION_ENV') ?: 'dev';
    ...

    require_once 'vendor/autoload.php';
    require_once 'error-handler.php';
    require_once 'application-env/'.$applicationEnv.'.php';

    ...

When all initialization steps are finished the handler runs the application using the following way:

.. code-block:: php

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

    $configsArray = $this->bootstrapper->loadModulesConfigs(
        $this->registeredModules
    );

The list of all defined modules (:code:`$this->registeredModules`) is stored in the root's :code:`modules.php` file:

.. code-block:: php

    return [
        'Base',
        'User',
        ...
    ];

Generally speaking the skeleton collects all modules configs and merges they in a one global config.
Example of a config file:

.. code-block:: php

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

    $serviceManager = $this->bootstrapper->initServiceManager(
        $configsArray
    );

Services definition are stored in `config files`:

.. code-block:: php

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

**PS:** To not to make `modules main config` to big we split configs on small parts, example:

.. code-block:: php

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

    $this->bootstrapper->initEventManager(
        $serviceManager->get(EventManager::class),
        $configsArray
    );

Listeners definition also are stored in `config files`:

.. code-block:: php

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

To make collected modules configs available in the application we need to register them as a service.

.. code-block:: php

    $this->bootstrapper->initConfigsService(
        $serviceManager->get(EventManager::class),
        $serviceManager->get(ConfigService::class),
        $configsArray
    );

Whenever you need an access to that configs you may inject the `config service` into you class and get access to any config value:

.. code-block:: php

    return new AfterCallingControllerViewInitListener(
        $serviceManager->get(ConfigService::class),
        ...
    );

    ...

    $configValue = $this->configService->getConfig('config_key');
    ...

**************
5. Init routes
**************

On this step application collects and registers routes which are used in the navigation.

.. code-block:: php

    $this->bootstrapper->initRoutes(
        $serviceManager->get(EventManager::class),
        $serviceManager->get(Router::class),
        $serviceManager->get(ConfigService::class),
        $this->isCliContext // auto detect the current context
    );

For the performance reason application collects only routes related to the current context. Context may be either :code:`console` or :code:`http`.
Routes definition are stored in `config files`:

.. code-block:: php

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
        ...
        'console'  => [
            [
                'request'     => 'user list',
                'controller'  => Controller\UserCliController::class,
                'action_list' => 'list',
            ],
        ],
    ];

**************
6. Init router
**************

******************
7. Init controller
******************

****************
8. Init response
****************

Lifecycle events
----------------

Controllers
-----------

View helpers
------------

Error handling
--------------


