<?php

namespace Tiny\Skeleton\Application\Service;

use Google\Cloud\Translate\V2\TranslateClient;

class TranslationApiService
{

    /**
     * @var TranslateClient
     */
    private TranslateClient $translateClient;

    public function __construct(TranslateClient $translateClient)
    {
        $this->translateClient = $translateClient;
    }

    /**
     * @param string $content
     * @param string $sourceLanguage
     * @param string $targetLanguage
     *
     * @return array
     */
    public function translate(
        string $content,
        string $sourceLanguage,
        string $targetLanguage
    ): array {
        return $this->translateClient->translate($content, [
            'source' => $sourceLanguage,
            'target' => $targetLanguage
        ]);
    }

}