<?php

namespace Tiny\Skeleton\Application\Form\Validator;

interface ValidatorInterface
{

    /**
     * @param       $value
     * @param array $values
     *
     * @return bool
     */
    public function isValid($value, array $values = []): bool;

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