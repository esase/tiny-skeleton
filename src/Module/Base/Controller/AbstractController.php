<?php

namespace Tiny\Skeleton\Module\Base\Controller;

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Tiny\View\View;
use Tiny\Http\AbstractResponse;

abstract class AbstractController
{

    /**
     * @param AbstractResponse $response
     * @param array            $variables
     * @param int              $code
     *
     * @return AbstractResponse
     */
    protected function viewResponse(
        AbstractResponse $response,
        array $variables = [],
        int $code = AbstractResponse::RESPONSE_OK
    ): AbstractResponse {
        $response->setResponse(new View($variables))
            ->setCode($code)
            ->setResponseType(
                AbstractResponse::RESPONSE_TYPE_HTML
            );

        return $response;
    }

    /**
     * @param AbstractResponse $response
     * @param array            $variables
     * @param int              $code
     *
     * @return AbstractResponse
     */
    protected function jsonResponse(
        AbstractResponse $response,
        array $variables = [],
        int $code = AbstractResponse::RESPONSE_OK
    ): AbstractResponse {
        $response->setResponse(json_encode($variables))
            ->setCode($code)
            ->setResponseType(
                AbstractResponse::RESPONSE_TYPE_JSON
            );

        return $response;
    }

    /**
     * @return array
     */
    public function getRawRequest(): array
    {
        return json_decode(file_get_contents("php://input"), true) ?? [];
    }

}
