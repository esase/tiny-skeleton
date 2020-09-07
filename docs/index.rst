.. _index-skeleton-label:


Skeleton
=========

It's a trying to union all the **Tiny's** packages in one  to make a demo web application which may be used as a foundation for you needs (as a web api server or traditional web application or even as a console app).

Installation
------------

Run the following to install this library:


.. code-block:: bash

    $ composer require esase/tiny-skeleton


Docker Installation
-------------------

For your convenience, we prepared a docker image which includes everything to start working with the skeleton.

- Install the `docker` locally - https://docs.docker.com/install/.
- For launching the `docker` containers you need to run the command: :code:`docker-compose up -d` in the project's root dir.
- For stopping running containers you need to run the command: :code:`docker-compose stop`.


After the `docker` launching the project is available on: http://localhost:8080 or http://your.ip:8080

Application's Life Cycle
------------------------

-----------
Entry point
-----------

We use the `Front controller <https://en.wikipedia.org/wiki/Front_controller>`_ pattern to capture all incoming web and console requests as well.
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

Bellow we check a look the details of bootstrapping process which contains of **8 steps**.

**************************
1. Loading modules configs
**************************


The skeleton follows a modular structure, it means each module provides it's own config file which may include (listeners, routes, factories, etc)  for managing the application’s state.

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