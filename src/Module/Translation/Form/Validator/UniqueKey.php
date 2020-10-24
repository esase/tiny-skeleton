<?php

namespace Tiny\Skeleton\Module\Translation\Form\Validator;

use Tiny\Skeleton\Application\Form\Validator\ValidatorInterface;
use Tiny\Skeleton\Module\Translation\Form\TranslationFormBuilder;
use Tiny\Skeleton\Module\Translation\Service\TranslationService;

class UniqueKey implements ValidatorInterface
{

    /**
     * @var TranslationService
     */
    private TranslationService $translationService;

    /**
     * @var int
     */
    private int $translationId;

    /**
     * UniqueKey constructor.
     *
     * @param TranslationService $translationService
     * @param int                $translationId
     */
    public function __construct(
        TranslationService $translationService,
        int $translationId
    ) {
        $this->translationService = $translationService;
        $this->translationId = $translationId;
    }

    /**
     * @param       $value
     * @param array $values
     *
     * @return bool
     */
    public function isValid($value, array $values = []): bool
    {
        $languageKey = $values[TranslationFormBuilder::LANGUAGE_KEY] ?? null;
        $language = $values[TranslationFormBuilder::LANGUAGE] ?? null;

        if ($languageKey && $language) {
            return !$this->translationService->isTranslationExist(
                $languageKey,
                $language,
                $this->translationId
            );
        }

        return true;
    }

    /**
     * @param string $elementName
     *
     * @return string
     */
    public function getErrorMessage(string $elementName): string
    {
        return '`' . $elementName . '` already exist';
    }

    /**
     * @return bool
     */
    public function breakChainOfValidators(): bool
    {
        return true;
    }

}
