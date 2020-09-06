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

use Tiny\Http\AbstractResponse;
use Tiny\Skeleton\Application\Exception\Request\BaseException;
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
     * @param  AbstractResponse  $response
     */
    public function index(AbstractResponse $response)
    {
        $this->service->getContent(
            $response,
            BaseException::TYPE_HTML
        );
    }

}
