<?php

namespace Netgen\BlockManager\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

class Layout extends Constraint
{
    /**
     * @var string
     */
    public $message = 'Layout "%identifier%" does not exist.';

    /**
     * Returns the name of the class that validates this constraint.
     *
     * @return string
     */
    public function validatedBy()
    {
        return 'ngbm_layout';
    }
}
