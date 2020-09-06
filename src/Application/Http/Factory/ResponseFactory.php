<?php

namespace Tiny\Skeleton\Application\Http\Factory;

use Tiny\Http;
use Tiny\ServiceManager\ServiceManager;

class ResponseFactory
{

    /**
     * @param  ServiceManager  $serviceManager
     *
     * @return Http\AbstractResponse
     */
    public function __invoke(ServiceManager $serviceManager
    ): Http\AbstractResponse {
        $response = php_sapi_name() === 'cli'
            ? new Http\ResponseCli()
            : new Http\ResponseHttp(
                $serviceManager->get(Http\ResponseHttpUtils::class)
            );

        return $response;
    }

}
