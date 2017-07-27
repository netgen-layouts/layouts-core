<?php

namespace Netgen\BlockManager\Validator\Constraint\Structs;

use Symfony\Component\Validator\Constraint;

class QueryUpdateStruct extends Constraint
{
    public $untranslatableMessage = 'netgen_block_manager.query_update_struct.untranslatable';

    /**
     * Returns the name of the class that validates this constraint.
     *
     * @return string
     */
    public function validatedBy()
    {
        return 'ngbm_query_update_struct';
    }
}
