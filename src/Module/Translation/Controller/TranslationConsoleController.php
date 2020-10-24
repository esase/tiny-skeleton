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

use Tiny\Skeleton\Module\Base\Controller\AbstractController;
use Tiny\Skeleton\Module\Translation\Service\TranslationService;

class TranslationConsoleController extends AbstractController
{

    const DEFAULT_ITEMS_LIMIT = 5;

    /**
     * @var TranslationService
     */
    protected TranslationService $translationService;

    /**
     * TranslationConsoleController constructor.
     *
     * @param TranslationService $translationService
     */
    public function __construct(
        TranslationService $translationService
    ) {
        $this->translationService = $translationService;
    }

    public function automaticTranslate()
    {
        $queueItems = $this->translationService->findQueuedItems(
            self::DEFAULT_ITEMS_LIMIT
        );

        foreach ($queueItems as $queueItem) {
            try {
                $translations = $this->translationService->findAllTranslations(
                    $queueItem['language_key']
                );

                if ($translations) {
                    $this->processEmptyTranslations($translations);
                }
            } finally {
//               $this->translationService->deleteQueuedItem($queueItem['id']);
            }
        }
    }

    private function processEmptyTranslations(array $translations)
    {
        $emptyLanguages = [];
        $sourceTranslation = '';
        $sourceLanguage = '';

        foreach ($translations as $translation) {
            if(!$translation['translation']) {
                $emptyLanguages[] =[
                    'id' => $translation['languageId'],
                    'iso' => $translation['languageIso'],
                ];
            }
            // define a source translation
            if ($translation['translation'] &&  !$translation['automatic']) {
                $sourceTranslation = $translation['translation'];
                $sourceLanguage = $translation['languageIso'];
            }
        }

        if ($emptyLanguages) {
            // TODO: do translations and update values in the DB
        }
    }
}
