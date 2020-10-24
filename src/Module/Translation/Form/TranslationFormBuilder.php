<?php

namespace Tiny\Skeleton\Module\Translation\Form;

use Tiny\Skeleton\Application\Form\AbstractFormBuilder;
use Tiny\Skeleton\Application\Form\Filter\TrimString;
use Tiny\Skeleton\Application\Form\Form;
use Tiny\Skeleton\Application\Form\Validator\Required;
use Tiny\Skeleton\Module\Language\Service\LanguageService;
use Tiny\Skeleton\Module\LanguageKey\Service\LanguageKeyService;
use Tiny\Skeleton\Module\Translation\Form\Validator\Language;
use Tiny\Skeleton\Module\Translation\Form\Validator\LanguageKey;
use Tiny\Skeleton\Module\Translation\Form\Validator\UniqueKey;
use Tiny\Skeleton\Module\Translation\Service\TranslationService;

class TranslationFormBuilder extends AbstractFormBuilder
{
    const LANGUAGE_KEY = 'language_key';
    const LANGUAGE = 'language';
    const TRANSLATION = 'translation';

    /**
     * @var int
     */
    protected int $translationId = -1;

    /**
     * @var TranslationService
     */
    private TranslationService $translationService;

    /**
     * @var LanguageKeyService
     */
    private LanguageKeyService $languageKeyService;

    /**
     * @var LanguageService
     */
    private LanguageService $languageService;

    public function __construct(
        Form $form,
        TranslationService $translationService,
        LanguageKeyService $languageKeyService,
        LanguageService $languageService
    ) {
        parent::__construct($form);
        $this->translationService = $translationService;
        $this->languageKeyService = $languageKeyService;
        $this->languageService = $languageService;
    }

    /**
     * @return Form
     */
    public function initializeForm(): Form
    {
        $this->form->addElement(
            self::LANGUAGE_KEY, [
            new Required(),
            new LanguageKey($this->languageKeyService),
        ]);

        $this->form->addElement(
            self::LANGUAGE, [
            new Required(),
            new Language($this->languageService),
        ]);

        $this->form->addElement(
            self::TRANSLATION, [
            new Required(),
            new UniqueKey($this->translationService, $this->translationId)
        ], [new TrimString()]
        );

        return $this->form;
    }

    /**
     * @param int $translationId
     */
    public function setTranslationId(int $translationId)
    {
        $this->translationId = $translationId;
    }

}