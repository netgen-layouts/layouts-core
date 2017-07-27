<?php

namespace Netgen\BlockManager\Validator\Constraint\Structs;

use Symfony\Component\Validator\Constraint;

class BlockUpdateStruct extends Constraint
{
    public $untranslatableMessage = 'netgen_block_manager.block_update_struct.untranslatable';

    /**
     * Returns the name of the class that validates this constraint.
     *
     * @return string
     */
    public function validatedBy()
    {
        return 'ngbm_block_update_struct';
    }
}
