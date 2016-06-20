<?php

namespace Netgen\BlockManager\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

class LayoutName extends Constraint
{
    /**
     * @var string
     */
    public $message = 'netgen_block_manager.layout_name.layout_exists';

    /**
     * Returns the name of the class that validates this constraint.
     *
     * @return string
     */
    public function validatedBy()
    {
        return 'ngbm_layout_name';
    }
}
