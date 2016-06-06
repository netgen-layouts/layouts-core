<?php

namespace Netgen\BlockManager\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

class BlockItemViewType extends Constraint
{
    /**
     * @var string
     */
    public $noViewTypeMessage = 'netgen_block_manager.block_item_view_type.no_view_type';

    /**
     * @var string
     */
    public $message = 'netgen_block_manager.block_item_view_type.no_item_view_type';

    /**
     * @var string
     */
    public $viewType;

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
        return 'ngbm_block_item_view_type';
    }
}
