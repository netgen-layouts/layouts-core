<?php

declare(strict_types=1);

namespace Netgen\Layouts\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

final class BlockViewType extends Constraint
{
    /**
     * @var string
     */
    public $message = 'netgen_layouts.block_view_type.no_view_type';

    /**
     * Block definition used to check the view type against.
     *
     * @var \Netgen\Layouts\Block\BlockDefinitionInterface
     */
    public $definition;

    public function validatedBy(): string
    {
        return 'nglayouts_block_view_type';
    }
}
