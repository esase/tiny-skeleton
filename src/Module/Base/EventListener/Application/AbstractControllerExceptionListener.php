<?php

namespace Tiny\Skeleton\Module\Base\EventListener\Application;

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Tiny\Http\AbstractResponse;
use Tiny\Skeleton\Application\Bootstrapper;
use Tiny\View\View;

abstract class AbstractControllerExceptionListener
{

    /**
     * @param  string  $context
     *
     * @return bool
     */
    protected function isJsonErrorResponse(string $context): bool
    {
        return in_array($context, [Bootstrapper::ROUTE_CONTEXT_CLI,
                                   Bootstrapper::ROUTE_CONTEXT_HTTP_API]
        );
    }

    /**
     * @param  AbstractResponse  $response
     * @param  View              $view
     * @param  int               $errorCode
     */
    protected function viewErrorResponse(
        AbstractResponse $response,
        View $view,
        int $errorCode
    ) {
        $response->setResponse($view)
            ->setCode($errorCode)
            ->setResponseType(
                AbstractResponse::RESPONSE_TYPE_HTML
            );
    }

    /**
     * @param  AbstractResponse  $response
     * @param  string            $errorMessage
     * @param  int               $errorCode
     */
    protected function jsonErrorResponse(
        AbstractResponse $response,
        string $errorMessage,
        int $errorCode
    ) {
        $response->setResponse(
            json_encode(
                [
                    'error' => $errorMessage,
                    'code'  => $errorCode,
                ]
            )
        )
            ->setCode(AbstractResponse::RESPONSE_OK)
            ->setResponseType(
                AbstractResponse::RESPONSE_TYPE_JSON
            );
    }

}
