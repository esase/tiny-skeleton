<?php

namespace Tiny\Skeleton\Application;

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Throwable;
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
    private string $errorTemplate;

    /**
     * @var bool
     */
    private bool $isProdEnv;

    /**
     * ErrorHandler constructor.
     *
     * @param  bool    $isProdEnv
     * @param  bool    $isCliContext
     * @param  string  $errorTemplate
     * @param  string  $errorLogPath
     */
    public function __construct(
        bool $isProdEnv,
        bool $isCliContext,
        string $errorTemplate,
        string $errorLogPath
    ) {
        $this->isProdEnv = $isProdEnv;
        $this->isCliContext = $isCliContext;
        $this->errorTemplate = $errorTemplate;
        $this->errorLogPath = $errorLogPath;
    }

    /**
     * @param  Throwable  $exception
     *
     * @return View|null
     * @throws Throwable
     */
    public function logError(Throwable $exception)
    {
        // developers must see the error
        if (!$this->isProdEnv) {
            throw $exception;
        }

        // save the error log
        file_put_contents(
            $this->errorLogPath, json_encode(
                [
                    'date'    => date('Y-m-d H:i:s'),
                    'message' => $exception->getMessage(),
                    'file'    => $exception->getFile(),
                    'line'    => $exception->getLine(),
                    'trace'   => $exception->getTrace(),
                ]
            )."\n", FILE_APPEND
        );

        // show the 500 page (only in the http context)
        if (!$this->isCliContext) {
            return new View(['error' => $exception], $this->errorTemplate);
        }
    }

}
