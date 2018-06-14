<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Validator\Constraint\Structs;

use Symfony\Component\Validator\Constraint;

final class BlockUpdateStruct extends Constraint
{
    public function validatedBy(): string
    {
        return 'ngbm_block_update_struct';
    }
}
