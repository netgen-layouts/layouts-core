<?php

declare(strict_types=1);

namespace Netgen\Layouts\Validator\Constraint\Structs;

use Symfony\Component\Validator\Constraint;

final class BlockCreateStruct extends Constraint
{
    public function validatedBy(): string
    {
        return 'nglayouts_block_create_struct';
    }
}
