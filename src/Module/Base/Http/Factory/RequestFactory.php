<?php

namespace Tiny\Skeleton\Module\Base\Http\Factory;

use Tiny\ServiceManager\ServiceManager;
use Tiny\Http\Request;
use Tiny\Http\RequestCliParams;
use Tiny\Http\RequestHttpParams;

class RequestFactory
{

    /**
     * @param  ServiceManager  $serviceManager
     *
     * @return Request
     */
    public function __invoke(ServiceManager $serviceManager): Request
    {
        return new Request(
            php_sapi_name() === 'cli'
                ? new RequestCliParams($_SERVER)
                : new RequestHttpParams($_SERVER)
        );
    }

}