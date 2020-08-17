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

require_once __DIR__.'/../vendor/autoload.php';

// init application
$application = new Skeleton\Application(
    new Skeleton\Bootstrapper(
        new Skeleton\BootstrapperUtils(dirname(__DIR__)),
        getenv('APPLICATION_ENV') === 'prod'
    ),
    php_sapi_name() === 'cli',
    require_once __DIR__.'/../modules.php'
);

echo $application->run();
