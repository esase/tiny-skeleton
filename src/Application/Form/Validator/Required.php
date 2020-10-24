<?php

namespace Tiny\Skeleton\Application\Form\Validator;


class Required implements ValidatorInterface
{

    /**
     * @param mixed
     *
     * @return bool
     */
    public function isValid($value): bool
    {
        if (is_array($value)) {
            if (sizeof($value) === 0) {

                return false;
            }
        }
        else if ($value === null || mb_strlen(trim($value)) === 0) {

            return false;
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
        return '`' . $elementName . '` must be filled';
    }

    /**
     * @return bool
     */
    public function breakChainOfValidators(): bool
    {
        return true;
    }

}
