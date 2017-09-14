<?php

namespace Netgen\BlockManager\Validator\Constraint\Structs;

use Symfony\Component\Validator\Constraint;

class BlockUpdateStruct extends Constraint
{
    public function validatedBy()
    {
        return 'ngbm_block_update_struct';
    }
}
