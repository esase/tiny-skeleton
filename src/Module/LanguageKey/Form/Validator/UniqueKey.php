<?php

namespace Tiny\Skeleton\Module\LanguageKey\Form\Validator;

use Tiny\Skeleton\Application\Form\Validator\ValidatorInterface;
use Tiny\Skeleton\Module\LanguageKey\Service\LanguageKeyService;

class UniqueKey implements ValidatorInterface
{

    /**
     * @var LanguageKeyService
     */
    private LanguageKeyService $languageKeyService;

    /**
     * @var int
     */
    private int $languageId;

    public function __construct(
        LanguageKeyService $languageKeyService,
        int $languageId
    ) {
        $this->languageKeyService = $languageKeyService;
        $this->languageId = $languageId;
    }

    /**
     * @param mixed
     *
     * @return bool
     */
    public function isValid($value): bool
    {
        return !$this->languageKeyService->isLanguageKeyExist(
            $value,
            $this->languageId
        );
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
