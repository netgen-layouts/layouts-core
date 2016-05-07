<?php

namespace Netgen\BlockManager\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

class QueryType extends Constraint
{
    /**
     * @var string
     */
    public $message = 'Query type "%queryType%" does not exist.';

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
