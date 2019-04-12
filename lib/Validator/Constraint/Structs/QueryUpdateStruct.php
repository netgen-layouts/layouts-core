<?php

declare(strict_types=1);

namespace Netgen\Layouts\Validator\Constraint\Structs;

use Symfony\Component\Validator\Constraint;

final class QueryUpdateStruct extends Constraint
{
    public function validatedBy(): string
    {
        return 'nglayouts_query_update_struct';
    }
}
