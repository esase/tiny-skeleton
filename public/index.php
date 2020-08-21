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

$currentAppEnv = getenv('APPLICATION_ENV') === 'prod' ? 'prod' : 'dev';

require_once 'application-env/'.$currentAppEnv.'.php';
require_once 'vendor/autoload.php';

// init application
$application = new Skeleton\Application(
    new Skeleton\Bootstrapper(
        new Skeleton\BootstrapperUtils(getcwd()),
        $currentAppEnv === 'prod'
    ),
    php_sapi_name() === 'cli',
    require_once 'modules.php'
);

echo $application->run();
