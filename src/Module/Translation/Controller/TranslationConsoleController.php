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
     * @param TranslationService     $translationService
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
            // TODO process the automatic translation
           try {

           }
           finally {
//               $this->translationService->deleteQueuedItem($queueItem['id']);
           }
       }
    }

}
