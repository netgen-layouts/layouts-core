<?php

namespace Netgen\BlockManager\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

class BlockParameters extends Constraint
{
    /**
     * @var string
     */
    public $message = '%whatIsWrong%';

    /**
     * @var string
     */
    public $missingParameterMessage = 'Parameter "%parameter%" is missing.';

    /**
     * @var string
     */
    public $excessParameterMessage = 'Parameter "%parameter%" does not exist in block definition.';

    /**
     * @var string
     */
    public $definitionIdentifier;

    /**
     * Returns the name of the class that validates this constraint.
     *
     * @return string
     */
    public function validatedBy()
    {
        return 'ngbm_block_parameters';
    }
}
