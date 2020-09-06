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

use Tiny\Skeleton\Application\EventManager\ControllerEvent;
use Tiny\Http;
use Tiny\Router\Route;
use Tiny\Skeleton\Module\Base\Controller\NotFoundController;

class BeforeCallingControllerCorsListener
{

    /**
     * @var Http\Request
     */
    private Http\Request $request;

    /**
     * @var Http\AbstractResponse
     */
    private Http\AbstractResponse $response;

    /**
     * @var Http\ResponseHttpUtils
     */
    private Http\ResponseHttpUtils $httpUtils;

    /**
     * @var string|null
     */
    private ?string $requestMethod;

    /**
     * @var string|null
     */
    private ?string $requestHeaders;

    /**
     * BeforeCallingControllerCorsListener constructor.
     *
     * @param  Http\Request            $request
     * @param  Http\AbstractResponse   $response
     * @param  Http\ResponseHttpUtils  $httpUtils
     * @param  string|null             $requestMethod
     * @param  string|null             $requestHeaders
     */
    public function __construct(
        Http\Request $request,
        Http\AbstractResponse $response,
        Http\ResponseHttpUtils $httpUtils,
        string $requestMethod = null,
        string $requestHeaders = null
    ) {
        $this->request = $request;
        $this->response = $response;
        $this->httpUtils = $httpUtils;
        $this->requestMethod = $requestMethod;
        $this->requestHeaders = $requestHeaders;
    }

    /**
     * @param ControllerEvent  $event
     */
    public function __invoke(ControllerEvent $event)
    {
        // send additional cors headers
        if ($this->request->isOptions()) {
            /** @var Route $route */
            $route = $event->getParams()['route'];
            $headers = [];

            if ($route->getController() === NotFoundController::class) {
                return;
            }

            if ($this->requestMethod) {
                $headers[] = sprintf(
                    'Access-Control-Allow-Methods: %s',
                    $this->collectAllowedMethods($route)
                );
            }

            // we may accept all custom headers
            if ($this->requestHeaders) {
                $headers[] = sprintf(
                    'Access-Control-Allow-Headers: %s', $this->requestHeaders
                );
            }

            if ($headers) {
                $this->httpUtils->sendHeaders($headers);
            }

            // return empty response
            $event->setData($this->response);
        }
    }

    /**
     * @param  Route  $route
     *
     * @return string
     */
    private function collectAllowedMethods(Route $route): string
    {
        // we only accept specific http methods supported by the route
        if (is_array($route->getActionList())) {
            return implode(', ', array_keys($route->getActionList()));
        }

        // we accept all the http methods
        return implode(
            ', ', [
                Http\Request::METHOD_GET,
                Http\Request::METHOD_POST,
                Http\Request::METHOD_DELETE,
                Http\Request::METHOD_PUT,
                Http\Request::METHOD_OPTIONS,
            ]
        );
    }

}
