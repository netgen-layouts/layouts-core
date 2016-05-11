<?php

namespace Netgen\BlockManager\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

class QueryType extends Constraint
{
    /**
     * @var string
     */
    public $message = 'netgen_block_manager.query_type.no_query_type';

    /**
     * Returns the name of the class that validates this constraint.
     *
     * @return string
     */
    public function validatedBy()
    {
        return 'ngbm_query_type';
    }
}
