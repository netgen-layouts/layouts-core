<?php

namespace Netgen\BlockManager\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

class ValueType extends Constraint
{
    /**
     * @var string
     */
    public $message = 'Value type "%valueType%" does not exist.';

    /**
     * Returns the name of the class that validates this constraint.
     *
     * @return string
     */
    public function validatedBy()
    {
        return 'ngbm_value_type';
    }
}
