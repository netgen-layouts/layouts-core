<?php

namespace Netgen\BlockManager\Validator\Constraint\Parameters;

use Symfony\Component\Validator\Constraint;

final class Link extends Constraint
{
    /**
     * If true, link value cannot be empty.
     *
     * @var bool
     */
    public $required = false;

    /**
     * If not empty, will limit valid value types to the specified list.
     *
     * @var array
     */
    public $valueTypes = array();

    public function validatedBy()
    {
        return 'ngbm_link';
    }
}
