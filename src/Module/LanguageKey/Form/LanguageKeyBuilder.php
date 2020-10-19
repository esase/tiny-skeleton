<?php

namespace Tiny\Skeleton\Module\LanguageKey\Form;

use Tiny\Skeleton\Application\Form\AbstractFormBuilder;
use Tiny\Skeleton\Application\Form\Filter\TrimString;
use Tiny\Skeleton\Application\Form\Form;
use Tiny\Skeleton\Application\Form\Validator\Required;

class LanguageKeyBuilder extends AbstractFormBuilder
{
    const NAME = 'name';

    /**
     * @return Form
     */
    public function initializeForm(): Form
    {
        $this->form->addElement(
            self::NAME, [
            new Required()
        ], [
                new TrimString()
            ]
        );

        return $this->form;
    }
}