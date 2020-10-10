.. _index-skeleton-files-structure-label:

Application Files structure
===========================

In the scheme below we demonstrate a default and preferred application structure
but you always may follow your own way adding extra folders and files:

.. code-block:: bash

    application
    ├── application-env # contains specific settings for both "dev" and "prod" mode
    ├── data # a place for storing dynamically created files such as logs, caches, etc
    ├── docker-config # docker files and configs
    ├── docs # application docs
    ├── public
    │   ├── index.php # an entry point for all incoming requests
    ├──src
    │   ├── Application # contains a core functionality including the app bootstrapper
    │   │  ├── EventManager # holds the "EventManager" initializer and a few core lifecycle event classes
    │   │  ├── Exception # a place for storing all kind of custom exceptions
    │   │  ├── Http # the http package initializer
    │   │  ├── Router # the route package initializer
    │   │  ├── Service # core services
    │   │  ├── view # contains core views such as "500.phtml"
    │   │  ├── Application.php # the main application loader
    │   │  ├── Bootstrapper.php # provides a set of helpful methods for the "Application.php"
    │   │  ├── BootstrapperUtils.php # a helper for the "Bootstrapper.php"
    │   │  ├── config.php # core settings
    │   │  ├── ErrorHandler.php # handles and logs errors
    │   ├── Module # a place for all custom modules
    │   │  ├── Base # contains some shared resources like "AbstractController", "view helpers", etc
    │   │  │    ├── config # module specific settings divided by type (listeners, service manager, etc)
    │   │  │    ├── Controller # a place for storing the module's specific controllers
    │   │  │    ├── EventListener # all listeners should be stored here
    │   │  │    │   ├── Application # application lifecycle listeners
    │   │  │    │   ├── ViewHelper # view helpers listeners
    │   │  │    ├── Utils # utilities (like services helpers)
    │   │  │    ├── Service # data providers, models, etc.
    │   │  │    ├── view # a place for storing controllers views
    │   │  │    ├── config.php # the main module's config which merges all sub configs from the "config" dir
    └── tests # unit and functional tests (the folders inside are reflected copy of the structure in the "src" dir)
    └── error-handler.php # an error handler initializer
    └── modules.php # contains a list of all registered modules
    └── Dockerfile # contains all dependencies to run application