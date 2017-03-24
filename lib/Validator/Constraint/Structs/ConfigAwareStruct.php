<?php

namespace Netgen\BlockManager\Validator\Constraint\Structs;

use Symfony\Component\Validator\Constraint;

class ConfigAwareStruct extends Constraint
{
    /**
     * Returns the name of the class that validates this constraint.
     *
     * @return string
     */
    public function validatedBy()
    {
        return 'ngbm_config_aware_struct';
    }
}
