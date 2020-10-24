<?php

namespace Tiny\Skeleton\Module\Translation\Form\Validator;

use Tiny\Skeleton\Application\Form\Validator\ValidatorInterface;
use Tiny\Skeleton\Module\Language\Service\LanguageService;

class Language implements ValidatorInterface
{

    /**
     * @var LanguageService
     */
    private LanguageService $languageService;

    /***
     * Language constructor.
     *
     * @param LanguageService $languageService
     */
    public function __construct(LanguageService $languageService)
    {
        $this->languageService = $languageService;
    }

    /**
     * @param       $value
     * @param array $values
     *
     * @return bool
     */
    public function isValid($value, array $values = []): bool
    {
        return $value && $this->languageService->findOne($value) != false;
    }

    /**
     * @param string $elementName
     *
     * @return string
     */
    public function getErrorMessage(string $elementName): string
    {
        return '`' . $elementName . '` is not exist';
    }

    /**
     * @return bool
     */
    public function breakChainOfValidators(): bool
    {
        return true;
    }

}
