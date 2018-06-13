<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

final class BlockItemViewType extends Constraint
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
     * View type used to check item view type against.
     *
     * @var string
     */
    public $viewType;

    /**
     * Block definition used to check item view type against.
     *
     * @var \Netgen\BlockManager\Block\BlockDefinitionInterface
     */
    public $definition;

    public function validatedBy()
    {
        return 'ngbm_block_item_view_type';
    }
}
