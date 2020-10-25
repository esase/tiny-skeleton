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
use Tiny\Skeleton\Application\Exception\Request\NotFoundException;
use Tiny\Skeleton\Application\Utility\ZipUtility;
use Tiny\Skeleton\Module\Base\Controller\AbstractController;
use Tiny\Skeleton\Module\Translation\Form\TranslationFormBuilder;
use Tiny\Skeleton\Module\Translation\Form\Validator\UniqueKey;
use Tiny\Skeleton\Module\Translation\Service\TranslationService;
use ZipArchive;

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
     * @var Http\ResponseHttpUtils
     */
    private Http\ResponseHttpUtils $httpUtils;

    /**
     * TranslationApiController constructor.
     *
     * @param TranslationService     $translationService
     * @param TranslationFormBuilder $translationFormBuilder
     * @param Http\ResponseHttpUtils $httpUtils
     */
    public function __construct(
        TranslationService $translationService,
        TranslationFormBuilder $translationFormBuilder,
        Http\ResponseHttpUtils $httpUtils
    ) {
        $this->translationService = $translationService;
        $this->translationFormBuilder = $translationFormBuilder;
        $this->httpUtils = $httpUtils;
    }

    /**
     * @param Http\AbstractResponse $response
     */
    public function list(Http\AbstractResponse $response)
    {
        $this->jsonResponse($response, $this->translationService->findAll());
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
        $form = $this->translationFormBuilder->initializeForm();
        $form->populateValues($request->getRawRequest());

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
        $form->populateValues($request->getRawRequest());

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

    /**
     * @param Http\AbstractResponse $response
     * @param Http\Request          $request
     */
    public function export(
        Http\AbstractResponse $response,
        Http\Request $request
    ) {
        $format = $request->getParam('format');

        switch ($format) {
            case 'json' :
                $filePath = $this->translationService->exportJsonTranslations();
                break;

            case 'yaml' :
            default:
                $filePath = '';
        }

        $this->httpUtils->sendHeaders(
            [
                'Content-type: application/force-download',
                'Content-Disposition: attachment; filename="test.zip"',
                'Content-Length: ' . filesize($filePath)
            ]
        );

        $response->setResponse(file_get_contents($filePath));
    }

}
