<?php

namespace Tiny\Skeleton\Module\LanguageKey\Controller;

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Exception;
use Tiny\Http;
use Tiny\Http\AbstractResponse;
use Tiny\Skeleton\Module\Base\Controller\AbstractController;
use Tiny\Skeleton\Module\LanguageKey\Form\LanguageKeyBuilder;
use Tiny\Skeleton\Module\LanguageKey\Service\LanguageKeyService;

class LanguageKeyApiController extends AbstractController
{

    /**
     * @var LanguageKeyService
     */
    protected LanguageKeyService $languageKeyService;

    /**
     * @var LanguageKeyBuilder
     */
    private LanguageKeyBuilder $languageKeyFormBuilder;

    /**
     * AbstractUserController constructor.
     *
     * @param LanguageKeyService $languageKeyService
     * @param LanguageKeyBuilder $languageKeyBuilder
     */
    public function __construct(
        LanguageKeyService $languageKeyService,
        LanguageKeyBuilder $languageKeyBuilder
    ) {
        $this->languageKeyService = $languageKeyService;
        $this->languageKeyFormBuilder = $languageKeyBuilder;
    }

    /**
     * @param AbstractResponse $response
     */
    public function list(AbstractResponse $response)
    {
        $this->jsonResponse($response, $this->languageKeyService->findAll());
    }

    /**
     * @param AbstractResponse $response
     *
     * @return AbstractResponse
     * @throws Exception
     */
    public function create(AbstractResponse $response)
    {
        // init the form
        $form = $this->languageKeyFormBuilder->initializeForm();
        $form->populateValues($_POST);

        // create a new key
        if ($form->isValid()) {
            $keyId = $this->languageKeyService->create(
                $form->getValue(LanguageKeyBuilder::NAME)
            );

            return  $this->jsonResponse(
                $response,
                $this->languageKeyService->findOne($keyId)
            );
        }

        return $this->jsonResponse(
            $response,
            [
                'errors' => $form->getErrors()
            ],
            AbstractResponse::RESPONSE_BAD_REQUEST
        );
    }

    /**
     * @param AbstractResponse $response
     * @param Http\Request     $request
     */
    public function delete(AbstractResponse $response, Http\Request $request)
    {
        // get a key
        $keyData = $this->languageKeyService->findOne(
            $request->getParam('id')
        );

        if ($keyData) {
            $this->languageKeyService->deleteOne(
                $request->getParam('id')
            );

            return;
        }

        $response->setCode(AbstractResponse::RESPONSE_NOT_FOUND);
    }

}
