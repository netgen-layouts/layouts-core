<?php

declare(strict_types=1);

namespace Netgen\Layouts\Validator\Constraint;

use Netgen\Layouts\Block\BlockDefinitionInterface;
use Symfony\Component\Validator\Constraint;

final class BlockViewType extends Constraint
{
    public string $message = 'netgen_layouts.block_view_type.no_view_type';

    /**
     * Block definition used to check the view type against.
     */
    public BlockDefinitionInterface $definition;

    /**
     * @return string[]
     */
    public function getRequiredOptions(): array
    {
        return ['definition'];
    }

    public function validatedBy(): string
    {
        return 'nglayouts_block_view_type';
    }
}
