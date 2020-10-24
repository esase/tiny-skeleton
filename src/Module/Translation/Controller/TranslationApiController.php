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

use Exception;
use Tiny\Http;
use Tiny\Http\AbstractResponse;
use Tiny\Skeleton\Application\Exception\Request\NotFoundException;
use Tiny\Skeleton\Module\Base\Controller\AbstractController;
use Tiny\Skeleton\Module\Translation\Form\TranslationFormBuilder;
use Tiny\Skeleton\Module\Translation\Form\Validator\UniqueKey;
use Tiny\Skeleton\Module\Translation\Service\TranslationService;

class TranslationApiController extends AbstractController
{

    /**
     * @var TranslationService
     */
    protected TranslationService $translationService;

    /**
     * @var TranslationFormBuilder
     */
    private TranslationFormBuilder $translationFormBuilder;

    /**
     * TranslationApiController constructor.
     *
     * @param TranslationService     $translationService
     * @param TranslationFormBuilder $translationFormBuilder
     */
    public function __construct(
        TranslationService $translationService,
        TranslationFormBuilder $translationFormBuilder
    ) {
        $this->translationService = $translationService;
        $this->translationFormBuilder = $translationFormBuilder;
    }

    /**
     * @param AbstractResponse $response
     */
    public function list(AbstractResponse $response)
    {
        $this->jsonResponse($response, $this->translationService->findAll());
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
        $form = $this->translationFormBuilder->initializeForm();
        $form->populateValues($this->getRawRequest());

        // create a new translation
        if ($form->isValid()) {
            $translationId = $this->translationService->create(
                $form->getValue(TranslationFormBuilder::LANGUAGE_KEY),
                $form->getValue(TranslationFormBuilder::LANGUAGE),
                $form->getValue(TranslationFormBuilder::TRANSLATION)
            );

            return $this->jsonResponse(
                $response,
                $this->translationService->findOne($translationId)
            );
        }

        $responseCode = in_array(
            UniqueKey::class, $form->getErroredValidators()
        )
            ? AbstractResponse::RESPONSE_CONFLICT
            : AbstractResponse::RESPONSE_BAD_REQUEST;

        return $this->jsonResponse(
            $response, [
            'errors' => $form->getErrors()
        ], $responseCode
        );
    }

    /**
     * @param AbstractResponse $response
     * @param Http\Request     $request
     *
     * @return AbstractResponse
     * @throws NotFoundException
     * @throws Exception
     */
    public function update(AbstractResponse $response, Http\Request $request)
    {
        // make sure we have an existing translation
        $translationData = $this->translationService->findOne(
            $request->getParam('id')
        );

        if (!$translationData) {
            throw new NotFoundException();
        }

        // init the form
        $this->translationFormBuilder->setTranslationId(
            $request->getParam('id') // init the edit mode
        );
        $form = $this->translationFormBuilder->initializeForm();
        $form->populateValues($this->getRawRequest());

        // update the translation
        if ($form->isValid()) {
            $this->translationService->update(
                $request->getParam('id'),
                $form->getValue(TranslationFormBuilder::LANGUAGE_KEY),
                $form->getValue(TranslationFormBuilder::LANGUAGE),
                $form->getValue(TranslationFormBuilder::TRANSLATION)
            );

            return $this->jsonResponse(
                $response,
                $this->translationService->findOne($request->getParam('id'))
            );
        }

        $responseCode = in_array(
            UniqueKey::class, $form->getErroredValidators()
        )
            ? AbstractResponse::RESPONSE_CONFLICT
            : AbstractResponse::RESPONSE_BAD_REQUEST;

        return $this->jsonResponse(
            $response, [
            'errors' => $form->getErrors()
        ], $responseCode
        );
    }

    /**
     * @param AbstractResponse $response
     * @param Http\Request     $request
     *
     * @throws NotFoundException
     */
    public function delete(AbstractResponse $response, Http\Request $request)
    {
        // make sure we have an existing language key
        $translationData = $this->translationService->findOne(
            $request->getParam('id')
        );

        if ($translationData) {
            $this->translationService->deleteOne(
                $request->getParam('id')
            );

            return;
        }

        throw new NotFoundException();
    }

}
