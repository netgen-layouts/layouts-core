<?php

namespace Netgen\BlockManager\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

class BlockViewType extends Constraint
{
    /**
     * @var string
     */
    public $message = 'netgen_block_manager.block_view_type.no_view_type';

    /**
     * @var \Netgen\BlockManager\Block\BlockDefinitionInterface
     */
    public $definition;

    /**
     * Returns the name of the class that validates this constraint.
     *
     * @return string
     */
    public function validatedBy()
    {
        return 'ngbm_block_view_type';
    }
}
