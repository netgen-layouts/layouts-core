<?php

namespace Netgen\BlockManager\Validator\Constraint\Parameters;

use Symfony\Component\Validator\Constraint;

class Link extends Constraint
{
    /**
     * @var array
     */
    public $valueTypes = array();

    /**
     * Returns the name of the class that validates this constraint.
     *
     * @return string
     */
    public function validatedBy()
    {
        return 'ngbm_link';
    }
}
