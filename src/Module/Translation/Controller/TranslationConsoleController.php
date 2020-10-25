<?php

namespace Tiny\Skeleton\Module\Translation\Controller;

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Throwable;
use Tiny\Skeleton\Application\Service\TranslationApiService;
use Tiny\Skeleton\Module\Base\Controller\AbstractController;
use Tiny\Skeleton\Module\Translation\Service\TranslationQueueService;
use Tiny\Skeleton\Module\Translation\Service\TranslationService;

class TranslationConsoleController extends AbstractController
{

    const DEFAULT_ITEMS_LIMIT = 5;

    /**
     * @var TranslationService
     */
    protected TranslationService $translationService;

    /**
     * @var TranslationQueueService
     */
    protected TranslationQueueService $translationQueueService;

    /**
     * @var TranslationApiService
     */
    private TranslationApiService $translationApiService;

    /**
     * TranslationConsoleController constructor.
     *
     * @param TranslationService      $translationService
     * @param TranslationQueueService $translationQueueService
     * @param TranslationApiService   $translationApiService
     */
    public function __construct(
        TranslationService $translationService,
        TranslationQueueService $translationQueueService,
        TranslationApiService $translationApiService
    ) {
        $this->translationService = $translationService;
        $this->translationQueueService = $translationQueueService;
        $this->translationApiService = $translationApiService;
    }

    public function automaticTranslate()
    {
        // get items form the queue
        $queueItems = $this->translationQueueService->findLimited(
            self::DEFAULT_ITEMS_LIMIT
        );

        foreach ($queueItems as $queueItem) {
            try {
                $translations = $this->translationService->findAllTranslations(
                    $queueItem['language_key']
                );

                if ($translations) {
                    $this->processEmptyTranslations(
                        $queueItem['language_key'],
                        $translations
                    );
                }
            }
            catch(Throwable $e) {}

            // delete the item from the queue
            $this->translationQueueService->deleteOne($queueItem['id']);
        }
    }

    /**
     * @param int   $languageKey
     * @param array $translations
     */
    private function processEmptyTranslations(
        int $languageKey,
        array $translations
    ) {
        $emptyLanguages = [];
        $sourceTranslation = '';
        $sourceLanguage = '';

        // collect a list of not translated keys with their languages
        foreach ($translations as $translation) {
            if (!$translation['translation']) {
                $emptyLanguages[] = [
                    'id'  => $translation['languageId'],
                    'iso' => $translation['languageIso'],
                ];
            }
            // define a source translation (it would be any not automatically translated one)
            if ($translation['translation'] && !$translation['automatic']) {
                $sourceTranslation = $translation['translation'];
                $sourceLanguage = $translation['languageIso'];
            }
        }

        // translate the key for the rest of languages
        if ($emptyLanguages) {
            foreach ($emptyLanguages as $language) {
                $result = $this->translationApiService->translate(
                    $sourceTranslation,
                    $sourceLanguage,
                    $language['iso']
                );
                if (!empty($result['text'])
                    && $sourceTranslation != $result['text']) {
                    $this->translationService->create(
                        $languageKey,
                        $language['id'],
                        $result['text'],
                        true
                    );
                }
            }
        }
    }
}
