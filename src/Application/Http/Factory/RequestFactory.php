<?php

namespace Tiny\Skeleton\Application\Http\Factory;

use Tiny\ServiceManager\ServiceManager;
use Tiny\Http;

class RequestFactory
{

    /**
     * @param  ServiceManager  $serviceManager
     *
     * @return Http\Request
     */
    public function __invoke(ServiceManager $serviceManager): Http\Request
    {
        return new Http\Request(
            php_sapi_name() === 'cli'
                ? new Http\RequestCliParams($_SERVER)
                : new Http\RequestHttpParams($_SERVER)
        );
    }

}
