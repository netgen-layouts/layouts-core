<?php

declare(strict_types=1);

namespace Netgen\Layouts\Validator\Constraint;

use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

final class BlockDefinition extends Constraint
{
    #[HasNamedArguments]
    public function __construct(
        public bool $allowInvalid = false,
        public string $message = 'netgen_layouts.block_definition.block_definition_not_found',
        ?array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct(null, $groups, $payload);
    }

    public function validatedBy(): string
    {
        return 'nglayouts_block_definition';
    }
}
