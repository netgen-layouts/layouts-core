<?php

declare(strict_types=1);

namespace Netgen\Layouts\Validator\Constraint;

use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

final class Layout extends Constraint
{
    #[HasNamedArguments]
    public function __construct(
        public bool $allowInvalid = false,
        public string $message = 'netgen_layouts.layout.layout_not_found',
        public string $invalidFormatMessage = 'netgen_layouts.layout.invalid_format',
        ?array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct(null, $groups, $payload);
    }

    public function validatedBy(): string
    {
        return 'nglayouts_layout';
    }
}
