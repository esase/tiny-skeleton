<?php

namespace Tiny\Skeleton\Application\Form\Filter;

interface FilterInterface
{

    /**
     * @param mixed
     *
     * @return mixed
     */
    public function getValue($value);

}