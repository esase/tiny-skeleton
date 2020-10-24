<?php

namespace Tiny\Skeleton\Module\Translation\Form\Validator;

use Tiny\Skeleton\Application\Form\Validator\ValidatorInterface;
use Tiny\Skeleton\Module\LanguageKey\Service\LanguageKeyService;

class LanguageKey implements ValidatorInterface
{

    /**
     * @var LanguageKeyService
     */
    private LanguageKeyService $languageKeyService;

    /***
     * Language key constructor.
     *
     * @param LanguageKeyService $languageKeyService
     */
    public function __construct(LanguageKeyService $languageKeyService)
    {
        $this->languageKeyService = $languageKeyService;
    }

    /**
     * @param       $value
     * @param array $values
     *
     * @return bool
     */
    public function isValid($value, array $values = []): bool
    {
        return $value && $this->languageKeyService->findOne($value) != false;
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
