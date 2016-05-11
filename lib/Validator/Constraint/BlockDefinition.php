<?php

namespace Netgen\BlockManager\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

class BlockDefinition extends Constraint
{
    /**
     * @var string
     */
    public $message = 'netgen_block_manager.block_definition.no_block_definition';

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
