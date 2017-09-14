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
     * If specified, layout with this ID will be excluded from the check.
     *
     * @var int
     */
    public $excludedLayoutId;

    public function validatedBy()
    {
        return 'ngbm_layout_name';
    }
}
