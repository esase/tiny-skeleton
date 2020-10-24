<?php

namespace Tiny\Skeleton\Module\LanguageKey\Form;

use Tiny\Skeleton\Application\Form\AbstractFormBuilder;
use Tiny\Skeleton\Application\Form\Filter\TrimString;
use Tiny\Skeleton\Application\Form\Form;
use Tiny\Skeleton\Application\Form\Validator\Required;
use Tiny\Skeleton\Module\LanguageKey\Form\Validator\UniqueKey;
use Tiny\Skeleton\Module\LanguageKey\Service\LanguageKeyService;

class LanguageKeyFormBuilder extends AbstractFormBuilder
{
    const NAME = 'name';

    /**
     * @var int
     */
    protected int $languageId = -1;

    /**
     * @var LanguageKeyService
     */
    private LanguageKeyService $languageKeyService;

    public function __construct(
        Form $form,
        LanguageKeyService $languageKeyService
    ) {
        parent::__construct($form);
        $this->languageKeyService = $languageKeyService;
    }

    /**
     * @return Form
     */
    public function initializeForm(): Form
    {
        $this->form->addElement(
            self::NAME, [
            new Required(),
            new UniqueKey($this->languageKeyService, $this->languageId)
        ], [new TrimString()]
        );

        return $this->form;
    }

    /**
     * @param int $languageId
     */
    public function setLanguageKeyId(int $languageId)
    {
        $this->languageId = $languageId;
    }

}