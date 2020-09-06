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

use Tiny\Http;
use Tiny\Skeleton\Application\EventManager\ControllerEvent;

class BeforeDisplayingResponseCorsListener
{

    /**
     * @var Http\Request
     */
    private Http\Request $request;

    /**
     * @var Http\ResponseHttpUtils
     */
    private Http\ResponseHttpUtils $httpUtils;

    /**
     * @var string
     */
    private ?string $httpOrigin;

    /**
     * BeforeDisplayingResponseCorsListener constructor.
     *
     * @param  Http\Request            $request
     * @param  Http\ResponseHttpUtils  $httpUtils
     * @param  string|null             $httpOrigin
     */
    public function __construct(
        Http\Request $request,
        Http\ResponseHttpUtils $httpUtils,
        string $httpOrigin = null
    ) {
        $this->request = $request;
        $this->httpUtils = $httpUtils;
        $this->httpOrigin = $httpOrigin;
    }

    /**
     * @param  ControllerEvent  $event
     */
    public function __invoke(ControllerEvent $event)
    {
        // send basic cors headers
        if (!$this->request->isConsole() && $this->httpOrigin) {
            $this->httpUtils->sendHeaders(
                [
                    sprintf(
                        'Access-Control-Allow-Origin: %s', $this->httpOrigin
                    ),
                    'Access-Control-Allow-Credentials: true',
                    'Access-Control-Max-Age: 86400',
                ]
            );
        }
    }

}
