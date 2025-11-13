<?php

declare(strict_types=1);

namespace Netgen\Layouts\Validator\Constraint;

use Netgen\Layouts\Block\BlockDefinitionInterface;
use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

final class BlockItemViewType extends Constraint
{
    #[HasNamedArguments]
    public function __construct(
        /**
         * View type used to check item view type against.
         */
        public string $viewType,
        /**
         * Block definition used to check item view type against.
         */
        public BlockDefinitionInterface $definition,
        public string $message = 'netgen_layouts.block_item_view_type.no_item_view_type',
        public string $noViewTypeMessage = 'netgen_layouts.block_item_view_type.no_view_type',
        ?array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct(null, $groups, $payload);
    }

    public function validatedBy(): string
    {
        return 'nglayouts_block_item_view_type';
    }
}
