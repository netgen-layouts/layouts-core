<?php

namespace Netgen\BlockManager\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

class BlockDefinition extends Constraint
{
    /**
     * @var string
     */
    public $message = 'Block definition "%definitionIdentifier%" does not exist.';

    /**
     * Returns the name of the class that validates this constraint.
     *
     * @return string
     */
    public function validatedBy()
    {
        return 'ngbm_block_definition';
    }
}
