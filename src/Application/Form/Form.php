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
    protected array $formElements = [];

    /**
     * @var array
     */
    protected array $validators = [];

    /**
     * @var array
     */
    protected array $filters = [];

    /**
     * @var array
     */
    protected array $formValues = [];

    /**
     * @var bool
     */
    private bool $isFormPopulated = false;

    /**
     * @var bool
     */
    protected bool $isFormValid = true;

    /**
     * @var array
     */
    protected array $formErrors = [];

    /**
     * @var array
     */
    protected array $formErroredValidators = [];

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
                        $this->formValues[$element] ?? null,
                        $this->formValues ?? []
                    )) {
                        $this->isFormValid = false;
                        $this->formErrors[] = $validator->getErrorMessage(
                            $element
                        );
                        $this->formErroredValidators[] = get_class($validator);

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

    /**
     * @return array
     */
    public function getErroredValidators()
    {
        return $this->formErroredValidators;
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