<?php

namespace Netgen\BlockManager\Validator\Constraint\Structs;

use Symfony\Component\Validator\Constraint;

class ParameterStruct extends Constraint
{
    /**
     * @var string
     */
    public $message = 'netgen_block_manager.parameter_struct.invalid_value';

    /**
     * @var \Netgen\BlockManager\Parameters\ParameterDefinitionInterface[]
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
        return 'ngbm_parameter_struct';
    }
}
