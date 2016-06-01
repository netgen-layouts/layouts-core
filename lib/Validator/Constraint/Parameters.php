<?php

namespace Netgen\BlockManager\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

class Parameters extends Constraint
{
    /**
     * @var string
     */
    public $message = 'netgen_block_manager.parameters.invalid_value';

    /**
     * @var \Netgen\BlockManager\Parameters\ParameterInterface[]
     */
    public $parameters = array();

    /**
     * @var bool
     */
    public $required = false;

    /**
     * Returns the name of the class that validates this constraint.
     *
     * @return string
     */
    public function validatedBy()
    {
        return 'ngbm_parameters';
    }
}
