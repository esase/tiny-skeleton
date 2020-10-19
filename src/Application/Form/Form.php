<?php

namespace Tiny\Skeleton\Application\Form;

use Tiny\Skeleton\Application\Form\Filter\FilterInterface;
use Tiny\Skeleton\Application\Form\Validator\ValidatorInterface;
use Exception;

class Form
{
    /**
     * @var array
     */
    protected $formElements = [];

    /**
     * @var array
     */
    protected $validators = [];

    /**
     * @var array
     */
    protected $filters = [];

    /**
     * @var array
     */
    protected $formValues = [];

    /**
     * @var bool
     */
    private $isFormPopulated = false;

    /**
     * @var bool
     */
    protected $isFormValid = true;

    /**
     * @var array
     */
    protected $formErrors = [];

    /**
     * @param string $name
     * @param ValidatorInterface[] $validators
     * @param FilterInterface[] $filters
     *
     * @return $this
     */
    public function addElement(
        string $name,
        array $validators = [],
        array $filters = []
    ): self {
        $this->formElements[] = $name;
        $this->validators[$name] = $validators;
        $this->filters[$name] = $filters;

        return $this;
    }

    /**
     * @param array $values
     *
     * @return $this
     */
    public function populateValues(array $values): self
    {
        $this->formValues = $values;
        $this->isFormPopulated = true;
        $this->applyFilters();
        return $this;
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function isValid(): bool
    {
        if (!$this->isFormPopulated) {
            throw new Exception('The form must be populated before validation');
        }

        // validate elements
        foreach ($this->formElements as $element) {
            if ($this->validators[$element]) {
                /** @var  ValidatorInterface $validator */
                foreach ($this->validators[$element] as $validator) {
                    if (!$validator->isValid(
                        $this->formValues[$element] ?? null
                    )) {
                        $this->isFormValid = false;
                        $this->formErrors[] = $validator->getErrorMessage(
                            $element
                        );

                        if ($validator->breakChainOfValidators()) {
                            break;
                        }
                    }
                }
            }
        }

        return $this->isFormValid;
    }

    /**
     * @param string $element
     *
     * @return mixed
     * @throws Exception
     */
    public function getValue(string $element)
    {
        if (!$this->isFormValid || !$this->isFormPopulated) {
            throw new Exception('The form is not valid or populated');
        }

        return $this->formValues[$element] ?? null;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->formErrors;
    }

    protected function applyFilters()
    {
        // filter values
        foreach ($this->formElements as $element) {
            if (isset($this->formValues[$element])
                && $this->filters[$element]) {
                /** @var FilterInterface $filter */
                foreach ($this->filters[$element] as $filter) {
                    $this->formValues[$element] = $filter->getValue(
                        $this->formValues[$element]
                    );
                }
            }
        }
    }

}