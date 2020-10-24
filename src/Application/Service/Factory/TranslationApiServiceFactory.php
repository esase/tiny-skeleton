<?php

namespace Tiny\Skeleton\Application\Service\Factory;

use Tiny\ServiceManager\ServiceManager;
use Tiny\Skeleton\Application\Service\ConfigService;
use Tiny\Skeleton\Application\Service\TranslationApiService;
use Google\Cloud\Translate\V2\TranslateClient;

class TranslationApiServiceFactory
{

    /**
     * @param  ServiceManager  $serviceManager
     *
     * @return TranslationApiService
     */
    public function __invoke(ServiceManager $serviceManager): TranslationApiService
    {
        /** @var ConfigService $config */
        $config = $serviceManager->get(ConfigService::class);

        $googleTranslateConfig = $config->getConfig('google_translate_config');

        $translateClient = new TranslateClient([
            'keyFile' => json_decode(file_get_contents($googleTranslateConfig), true)
        ]);

        return new TranslationApiService($translateClient);
    }

}
