<?php

declare(strict_types=1);

namespace Netgen\Layouts\Validator\Constraint;

use Netgen\Layouts\Block\BlockDefinitionInterface;
use Symfony\Component\Validator\Constraint;

final class BlockItemViewType extends Constraint
{
    public string $noViewTypeMessage = 'netgen_layouts.block_item_view_type.no_view_type';

    public string $message = 'netgen_layouts.block_item_view_type.no_item_view_type';

    /**
     * View type used to check item view type against.
     */
    public string $viewType;

    /**
     * Block definition used to check item view type against.
     */
    public BlockDefinitionInterface $definition;

    /**
     * @return string[]
     */
    public function getRequiredOptions(): array
    {
        return ['viewType', 'definition'];
    }

    public function validatedBy(): string
    {
        return 'nglayouts_block_item_view_type';
    }
}
