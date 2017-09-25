<?php

namespace Netgen\BlockManager\Validator\Constraint\Structs;

use Symfony\Component\Validator\Constraint;

final class ConfigAwareStruct extends Constraint
{
    public function validatedBy()
    {
        return 'ngbm_config_aware_struct';
    }
}
