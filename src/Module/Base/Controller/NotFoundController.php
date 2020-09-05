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

use Tiny\Http;
use Tiny\Skeleton\Module\Base\Service\NotFoundService;
use Tiny\Skeleton\Module\User;

class NotFoundController
{

    /**
     * @var NotFoundService
     */
    private NotFoundService $service;

    /**
     * NotFoundController constructor.
     *
     * @param  NotFoundService  $service
     */
    public function __construct(NotFoundService $service)
    {
        $this->service = $service;
    }

    /**
     * @param  Http\AbstractResponse  $response
     */
    public function index(Http\AbstractResponse $response)
    {
        $response->setResponse(
            $this->service->getContent()
        )->setCode(Http\AbstractResponse::RESPONSE_NOT_FOUND);
    }

}
