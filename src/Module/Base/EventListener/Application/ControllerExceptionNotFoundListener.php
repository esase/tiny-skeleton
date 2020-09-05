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
use Tiny\Skeleton\Application\Exception\Request\NotFoundException;
use Tiny\Skeleton\Module\Base;
use Tiny\Http;
use Tiny\Skeleton\Module\Base\Service\NotFoundService;

class ControllerExceptionNotFoundListener
{

    /**
     * @var Http\AbstractResponse
     */
    private Http\AbstractResponse $response;

    /**
     * @var NotFoundService
     */
    private NotFoundService $service;

    /**
     * ControllerExceptionNotFoundListener constructor.
     *
     * @param  Http\AbstractResponse  $response
     * @param  NotFoundService        $service
     */
    public function __construct(
        Http\AbstractResponse $response,
        NotFoundService $service
    ) {
        $this->response = $response;
        $this->service = $service;
    }

    /**
     * @param  ControllerEvent  $event
     */
    public function __invoke(ControllerEvent $event)
    {
        $exception = $event->getParams()['exception'] ?? null;

        if ($exception && $exception instanceof NotFoundException) {
            $this->response
                ->setCode(Http\AbstractResponse::RESPONSE_NOT_FOUND)
                ->setResponse($this->service->getContent());

            $event->setData($this->response);
        }
    }

}
