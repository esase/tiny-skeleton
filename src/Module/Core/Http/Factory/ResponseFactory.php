<?php

namespace Tiny\Skeleton\Module\Core\Http\Factory;

use Tiny\Http\AbstractResponse;
use Tiny\Http\ResponseCli;
use Tiny\Http\ResponseHttp;
use Tiny\Http\ResponseHttpUtils;
use Tiny\ServiceManager\ServiceManager;

class ResponseFactory
{

    /**
     * @param  ServiceManager  $serviceManager
     *
     * @return AbstractResponse
     */
    public function __invoke(ServiceManager $serviceManager): AbstractResponse
    {
        $response = php_sapi_name() === 'cli'
            ? new ResponseCli()
            : new ResponseHttp(
                $serviceManager->get(ResponseHttpUtils::class)
            );

        return $response;
    }

}
