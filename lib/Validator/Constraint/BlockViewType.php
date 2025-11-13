<?php

declare(strict_types=1);

namespace Netgen\Layouts\Validator\Constraint;

use Netgen\Layouts\Block\BlockDefinitionInterface;
use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

final class BlockViewType extends Constraint
{
    #[HasNamedArguments]
    public function __construct(
        /**
         * Block definition used to check the view type against.
         */
        public BlockDefinitionInterface $definition,
        public string $message = 'netgen_layouts.block_view_type.no_view_type',
        ?array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct(null, $groups, $payload);
    }

    public function validatedBy(): string
    {
        return 'nglayouts_block_view_type';
    }
}
