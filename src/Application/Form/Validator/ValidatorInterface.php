<?php

namespace Tiny\Skeleton\Application\Form\Validator;

interface ValidatorInterface
{

    /**
     * @param mixed
     *
     * @return bool
     */
    public function isValid($value): bool;

    /**
     * @return bool
     */
    public function breakChainOfValidators(): bool;

    /**
     * @param string $elementName
     *
     * @return string
     */
    public function getErrorMessage(string $elementName): string;

}