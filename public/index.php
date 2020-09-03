<?php

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Tiny\Skeleton;

chdir(dirname(__DIR__));

require_once 'vendor/autoload.php';

$currentAppEnv = getenv('APPLICATION_ENV') === 'prod' ? 'prod' : 'dev';
$isCliContext = php_sapi_name() === 'cli';

// init error handler
$errorHandler = new Skeleton\ErrorHandler(
    $currentAppEnv === 'prod',
    $isCliContext,
    'src/Module/Core/view/layout/500.phtml',
    'data/log/error.log'
);

$errorHandler->initHandlers();

require_once 'application-env/'.$currentAppEnv.'.php';

// init application
$application = new Skeleton\Application(
    new Skeleton\Bootstrapper(
        new Skeleton\BootstrapperUtils(getcwd()),
        $currentAppEnv === 'prod'
    ),
    $isCliContext,
    require_once 'modules.php'
);

echo $application->run();

// The schema is very simple:
// Either you control you exceptions or the core will handle all error for you and shows  the 500 page