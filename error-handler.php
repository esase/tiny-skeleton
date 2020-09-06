<?php

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Tiny\Skeleton\Application\ErrorHandler;
use Tiny\View\View;

// convert all php errors to exceptions
set_error_handler(
    function ($severity, $message, $file, $line) {
        if (error_reporting() & $severity) {
            throw new ErrorException(
                $message, 0, $severity, $file, $line
            );
        }
    }
);

// init the error handler
$errorHandler = new ErrorHandler(
    $isProdEnv,
    $isCliContext,
    'src/Application/view/500.phtml',
    'data/log/error.log'
);

// collect all uncaught errors
set_exception_handler(
    function ($exception) use ($errorHandler) {
        $result = $errorHandler->logError($exception);

        if ($result instanceof View) {
            header(
                $_SERVER['SERVER_PROTOCOL'].' 500 Internal Server Error', true,
                500
            );
            echo $result;

            return;
        }

        echo 'Error occurred, please try again later';
    }
);
