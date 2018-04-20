<?php

namespace Netgen\BlockManager\Validator\Constraint\Structs;

use Symfony\Component\Validator\Constraint;

final class BlockCreateStruct extends Constraint
{
    public function validatedBy()
    {
        return 'ngbm_block_create_struct';
    }
}
