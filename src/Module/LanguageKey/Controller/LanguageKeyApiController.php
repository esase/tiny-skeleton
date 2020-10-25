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
use Tiny\Skeleton\Application\Exception\Request\NotFoundException;
use Tiny\Skeleton\Module\Base\Controller\AbstractController;
use Tiny\Skeleton\Module\LanguageKey\Form\LanguageKeyFormBuilder;
use Tiny\Skeleton\Module\LanguageKey\Form\Validator\UniqueKey;
use Tiny\Skeleton\Module\LanguageKey\Service\LanguageKeyService;

class LanguageKeyApiController extends AbstractController
{

    /**
     * @var LanguageKeyService
     */
    protected LanguageKeyService $languageKeyService;

    /**
     * @var LanguageKeyFormBuilder
     */
    private LanguageKeyFormBuilder $languageKeyFormBuilder;

    /**
     * LanguageKeyApiController constructor.
     *
     * @param LanguageKeyService $languageKeyService
     * @param LanguageKeyFormBuilder $languageKeyFormBuilder
     */
    public function __construct(
        LanguageKeyService $languageKeyService,
        LanguageKeyFormBuilder $languageKeyFormBuilder
    ) {
        $this->languageKeyService = $languageKeyService;
        $this->languageKeyFormBuilder = $languageKeyFormBuilder;
    }

    /**
     * @param Http\AbstractResponse $response
     */
    public function list(Http\AbstractResponse $response)
    {
        $this->jsonResponse($response, $this->languageKeyService->findAll());
    }

    /**
     * @param Http\AbstractResponse $response
     * @param Http\Request          $request
     *
     * @return Http\AbstractResponse
     *
     * @throws Exception
     */
    public function create(
        Http\AbstractResponse $response,
        Http\Request $request
    ) {
        // init the form
        $form = $this->languageKeyFormBuilder->initializeForm();
        $form->populateValues($request->getRawRequest());

        // create a new language key
        if ($form->isValid()) {
            $keyId = $this->languageKeyService->create(
                $form->getValue(LanguageKeyFormBuilder::NAME)
            );

            return $this->jsonResponse(
                $response,
                $this->languageKeyService->findOne($keyId)
            );
        }

        $responseCode = in_array(
            UniqueKey::class, $form->getErroredValidators()
        )
            ? Http\AbstractResponse::RESPONSE_CONFLICT
            : Http\AbstractResponse::RESPONSE_BAD_REQUEST;

        return $this->jsonResponse(
            $response, [
            'errors' => $form->getErrors()
        ], $responseCode
        );
    }

    /**
     * @param Http\AbstractResponse $response
     * @param Http\Request          $request
     *
     * @return Http\AbstractResponse
     *
     * @throws NotFoundException
     * @throws Exception
     */
    public function update(
        Http\AbstractResponse $response,
        Http\Request $request
    ) {
        // make sure we have an existing language key
        $languageKeyData = $this->languageKeyService->findOne(
            $request->getParam('id')
        );

        if (!$languageKeyData) {
            throw new NotFoundException();
        }

        // init the form
        $this->languageKeyFormBuilder->setLanguageKeyId(
            $request->getParam('id') // init the edit mode
        );
        $form = $this->languageKeyFormBuilder->initializeForm();
        $form->populateValues($request->getRawRequest());

        // update the language key
        if ($form->isValid()) {
            $this->languageKeyService->update(
                $request->getParam('id'),
                $form->getValue(LanguageKeyFormBuilder::NAME)
            );

            return $this->jsonResponse(
                $response,
                $this->languageKeyService->findOne($request->getParam('id'))
            );
        }

        $responseCode = in_array(
            UniqueKey::class, $form->getErroredValidators()
        )
            ? Http\AbstractResponse::RESPONSE_CONFLICT
            : Http\AbstractResponse::RESPONSE_BAD_REQUEST;

        return $this->jsonResponse(
            $response, [
            'errors' => $form->getErrors()
        ], $responseCode
        );
    }

    /**
     * @param Http\AbstractResponse $response
     * @param Http\Request          $request
     *
     * @throws NotFoundException
     */
    public function delete(
        Http\AbstractResponse $response,
        Http\Request $request
    ) {
        // make sure we have an existing language key
        $languageKeyData = $this->languageKeyService->findOne(
            $request->getParam('id')
        );

        if ($languageKeyData) {
            $this->languageKeyService->deleteOne(
                $request->getParam('id')
            );

            return;
        }

        throw new NotFoundException();
    }

}
