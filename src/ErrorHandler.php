<?php

namespace Tiny\Skeleton;

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ErrorException;
use Tiny\View\View;

class ErrorHandler
{

    /**
     * @var bool
     */
    private bool $isCliContext;

    /**
     * @var string
     */
    private string $errorLogPath;

    /**
     * @var string
     */
    private string $errorlayout;

    /**
     * @var bool
     */
    private bool $isProdEnv;


    public function __construct(bool $isProdEnv, bool $isCliContext, string $errorlayout, string $errorLogPath)
    {
        $this->isProdEnv = $isProdEnv;
        $this->isCliContext = $isCliContext;
        $this->errorlayout = $errorlayout;
        $this->errorLogPath = $errorLogPath;
    }

    public function initHandlers()
    {
        // convert all php errors to exceptions
        set_error_handler(function ($severity, $message, $file, $line) {
            if (error_reporting() & $severity) {
                throw new ErrorException($message, 0, $severity, $file, $line);
            }
        });

        set_exception_handler(function ($exception) {
            // TODO: log the error
            echo 'log error';

            // developers must see the error
            if (!$this->isProdEnv) {
                throw $exception;
            }


            // show the 500 page
            if (!$this->isCliContext) {
                $this->display500Page();
            }

//            // TODO: if is dev mode just re throw exception
//            // otherwise show
//            // TODO: log the error in the file
//            echo '!!!log error and show 500 page??? FOR THE WEB';
//
////            echo $exception; /// what should we do with dev mode ??
//            /// I have to display the error
//
//            if (!$this->isCliContext) {
//                $this->display500Page();
//            }
//
//
//            throw $exception;
        });
    }

    protected function display500Page()
    {
        header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
        echo new View([], $this->errorlayout);
    }

}
