<?php

namespace Tiny\Skeleton\Module\Language\Controller;

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Tiny\Http\AbstractResponse;
use Tiny\Skeleton\Module\Base\Controller\AbstractController;
use Tiny\Skeleton\Module\Language\Service\LanguageService;

class LanguageApiController extends AbstractController
{

    /**
     * @var LanguageService
     */
    protected LanguageService $languageService;

    /**
     * AbstractUserController constructor.
     *
     * @param LanguageService $languageService
     */
    public function __construct(LanguageService $languageService)
    {
        $this->languageService = $languageService;
    }

    /**
     * @param  AbstractResponse  $response
     */
    public function list(AbstractResponse $response)
    {
        $this->jsonResponse($response, $this->languageService->findAll());
    }

}
