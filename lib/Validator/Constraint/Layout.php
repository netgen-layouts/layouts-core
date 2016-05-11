<?php

namespace Netgen\BlockManager\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

class Layout extends Constraint
{
    /**
     * @var string
     */
    public $message = 'netgen_block_manager.layout.no_layout_type';

    /**
     * Returns the name of the class that validates this constraint.
     *
     * @return string
     */
    public function validatedBy()
    {
        return 'ngbm_layout';
    }
}
