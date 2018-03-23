<?php

namespace Netgen\BlockManager\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

final class DateTime extends Constraint
{
    public $invalidTimeZoneMessage = 'netgen_block_manager.datetime.invalid_timezone';

    public function validatedBy()
    {
        return 'ngbm_datetime';
    }
}
