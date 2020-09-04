<?php

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Tiny\Skeleton\Application;

chdir(dirname(__DIR__));

$applicationEnv = getenv('APPLICATION_ENV') ?: 'dev';
$isProdEnv = $applicationEnv === 'prod';
$isCliContext = php_sapi_name() === 'cli';

require_once 'vendor/autoload.php';
require_once 'error-handler.php';
require_once 'application-env/'.$applicationEnv.'.php';

// init application
$application = new Application\Application(
    new Application\Bootstrapper(
        new Application\BootstrapperUtils(getcwd()),
        $isProdEnv
    ),
    $isCliContext,
    require_once 'modules.php'
);

echo $application->run();
