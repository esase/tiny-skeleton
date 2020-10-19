<?php

namespace Tiny\Skeleton\Application\Form;

abstract class AbstractFormBuilder
{
    /**
     * @var Form
     */
    protected $form;

    /**
     * FormBuilder constructor.
     *
     * @param Form $form
     */
    public function __construct(Form $form)
    {
        $this->form = $form;
    }

    /**
     * @return Form
     */
    public abstract function initializeForm(): Form;
}