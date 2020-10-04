.. _index-skeleton-error-handling-label:

Error handling
==============

There are two possible error modes (:code:`dev` and :code:`prod`) which you can activate directly in the :code:`public/index.php`
file or set as application environment variable in the :code:`.htaccess`.

.. code-block:: bash

    // sourced from: public/.htaccess

    Options -Indexes
    SetEnv APPLICATION_ENV dev # can be either "dev" or "prod"


The main application entry point :code:`public/index.php` uses that variable to initialize the desired error mode.

.. code-block:: php

    <?php

        // sourced from: public/index.php

        // you may either set it manually or using an app's environment variable
        $applicationEnv = getenv('APPLICATION_ENV') ?: 'dev';
        $isProdEnv = $applicationEnv === 'prod';
        $isCliContext = php_sapi_name() === 'cli';

        require_once 'vendor/autoload.php';
        require_once 'error-handler.php';

        // load either 'dev' or 'prod' initialization file
        require_once 'application-env/'.$applicationEnv.'.php';

Keep in mind that in the application everything is :code:`Exception`.
There is no difference between :code:`core errors` (`warnings`, etc) and :code:`exceptions`.

We combined exceptions and errors to make error controlling better.
You don't need to have several methods to catch all possible kind of errors.
Now you have only exceptions which can be easily handled by the :code:`try() catch`

To read more about errors:  https://www.php.net/manual/en/language.errors.basics.php

To read more about exceptions: https://www.php.net/manual/en/language.exceptions.php

Let's check a look those files closer to understand what exactly they do:

.. code-block:: php

    <?php

        // sourced from: application-env/dev.php

        error_reporting(E_ALL | E_STRICT);
        ini_set('display_errors', 1);


.. code-block:: php

    <?php

        // sourced from: application-env/prod.php

        error_reporting(E_PARSE | E_COMPILE_ERROR | E_ERROR | E_CORE_ERROR | E_USER_ERROR);
        ini_set('display_errors', 0);


So the first difference between :code:`dev` and :code:`prod` that the :code:`prod` doesn't display errors, it means
you just will see the `500 error page` (without the error trace of course) and in case of :code:`dev` you will see the :code:`Exception`
which is really helpful during the development process.

Secondly: you see that we consider about different kind of errors. For example in the :code:`dev` everything will be  :code:`Exception`,
even warnings about `not defined variables`, or `missing array keys`, etc (because of :code:`E_ALL`). And vise versa in the :code:`prod` we take into account only very serious
errors like: `parsing errors` (:code:`E_PARSE`), `compile errors` (:code:`E_COMPILE_ERROR`), etc.
All other will not be considered as errors and will be skipped.

You can read more about the error types on:

https://www.php.net/manual/en/function.error-reporting.php to make you own configuration.


When the :code:`prod` mode is activated all errors will be written to: :code:`data/log/error.log` file.

**PS:** when you application is going to be public, don't  forget to switch to  the :code:`prod`;
