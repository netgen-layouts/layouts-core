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
     * Parameter collection used to validate parameter values against.
     *
     * @var \Netgen\BlockManager\Parameters\ParameterCollectionInterface
     */
    public $parameterCollection;

    /**
     * If true, missing parameters will pass validation (e.g. when updating the value).
     *
     * @var bool
     */
    public $allowMissingFields = false;

    public function validatedBy()
    {
        return 'ngbm_parameter_struct';
    }
}
